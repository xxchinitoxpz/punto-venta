@extends('layouts.dashboard')

@section('content')
    <div class="w-full">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">
                {{ isset($promotion) ? 'Editar Promoción' : 'Crear Nueva Promoción' }}
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

            <form action="{{ isset($promotion) ? route('promotions.update', $promotion) : route('promotions.store') }}" method="POST" id="promotionForm">
                @csrf
                @if(isset($promotion))
                    @method('PUT')
                @endif

                <!-- Sección 1: Datos de la Promoción -->
                <div class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-700 mb-4">Datos de la Promoción</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nombre -->
                        <div>
                            <label for="nombre" class="block text-sm font-medium text-gray-700 mb-2">
                                Nombre <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="nombre" 
                                   id="nombre" 
                                   value="{{ old('nombre', $promotion->nombre ?? '') }}" 
                                   required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('nombre')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Precio Promocional -->
                        <div>
                            <label for="precio_promocional" class="block text-sm font-medium text-gray-700 mb-2">
                                Precio Promocional (S/) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" 
                                   name="precio_promocional" 
                                   id="precio_promocional" 
                                   value="{{ old('precio_promocional', $promotion->precio_promocional ?? '') }}" 
                                   step="0.01" 
                                   min="0" 
                                   required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('precio_promocional')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Fecha Inicio -->
                        <div>
                            <label for="fecha_inicio" class="block text-sm font-medium text-gray-700 mb-2">
                                Fecha de Inicio <span class="text-red-500">*</span>
                            </label>
                            <input type="date" 
                                   name="fecha_inicio" 
                                   id="fecha_inicio" 
                                   value="{{ old('fecha_inicio', isset($promotion) && $promotion->fecha_inicio ? $promotion->fecha_inicio->format('Y-m-d') : '') }}" 
                                   required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('fecha_inicio')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Fecha Fin -->
                        <div>
                            <label for="fecha_fin" class="block text-sm font-medium text-gray-700 mb-2">
                                Fecha de Fin <span class="text-red-500">*</span>
                            </label>
                            <input type="date" 
                                   name="fecha_fin" 
                                   id="fecha_fin" 
                                   value="{{ old('fecha_fin', isset($promotion) && $promotion->fecha_fin ? $promotion->fecha_fin->format('Y-m-d') : '') }}" 
                                   required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('fecha_fin')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Descripción -->
                        <div class="md:col-span-2">
                            <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-2">
                                Descripción
                            </label>
                            <textarea name="descripcion" 
                                      id="descripcion" 
                                      rows="3"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('descripcion', $promotion->descripcion ?? '') }}</textarea>
                            @error('descripcion')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Activa -->
                        <div class="md:col-span-2">
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" 
                                       name="activa" 
                                       value="1"
                                       {{ old('activa', isset($promotion) && $promotion->activa ? 'checked' : 'checked') }}
                                       class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <span class="text-sm font-medium text-gray-700">Promoción Activa</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Sección 2: Productos de la Promoción -->
                <div class="mb-8">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-semibold text-gray-700">Productos Incluidos</h2>
                        <button type="button" 
                                onclick="agregarProducto()" 
                                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                            + Agregar Producto
                        </button>
                    </div>

                    <div id="productos-container" class="space-y-4">
                        @if(isset($promotion) && $promotion->presentations->count() > 0)
                            @foreach($promotion->presentations as $index => $presentation)
                                <div class="producto-item border border-gray-300 rounded-lg p-4 bg-gray-50">
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div class="md:col-span-2">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                                Presentación <span class="text-red-500">*</span>
                                            </label>
                                            <select name="presentaciones[{{ $index }}][presentation_id]" 
                                                    required
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                                <option value="">Seleccione una presentación</option>
                                                @foreach($presentations as $pres)
                                                    <option value="{{ $pres->id }}" 
                                                            {{ $presentation->id == $pres->id ? 'selected' : '' }}
                                                            data-product-name="{{ $pres->product->nombre ?? '' }}"
                                                            data-pres-name="{{ $pres->nombre }}">
                                                        {{ ($pres->product->nombre ?? 'N/A') }} - {{ $pres->nombre }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                                Cantidad Requerida <span class="text-red-500">*</span>
                                            </label>
                                            <div class="flex items-center space-x-2">
                                                <input type="number" 
                                                       name="presentaciones[{{ $index }}][cantidad_requerida]" 
                                                       value="{{ old("presentaciones.$index.cantidad_requerida", $presentation->pivot->cantidad_requerida) }}" 
                                                       min="1" 
                                                       required
                                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                                <button type="button" 
                                                        onclick="eliminarProducto(this)" 
                                                        class="px-3 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                                                    Eliminar
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <!-- Primera fila para creación -->
                            <div class="producto-item border border-gray-300 rounded-lg p-4 bg-gray-50">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Presentación <span class="text-red-500">*</span>
                                        </label>
                                        <select name="presentaciones[0][presentation_id]" 
                                                required
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg presentation-select">
                                            <option value="">Seleccione una presentación</option>
                                            @foreach($presentations as $pres)
                                                <option value="{{ $pres->id }}"
                                                        data-product-name="{{ $pres->product->nombre ?? '' }}"
                                                        data-pres-name="{{ $pres->nombre }}">
                                                    {{ ($pres->product->nombre ?? 'N/A') }} - {{ $pres->nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Cantidad Requerida <span class="text-red-500">*</span>
                                        </label>
                                        <input type="number" 
                                               name="presentaciones[0][cantidad_requerida]" 
                                               value="{{ old('presentaciones.0.cantidad_requerida', '1') }}" 
                                               min="1" 
                                               required
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Botones -->
                <div class="flex items-center justify-end space-x-4 mt-6">
                    <a href="{{ route('promotions.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancelar
                    </a>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        {{ isset($promotion) ? 'Actualizar Promoción' : 'Crear Promoción' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let productoIndex = {{ isset($promotion) && $promotion->presentations->count() > 0 ? $promotion->presentations->count() : 1 }};

        function agregarProducto() {
            const container = document.getElementById('productos-container');
            // Obtener la primera fila completa para clonar su estructura
            const primeraFila = container.querySelector('.producto-item');
            
            if (!primeraFila) return;
            
            // Clonar la fila completa
            const nuevaFila = primeraFila.cloneNode(true);
            nuevaFila.className = 'producto-item border border-gray-300 rounded-lg p-4 bg-gray-50';
            
            // Actualizar los nombres de los campos
            const select = nuevaFila.querySelector('select[name*="[presentation_id]"]');
            if (select) {
                const oldName = select.name;
                const match = oldName.match(/presentaciones\[(\d+)\]/);
                if (match) {
                    select.name = `presentaciones[${productoIndex}][presentation_id]`;
                } else {
                    select.name = `presentaciones[${productoIndex}][presentation_id]`;
                }
                select.value = ''; // Resetear el valor seleccionado
            }
            
            const cantidadInput = nuevaFila.querySelector('input[name*="[cantidad_requerida]"]');
            if (cantidadInput) {
                const oldName = cantidadInput.name;
                const match = oldName.match(/presentaciones\[(\d+)\]/);
                if (match) {
                    cantidadInput.name = `presentaciones[${productoIndex}][cantidad_requerida]`;
                } else {
                    cantidadInput.name = `presentaciones[${productoIndex}][cantidad_requerida]`;
                }
                cantidadInput.value = '1';
            }
            
            container.appendChild(nuevaFila);
            productoIndex++;
        }

        function eliminarProducto(button) {
            const item = button.closest('.producto-item');
            const container = document.getElementById('productos-container');
            
            // No permitir eliminar si solo queda una fila
            if (container && container.children.length > 1) {
                item.remove();
            } else if (item) {
                item.remove();
            }
        }
    </script>
@endsection

