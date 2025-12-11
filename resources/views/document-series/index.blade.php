@extends('layouts.dashboard')

@section('content')
    <div class="w-full">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <!-- Header con botón crear -->
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Gestión de Series de Documentos</h1>
                @can('crear serie de documento')
                    <a href="{{ route('document-series.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Crear Nueva Serie
                    </a>
                @endcan
            </div>

            <!-- Mensajes de éxito/error -->
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Tabla de series de documentos -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo de Comprobante</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Serie</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Último Correlativo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sucursal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($documentSeries as $series)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $series->id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $series->tipo_comprobante }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $series->serie }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $series->ultimo_correlativo }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $series->branch->nombre ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        @can('editar serie de documento')
                                            <a href="{{ route('document-series.edit', $series) }}" class="text-indigo-600 hover:text-indigo-900">Editar</a>
                                        @endcan
                                        @can('eliminar serie de documento')
                                            <form action="{{ route('document-series.destroy', $series) }}" method="POST" class="inline" onsubmit="return confirm('¿Estás seguro de que deseas eliminar esta serie de documento?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">Eliminar</button>
                                            </form>
                                        @endcan
                                        @cannot('editar serie de documento')
                                            @cannot('eliminar serie de documento')
                                                <span class="text-gray-400">Sin acciones disponibles</span>
                                            @endcannot
                                        @endcannot
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                    No hay series de documentos registradas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            @if($documentSeries->hasPages())
                <div class="mt-4">
                    {{ $documentSeries->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

