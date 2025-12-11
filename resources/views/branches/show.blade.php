@extends('layouts.dashboard')

@section('content')
    <div class="w-full">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Detalles de la Sucursal</h1>
                <a href="{{ route('branches.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Volver
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">ID</label>
                    <p class="text-gray-900">{{ $branch->id }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                    <p class="text-gray-900">{{ $branch->nombre }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                    <p class="text-gray-900">{{ $branch->telefono }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Empresa</label>
                    <p class="text-gray-900">{{ $branch->company->razon_social ?? 'N/A' }}</p>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dirección</label>
                    <p class="text-gray-900">{{ $branch->direccion }}</p>
                </div>
            </div>

            <div class="flex items-center space-x-4 pt-6">
                @can('editar sucursal')
                    <a href="{{ route('branches.edit', $branch) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Editar Sucursal
                    </a>
                @endcan
            </div>
        </div>
    </div>
@endsection

