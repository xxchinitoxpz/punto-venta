@extends('layouts.dashboard')

@section('content')
    <div class="w-full">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">
                {{ isset($inventory) ? 'Editar Registro de Inventario' : 'Crear Nuevo Registro de Inventario' }}
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

            <form action="{{ isset($inventory) ? route('inventories.update', $inventory) : route('inventories.store') }}" method="POST" id="inventoryForm">
                @csrf
                @if(isset($inventory))
                    @method('PUT')
                @endif

                @if(isset($inventory))
                    <!-- Modo Edición: Un solo registro -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Producto -->
                        <div>
                            <label for="producto_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Producto <span class="text-red-500">*</span>
                            </label>
                            <select name="producto_id" 
                                    id="producto_id" 
                                    required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Seleccione un producto</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" 
                                            {{ old('producto_id', $inventory->producto_id ?? '') == $product->id ? 'selected' : '' }}>
                                        {{ $product->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('producto_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Stock -->
                        <div>
                            <label for="stock" class="block text-sm font-medium text-gray-700 mb-2">
                                Stock <span class="text-red-500">*</span>
                            </label>
                            <input type="number" 
                                   name="stock" 
                                   id="stock" 
                                   value="{{ old('stock', $inventory->stock ?? 0) }}" 
                                   min="0"
                                   step="1"
                                   required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('stock')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Fecha de Vencimiento -->
                        <div>
                            <label for="fecha_vencimiento" class="block text-sm font-medium text-gray-700 mb-2">
                                Fecha de Vencimiento
                            </label>
                            <input type="date" 
                                   name="fecha_vencimiento" 
                                   id="fecha_vencimiento" 
                                   value="{{ old('fecha_vencimiento', isset($inventory) && $inventory->fecha_vencimiento ? $inventory->fecha_vencimiento->format('Y-m-d') : '') }}" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('fecha_vencimiento')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                @else
                    <!-- Modo Creación: Múltiples registros -->
                    <div class="mb-6">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-lg font-semibold text-gray-700">Registros de Inventario</h2>
                            <button type="button" 
                                    onclick="agregarFila()" 
                                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                + Agregar Fila
                            </button>
                        </div>

                        <div id="inventories-container" class="space-y-4">
                            <!-- Primera fila -->
                            <div class="inventory-row border border-gray-300 rounded-lg p-4 bg-gray-50">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Producto <span class="text-red-500">*</span></label>
                                        <select name="inventories[0][producto_id]" 
                                                required
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                            <option value="">Seleccione un producto</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}">{{ $product->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Stock <span class="text-red-500">*</span></label>
                                        <input type="number" 
                                               name="inventories[0][stock]" 
                                               value="0" 
                                               min="0"
                                               step="1"
                                               required
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Vencimiento</label>
                                        <div class="flex items-center space-x-2">
                                            <input type="date" 
                                                   name="inventories[0][fecha_vencimiento]" 
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                            <button type="button" 
                                                    onclick="eliminarFila(this)" 
                                                    class="px-3 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                                                Eliminar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Botones -->
                <div class="flex items-center justify-end space-x-4 mt-6">
                    <a href="{{ route('inventories.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancelar
                    </a>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        {{ isset($inventory) ? 'Actualizar Inventario' : 'Crear Inventario' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if(!isset($inventory))
    <script>
        let filaIndex = 1;

        function agregarFila() {
            const container = document.getElementById('inventories-container');
            // Obtener la primera fila completa para clonar su estructura
            const primeraFila = container.querySelector('.inventory-row');
            
            if (!primeraFila) return;
            
            // Clonar la fila completa
            const nuevaFila = primeraFila.cloneNode(true);
            nuevaFila.className = 'inventory-row border border-gray-300 rounded-lg p-4 bg-gray-50';
            
            // Actualizar los nombres de los campos
            const select = nuevaFila.querySelector('select[name^="inventories[0]"]');
            if (select) {
                select.name = `inventories[${filaIndex}][producto_id]`;
                select.value = ''; // Resetear el valor seleccionado
            }
            
            const stockInput = nuevaFila.querySelector('input[name^="inventories[0]"][type="number"]');
            if (stockInput) {
                stockInput.name = `inventories[${filaIndex}][stock]`;
                stockInput.value = '0';
            }
            
            const fechaInput = nuevaFila.querySelector('input[name^="inventories[0]"][type="date"]');
            if (fechaInput) {
                fechaInput.name = `inventories[${filaIndex}][fecha_vencimiento]`;
                fechaInput.value = '';
            }
            
            container.appendChild(nuevaFila);
            filaIndex++;
        }

        function eliminarFila(button) {
            const fila = button.closest('.inventory-row');
            const container = document.getElementById('inventories-container');
            
            // No permitir eliminar si solo queda una fila
            if (container.children.length > 1) {
                fila.remove();
            } else {
                alert('Debe haber al menos un registro de inventario.');
            }
        }
    </script>
    @endif
@endsection

