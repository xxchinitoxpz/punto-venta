@extends('layouts.dashboard')

@section('content')
    <div class="w-full">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Detalles del Usuario</h1>
                <a href="{{ route('users.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Volver
                </a>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">ID</label>
                    <p class="text-gray-900">{{ $user->id }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                    <p class="text-gray-900">{{ $user->name }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <p class="text-gray-900">{{ $user->email }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sucursal</label>
                    @if($user->branch)
                        <p class="text-gray-900">{{ $user->branch->nombre }} 
                            @if($user->branch->company)
                                - {{ $user->branch->company->razon_social }}
                            @endif
                        </p>
                    @else
                        <p class="text-gray-500">Sin sucursal asignada</p>
                    @endif
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Roles</label>
                    @if($user->roles->count() > 0)
                        <div class="flex flex-wrap gap-2">
                            @foreach($user->roles as $role)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                                    {{ $role->name }}
                                </span>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500">Este usuario no tiene roles asignados.</p>
                    @endif
                </div>

                <div class="flex items-center space-x-4 pt-4">
                    @can('editar usuarios')
                        <a href="{{ route('users.edit', $user) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            Editar Usuario
                        </a>
                    @endcan
                </div>
            </div>
        </div>
    </div>
@endsection

