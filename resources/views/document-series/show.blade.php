@extends('layouts.dashboard')

@section('content')
    <div class="w-full">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Detalles de la Serie de Documento</h1>
                <a href="{{ route('document-series.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Volver
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">ID</label>
                    <p class="text-gray-900">{{ $documentSeries->id }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Comprobante</label>
                    <p class="text-gray-900">{{ $documentSeries->tipo_comprobante }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Serie</label>
                    <p class="text-gray-900">{{ $documentSeries->serie }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Último Correlativo</label>
                    <p class="text-gray-900">{{ $documentSeries->ultimo_correlativo }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sucursal</label>
                    <p class="text-gray-900">{{ $documentSeries->branch->nombre ?? 'N/A' }}</p>
                </div>

                @if($documentSeries->branch && $documentSeries->branch->company)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Empresa</label>
                        <p class="text-gray-900">{{ $documentSeries->branch->company->razon_social }}</p>
                    </div>
                @endif
            </div>

            <div class="flex items-center space-x-4 pt-6">
                @can('editar serie de documento')
                    <a href="{{ route('document-series.edit', $documentSeries) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Editar Serie
                    </a>
                @endcan
            </div>
        </div>
    </div>
@endsection

