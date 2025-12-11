@extends('layouts.dashboard')

@section('content')
    <div class="w-full">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Detalles de la Promoción</h1>
                <a href="{{ route('promotions.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Volver
                </a>
            </div>

            <!-- Datos de la Promoción -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">Información de la Promoción</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">ID</label>
                        <p class="text-gray-900">{{ $promotion->id }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                        <p class="text-gray-900 font-semibold">{{ $promotion->nombre }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                        <p class="text-gray-900">{{ $promotion->descripcion ?? 'Sin descripción' }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Precio Promocional</label>
                        <p class="text-gray-900 font-semibold text-green-600 text-lg">S/ {{ number_format($promotion->precio_promocional, 2) }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Inicio</label>
                        <p class="text-gray-900">{{ $promotion->fecha_inicio->format('d/m/Y') }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Fin</label>
                        <p class="text-gray-900">{{ $promotion->fecha_fin->format('d/m/Y') }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                        <p class="text-gray-900">
                            @if($promotion->activa)
                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">Activa</span>
                            @else
                                <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs font-medium">Inactiva</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Productos Incluidos -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">Productos Incluidos ({{ $promotion->presentations->count() }})</h2>
                @if($promotion->presentations->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producto</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Presentación</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad Requerida</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio Unitario</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($promotion->presentations as $presentation)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $presentation->product->nombre ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $presentation->nombre }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">
                                                {{ $presentation->pivot->cantidad_requerida }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">S/ {{ number_format($presentation->precio_venta, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-gray-500">Esta promoción no tiene productos asociados.</p>
                @endif
            </div>

            <div class="flex items-center space-x-4 pt-6">
                @can('editar promociones')
                    <a href="{{ route('promotions.edit', $promotion) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Editar Promoción
                    </a>
                @endcan
            </div>
        </div>
    </div>
@endsection

