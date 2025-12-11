@extends('layouts.app')

@section('content')
    <div class="p-6">
        <h1>Página de Bienvenida</h1>
        <p>¡Hola desde Laravel y Turbo!</p>
        <a href="{{ url('/about') }}" class="text-blue-500 hover:underline">Ir a la página About</a>
                </div>
@endsection
