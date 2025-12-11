@extends('layouts.dashboard')

@section('content')
    <div class="w-full">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">
                {{ isset($documentSeries) ? 'Editar Serie de Documento' : 'Crear Nueva Serie de Documento' }}
            </h1>

            <!-- Mensajes de error -->
            @if($errors->any())
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ isset($documentSeries) ? route('document-series.update', $documentSeries) : route('document-series.store') }}" method="POST">
                @csrf
                @if(isset($documentSeries))
                    @method('PUT')
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Tipo de Comprobante -->
                    <div>
                        <label for="tipo_comprobante" class="block text-sm font-medium text-gray-700 mb-2">
                            Tipo de Comprobante <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="tipo_comprobante" 
                               id="tipo_comprobante" 
                               value="{{ old('tipo_comprobante', $documentSeries->tipo_comprobante ?? '') }}" 
                               required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('tipo_comprobante')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Serie -->
                    <div>
                        <label for="serie" class="block text-sm font-medium text-gray-700 mb-2">
                            Serie <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="serie" 
                               id="serie" 
                               value="{{ old('serie', $documentSeries->serie ?? '') }}" 
                               required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('serie')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Último Correlativo -->
                    <div>
                        <label for="ultimo_correlativo" class="block text-sm font-medium text-gray-700 mb-2">
                            Último Correlativo <span class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                               name="ultimo_correlativo" 
                               id="ultimo_correlativo" 
                               value="{{ old('ultimo_correlativo', $documentSeries->ultimo_correlativo ?? 0) }}" 
                               min="0"
                               required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('ultimo_correlativo')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Sucursal -->
                    <div>
                        <label for="sucursal_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Sucursal <span class="text-red-500">*</span>
                        </label>
                        <select name="sucursal_id" 
                                id="sucursal_id" 
                                required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Seleccione una sucursal</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" 
                                        {{ old('sucursal_id', $documentSeries->sucursal_id ?? '') == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->nombre }} - {{ $branch->company->razon_social ?? 'N/A' }}
                                </option>
                            @endforeach
                        </select>
                        @error('sucursal_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Botones -->
                <div class="flex items-center justify-end space-x-4 mt-6">
                    <a href="{{ route('document-series.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancelar
                    </a>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        {{ isset($documentSeries) ? 'Actualizar Serie' : 'Crear Serie' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

