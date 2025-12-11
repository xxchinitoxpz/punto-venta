@extends('layouts.dashboard')

@section('content')
    <div class="w-full">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-4">Bienvenido al Dashboard</h1>
            <p class="text-gray-600 mb-6">Contenido reemplazable. Esta área será actualizada por Turbo Drive al navegar entre secciones.</p>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                <!-- Card 1 -->
                <div class="bg-blue-50 rounded-lg p-6 border border-blue-200">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-lg font-semibold text-blue-900">Dashboard</h3>
                        <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                    </div>
                    <p class="text-blue-700 text-sm">Vista principal del panel de administración</p>
                </div>

                <!-- Card 2 -->
                <div class="bg-green-50 rounded-lg p-6 border border-green-200">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-lg font-semibold text-green-900">Usuarios</h3>
                        <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                    <p class="text-green-700 text-sm">Gestión de usuarios del sistema</p>
                </div>

                <!-- Card 3 -->
                <div class="bg-purple-50 rounded-lg p-6 border border-purple-200">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-lg font-semibold text-purple-900">Artículos</h3>
                        <svg class="w-8 h-8 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <p class="text-purple-700 text-sm">Administración de artículos</p>
                </div>
            </div>
        </div>
    </div>
@endsection

