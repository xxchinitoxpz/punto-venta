@extends('layouts.dashboard')

@section('content')
    <div class="w-full">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Detalles del Inventario</h1>
                <a href="{{ route('inventories.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Volver
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">ID</label>
                    <p class="text-gray-900">{{ $inventory->id }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Producto</label>
                    <p class="text-gray-900">{{ $inventory->product->nombre ?? 'N/A' }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Stock</label>
                    <p class="text-gray-900">
                        <span class="px-2 py-1 {{ $inventory->stock > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} rounded-full text-xs font-medium">
                            {{ $inventory->stock }}
                        </span>
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Vencimiento</label>
                    <p class="text-gray-900">
                        @if($inventory->fecha_vencimiento)
                            @php
                                $fechaVencimiento = \Carbon\Carbon::parse($inventory->fecha_vencimiento);
                                $diasRestantes = now()->diffInDays($fechaVencimiento, false);
                            @endphp
                            <span class="{{ $diasRestantes < 0 ? 'text-red-600 font-semibold' : ($diasRestantes <= 30 ? 'text-yellow-600 font-semibold' : 'text-gray-900') }}">
                                {{ $inventory->fecha_vencimiento->format('d/m/Y') }}
                                @if($diasRestantes < 0)
                                    <span class="text-xs">(Vencido hace {{ abs($diasRestantes) }} días)</span>
                                @elseif($diasRestantes <= 30)
                                    <span class="text-xs">(Vence en {{ $diasRestantes }} días)</span>
                                @else
                                    <span class="text-xs">(Vence en {{ $diasRestantes }} días)</span>
                                @endif
                            </span>
                        @else
                            <span class="text-gray-400">Sin fecha de vencimiento</span>
                        @endif
                    </p>
                </div>
            </div>

            <div class="flex items-center space-x-4 pt-6">
                @can('editar inventario')
                    <a href="{{ route('inventories.edit', $inventory) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Editar Inventario
                    </a>
                @endcan
            </div>
        </div>
    </div>
@endsection

