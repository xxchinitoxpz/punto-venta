@extends('layouts.dashboard')

@section('content')
    <div class="w-full">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <!-- Header con botón crear -->
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Gestión de Inventario</h1>
                @can('crear inventario')
                    <a href="{{ route('inventories.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Crear Nuevo Registro de Inventario
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

            <!-- Tabla de inventario -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producto</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha de Vencimiento</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($inventories as $inventory)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $inventory->id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $inventory->product->nombre ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <span class="px-2 py-1 {{ $inventory->stock > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} rounded-full text-xs font-medium">
                                        {{ $inventory->stock }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($inventory->fecha_vencimiento)
                                        @php
                                            $fechaVencimiento = \Carbon\Carbon::parse($inventory->fecha_vencimiento);
                                            $diasRestantes = now()->diffInDays($fechaVencimiento, false);
                                        @endphp
                                        <span class="{{ $diasRestantes < 0 ? 'text-red-600 font-semibold' : ($diasRestantes <= 30 ? 'text-yellow-600 font-semibold' : 'text-gray-900') }}">
                                            {{ $inventory->fecha_vencimiento->format('d/m/Y') }}
                                            @if($diasRestantes < 0)
                                                <span class="text-xs">(Vencido)</span>
                                            @elseif($diasRestantes <= 30)
                                                <span class="text-xs">({{ $diasRestantes }} días)</span>
                                            @endif
                                        </span>
                                    @else
                                        <span class="text-gray-400">Sin fecha</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        @can('editar inventario')
                                            <a href="{{ route('inventories.edit', $inventory) }}" class="text-indigo-600 hover:text-indigo-900">Editar</a>
                                        @endcan
                                        @can('eliminar inventario')
                                            <form action="{{ route('inventories.destroy', $inventory) }}" method="POST" class="inline" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este registro de inventario?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">Eliminar</button>
                                            </form>
                                        @endcan
                                        @cannot('editar inventario')
                                            @cannot('eliminar inventario')
                                                <span class="text-gray-400">Sin acciones disponibles</span>
                                            @endcannot
                                        @endcannot
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                    No hay registros de inventario.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            @if($inventories->hasPages())
                <div class="mt-4">
                    {{ $inventories->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

