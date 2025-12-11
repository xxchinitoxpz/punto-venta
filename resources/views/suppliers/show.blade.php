@extends('layouts.dashboard')

@section('content')
    <div class="w-full">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Detalles del Proveedor</h1>
                <a href="{{ route('suppliers.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Volver
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">ID</label>
                    <p class="text-gray-900">{{ $supplier->id }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre Completo</label>
                    <p class="text-gray-900">{{ $supplier->nombre_completo }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Documento</label>
                    <p class="text-gray-900">
                        <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">
                            {{ $supplier->tipo_documento }}
                        </span>
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Número de Documento</label>
                    <p class="text-gray-900">{{ $supplier->nro_documento }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                    <p class="text-gray-900">{{ $supplier->telefono ?? 'N/A' }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <p class="text-gray-900">{{ $supplier->email ?? 'N/A' }}</p>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dirección</label>
                    <p class="text-gray-900">{{ $supplier->direccion ?? 'N/A' }}</p>
                </div>
            </div>

            <div class="flex items-center space-x-4 pt-6">
                @can('editar proveedores')
                    <a href="{{ route('suppliers.edit', $supplier) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Editar Proveedor
                    </a>
                @endcan
            </div>
        </div>
    </div>
@endsection

