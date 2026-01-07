@extends('layouts.dashboard')

@section('content')
    <div class="w-full">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <!-- Header -->
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Facturador SUNAT</h1>
            </div>

            <!-- Mensajes de éxito/error -->
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

            <!-- Respuesta de SUNAT -->
            @if(session('sunat_response'))
                @php
                    $response = session('sunat_response');
                @endphp
                <div class="mb-6 rounded-lg p-6 border-2 
                    @if(isset($response['estado']) && $response['estado'] === 'ACEPTADA')
                        bg-green-50 border-green-400
                    @elseif(isset($response['estado']) && $response['estado'] === 'RECHAZADA')
                        bg-red-50 border-red-400
                    @elseif(isset($response['estado']) && $response['estado'] === 'EXCEPCIÓN')
                        bg-yellow-50 border-yellow-400
                    @else
                        bg-orange-50 border-orange-400
                    @endif">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Respuesta de SUNAT</h2>
                    
                    <div class="space-y-4">
                        @if(isset($response['estado']))
                        <div>
                            <p class="text-sm font-semibold text-gray-600">Estado:</p>
                            <p class="text-lg font-bold 
                                @if($response['estado'] === 'ACEPTADA') text-green-700
                                @elseif($response['estado'] === 'RECHAZADA') text-red-700
                                @elseif($response['estado'] === 'EXCEPCIÓN') text-yellow-700
                                @else text-orange-700
                                @endif">
                                {{ $response['estado'] }}
                            </p>
                        </div>
                        @endif

                        @if(isset($response['codigo']))
                        <div>
                            <p class="text-sm font-semibold text-gray-600">Código:</p>
                            <p class="text-gray-800 font-mono">{{ $response['codigo'] }}</p>
                        </div>
                        @endif

                        @if(isset($response['descripcion']))
                        <div>
                            <p class="text-sm font-semibold text-gray-600">Descripción:</p>
                            <p class="text-gray-800">{{ $response['descripcion'] }}</p>
                        </div>
                        @endif

                        @if(isset($response['codigo_error']))
                        <div>
                            <p class="text-sm font-semibold text-gray-600">Código de Error:</p>
                            <p class="text-gray-800 font-mono">{{ $response['codigo_error'] }}</p>
                        </div>
                        @endif

                        @if(isset($response['mensaje_error']))
                        <div>
                            <p class="text-sm font-semibold text-gray-600">Mensaje de Error:</p>
                            <p class="text-gray-800">{{ $response['mensaje_error'] }}</p>
                        </div>
                        @endif

                        @if(isset($response['observaciones']) && count($response['observaciones']) > 0)
                        <div>
                            <p class="text-sm font-semibold text-gray-600 mb-2">Observaciones:</p>
                            <ul class="list-disc list-inside space-y-2">
                                @foreach($response['observaciones'] as $obs)
                                <li class="text-gray-800">
                                    <span class="font-semibold">Código {{ $obs['codigo'] ?? 'N/A' }}:</span>
                                    {{ $obs['mensaje'] ?? 'N/A' }}
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Contenido principal -->
            <div class="bg-gray-50 rounded-lg p-6">
                <div class="flex items-center justify-center">
                    <form action="{{ route('invoices.send') }}" method="POST" class="inline-block">
                        @csrf
                        <button type="submit" class="px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors shadow-md hover:shadow-lg">
                            Send
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

