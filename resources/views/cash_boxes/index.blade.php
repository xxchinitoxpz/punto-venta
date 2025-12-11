@extends('layouts.dashboard')

@section('content')
    <div class="w-full">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <!-- Header con botón crear -->
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Gestión de Cajas</h1>
                @can('ver cajas')
                    <a href="{{ route('cashboxes.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Crear Nueva Caja
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

            @if(session('warning'))
                <div class="mb-4 p-4 bg-yellow-100 border border-yellow-400 text-yellow-700 rounded-lg">
                    {{ session('warning') }}
                </div>
            @endif

            <!-- Tabla de cajas -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sucursal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($cashBoxes as $cashBox)
                            @php
                                $currentSession = $cashBox->sessions->first();
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $cashBox->id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $cashBox->nombre }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $cashBox->branch->nombre }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if($currentSession && $currentSession->estado === 'abierta')
                                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">
                                            Abierta
                                        </span>
                                    @else
                                        <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs font-medium">
                                            Cerrada
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        @if($currentSession && $currentSession->estado === 'abierta')
                                            @can('cerrar caja')
                                                <a href="{{ route('cashboxes.closeSession', $currentSession) }}" class="text-red-600 hover:text-red-900">Cerrar Caja</a>
                                            @endcan
                                            @can('ver cajas')
                                                <a href="{{ route('cashboxes.showSession', $currentSession) }}" class="text-indigo-600 hover:text-indigo-900">Ver Sesión</a>
                                            @endcan
                                        @else
                                            @can('abrir caja')
                                                <a href="{{ route('cashboxes.openSession', $cashBox) }}" class="text-green-600 hover:text-green-900">Abrir Caja</a>
                                            @endcan
                                        @endif
                                        @can('ver cajas')
                                            <a href="{{ route('cashboxes.show', $cashBox) }}" class="text-indigo-600 hover:text-indigo-900">Ver</a>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                    No hay cajas registradas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            @if($cashBoxes->hasPages())
                <div class="mt-4">
                    {{ $cashBoxes->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

