<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded text-emerald-700 text-sm" :status="session('status')" />

    <!-- Encabezado -->
    <div class="mb-10">
        <h2 class="text-3xl font-bold text-gray-900 mb-2">Iniciar Sesión</h2>
        <p class="text-gray-600">Ingresa tus credenciales para acceder al sistema</p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Correo electrónico')" />
            <div class="relative mt-2 group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400 group-focus-within:text-slate-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
                <x-text-input id="email" class="block w-full pl-12 pr-4 py-3 bg-white border-2 border-gray-200 focus:border-slate-600 focus:ring-0 rounded-xl shadow-sm transition-all placeholder:text-gray-400" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="correo@ejemplo.com" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Contraseña')" />
            <div class="relative mt-2 group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400 group-focus-within:text-slate-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <x-text-input id="password" class="block w-full pl-12 pr-4 py-3 bg-white border-2 border-gray-200 focus:border-slate-600 focus:ring-0 rounded-xl shadow-sm transition-all placeholder:text-gray-400"
                            type="password"
                            name="password"
                            required autocomplete="current-password"
                            placeholder="••••••••" />
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="flex items-center justify-between pt-2">
            <label for="remember_me" class="inline-flex items-center cursor-pointer group">
                <input id="remember_me" type="checkbox" class="w-4 h-4 text-slate-600 border-gray-300 rounded focus:ring-slate-500 focus:ring-2 cursor-pointer transition-all" name="remember">
                <span class="ms-2 text-sm text-gray-700 group-hover:text-gray-900 transition-colors font-medium">{{ __('Recordarme') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm text-slate-600 hover:text-slate-900 font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 rounded-md px-2 py-1" href="{{ route('password.request') }}">
                    {{ __('¿Olvidaste tu contraseña?') }}
                </a>
            @endif
        </div>

        <!-- Submit Button -->
        <div class="pt-6">
            <x-primary-button class="w-full flex items-center justify-center py-3.5 text-base font-semibold rounded-xl shadow-md hover:shadow-lg transform hover:scale-[1.02] active:scale-[0.98] transition-all duration-200">
                <span>{{ __('Iniciar sesión') }}</span>
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                </svg>
            </x-primary-button>
        </div>
    </form>

    <!-- Separador opcional -->
    <div class="mt-8 pt-8 border-t border-gray-200">
        <p class="text-center text-sm text-gray-600">
            ¿Necesitas ayuda? 
            <a href="#" class="text-slate-600 hover:text-slate-900 font-medium">Contacta con soporte</a>
        </p>
    </div>
</x-guest-layout>
