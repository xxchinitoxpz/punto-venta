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

                    <!-- Nombre Comercial -->
                    <div class="md:col-span-2">
                        <label for="nombre_comercial" class="block text-sm font-medium text-gray-700 mb-2">
                            Nombre Comercial
                        </label>
                        <input type="text" 
                               name="nombre_comercial" 
                               id="nombre_comercial" 
                               value="{{ old('nombre_comercial', $company->nombre_comercial ?? '') }}" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('nombre_comercial')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- RUC -->
                    <div>
                        <label for="ruc" class="block text-sm font-medium text-gray-700 mb-2">
                            RUC <span class="text-red-500">*</span>
                        </label>
                        <div class="flex gap-2">
                            <input type="text" 
                                   name="ruc" 
                                   id="ruc" 
                                   value="{{ old('ruc', $company->ruc ?? '') }}" 
                                   required
                                   class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <button type="button" 
                                    id="btn-consultar-ruc" 
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                                    title="Consultar RUC">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </button>
                        </div>
                        <div id="ruc-loading" class="hidden mt-2 text-sm text-blue-600">
                            Consultando...
                        </div>
                        <div id="ruc-error" class="hidden mt-2 text-sm text-red-600"></div>
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

                    <!-- Ubigeo -->
                    <div>
                        <label for="ubigueo" class="block text-sm font-medium text-gray-700 mb-2">
                            Ubigeo
                        </label>
                        <input type="text" 
                               name="ubigueo" 
                               id="ubigueo" 
                               value="{{ old('ubigueo', $company->ubigueo ?? '') }}" 
                               maxlength="6"
                               placeholder="Ej: 140101"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('ubigueo')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Departamento -->
                    <div>
                        <label for="departamento" class="block text-sm font-medium text-gray-700 mb-2">
                            Departamento
                        </label>
                        <input type="text" 
                               name="departamento" 
                               id="departamento" 
                               value="{{ old('departamento', $company->departamento ?? '') }}" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('departamento')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Provincia -->
                    <div>
                        <label for="provincia" class="block text-sm font-medium text-gray-700 mb-2">
                            Provincia
                        </label>
                        <input type="text" 
                               name="provincia" 
                               id="provincia" 
                               value="{{ old('provincia', $company->provincia ?? '') }}" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('provincia')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Distrito -->
                    <div>
                        <label for="distrito" class="block text-sm font-medium text-gray-700 mb-2">
                            Distrito
                        </label>
                        <input type="text" 
                               name="distrito" 
                               id="distrito" 
                               value="{{ old('distrito', $company->distrito ?? '') }}" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('distrito')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Urbanización -->
                    <div>
                        <label for="urbanizacion" class="block text-sm font-medium text-gray-700 mb-2">
                            Urbanización
                        </label>
                        <input type="text" 
                               name="urbanizacion" 
                               id="urbanizacion" 
                               value="{{ old('urbanizacion', $company->urbanizacion ?? '-') }}" 
                               placeholder="-"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('urbanizacion')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Código Local -->
                    <div>
                        <label for="cod_local" class="block text-sm font-medium text-gray-700 mb-2">
                            Código Local
                        </label>
                        <input type="text" 
                               name="cod_local" 
                               id="cod_local" 
                               value="{{ old('cod_local', $company->cod_local ?? '0000') }}" 
                               maxlength="4"
                               placeholder="0000"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('cod_local')
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

    <script>
        // Función para inicializar el consultor de RUC
        function initConsultarRuc() {
            const btnConsultarRuc = document.getElementById('btn-consultar-ruc');
            const inputRuc = document.getElementById('ruc');
            const loadingDiv = document.getElementById('ruc-loading');
            const errorDiv = document.getElementById('ruc-error');

            if (!btnConsultarRuc || !inputRuc) return;

            // Remover event listeners anteriores si existen
            const newBtn = btnConsultarRuc.cloneNode(true);
            btnConsultarRuc.parentNode.replaceChild(newBtn, btnConsultarRuc);

            newBtn.addEventListener('click', function() {
                const ruc = inputRuc.value.trim();
                
                if (!ruc) {
                    errorDiv.textContent = 'Por favor ingrese un RUC';
                    errorDiv.classList.remove('hidden');
                    return;
                }

                // Validar formato básico de RUC (11 dígitos)
                if (!/^\d{11}$/.test(ruc)) {
                    errorDiv.textContent = 'El RUC debe tener 11 dígitos';
                    errorDiv.classList.remove('hidden');
                    return;
                }

                // Mostrar loading
                loadingDiv.classList.remove('hidden');
                errorDiv.classList.add('hidden');
                newBtn.disabled = true;

                // Consultar API
                fetch('{{ route("companies.consultarRuc") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ ruc: ruc })
                })
                .then(response => response.json())
                .then(data => {
                    loadingDiv.classList.add('hidden');
                    newBtn.disabled = false;

                    if (data.success) {
                        // Llenar los campos con los datos obtenidos
                        if (data.data.razon_social) {
                            document.getElementById('razon_social').value = data.data.razon_social;
                        }
                        if (data.data.direccion) {
                            document.getElementById('direccion').value = data.data.direccion;
                        }
                        if (data.data.ubigueo) {
                            document.getElementById('ubigueo').value = data.data.ubigueo;
                        }
                        if (data.data.departamento) {
                            document.getElementById('departamento').value = data.data.departamento;
                        }
                        if (data.data.provincia) {
                            document.getElementById('provincia').value = data.data.provincia;
                        }
                        if (data.data.distrito) {
                            document.getElementById('distrito').value = data.data.distrito;
                        }
                        if (data.data.urbanizacion) {
                            document.getElementById('urbanizacion').value = data.data.urbanizacion;
                        }
                        if (data.data.cod_local) {
                            document.getElementById('cod_local').value = data.data.cod_local;
                        }
                        
                        // Opcional: también llenar nombre_comercial con la razón social
                        if (data.data.razon_social && !document.getElementById('nombre_comercial').value) {
                            document.getElementById('nombre_comercial').value = data.data.razon_social;
                        }
                    } else {
                        errorDiv.textContent = data.message || 'No se encontró información para el RUC proporcionado';
                        errorDiv.classList.remove('hidden');
                    }
                })
                .catch(error => {
                    loadingDiv.classList.add('hidden');
                    newBtn.disabled = false;
                    errorDiv.textContent = 'Error al consultar el RUC. Por favor intente nuevamente.';
                    errorDiv.classList.remove('hidden');
                    console.error('Error:', error);
                });
            });
        }

        // Ejecutar cuando se carga la página inicialmente
        document.addEventListener('DOMContentLoaded', initConsultarRuc);
        
        // Ejecutar cuando Turbo carga una nueva página
        document.addEventListener('turbo:load', initConsultarRuc);
        
        // También ejecutar en turbo:render por si acaso
        document.addEventListener('turbo:render', initConsultarRuc);
    </script>
@endsection

