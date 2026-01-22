@extends('layouts.dashboard')

@section('content')
    <div class="w-full">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <!-- Mensajes de error -->
            @if($errors->any())
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('sales.store') }}" method="POST" id="saleForm">
                @csrf

                <!-- Tipo de Comprobante (oculto en el formulario, se puede mostrar en el resumen) -->
                <input type="hidden" name="tipo_comprobante" id="tipo_comprobante" value="{{ old('tipo_comprobante', 'ticket') }}" required>

                <!-- Sesión de Caja (oculto) -->
                <input type="hidden" name="sesion_caja_id" value="{{ $session->id }}">

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- PRIMERA FILA: Productos -->
                    <div class="space-y-6">
                        <!-- Buscador -->
                        <div>
                            <div class="flex items-center gap-3">
                                <div class="relative flex-1">
                                    <input type="text" 
                                           id="searchProduct" 
                                           placeholder="Buscar producto..." 
                                           class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <svg class="absolute left-3 top-3.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                                <!-- Toggle Switch -->
                                <div class="flex items-center">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" id="toggleSwitch" class="sr-only peer" />
                                        <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-blue-600"></div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Carrusel de Categorías -->
                        <div>
                            <div class="flex space-x-2 overflow-x-auto pb-2" id="categoriesCarousel">
                                <button type="button" 
                                        onclick="filtrarPorCategoria(null)" 
                                        class="categoria-btn px-4 py-2 bg-blue-600 text-white rounded-lg whitespace-nowrap hover:bg-blue-700 transition-colors font-medium">
                                    Todas
                                </button>
                                @foreach($categories as $category)
                                    <button type="button" 
                                            onclick="filtrarPorCategoria({{ $category->id }})" 
                                            class="categoria-btn px-4 py-2 bg-white text-blue-600 border border-blue-600 rounded-lg whitespace-nowrap hover:bg-blue-50 transition-colors"
                                            data-categoria-id="{{ $category->id }}">
                                        {{ $category->nombre }}
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <!-- Productos en Tarjetas -->
                        <div id="productsContainer" class="grid grid-cols-2 gap-4 max-h-[600px] overflow-y-auto">
                            <!-- Las tarjetas se generarán dinámicamente con JavaScript -->
                        </div>
                    </div>

                    <!-- SEGUNDA FILA: Cliente, Resumen y Pagos -->
                    <div class="space-y-6">
                        <!-- Cliente -->
                        <div class="bg-white border border-gray-300 rounded-lg p-4">
                            <h3 class="text-lg font-semibold text-gray-700 mb-4">Cliente</h3>
                            
                            <!-- Tipo de Comprobante -->
                            <div class="mb-4">
                                <label for="tipo_comprobante_select" class="block text-sm font-medium text-gray-700 mb-2">
                                    Tipo de Comprobante <span class="text-red-500">*</span>
                                </label>
                                <select name="tipo_comprobante_select" 
                                        id="tipo_comprobante_select" 
                                        onchange="cambiarTipoComprobante(this.value)"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="ticket" {{ old('tipo_comprobante', 'ticket') == 'ticket' ? 'selected' : '' }}>Ticket</option>
                                    <option value="boleta" {{ old('tipo_comprobante') == 'boleta' ? 'selected' : '' }}>Boleta</option>
                                    <option value="factura" {{ old('tipo_comprobante') == 'factura' ? 'selected' : '' }}>Factura</option>
                                </select>
                            </div>

                            <!-- Cliente Genérico (solo para Ticket) -->
                            <div id="cliente-generico-container">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Cliente
                                </label>
                                <div class="w-full px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-600">
                                    Cliente Genérico
                                </div>
                                <input type="hidden" name="cliente_id" id="cliente_id" value="">
                            </div>

                            <!-- Input DNI/RUC (solo para Boleta/Factura) -->
                            <div id="cliente-documento-container" style="display: none;">
                                <label for="numero_documento" class="block text-sm font-medium text-gray-700 mb-2">
                                    <span id="label-documento">DNI</span> <span id="asterisco-documento" class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="text" 
                                           name="numero_documento" 
                                           id="numero_documento"
                                           placeholder="Ingrese DNI o RUC"
                                           maxlength="11"
                                           oninput="consultarDocumento(this.value)"
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <div id="loading-documento" class="absolute right-3 top-2.5 hidden">
                                        <svg class="animate-spin h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <p class="mt-1 text-xs text-gray-500" id="hint-documento">
                                    Ingrese el número de documento del cliente
                                </p>
                                <p class="mt-1 text-xs text-blue-600 hidden" id="hint-boleta-simple">
                                    <strong>Boleta Simple:</strong> Deje este campo vacío para generar una boleta sin documento del cliente
                                </p>
                                
                                <!-- Información del cliente consultado -->
                                <div id="cliente-info-container" class="mt-3 hidden">
                                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1">
                                                <p class="text-sm font-semibold text-gray-800" id="cliente-nombre-info"></p>
                                                <p class="text-xs text-gray-600 mt-1" id="cliente-documento-info"></p>
                                                <p class="text-xs text-gray-600 mt-1" id="cliente-direccion-info"></p>
                                                <p class="text-xs text-green-600 mt-1 font-medium" id="cliente-existe-info"></p>
                                            </div>
                                            <button type="button" 
                                                    onclick="cerrarInfoCliente()"
                                                    class="text-gray-400 hover:text-gray-600">
                                                ✕
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                @error('numero_documento')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                @error('cliente_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Resumen de Compra -->
                        <div class="bg-white border border-gray-300 rounded-lg p-4">
                            <h3 class="text-lg font-semibold text-gray-700 mb-4">Resumen de Compra</h3>
                            
                            <!-- Lista de productos agregados -->
                            <div id="cartItems" class="mb-4 max-h-[300px] overflow-y-auto">
                                <p class="text-sm text-gray-500 text-center py-4">No hay productos en el carrito</p>
                            </div>

                            <!-- Totales -->
                            <div class="border-t border-gray-300 pt-4 space-y-2">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-700">Subtotal (sin IGV):</span>
                                    <span id="display-total-gravado" class="font-medium">S/ 0.00</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-700">IGV (18%):</span>
                                    <span id="display-total-igv" class="font-medium">S/ 0.00</span>
                                </div>
                                <div class="flex justify-between text-lg font-bold border-t border-gray-300 pt-2">
                                    <span class="text-gray-800">Total (con IGV):</span>
                                    <span id="display-total-venta" class="text-blue-600">S/ 0.00</span>
                                </div>
                            </div>

                            <!-- Campos ocultos para el formulario -->
                            <input type="hidden" name="total_gravado" id="total_gravado" value="0" required>
                            <input type="hidden" name="total_igv" id="total_igv" value="0" required>
                            <input type="hidden" name="total_venta" id="total_venta" value="0" required>
                        </div>

                        <!-- Datos de Pago -->
                        <div class="bg-white border border-gray-300 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-700">Datos de Pago</h3>
                                <button type="button" 
                                        onclick="agregarPago()" 
                                        class="px-3 py-1.5 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition-colors">
                                    + Agregar
                                </button>
                            </div>

                            <div id="pagos-container" class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Método de Pago</th>
                                            <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Monto</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Referencia</th>
                                            <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <!-- Primera fila de pago -->
                                        <tr class="pago-item hover:bg-gray-50">
                                            <td class="px-3 py-2 whitespace-nowrap">
                                                <select name="pagos[0][metodo_pago]" 
                                                        required
                                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                                    <option value="">Seleccione...</option>
                                                    <option value="efectivo">Efectivo</option>
                                                    <option value="tarjeta">Tarjeta</option>
                                                    <option value="billetera_virtual">Billetera Virtual</option>
                                                    <option value="otro">Otro</option>
                                                </select>
                                            </td>
                                            <td class="px-3 py-2 whitespace-nowrap text-right">
                                                <input type="number" 
                                                       name="pagos[0][monto_pagado]" 
                                                       value="0" 
                                                       step="0.01" 
                                                       min="0.01" 
                                                       required
                                                       onchange="calcularTotalPagos()"
                                                       class="w-32 px-3 py-2 text-sm text-right border border-gray-300 rounded-lg monto-pago-input focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                            </td>
                                            <td class="px-3 py-2 whitespace-nowrap">
                                                <input type="text" 
                                                       name="pagos[0][referencia]" 
                                                       value="" 
                                                       placeholder="Opcional"
                                                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                            </td>
                                            <td class="px-3 py-2 whitespace-nowrap text-center">
                                                <button type="button" 
                                                        onclick="eliminarPago(this)" 
                                                        class="text-red-600 hover:text-red-800 text-sm font-medium">
                                                    ✕
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-4 bg-yellow-50 border border-yellow-300 rounded-lg p-3">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-medium text-gray-700">Total Pagado:</span>
                                    <span id="total-pagado" class="text-lg font-semibold text-gray-900">S/ 0.00</span>
                                </div>
                                <div>
                                    <span id="diferencia-pago" class="text-sm"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="flex items-center justify-end space-x-4">
                            <a href="{{ route('sales.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                                Cancelar
                            </a>
                            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                                Registrar Venta
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Contenedor oculto para los detalles del formulario -->
                <div id="detalles-hidden-container" style="display: none;"></div>
            </form>
        </div>
    </div>

    <script>
        (function() {
            // Limpiar datos anteriores si existen
            if (window.salesData) {
                delete window.salesData;
            }
            
            // Datos disponibles - actualizar siempre para tener los datos más recientes
            window.salesData = {
                presentations: @json($presentations),
                promotions: @json($promotions),
                categories: @json($categories)
            };
            
            // Usar referencias locales
            const presentations = window.salesData.presentations;
            const promotions = window.salesData.promotions;
            const categories = window.salesData.categories;

            let detalleIndex = 0;
            let pagoIndex = 1;
            let cartItems = [];
            let categoriaFiltro = null;

            // Inicializar productos al cargar
            function inicializar() {
                renderizarProductos();
                calcularTotales();
                calcularTotalPagos();
            }

            // Inicializar cuando el DOM esté listo
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', inicializar);
            } else {
                inicializar();
            }

            // Función para renderizar productos
            function renderizarProductos() {
            const container = document.getElementById('productsContainer');
            const searchTerm = document.getElementById('searchProduct').value.toLowerCase();
            
            let productosFiltrados = presentations.filter(pres => {
                const matchSearch = !searchTerm || 
                    pres.nombre.toLowerCase().includes(searchTerm) ||
                    (pres.product?.nombre || '').toLowerCase().includes(searchTerm) ||
                    (pres.barcode || '').toLowerCase().includes(searchTerm);
                
                const matchCategoria = !categoriaFiltro || 
                    (pres.product?.categoria_id == categoriaFiltro);
                
                return matchSearch && matchCategoria;
            });

            // Agregar promociones activas
            let promocionesFiltradas = promotions.filter(promo => {
                const matchSearch = !searchTerm || 
                    promo.nombre.toLowerCase().includes(searchTerm) ||
                    (promo.descripcion || '').toLowerCase().includes(searchTerm);
                
                return matchSearch; // Las promociones no tienen categoría
            });

            if (productosFiltrados.length === 0 && promocionesFiltradas.length === 0) {
                container.innerHTML = '<div class="col-span-2 text-center text-gray-500 py-8">No se encontraron productos</div>';
                return;
            }

            let html = productosFiltrados.map(pres => {
                const categoriaNombre = pres.product?.category?.nombre || 'Sin categoría';
                const stock = pres.stock || 0;
                const nombreProducto = pres.product?.nombre || 'Sin nombre';
                const nombrePresentacion = pres.nombre || 'Sin presentación';
                const unidades = pres.unidades || 1;
                const codigoBarras = pres.barcode || 'Sin código';

                return `
                    <div class="bg-white border border-gray-300 rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow cursor-pointer product-card" 
                         onclick="agregarAlCarrito(${pres.id}, 'presentation')"
                         data-product-id="${pres.id}"
                         data-categoria-id="${pres.product?.categoria_id || ''}">
                        <div class="flex justify-between items-start mb-2">
                            <h4 class="font-bold text-gray-800 text-sm">
                                ${nombreProducto} - ${nombrePresentacion}
                            </h4>
                            <span class="px-2 py-1 bg-green-500 text-white text-xs rounded">${unidades} ud.</span>
                        </div>
                        <p class="text-xs text-gray-500 mb-1">Código: ${codigoBarras}</p>
                        <p class="text-xs text-gray-500 mb-3">${categoriaNombre}</p>
                        <div class="flex items-center justify-between">
                            <span class="text-lg font-bold text-blue-600">S/ ${parseFloat(pres.precio_venta).toFixed(2)}</span>
                            <button type="button" 
                                    onclick="event.stopPropagation(); agregarAlCarrito(${pres.id}, 'presentation')"
                                    class="px-3 py-1.5 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700 transition-colors ${stock <= 0 ? 'opacity-50 cursor-not-allowed' : ''}"
                                    ${stock <= 0 ? 'disabled' : ''}>
                                Añadir al carrito
                            </button>
                        </div>
                    </div>
                `;
            }).join('');

            // Agregar promociones
            html += promocionesFiltradas.map(promo => {
                const fechaInicio = new Date(promo.fecha_inicio);
                const fechaFin = new Date(promo.fecha_fin);
                const hoy = new Date();
                const activa = promo.activa && fechaInicio <= hoy && fechaFin >= hoy;

                return `
                    <div class="bg-white border-2 border-purple-300 rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow cursor-pointer product-card" 
                         onclick="agregarAlCarrito(${promo.id}, 'promotion')"
                         data-product-id="${promo.id}"
                         data-tipo="promotion">
                        <div class="flex justify-between items-start mb-2">
                            <h4 class="font-bold text-gray-800 text-sm">${promo.nombre}</h4>
                            <span class="px-2 py-1 bg-purple-500 text-white text-xs rounded">Promoción</span>
                        </div>
                        <p class="text-xs text-gray-500 mb-1">Combo</p>
                        <p class="text-xs text-gray-600 mb-3 line-clamp-2">${promo.descripcion || 'Promoción especial'}</p>
                        <div class="flex items-center justify-between">
                            <span class="text-lg font-bold text-purple-600">S/ ${parseFloat(promo.precio_promocional).toFixed(2)}</span>
                            <button type="button" 
                                    onclick="event.stopPropagation(); agregarAlCarrito(${promo.id}, 'promotion')"
                                    class="px-3 py-1.5 bg-purple-600 text-white text-xs rounded-lg hover:bg-purple-700 transition-colors ${!activa ? 'opacity-50 cursor-not-allowed' : ''}"
                                    ${!activa ? 'disabled' : ''}>
                                Añadir al carrito
                            </button>
                        </div>
                    </div>
                `;
            }).join('');

            container.innerHTML = html;
        }

            // Función para obtener stock
            function obtenerStock(productId) {
            const presentation = presentations.find(p => p.product_id == productId);
            return presentation ? (presentation.stock || 0) : 0;
        }

            // Función para filtrar por categoría
            function filtrarPorCategoria(categoriaId) {
            categoriaFiltro = categoriaId;
            
            // Actualizar estilos de botones
            document.querySelectorAll('.categoria-btn').forEach(btn => {
                if (categoriaId === null && btn.textContent.trim() === 'Todas') {
                    btn.className = 'categoria-btn px-4 py-2 bg-blue-600 text-white rounded-lg whitespace-nowrap hover:bg-blue-700 transition-colors font-medium';
                } else if (btn.dataset.categoriaId == categoriaId) {
                    btn.className = 'categoria-btn px-4 py-2 bg-blue-600 text-white rounded-lg whitespace-nowrap hover:bg-blue-700 transition-colors font-medium';
                } else {
                    btn.className = 'categoria-btn px-4 py-2 bg-white text-blue-600 border border-blue-600 rounded-lg whitespace-nowrap hover:bg-blue-50 transition-colors';
                }
            });
            
            renderizarProductos();
        }

            // Buscador
            document.getElementById('searchProduct').addEventListener('input', function() {
                renderizarProductos();
            });

            // Función para agregar al carrito
            function agregarAlCarrito(vendibleId, tipo) {
            let vendible;
            if (tipo === 'presentation') {
                vendible = presentations.find(p => p.id == vendibleId);
                if (!vendible) return;
                
                // Verificar stock
                const stock = vendible.stock || 0;
                if (stock <= 0) {
                    alert('No hay stock disponible para este producto.');
                    return;
                }
            } else if (tipo === 'promotion') {
                vendible = promotions.find(p => p.id == vendibleId);
                if (!vendible) return;
            } else {
                return;
            }

            // Buscar si ya existe en el carrito
            const existente = cartItems.findIndex(item => 
                item.vendible_id == vendibleId && item.tipo === tipo
            );

            if (existente >= 0) {
                const nuevaCantidad = parseFloat(cartItems[existente].cantidad) + 1;
                // Validar stock al incrementar cantidad
                if (tipo === 'presentation') {
                    const unidadesNecesarias = nuevaCantidad * parseFloat(cartItems[existente].unidades);
                    if (vendible.stock < unidadesNecesarias) {
                        alert(`Stock insuficiente. Stock disponible: ${vendible.stock} unidades.`);
                        return;
                    }
                }
                cartItems[existente].cantidad = nuevaCantidad;
                actualizarSubtotal(existente);
            } else {
                const precio = tipo === 'presentation' ? parseFloat(vendible.precio_venta) || 0 : parseFloat(vendible.precio_promocional) || 0;
                const nombreCompleto = tipo === 'presentation' 
                    ? `${vendible.product?.nombre || 'Sin nombre'} - ${vendible.nombre}`
                    : vendible.nombre;
                
                cartItems.push({
                    tipo: tipo,
                    vendible_id: vendibleId,
                    nombre: nombreCompleto,
                    nombre_producto: tipo === 'presentation' ? vendible.product?.nombre : '',
                    nombre_presentacion: tipo === 'presentation' ? vendible.nombre : '',
                    unidades: tipo === 'presentation' ? (parseFloat(vendible.unidades) || 1) : 1,
                    precio_unitario: precio,
                    cantidad: 1,
                    descuento: 0,
                    subtotal: precio
                });
            }

            actualizarCarrito();
            calcularTotales();
        }

            // Función para actualizar la vista del carrito
            function actualizarCarrito() {
            const container = document.getElementById('cartItems');
            
            if (cartItems.length === 0) {
                container.innerHTML = '<p class="text-sm text-gray-500 text-center py-4">No hay productos en el carrito</p>';
                return;
            }

            let html = `
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producto</th>
                                <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Precio Unit.</th>
                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Descuento</th>
                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                                <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
            `;

            cartItems.forEach((item, index) => {
                const cantidad = parseFloat(item.cantidad) || 0;
                const precioUnitario = parseFloat(item.precio_unitario) || 0;
                const descuento = parseFloat(item.descuento) || 0;
                const subtotal = (cantidad * precioUnitario) - descuento;
                
                // Actualizar el subtotal en el item
                item.subtotal = subtotal;

                html += `
                    <tr class="hover:bg-gray-50">
                        <td class="px-3 py-2 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">${item.nombre}</div>
                        </td>
                        <td class="px-3 py-2 whitespace-nowrap text-center">
                            <input type="number" 
                                   value="${cantidad}" 
                                   min="0.01" 
                                   step="0.01"
                                   onchange="actualizarCantidad(${index}, this.value)"
                                   class="w-20 px-2 py-1 text-sm text-center border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </td>
                        <td class="px-3 py-2 whitespace-nowrap text-right text-sm text-gray-900">
                            S/ ${precioUnitario.toFixed(2)}
                        </td>
                        <td class="px-3 py-2 whitespace-nowrap text-right">
                            <input type="number" 
                                   value="${descuento}" 
                                   min="0" 
                                   step="0.01"
                                   onchange="actualizarDescuento(${index}, this.value)"
                                   class="w-24 px-2 py-1 text-sm text-right border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </td>
                        <td class="px-3 py-2 whitespace-nowrap text-right text-sm font-medium text-gray-900">
                            S/ ${subtotal.toFixed(2)}
                        </td>
                        <td class="px-3 py-2 whitespace-nowrap text-center">
                            <button type="button" 
                                    onclick="eliminarDelCarrito(${index})"
                                    class="text-red-600 hover:text-red-800 text-sm font-medium">
                                ✕
                            </button>
                        </td>
                    </tr>
                `;
            });

            html += `
                        </tbody>
                    </table>
                </div>
            `;

            container.innerHTML = html;
        }

            // Función para actualizar cantidad
            function actualizarCantidad(index, cantidad) {
            if (cartItems[index]) {
                const nuevaCantidad = parseFloat(cantidad) || 1;
                
                // Validar stock si es una presentación
                if (cartItems[index].tipo === 'presentation') {
                    const vendible = presentations.find(p => p.id == cartItems[index].vendible_id);
                    if (vendible) {
                        const unidadesNecesarias = nuevaCantidad * cartItems[index].unidades;
                        if (vendible.stock < unidadesNecesarias) {
                            alert(`Stock insuficiente. Stock disponible: ${vendible.stock} unidades. Necesitas: ${unidadesNecesarias} unidades.`);
                            // Restaurar valor anterior
                            const item = document.querySelectorAll('#cartItems .border')[index];
                            if (item) {
                                const cantidadInput = item.querySelector('input[type="number"]');
                                if (cantidadInput) {
                                    cantidadInput.value = cartItems[index].cantidad;
                                }
                            }
                            return;
                        }
                    }
                }
                
                cartItems[index].cantidad = nuevaCantidad;
                actualizarSubtotal(index);
                actualizarCarrito();
                calcularTotales();
            }
        }

            // Función para actualizar descuento
            function actualizarDescuento(index, descuento) {
            if (cartItems[index]) {
                cartItems[index].descuento = parseFloat(descuento) || 0;
                actualizarSubtotal(index);
                actualizarCarrito();
                calcularTotales();
            }
        }

            // Función para actualizar subtotal
            function actualizarSubtotal(index) {
            const item = cartItems[index];
            if (item) {
                const cantidad = parseFloat(item.cantidad) || 0;
                const precioUnitario = parseFloat(item.precio_unitario) || 0;
                const descuento = parseFloat(item.descuento) || 0;
                item.subtotal = (cantidad * precioUnitario) - descuento;
            }
        }

            // Función para eliminar del carrito
            function eliminarDelCarrito(index) {
            cartItems.splice(index, 1);
            actualizarCarrito();
            calcularTotales();
        }

            // Función para calcular totales
            // Nota: Los precios ya incluyen IGV, por lo que debemos calcular el total gravado y el IGV a partir del total con IGV
            function calcularTotales() {
            let totalVenta = 0; // Total con IGV incluido
            
            cartItems.forEach(item => {
                const subtotal = parseFloat(item.subtotal) || 0;
                totalVenta += subtotal;
            });
            
            totalVenta = parseFloat(totalVenta.toFixed(2));
            
            // Calcular el total gravado (sin IGV) y el IGV
            // Si el precio incluye IGV al 18%: Total con IGV = Total Gravado * 1.18
            // Por lo tanto: Total Gravado = Total con IGV / 1.18
            const totalGravado = parseFloat((totalVenta / 1.18).toFixed(2));
            const totalIGV = parseFloat((totalVenta - totalGravado).toFixed(2));
            
            document.getElementById('total_gravado').value = totalGravado.toFixed(2);
            document.getElementById('total_igv').value = totalIGV.toFixed(2);
            document.getElementById('total_venta').value = totalVenta.toFixed(2);
            
            document.getElementById('display-total-gravado').textContent = `S/ ${totalGravado.toFixed(2)}`;
            document.getElementById('display-total-igv').textContent = `S/ ${totalIGV.toFixed(2)}`;
            document.getElementById('display-total-venta').textContent = `S/ ${totalVenta.toFixed(2)}`;
            
            calcularTotalPagos();
            generarDetallesOcultos();
        }

            // Función para generar los campos ocultos de detalles
            function generarDetallesOcultos() {
            const container = document.getElementById('detalles-hidden-container');
            container.innerHTML = cartItems.map((item, index) => {
                return `
                    <input type="hidden" name="detalles[${index}][tipo]" value="${item.tipo}">
                    <input type="hidden" name="detalles[${index}][vendible_id]" value="${item.vendible_id}">
                    <input type="hidden" name="detalles[${index}][cantidad]" value="${item.cantidad}">
                    <input type="hidden" name="detalles[${index}][precio_unitario]" value="${item.precio_unitario}">
                    <input type="hidden" name="detalles[${index}][descuento]" value="${item.descuento}">
                    <input type="hidden" name="detalles[${index}][subtotal]" value="${item.subtotal}">
                `;
            }).join('');
        }

            // Función para agregar pago
            function agregarPago() {
                const container = document.getElementById('pagos-container');
                const tbody = container.querySelector('tbody');
                const primeraFila = tbody.querySelector('.pago-item');
                
                if (!primeraFila || !tbody) return;
                
                const nuevaFila = primeraFila.cloneNode(true);
                nuevaFila.className = 'pago-item hover:bg-gray-50';
                
                // Actualizar índices
                nuevaFila.querySelectorAll('input, select').forEach(input => {
                    if (input.name) {
                        input.name = input.name.replace(/\[0\]/, `[${pagoIndex}]`);
                    }
                });
                
                // Resetear valores
                nuevaFila.querySelector('select').value = '';
                nuevaFila.querySelector('.monto-pago-input').value = '0';
                nuevaFila.querySelector('input[name*="[referencia]"]').value = '';
                
                tbody.appendChild(nuevaFila);
                pagoIndex++;
            }

            // Función para eliminar pago
            function eliminarPago(button) {
                const item = button.closest('.pago-item');
                const container = document.getElementById('pagos-container');
                const tbody = container.querySelector('tbody');
                
                if (tbody && tbody.children.length > 1) {
                    item.remove();
                    calcularTotalPagos();
                } else {
                    alert('Debe haber al menos un método de pago.');
                }
            }

            // Función para calcular total de pagos
            function calcularTotalPagos() {
            let totalPagado = 0;
            let totalEfectivo = 0;
            
            // Calcular total pagado y total en efectivo
            document.querySelectorAll('.pago-item').forEach(row => {
                const montoInput = row.querySelector('.monto-pago-input');
                const metodoSelect = row.querySelector('select[name*="[metodo_pago]"]');
                
                if (montoInput && metodoSelect) {
                    const monto = parseFloat(montoInput.value) || 0;
                    totalPagado += monto;
                    
                    if (metodoSelect.value === 'efectivo') {
                        totalEfectivo += monto;
                    }
                }
            });
            
            document.getElementById('total-pagado').textContent = `S/ ${totalPagado.toFixed(2)}`;
            
            const totalVenta = parseFloat(document.getElementById('total_venta').value) || 0;
            const diferencia = totalPagado - totalVenta;
            const vuelto = Math.max(0, diferencia);
            
            const diferenciaElement = document.getElementById('diferencia-pago');
            if (Math.abs(diferencia) < 0.01) {
                diferenciaElement.innerHTML = '<span class="text-green-600">✓ El total de pagos coincide con el total de la venta.</span>';
                diferenciaElement.className = 'text-sm font-medium';
            } else if (diferencia > 0) {
                // Hay sobrepago - mostrar vuelto si hay pago en efectivo
                if (totalEfectivo > 0 && vuelto > 0) {
                    diferenciaElement.innerHTML = `<span class="text-blue-600 font-semibold">💰 Vuelto: S/ ${vuelto.toFixed(2)}</span>`;
                    diferenciaElement.className = 'text-sm font-medium';
                } else {
                    diferenciaElement.innerHTML = `<span class="text-yellow-600">⚠ Sobrepago: S/ ${diferencia.toFixed(2)} (No hay vuelto - no hay pago en efectivo)</span>`;
                    diferenciaElement.className = 'text-sm font-medium';
                }
            } else {
                diferenciaElement.innerHTML = `<span class="text-red-600">✗ Falta pagar: S/ ${Math.abs(diferencia).toFixed(2)}</span>`;
                diferenciaElement.className = 'text-sm font-medium';
            }
        }

            // Variable para almacenar el cliente consultado
            let clienteConsultado = null;
            let timeoutConsulta = null;

            // Función para cambiar tipo de comprobante
            function cambiarTipoComprobante(tipo) {
                // Actualizar el campo oculto
                document.getElementById('tipo_comprobante').value = tipo;
                
                const clienteGenericoContainer = document.getElementById('cliente-generico-container');
                const clienteDocumentoContainer = document.getElementById('cliente-documento-container');
                const numeroDocumentoInput = document.getElementById('numero_documento');
                const labelDocumento = document.getElementById('label-documento');
                const hintDocumento = document.getElementById('hint-documento');
                
                // Cerrar info del cliente si está abierta
                cerrarInfoCliente();
                clienteConsultado = null;
                
                if (tipo === 'ticket') {
                    // Mostrar Cliente Genérico, ocultar input DNI/RUC
                    clienteGenericoContainer.style.display = 'block';
                    clienteDocumentoContainer.style.display = 'none';
                    document.getElementById('cliente_id').value = '';
                    numeroDocumentoInput.value = '';
                    numeroDocumentoInput.removeAttribute('required');
                } else {
                    // Ocultar Cliente Genérico, mostrar input DNI/RUC
                    clienteGenericoContainer.style.display = 'none';
                    clienteDocumentoContainer.style.display = 'block';
                    
                    // Cambiar label según tipo de comprobante
                    if (tipo === 'boleta') {
                        labelDocumento.textContent = 'DNI';
                        hintDocumento.textContent = 'Ingrese el DNI del cliente (8 dígitos)';
                        hintDocumento.classList.remove('hidden');
                        // Mostrar hint de boleta simple
                        const hintBoletaSimple = document.getElementById('hint-boleta-simple');
                        if (hintBoletaSimple) {
                            hintBoletaSimple.classList.remove('hidden');
                        }
                        // Quitar requerido y asterisco para boletas (pueden ser simples)
                        numeroDocumentoInput.removeAttribute('required');
                        const asteriscoDocumento = document.getElementById('asterisco-documento');
                        if (asteriscoDocumento) {
                            asteriscoDocumento.style.display = 'none';
                        }
                        numeroDocumentoInput.setAttribute('maxlength', '8');
                        numeroDocumentoInput.setAttribute('pattern', '[0-9]{8}');
                    } else if (tipo === 'factura') {
                        labelDocumento.textContent = 'RUC';
                        hintDocumento.textContent = 'Ingrese el RUC del cliente (11 dígitos)';
                        hintDocumento.classList.remove('hidden');
                        // Ocultar hint de boleta simple
                        const hintBoletaSimple = document.getElementById('hint-boleta-simple');
                        if (hintBoletaSimple) {
                            hintBoletaSimple.classList.add('hidden');
                        }
                        // Facturas siempre requieren RUC
                        numeroDocumentoInput.setAttribute('required', 'required');
                        const asteriscoDocumento = document.getElementById('asterisco-documento');
                        if (asteriscoDocumento) {
                            asteriscoDocumento.style.display = 'inline';
                        }
                        numeroDocumentoInput.setAttribute('maxlength', '11');
                        numeroDocumentoInput.setAttribute('pattern', '[0-9]{11}');
                    }
                }
            }

            // Función para consultar documento
            function consultarDocumento(numero) {
                // Limpiar timeout anterior
                if (timeoutConsulta) {
                    clearTimeout(timeoutConsulta);
                }
                
                // Ocultar info anterior
                cerrarInfoCliente();
                clienteConsultado = null;
                
                // Validar que tenga la longitud correcta
                const tipoComprobante = document.getElementById('tipo_comprobante_select').value;
                const tipoDocumento = tipoComprobante === 'boleta' ? 'dni' : 'ruc';
                const longitudMinima = tipoDocumento === 'dni' ? 8 : 11;
                
                if (!numero || numero.length < longitudMinima) {
                    return;
                }
                
                // Mostrar loading
                const loadingElement = document.getElementById('loading-documento');
                loadingElement.classList.remove('hidden');
                
                // Consultar después de un pequeño delay para evitar consultas excesivas
                timeoutConsulta = setTimeout(() => {
                    const url = '{{ route("sales.consultarDocumento") }}';
                    const token = '{{ csrf_token() }}';
                    
                    fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            tipo: tipoDocumento,
                            numero: numero
                        })
                    })
                    .then(response => {
                        // Verificar si la respuesta es exitosa antes de parsear JSON
                        if (!response.ok) {
                            return response.json().then(data => {
                                throw new Error(data.message || 'Error al consultar el documento');
                            }).catch(() => {
                                throw new Error('Error al consultar el documento. Verifique el número ingresado.');
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        loadingElement.classList.add('hidden');
                        
                        if (data.success) {
                            clienteConsultado = data;
                            mostrarInfoCliente(data);
                        } else {
                            mostrarErrorCliente(data.message || 'No se pudo consultar el documento');
                        }
                    })
                    .catch(error => {
                        loadingElement.classList.add('hidden');
                        console.error('Error:', error);
                        mostrarErrorCliente(error.message || 'Error al consultar el documento. Intente nuevamente.');
                    });
                }, 500); // Esperar 500ms después de que el usuario deje de escribir
            }

            // Función para mostrar información del cliente
            function mostrarInfoCliente(data) {
                const container = document.getElementById('cliente-info-container');
                const nombreInfo = document.getElementById('cliente-nombre-info');
                const documentoInfo = document.getElementById('cliente-documento-info');
                const direccionInfo = document.getElementById('cliente-direccion-info');
                const existeInfo = document.getElementById('cliente-existe-info');
                
                const tipoComprobante = document.getElementById('tipo_comprobante_select').value;
                const tipoDoc = tipoComprobante === 'boleta' ? 'DNI' : 'RUC';
                
                nombreInfo.textContent = data.cliente_nombre || 'N/A';
                documentoInfo.textContent = `${tipoDoc}: ${data.numero_documento || 'N/A'}`;
                
                if (data.direccion) {
                    direccionInfo.textContent = `Dirección: ${data.direccion}`;
                    direccionInfo.style.display = 'block';
                } else {
                    direccionInfo.style.display = 'none';
                }
                
                if (data.cliente_existe) {
                    existeInfo.textContent = '✓ Cliente ya existe en el sistema';
                    existeInfo.className = 'text-xs text-green-600 mt-1 font-medium';
                } else {
                    existeInfo.textContent = '⚠ Cliente nuevo - se creará automáticamente';
                    existeInfo.className = 'text-xs text-yellow-600 mt-1 font-medium';
                }
                
                container.classList.remove('hidden');
            }

            // Función para mostrar error
            function mostrarErrorCliente(mensaje) {
                const container = document.getElementById('cliente-info-container');
                const nombreInfo = document.getElementById('cliente-nombre-info');
                const documentoInfo = document.getElementById('cliente-documento-info');
                const direccionInfo = document.getElementById('cliente-direccion-info');
                const existeInfo = document.getElementById('cliente-existe-info');
                
                nombreInfo.textContent = 'Error';
                documentoInfo.textContent = mensaje;
                direccionInfo.style.display = 'none';
                existeInfo.style.display = 'none';
                container.classList.remove('hidden');
                container.querySelector('.bg-blue-50').className = 'bg-red-50 border border-red-200 rounded-lg p-3';
            }

            // Función para cerrar info del cliente
            function cerrarInfoCliente() {
                const container = document.getElementById('cliente-info-container');
                container.classList.add('hidden');
                const bgElement = container.querySelector('.bg-red-50, .bg-blue-50');
                if (bgElement) {
                    bgElement.className = 'bg-blue-50 border border-blue-200 rounded-lg p-3';
                }
                document.getElementById('cliente-direccion-info').style.display = 'block';
                document.getElementById('cliente-existe-info').style.display = 'block';
            }

            // Inicializar según el valor por defecto
            const tipoInicial = document.getElementById('tipo_comprobante_select').value;
            cambiarTipoComprobante(tipoInicial);

            // Exponer funciones al scope global para que los onclick puedan accederlas
            window.filtrarPorCategoria = filtrarPorCategoria;
            window.agregarAlCarrito = agregarAlCarrito;
            window.agregarPago = agregarPago;
            window.eliminarPago = eliminarPago;
            window.eliminarDelCarrito = eliminarDelCarrito;
            window.actualizarCantidad = actualizarCantidad;
            window.actualizarDescuento = actualizarDescuento;
            window.calcularTotalPagos = calcularTotalPagos;
            window.cambiarTipoComprobante = cambiarTipoComprobante;
            window.consultarDocumento = consultarDocumento;
            window.cerrarInfoCliente = cerrarInfoCliente;

            // Validar antes de enviar
            document.getElementById('saleForm').addEventListener('submit', function(e) {
                if (cartItems.length === 0) {
                    e.preventDefault();
                    alert('Debe agregar al menos un producto al carrito.');
                    return false;
                }
                
                generarDetallesOcultos();
            });
        })();
    </script>
@endsection
