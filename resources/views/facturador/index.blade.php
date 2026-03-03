@extends('layouts.dashboard')

@section('content')
    <div class="w-full" x-data="{ tab: '{{ $tab }}' }" x-init="
        $watch('tab', value => {
            const url = new URL(window.location);
            url.searchParams.set('tab', value);
            window.history.replaceState({}, '', url);
        });
    ">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <!-- Header -->
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Facturador</h1>
            </div>

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
                <div class="mb-4 p-4 bg-amber-100 border border-amber-400 text-amber-700 rounded-lg">
                    {{ session('warning') }}
                </div>
            @endif

            <!-- Tabs -->
            <div class="border-b border-gray-200 mb-6">
                <nav class="flex space-x-4" aria-label="Tabs">
                    <button type="button"
                            @click="tab = 'facturas'"
                            :class="tab === 'facturas' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm">
                        Facturas
                    </button>
                    <button type="button"
                            @click="tab = 'boletas'"
                            :class="tab === 'boletas' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm">
                        Boletas
                    </button>
                    <button type="button"
                            @click="tab = 'notas'"
                            :class="tab === 'notas' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm">
                        Notas de crédito
                    </button>
                </nav>
            </div>

            <!-- Tab: Facturas -->
            <div x-show="tab === 'facturas'" x-cloak class="overflow-x-auto">
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
                        @forelse($facturas as $sale)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    <div class="font-semibold">FACTURA</div>
                                    <div class="text-gray-500">{{ $sale->serie }}-{{ str_pad($sale->correlativo, 8, '0', STR_PAD_LEFT) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $sale->client->nombre_completo ?? '—' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><span class="font-semibold text-green-600">S/ {{ number_format($sale->total_venta, 2) }}</span></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $sale->user->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if($sale->estado === 'registrada')
                                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">Registrada</span>
                                    @else
                                        <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium">Anulada</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('sales.pdf', $sale) }}" target="_blank" class="text-blue-600 hover:text-blue-900">PDF</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">No hay facturas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                @if($facturas->hasPages())
                    <div class="mt-4">{{ $facturas->appends(['tab' => 'facturas'])->links() }}</div>
                @endif
            </div>

            <!-- Tab: Boletas -->
            <div x-show="tab === 'boletas'" x-cloak class="overflow-x-auto">
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
                        @forelse($boletas as $sale)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    <div class="font-semibold">BOLETA</div>
                                    <div class="text-gray-500">{{ $sale->serie }}-{{ str_pad($sale->correlativo, 8, '0', STR_PAD_LEFT) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $sale->client->nombre_completo ?? 'Cliente vario' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><span class="font-semibold text-green-600">S/ {{ number_format($sale->total_venta, 2) }}</span></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $sale->user->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if($sale->estado === 'registrada')
                                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">Registrada</span>
                                    @else
                                        <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium">Anulada</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('sales.pdf', $sale) }}" target="_blank" class="text-blue-600 hover:text-blue-900">PDF</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">No hay boletas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                @if($boletas->hasPages())
                    <div class="mt-4">{{ $boletas->appends(['tab' => 'boletas'])->links() }}</div>
                @endif
            </div>

            <!-- Tab: Notas de crédito -->
            <div x-show="tab === 'notas'" x-cloak class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nota (serie-correlativo)</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Doc. afectado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado SUNAT</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($notas as $response)
                            @php $sale = $response->sale; @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    <div class="font-semibold">NOTA DE CRÉDITO</div>
                                    <div class="text-gray-500">
                                        @if(!empty($response->serie) && $response->correlativo !== null && $response->correlativo !== '')
                                            {{ $response->serie }}-{{ str_pad($response->correlativo, 8, '0', STR_PAD_LEFT) }}
                                        @else
                                            —
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    <div class="font-semibold">{{ $sale ? strtoupper($sale->tipo_comprobante) : '—' }}</div>
                                    <div class="text-gray-500">{{ $sale ? $sale->serie . '-' . str_pad($sale->correlativo, 8, '0', STR_PAD_LEFT) : '—' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $sale && $sale->client ? $sale->client->nombre_completo : ($sale ? 'Cliente vario' : '—') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><span class="font-semibold text-green-600">S/ {{ $sale ? number_format($sale->total_venta, 2) : '—' }}</span></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if($response->estado === 'aceptada')
                                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">Aceptada</span>
                                    @elseif($response->estado === 'rechazada')
                                        <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium">Rechazada</span>
                                    @elseif($response->estado === 'excepcion')
                                        <span class="px-2 py-1 bg-amber-100 text-amber-800 rounded-full text-xs font-medium">Excepción</span>
                                    @else
                                        <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs font-medium">{{ $response->estado ?? '—' }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $response->created_at->format('d/m/Y H:i') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex flex-col gap-1">
                                        @if(!empty($response->serie) && $response->correlativo !== null && $response->correlativo !== '')
                                            <a href="{{ route('facturador.notaPdf', $response) }}" target="_blank" class="text-blue-600 hover:text-blue-900">PDF nota</a>
                                        @endif
                                        @if($sale && in_array($sale->tipo_comprobante, ['factura', 'boleta']))
                                            <a href="{{ route('sales.pdf', $sale) }}" target="_blank" class="text-gray-600 hover:text-gray-900">PDF doc. afectado</a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">No hay notas de crédito.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                @if($notas->hasPages())
                    <div class="mt-4">{{ $notas->appends(['tab' => 'notas'])->links() }}</div>
                @endif
            </div>
        </div>
    </div>
    <style>[x-cloak] { display: none !important; }</style>
@endsection
