@extends('layouts.dashboard')

@section('content')
    <div class="w-full">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">
                {{ isset($product) ? 'Editar Producto' : 'Crear Nuevo Producto' }}
            </h1>

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

            <form action="{{ isset($product) ? route('products.update', $product) : route('products.store') }}" method="POST" id="productForm">
                @csrf
                @if(isset($product))
                    @method('PUT')
                @endif

                <!-- Sección 1: Datos del Producto -->
                <div class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-700 mb-4">Datos del Producto</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nombre -->
                        <div>
                            <label for="nombre" class="block text-sm font-medium text-gray-700 mb-2">
                                Nombre <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="nombre" 
                                   id="nombre" 
                                   value="{{ old('nombre', $product->nombre ?? '') }}" 
                                   required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('nombre')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Descripción -->
                        <div>
                            <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-2">
                                Descripción
                            </label>
                            <textarea name="descripcion" 
                                      id="descripcion" 
                                      rows="3"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('descripcion', $product->descripcion ?? '') }}</textarea>
                            @error('descripcion')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Categoría -->
                        <div>
                            <label for="categoria_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Categoría <span class="text-red-500">*</span>
                            </label>
                            <select name="categoria_id" 
                                    id="categoria_id" 
                                    required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Seleccione una categoría</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" 
                                            {{ old('categoria_id', $product->categoria_id ?? '') == $category->id ? 'selected' : '' }}>
                                        {{ $category->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('categoria_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Marca -->
                        <div>
                            <label for="marca_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Marca <span class="text-red-500">*</span>
                            </label>
                            <select name="marca_id" 
                                    id="marca_id" 
                                    required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Seleccione una marca</option>
                                @foreach($brands as $brand)
                                    <option value="{{ $brand->id }}" 
                                            {{ old('marca_id', $product->marca_id ?? '') == $brand->id ? 'selected' : '' }}>
                                        {{ $brand->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('marca_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Sección 2: Presentaciones -->
                <div class="mb-8">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-semibold text-gray-700">Presentaciones</h2>
                        <button type="button" 
                                onclick="agregarPresentacion()" 
                                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                            + Agregar Presentación
                        </button>
                    </div>

                    <div id="presentaciones-container" class="space-y-4">
                        @if(isset($product) && $product->presentations->count() > 0)
                            @foreach($product->presentations as $index => $presentation)
                                <div class="presentacion-item border border-gray-300 rounded-lg p-4 bg-gray-50" data-presentacion-id="{{ $presentation->id }}">
                                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                        <input type="hidden" name="presentaciones[{{ $index }}][id]" value="{{ $presentation->id }}">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre <span class="text-red-500">*</span></label>
                                            <input type="text" 
                                                   name="presentaciones[{{ $index }}][nombre]" 
                                                   value="{{ old("presentaciones.$index.nombre", $presentation->nombre) }}" 
                                                   required
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Código de Barras <span class="text-red-500">*</span></label>
                                            <input type="text" 
                                                   name="presentaciones[{{ $index }}][barcode]" 
                                                   value="{{ old("presentaciones.$index.barcode", $presentation->barcode) }}" 
                                                   required
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Precio de Venta <span class="text-red-500">*</span></label>
                                            <input type="number" 
                                                   name="presentaciones[{{ $index }}][precio_venta]" 
                                                   value="{{ old("presentaciones.$index.precio_venta", $presentation->precio_venta) }}" 
                                                   step="0.01" 
                                                   min="0" 
                                                   required
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Unidades <span class="text-red-500">*</span></label>
                                            <div class="flex items-center space-x-2">
                                                <input type="number" 
                                                       name="presentaciones[{{ $index }}][unidades]" 
                                                       value="{{ old("presentaciones.$index.unidades", $presentation->unidades) }}" 
                                                       step="0.01" 
                                                       min="0.01" 
                                                       required
                                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                                <button type="button" 
                                                        onclick="eliminarPresentacion(this, {{ $presentation->id }})" 
                                                        class="px-3 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                                                    Eliminar
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <!-- Primera presentación para creación -->
                            <div class="presentacion-item border border-gray-300 rounded-lg p-4 bg-gray-50">
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                    <input type="hidden" name="presentaciones[0][id]" value="">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre <span class="text-red-500">*</span></label>
                                        <input type="text" 
                                               name="presentaciones[0][nombre]" 
                                               value="{{ old('presentaciones.0.nombre', '') }}" 
                                               required
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Código de Barras <span class="text-red-500">*</span></label>
                                        <input type="text" 
                                               name="presentaciones[0][barcode]" 
                                               value="{{ old('presentaciones.0.barcode', '') }}" 
                                               required
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Precio de Venta <span class="text-red-500">*</span></label>
                                        <input type="number" 
                                               name="presentaciones[0][precio_venta]" 
                                               value="{{ old('presentaciones.0.precio_venta', '') }}" 
                                               step="0.01" 
                                               min="0" 
                                               required
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Unidades <span class="text-red-500">*</span></label>
                                        <input type="number" 
                                               name="presentaciones[0][unidades]" 
                                               value="{{ old('presentaciones.0.unidades', '1') }}" 
                                               step="0.01" 
                                               min="0.01" 
                                               required
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Contenedor para IDs de presentaciones a eliminar -->
                    <div id="presentaciones-eliminar-container"></div>
                </div>

                <!-- Botones -->
                <div class="flex items-center justify-end space-x-4 mt-6">
                    <a href="{{ route('products.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancelar
                    </a>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        {{ isset($product) ? 'Actualizar Producto' : 'Crear Producto' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let presentacionIndex = {{ isset($product) && $product->presentations->count() > 0 ? $product->presentations->count() : 1 }};
        let presentacionesAEliminar = [];

        function agregarPresentacion() {
            const container = document.getElementById('presentaciones-container');
            const nuevaPresentacion = document.createElement('div');
            nuevaPresentacion.className = 'presentacion-item border border-gray-300 rounded-lg p-4 bg-gray-50';
            nuevaPresentacion.innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <input type="hidden" name="presentaciones[${presentacionIndex}][id]" value="">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre <span class="text-red-500">*</span></label>
                        <input type="text" 
                               name="presentaciones[${presentacionIndex}][nombre]" 
                               required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Código de Barras <span class="text-red-500">*</span></label>
                        <input type="text" 
                               name="presentaciones[${presentacionIndex}][barcode]" 
                               required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Precio de Venta <span class="text-red-500">*</span></label>
                        <input type="number" 
                               name="presentaciones[${presentacionIndex}][precio_venta]" 
                               step="0.01" 
                               min="0" 
                               required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Unidades <span class="text-red-500">*</span></label>
                        <div class="flex items-center space-x-2">
                            <input type="number" 
                                   name="presentaciones[${presentacionIndex}][unidades]" 
                                   value="1" 
                                   step="0.01" 
                                   min="0.01" 
                                   required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            <button type="button" 
                                    onclick="eliminarPresentacion(this)" 
                                    class="px-3 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                                Eliminar
                            </button>
                        </div>
                    </div>
                </div>
            `;
            container.appendChild(nuevaPresentacion);
            presentacionIndex++;
        }

        function eliminarPresentacion(button, presentacionId = null) {
            const item = button.closest('.presentacion-item');
            
            if (presentacionId) {
                // Si tiene ID, agregarlo a la lista de eliminación
                if (!presentacionesAEliminar.includes(presentacionId)) {
                    presentacionesAEliminar.push(presentacionId);
                    const container = document.getElementById('presentaciones-eliminar-container');
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'presentaciones_eliminar[]';
                    input.value = presentacionId;
                    container.appendChild(input);
                }
            }
            
            item.remove();
        }
    </script>
@endsection

