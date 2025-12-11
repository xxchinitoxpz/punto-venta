@extends('layouts.dashboard')

@section('content')
    <div class="w-full">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-800">
                    Sesión de Caja: {{ $session->cashBox->nombre }}
                </h1>
                <a href="{{ route('cashboxes.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Volver
                </a>
            </div>

            <!-- Mensajes -->
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            @if(session('warning'))
                <div class="mb-4 p-4 bg-yellow-100 border border-yellow-400 text-yellow-700 rounded-lg">
                    {{ session('warning') }}
                </div>
            @endif

            <!-- Información de la Sesión -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-blue-50 p-4 rounded-lg">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Monto Apertura</label>
                    <p class="text-2xl font-bold text-blue-900">S/ {{ number_format($session->monto_apertura_efectivo, 2) }}</p>
                </div>
                <div class="bg-green-50 p-4 rounded-lg">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Monto Esperado</label>
                    <p class="text-2xl font-bold text-green-900">S/ {{ number_format($session->monto_esperado_efectivo, 2) }}</p>
                </div>
                @if($session->estado === 'cerrada')
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Monto Contado</label>
                        <p class="text-2xl font-bold text-gray-900">S/ {{ number_format($session->monto_cierre_efectivo_contado, 2) }}</p>
                    </div>
                @endif
            </div>

            <!-- Botón Cerrar Sesión -->
            @if($session->estado === 'abierta')
                <div class="mb-6">
                    @can('cerrar caja')
                        <a href="{{ route('cashboxes.closeSession', $session) }}" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                            Cerrar Sesión
                        </a>
                    @endcan
                </div>
            @endif

            <!-- Formulario para Movimiento Manual -->
            @if($session->estado === 'abierta')
                @can('hacer ajustes de caja')
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                        <h2 class="text-lg font-bold text-gray-800 mb-4">Agregar Movimiento Manual</h2>
                        <form action="{{ route('cashboxes.storeManualMovement', $session) }}" method="POST">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="tipo" class="block text-sm font-medium text-gray-700 mb-2">
                                        Tipo <span class="text-red-500">*</span>
                                    </label>
                                    <select name="tipo" 
                                            id="tipo" 
                                            required
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">Seleccione</option>
                                        <option value="ingreso">Ingreso</option>
                                        <option value="salida">Salida</option>
                                    </select>
                                    @error('tipo')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="monto" class="block text-sm font-medium text-gray-700 mb-2">
                                        Monto <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" 
                                           name="monto" 
                                           id="monto" 
                                           step="0.01"
                                           min="0.01"
                                           required
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    @error('monto')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="metodo_pago" class="block text-sm font-medium text-gray-700 mb-2">
                                        Método de Pago <span class="text-red-500">*</span>
                                    </label>
                                    <select name="metodo_pago" 
                                            id="metodo_pago" 
                                            required
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">Seleccione</option>
                                        <option value="efectivo">Efectivo</option>
                                        <option value="tarjeta">Tarjeta</option>
                                        <option value="transferencia">Transferencia</option>
                                        <option value="billetera_virtual">Billetera Virtual</option>
                                    </select>
                                    @error('metodo_pago')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-2">
                                        Descripción <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" 
                                           name="descripcion" 
                                           id="descripcion" 
                                           maxlength="500"
                                           required
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    @error('descripcion')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="mt-4">
                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                    Agregar Movimiento
                                </button>
                            </div>
                        </form>
                    </div>
                @endcan
            @endif

            <!-- Tabla de Movimientos -->
            <div class="mt-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Movimientos</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Monto</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Método Pago</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descripción</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($session->movements as $movement)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $movement->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if($movement->tipo === 'ingreso')
                                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">
                                                Ingreso
                                            </span>
                                        @else
                                            <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium">
                                                Salida
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        S/ {{ number_format($movement->monto, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ ucfirst(str_replace('_', ' ', $movement->metodo_pago)) }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">{{ $movement->descripcion }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                        No hay movimientos registrados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

