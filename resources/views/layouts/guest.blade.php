<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex">
            <!-- Lado izquierdo - Branding -->
            <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 relative overflow-hidden">
                <!-- Patrón de fondo -->
                <div class="absolute inset-0 opacity-10">
                    <div class="absolute inset-0" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 40px 40px;"></div>
                </div>
                
                <!-- Contenido del branding -->
                <div class="relative z-10 flex flex-col justify-center px-12 text-white">
                    <div class="mb-8">
                        <a href="/" class="inline-block">
                            <div class="bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/20 shadow-2xl">
                                <x-application-logo class="w-20 h-20 fill-current text-white" />
                            </div>
                        </a>
                    </div>
                    
                    <h1 class="text-5xl font-bold mb-4 leading-tight">
                        Sistema de<br/>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-purple-400">Punto de Venta</span>
                    </h1>
                    
                    <p class="text-xl text-gray-300 mb-8 leading-relaxed">
                        Gestiona tu negocio de manera eficiente y profesional. Control total sobre ventas, inventario y reportes.
                    </p>
                    
                    <!-- Características -->
                    <div class="space-y-4 mt-8">
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0 w-10 h-10 bg-blue-500/20 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <span class="text-gray-300">Control de inventario en tiempo real</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0 w-10 h-10 bg-purple-500/20 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <span class="text-gray-300">Reportes y análisis detallados</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0 w-10 h-10 bg-indigo-500/20 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <span class="text-gray-300">Interfaz intuitiva y fácil de usar</span>
                        </div>
                    </div>
                </div>
                
                <!-- Formas decorativas -->
                <div class="absolute top-0 right-0 w-64 h-64 bg-blue-500/10 rounded-full blur-3xl"></div>
                <div class="absolute bottom-0 left-0 w-64 h-64 bg-purple-500/10 rounded-full blur-3xl"></div>
            </div>

            <!-- Lado derecho - Formulario -->
            <div class="w-full lg:w-1/2 flex items-center justify-center bg-gray-50 px-4 sm:px-6 lg:px-8">
                <div class="w-full max-w-md">
                    <!-- Logo móvil -->
                    <div class="lg:hidden mb-8 text-center">
                        <a href="/" class="inline-block">
                            <div class="bg-gradient-to-br from-slate-900 to-slate-800 rounded-2xl p-4 shadow-lg inline-block">
                                <x-application-logo class="w-16 h-16 fill-current text-white" />
                            </div>
                        </a>
                    </div>
                    
                    {{ $slot }}
                    
                    <!-- Footer móvil -->
                    <div class="lg:hidden mt-8 text-center">
                        <p class="text-gray-500 text-sm">© {{ date('Y') }} {{ config('app.name', 'Laravel') }}. Todos los derechos reservados.</p>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
