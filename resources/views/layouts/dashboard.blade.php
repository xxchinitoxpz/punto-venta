<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Dashboard</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100" x-data="{ sidebarOpen: false, isFullscreen: false }" x-init="
    document.addEventListener('fullscreenchange', () => { isFullscreen = !!document.fullscreenElement; });
    document.addEventListener('webkitfullscreenchange', () => { isFullscreen = !!document.webkitFullscreenElement; });
    document.addEventListener('mozfullscreenchange', () => { isFullscreen = !!document.mozFullScreenElement; });
    document.addEventListener('MSFullscreenChange', () => { isFullscreen = !!document.msFullscreenElement; });
">
    <div class="min-h-screen flex">
        <!-- Overlay para móvil -->
        <div x-show="sidebarOpen" 
             @click="sidebarOpen = false"
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-gray-600 bg-opacity-75 z-20 lg:hidden"></div>

        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
                class="fixed left-0 top-0 h-full w-64 bg-gray-800 text-white transform transition-transform duration-300 ease-in-out z-30">
            <div class="flex flex-col h-full">
                <!-- Logo/Brand -->
                <div class="flex items-center justify-center h-16 bg-gray-900 border-b border-gray-700">
                    <h1 class="text-xl font-bold">{{ config('app.name', 'Laravel') }}</h1>
                </div>

                <!-- Navigation Links -->
                <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
                    <!-- 1. Dashboard -->
                    <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-3 text-gray-300 rounded-lg hover:bg-gray-700 hover:text-white transition-colors duration-200 {{ request()->routeIs('dashboard') ? 'bg-gray-700 text-white' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Dashboard
                    </a>

                    <!-- 2. Cajas -->
                    @can('ver cajas')
                        <a href="{{ route('cashboxes.index') }}" class="flex items-center px-4 py-3 text-gray-300 rounded-lg hover:bg-gray-700 hover:text-white transition-colors duration-200 {{ request()->routeIs('cashboxes*') || request()->routeIs('sessions*') ? 'bg-gray-700 text-white' : '' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                            </svg>
                            Cajas
                        </a>
                    @endcan

                    <!-- Ventas -->
                    @can('ver ventas')
                        <a href="{{ route('sales.index') }}" class="flex items-center px-4 py-3 text-gray-300 rounded-lg hover:bg-gray-700 hover:text-white transition-colors duration-200 {{ request()->routeIs('sales*') ? 'bg-gray-700 text-white' : '' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                            </svg>
                            Ventas
                        </a>
                    @endcan

                    <!-- Facturador -->
                    <a href="{{ route('invoices.index') }}" class="flex items-center px-4 py-3 text-gray-300 rounded-lg hover:bg-gray-700 hover:text-white transition-colors duration-200 {{ request()->routeIs('invoices*') ? 'bg-gray-700 text-white' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Facturador
                    </a>

                    <!-- 3. Productos -->
                    @can('ver productos')
                        <a href="{{ route('products.index') }}" class="flex items-center px-4 py-3 text-gray-300 rounded-lg hover:bg-gray-700 hover:text-white transition-colors duration-200 {{ request()->routeIs('products*') ? 'bg-gray-700 text-white' : '' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                            Productos
                        </a>
                    @endcan

                    <!-- 4. Inventario -->
                    @can('ver inventario')
                        <a href="{{ route('inventories.index') }}" class="flex items-center px-4 py-3 text-gray-300 rounded-lg hover:bg-gray-700 hover:text-white transition-colors duration-200 {{ request()->routeIs('inventories*') ? 'bg-gray-700 text-white' : '' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                            </svg>
                            Inventario
                        </a>
                    @endcan

                    <!-- 5. Promociones -->
                    @can('ver promociones')
                        <a href="{{ route('promotions.index') }}" class="flex items-center px-4 py-3 text-gray-300 rounded-lg hover:bg-gray-700 hover:text-white transition-colors duration-200 {{ request()->routeIs('promotions*') ? 'bg-gray-700 text-white' : '' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Promociones
                        </a>
                    @endcan

                    <!-- 6. Categorías -->
                    @can('ver categoria')
                        <a href="{{ route('categories.index') }}" class="flex items-center px-4 py-3 text-gray-300 rounded-lg hover:bg-gray-700 hover:text-white transition-colors duration-200 {{ request()->routeIs('categories*') ? 'bg-gray-700 text-white' : '' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                            Categorías
                        </a>
                    @endcan

                    <!-- 7. Marcas -->
                    @can('ver marca')
                        <a href="{{ route('brands.index') }}" class="flex items-center px-4 py-3 text-gray-300 rounded-lg hover:bg-gray-700 hover:text-white transition-colors duration-200 {{ request()->routeIs('brands*') ? 'bg-gray-700 text-white' : '' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                            Marcas
                        </a>
                    @endcan

                    <!-- 8. Clientes -->
                    @can('ver clientes')
                        <a href="{{ route('clients.index') }}" class="flex items-center px-4 py-3 text-gray-300 rounded-lg hover:bg-gray-700 hover:text-white transition-colors duration-200 {{ request()->routeIs('clients*') ? 'bg-gray-700 text-white' : '' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            Clientes
                        </a>
                    @endcan

                    <!-- 9. Proveedores -->
                    @can('ver proveedores')
                        <a href="{{ route('suppliers.index') }}" class="flex items-center px-4 py-3 text-gray-300 rounded-lg hover:bg-gray-700 hover:text-white transition-colors duration-200 {{ request()->routeIs('suppliers*') ? 'bg-gray-700 text-white' : '' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            Proveedores
                        </a>
                    @endcan

                    <!-- 10. Empresas -->
                    @can('ver empresa')
                        <a href="{{ route('companies.index') }}" class="flex items-center px-4 py-3 text-gray-300 rounded-lg hover:bg-gray-700 hover:text-white transition-colors duration-200 {{ request()->routeIs('companies*') ? 'bg-gray-700 text-white' : '' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            Empresas
                        </a>
                    @endcan

                    <!-- 11. Sucursales -->
                    @can('ver sucursal')
                        <a href="{{ route('branches.index') }}" class="flex items-center px-4 py-3 text-gray-300 rounded-lg hover:bg-gray-700 hover:text-white transition-colors duration-200 {{ request()->routeIs('branches*') ? 'bg-gray-700 text-white' : '' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Sucursales
                        </a>
                    @endcan

                    <!-- 12. Series de Documentos -->
                    @can('ver serie de documento')
                        <a href="{{ route('document-series.index') }}" class="flex items-center px-4 py-3 text-gray-300 rounded-lg hover:bg-gray-700 hover:text-white transition-colors duration-200 {{ request()->routeIs('document-series*') ? 'bg-gray-700 text-white' : '' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Series de Documentos
                        </a>
                    @endcan

                    <!-- 13. Usuarios -->
                    @can('ver usuarios')
                        <a href="{{ route('users.index') }}" class="flex items-center px-4 py-3 text-gray-300 rounded-lg hover:bg-gray-700 hover:text-white transition-colors duration-200 {{ request()->routeIs('users*') ? 'bg-gray-700 text-white' : '' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                            Usuarios
                        </a>
                    @endcan

                    <!-- 14. Roles y Permisos -->
                    @can('ver roles y permisos')
                        <a href="{{ route('roles.index') }}" class="flex items-center px-4 py-3 text-gray-300 rounded-lg hover:bg-gray-700 hover:text-white transition-colors duration-200 {{ request()->routeIs('roles*') ? 'bg-gray-700 text-white' : '' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                            Roles y Permisos
                        </a>
                    @endcan
                </nav>

                <!-- User Section -->
                <div class="p-4 border-t border-gray-700">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 rounded-full bg-gray-600 flex items-center justify-center">
                                <span class="text-sm font-medium">{{ substr(auth()->user()->name ?? 'U', 0, 1) }}</span>
                            </div>
                        </div>
                        <div class="ml-3 flex-1 min-w-0">
                            <p class="text-sm font-medium text-white truncate">
                                {{ auth()->user()->name ?? 'Usuario' }}
                            </p>
                            <p class="text-xs text-gray-400 truncate">
                                {{ auth()->user()->email ?? '' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div :class="sidebarOpen ? 'ml-64' : 'ml-0'" class="flex-1 flex flex-col transition-all duration-300 ease-in-out">
            <!-- Navbar -->
            <header :class="sidebarOpen ? 'left-64' : 'left-0'" class="fixed top-0 right-0 h-16 bg-white shadow-sm border-b border-gray-200 z-20 transition-all duration-300 ease-in-out">
                <div class="flex items-center justify-between h-full px-6">
                    <div class="flex items-center space-x-4">
                        <!-- Botón de menú -->
                        <button @click="sidebarOpen = !sidebarOpen" 
                                class="p-2 text-gray-600 hover:text-gray-900 rounded-lg hover:bg-gray-100 transition-colors">
                            <svg x-show="sidebarOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            <svg x-show="!sidebarOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>
                        <h2 class="text-lg font-semibold text-gray-800">Panel de Administración</h2>
                    </div>

                    <div class="flex items-center space-x-4">
                        <!-- Fullscreen Toggle -->
                        <button @click="
                            if (!isFullscreen) {
                                if (document.documentElement.requestFullscreen) {
                                    document.documentElement.requestFullscreen();
                                } else if (document.documentElement.webkitRequestFullscreen) {
                                    document.documentElement.webkitRequestFullscreen();
                                } else if (document.documentElement.mozRequestFullScreen) {
                                    document.documentElement.mozRequestFullScreen();
                                } else if (document.documentElement.msRequestFullscreen) {
                                    document.documentElement.msRequestFullscreen();
                                }
                            } else {
                                if (document.exitFullscreen) {
                                    document.exitFullscreen();
                                } else if (document.webkitExitFullscreen) {
                                    document.webkitExitFullscreen();
                                } else if (document.mozCancelFullScreen) {
                                    document.mozCancelFullScreen();
                                } else if (document.msExitFullscreen) {
                                    document.msExitFullscreen();
                                }
                            }
                        " 
                                class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 transition-colors"
                                title="Pantalla completa">
                            <!-- Icono expandir (cuando NO está en pantalla completa) -->
                            <svg x-show="!isFullscreen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                            </svg>
                            <!-- Icono contraer (cuando SÍ está en pantalla completa) -->
                            <svg x-show="isFullscreen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>

                        <!-- Notifications (placeholder) -->
                        <button class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                        </button>

                        <!-- Logout -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 rounded-lg hover:bg-gray-100 transition-colors">
                                Cerrar Sesión
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 mt-16 py-6 px-6">
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>

