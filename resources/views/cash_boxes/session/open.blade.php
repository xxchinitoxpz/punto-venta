@extends('layouts.dashboard')

@section('content')
    <div class="w-full">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">
                Abrir Sesión de Caja: {{ $cashbox->nombre }}
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

            <form action="{{ route('cashboxes.storeOpenSession', $cashbox) }}" method="POST">
                @csrf

                <div class="max-w-md">
                    <div class="mb-6">
                        <label for="monto_apertura_efectivo" class="block text-sm font-medium text-gray-700 mb-2">
                            Monto de Apertura en Efectivo <span class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                               name="monto_apertura_efectivo" 
                               id="monto_apertura_efectivo" 
                               step="0.01"
                               min="0"
                               value="{{ old('monto_apertura_efectivo', '0.00') }}" 
                               required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('monto_apertura_efectivo')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Botones -->
                <div class="flex items-center justify-end space-x-4 mt-6">
                    <a href="{{ route('cashboxes.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancelar
                    </a>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        Abrir Sesión
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

