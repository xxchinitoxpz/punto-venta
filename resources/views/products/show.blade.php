@extends('layouts.dashboard')

@section('content')
    <div class="w-full">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Detalles del Producto</h1>
                <a href="{{ route('products.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Volver
                </a>
            </div>

            <!-- Datos del Producto -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">Información del Producto</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">ID</label>
                        <p class="text-gray-900">{{ $product->id }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                        <p class="text-gray-900">{{ $product->nombre }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                        <p class="text-gray-900">{{ $product->descripcion ?? 'Sin descripción' }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Categoría</label>
                        <p class="text-gray-900">{{ $product->category->nombre ?? 'N/A' }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Marca</label>
                        <p class="text-gray-900">{{ $product->brand->nombre ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <!-- Presentaciones -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">Presentaciones ({{ $product->presentations->count() }})</h2>
                @if($product->presentations->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Código de Barras</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio de Venta</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unidades</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($product->presentations as $presentation)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $presentation->id }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $presentation->nombre }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $presentation->barcode }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">S/ {{ number_format($presentation->precio_venta, 2) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($presentation->unidades, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-gray-500">Este producto no tiene presentaciones registradas.</p>
                @endif
            </div>

            <div class="flex items-center space-x-4 pt-6">
                @can('editar productos')
                    <a href="{{ route('products.edit', $product) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Editar Producto
                    </a>
                @endcan
            </div>
        </div>
    </div>
@endsection

