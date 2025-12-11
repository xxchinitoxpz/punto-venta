@extends('layouts.dashboard')

@section('content')
    <div class="w-full">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Detalles de la Empresa</h1>
                <a href="{{ route('companies.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Volver
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">ID</label>
                    <p class="text-gray-900">{{ $company->id }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Razón Social</label>
                    <p class="text-gray-900">{{ $company->razon_social }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">RUC</label>
                    <p class="text-gray-900">{{ $company->ruc }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dirección</label>
                    <p class="text-gray-900">{{ $company->direccion }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">SOL Usuario</label>
                    <p class="text-gray-900">{{ $company->sol_user }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ambiente</label>
                    @if($company->production)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            Producción
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                            Pruebas
                        </span>
                    @endif
                </div>

                @if($company->client_id)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Client ID</label>
                        <p class="text-gray-900">{{ $company->client_id }}</p>
                    </div>
                @endif

                @if($company->logo_path)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Logo</label>
                        <img src="{{ Storage::url($company->logo_path) }}" alt="Logo" class="h-20 w-auto object-contain">
                    </div>
                @endif

                @if($company->cert_path)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Certificado</label>
                        <a href="{{ Storage::url($company->cert_path) }}" target="_blank" class="text-blue-600 hover:underline">
                            Ver certificado
                        </a>
                    </div>
                @endif
            </div>

            <div class="flex items-center space-x-4 pt-6">
                @can('editar empresa')
                    <a href="{{ route('companies.edit', $company) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Editar Empresa
                    </a>
                @endcan
            </div>
        </div>
    </div>
@endsection

