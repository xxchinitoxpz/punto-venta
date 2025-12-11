@extends('layouts.dashboard')

@section('content')
    <div class="w-full">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">
                {{ isset($company) ? 'Editar Empresa' : 'Crear Nueva Empresa' }}
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

            <form action="{{ isset($company) ? route('companies.update', $company) : route('companies.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @if(isset($company))
                    @method('PUT')
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Razón Social -->
                    <div class="md:col-span-2">
                        <label for="razon_social" class="block text-sm font-medium text-gray-700 mb-2">
                            Razón Social <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="razon_social" 
                               id="razon_social" 
                               value="{{ old('razon_social', $company->razon_social ?? '') }}" 
                               required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('razon_social')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- RUC -->
                    <div>
                        <label for="ruc" class="block text-sm font-medium text-gray-700 mb-2">
                            RUC <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="ruc" 
                               id="ruc" 
                               value="{{ old('ruc', $company->ruc ?? '') }}" 
                               required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('ruc')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Dirección -->
                    <div>
                        <label for="direccion" class="block text-sm font-medium text-gray-700 mb-2">
                            Dirección <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="direccion" 
                               id="direccion" 
                               value="{{ old('direccion', $company->direccion ?? '') }}" 
                               required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('direccion')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Logo -->
                    <div>
                        <label for="logo_path" class="block text-sm font-medium text-gray-700 mb-2">
                            Logo
                            @if(isset($company) && $company->logo_path)
                                <span class="text-xs text-gray-500">(Dejar vacío para mantener el actual)</span>
                            @endif
                        </label>
                        <input type="file" 
                               name="logo_path" 
                               id="logo_path" 
                               accept="image/jpeg,image/png,image/jpg,image/gif"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @if(isset($company) && $company->logo_path)
                            <p class="mt-2 text-xs text-gray-500">Logo actual: 
                                <a href="{{ Storage::url($company->logo_path) }}" target="_blank" class="text-blue-600 hover:underline">Ver logo</a>
                            </p>
                        @endif
                        @error('logo_path')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Producción -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Ambiente
                        </label>
                        <div class="flex items-center mt-2">
                            <input type="checkbox" 
                                   name="production" 
                                   id="production" 
                                   value="1"
                                   {{ old('production', isset($company) && $company->production ? true : false) ? 'checked' : '' }}
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="production" class="ml-2 text-sm text-gray-700">
                                Producción (marcar si es ambiente de producción)
                            </label>
                        </div>
                    </div>

                    <!-- SOL User -->
                    <div>
                        <label for="sol_user" class="block text-sm font-medium text-gray-700 mb-2">
                            SOL Usuario <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="sol_user" 
                               id="sol_user" 
                               value="{{ old('sol_user', $company->sol_user ?? '') }}" 
                               required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('sol_user')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- SOL Password -->
                    <div>
                        <label for="sol_pass" class="block text-sm font-medium text-gray-700 mb-2">
                            SOL Contraseña 
                            <span class="text-red-500">*</span>
                            @if(isset($company))
                                <span class="text-xs text-gray-500">(Dejar vacío para mantener la actual)</span>
                            @endif
                        </label>
                        <input type="password" 
                               name="sol_pass" 
                               id="sol_pass" 
                               {{ !isset($company) ? 'required' : '' }}
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('sol_pass')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Certificado -->
                    <div>
                        <label for="cert_path" class="block text-sm font-medium text-gray-700 mb-2">
                            Certificado 
                            @if(isset($company))
                                <span class="text-xs text-gray-500">(Dejar vacío para mantener el actual)</span>
                            @else
                                <span class="text-red-500">*</span>
                            @endif
                        </label>
                        <input type="file" 
                               name="cert_path" 
                               id="cert_path" 
                               accept=".pem,.cer,.crt"
                               {{ !isset($company) ? 'required' : '' }}
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @if(isset($company) && $company->cert_path)
                            <p class="mt-2 text-xs text-gray-500">Certificado actual: 
                                <a href="{{ Storage::url($company->cert_path) }}" target="_blank" class="text-blue-600 hover:underline">Ver certificado</a>
                            </p>
                        @endif
                        @error('cert_path')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Client ID -->
                    <div>
                        <label for="client_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Client ID
                        </label>
                        <input type="text" 
                               name="client_id" 
                               id="client_id" 
                               value="{{ old('client_id', $company->client_id ?? '') }}" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('client_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Client Secret -->
                    <div>
                        <label for="client_secret" class="block text-sm font-medium text-gray-700 mb-2">
                            Client Secret
                            @if(isset($company))
                                <span class="text-xs text-gray-500">(Dejar vacío para mantener el actual)</span>
                            @endif
                        </label>
                        <input type="password" 
                               name="client_secret" 
                               id="client_secret" 
                               value="{{ old('client_secret', '') }}" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('client_secret')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @if(isset($company) && $company->client_secret)
                            <p class="mt-1 text-xs text-gray-500">Hay un Client Secret configurado. Completa el campo solo si deseas cambiarlo.</p>
                        @endif
                    </div>
                </div>

                <!-- Botones -->
                <div class="flex items-center justify-end space-x-4 mt-6">
                    <a href="{{ route('companies.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancelar
                    </a>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        {{ isset($company) ? 'Actualizar Empresa' : 'Crear Empresa' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

