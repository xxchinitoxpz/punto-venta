@extends('layouts.dashboard')

@section('content')
    <div class="w-full">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Detalles del Rol</h1>
                <a href="{{ route('roles.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Volver
                </a>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">ID</label>
                    <p class="text-gray-900">{{ $role->id }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                    <p class="text-gray-900">{{ $role->name }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Permisos</label>
                    @if($role->permissions->count() > 0)
                        <div class="flex flex-wrap gap-2">
                            @foreach($role->permissions as $permission)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                    {{ $permission->name }}
                                </span>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500">Este rol no tiene permisos asignados.</p>
                    @endif
                </div>

                <div class="flex items-center space-x-4 pt-4">
                    @can('editar roles y permisos')
                        <a href="{{ route('roles.edit', $role) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            Editar Rol
                        </a>
                    @endcan
                </div>
            </div>
        </div>
    </div>
@endsection

