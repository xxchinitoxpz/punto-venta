@extends('layouts.dashboard')

@section('content')
    <div class="w-full">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <!-- Header con enlace a roles -->
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Gestión de Permisos</h1>
                @can('ver roles y permisos')
                    <a href="{{ route('roles.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors text-sm">
                        Ver Roles
                    </a>
                @endcan
            </div>

            <!-- Mensajes de éxito -->
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Formulario para crear permiso -->
            @can('crear roles y permisos')
            <div class="mb-6 p-6 bg-blue-50 rounded-lg border-2 border-blue-200">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-800">Crear Nuevo Permiso</h2>
                    <span class="text-sm text-gray-600">Total: {{ $permissions->total() }} permisos</span>
                </div>
                <form action="{{ route('permissions.store') }}" method="POST" id="permissionForm">
                    @csrf
                    <div class="flex items-end space-x-4">
                        <div class="flex-1">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Nombre del Permiso <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="name" 
                                   id="name" 
                                   value="{{ old('name') }}" 
                                   required
                                   placeholder="Ej: crear usuarios, editar productos, eliminar ventas"
                                   autofocus
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">Puedes crear múltiples permisos. Después de crear uno, puedes crear otro inmediatamente.</p>
                        </div>
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                            Crear Permiso
                        </button>
                    </div>
                </form>
            </div>
            @endcan

            <!-- Tabla de permisos -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha de Creación</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($permissions as $permission)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $permission->id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $permission->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $permission->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">
                                    No hay permisos registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            @if($permissions->hasPages())
                <div class="mt-4">
                    {{ $permissions->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

