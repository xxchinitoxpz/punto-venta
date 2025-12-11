@extends('layouts.dashboard')

@section('content')
    <div class="w-full">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">
                Cerrar Sesión de Caja: {{ $session->cashBox->nombre }}
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

            <!-- Resumen de la Sesión -->
            <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                <h2 class="text-lg font-bold text-gray-800 mb-4">Resumen de la Sesión</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Monto Apertura</label>
                        <p class="text-xl font-bold text-gray-900">S/ {{ number_format($session->monto_apertura_efectivo, 2) }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ingresos en Efectivo</label>
                        <p class="text-xl font-bold text-green-600">
                            S/ {{ number_format($session->movements->where('tipo', 'ingreso')->where('metodo_pago', 'efectivo')->sum('monto'), 2) }}
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Salidas en Efectivo</label>
                        <p class="text-xl font-bold text-red-600">
                            S/ {{ number_format($session->movements->where('tipo', 'salida')->where('metodo_pago', 'efectivo')->sum('monto'), 2) }}
                        </p>
                    </div>
                </div>
                <div class="mt-4 p-4 bg-blue-50 rounded-lg">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Monto Esperado en Efectivo</label>
                    <p class="text-2xl font-bold text-blue-900">S/ {{ number_format($session->monto_esperado_efectivo, 2) }}</p>
                    <p class="text-xs text-gray-600 mt-1">
                        (Apertura + Ingresos - Salidas)
                    </p>
                </div>
            </div>

            <form action="{{ route('cashboxes.storeCloseSession', $session) }}" method="POST">
                @csrf

                <div class="max-w-md">
                    <div class="mb-6">
                        <label for="monto_cierre_efectivo_contado" class="block text-sm font-medium text-gray-700 mb-2">
                            Monto Contado en Efectivo <span class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                               name="monto_cierre_efectivo_contado" 
                               id="monto_cierre_efectivo_contado" 
                               step="0.01"
                               min="0"
                               value="{{ old('monto_cierre_efectivo_contado', number_format($session->monto_esperado_efectivo, 2)) }}" 
                               required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('monto_cierre_efectivo_contado')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">
                            Ingrese el monto que realmente cuenta en efectivo.
                        </p>
                    </div>
                </div>

                <!-- Botones -->
                <div class="flex items-center justify-end space-x-4 mt-6">
                    <a href="{{ route('cashboxes.showSession', $session) }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancelar
                    </a>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                        Cerrar Sesión
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

