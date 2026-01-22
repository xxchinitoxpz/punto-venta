@extends('layouts.dashboard')

@section('content')
    <div class="w-full">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <!-- Header con botón crear -->
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Gestión de Ventas</h1>
                @can('crear ventas')
                    <a href="{{ route('sales.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Nueva Venta
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

            <!-- Tabla de ventas -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Comprobante</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuario</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($sales as $sale)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    <div>
                                        <div class="font-semibold">{{ strtoupper($sale->tipo_comprobante) }}</div>
                                        <div class="text-gray-500">{{ $sale->serie }}-{{ str_pad($sale->correlativo, 8, '0', STR_PAD_LEFT) }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $sale->client->nombre_completo ?? 'Cliente Genérico' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <span class="font-semibold text-green-600">S/ {{ number_format($sale->total_venta, 2) }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $sale->user->name ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if($sale->estado === 'registrada')
                                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">Registrada</span>
                                    @else
                                        <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium">Anulada</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $sale->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        @if(in_array($sale->tipo_comprobante, ['factura', 'boleta']))
                                            <a href="{{ route('sales.pdf', $sale) }}" target="_blank" class="text-blue-600 hover:text-blue-900" title="Ver PDF">
                                                PDF
                                            </a>
                                        @endif
                                        @can('anular ventas')
                                            @if($sale->estado === 'registrada')
                                                @php
                                                    $diasTranscurridos = $sale->created_at->diffInDays(now());
                                                    $puedeAnular = $diasTranscurridos <= 3;
                                                @endphp
                                                @if($puedeAnular)
                                                    <form action="{{ route('sales.destroy', $sale) }}" method="POST" class="inline" onsubmit="return confirm('¿Estás seguro de que deseas anular esta venta? Se revertirá el stock y los movimientos de caja.');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-900">Anular</button>
                                                    </form>
                                                @endif
                                            @else
                                                <span class="text-gray-400">Anulada</span>
                                            @endif
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                    No hay ventas registradas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            @if($sales->hasPages())
                <div class="mt-4">
                    {{ $sales->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

