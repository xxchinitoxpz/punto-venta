<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Definir todos los permisos
        $permissions = [
            'crear usuarios',
            'ver usuarios',
            'editar usuarios',
            'eliminar usuarios',
            'ver roles y permisos',
            'crear roles y permisos',
            'editar roles y permisos',
            'eliminar roles y permisos',
            'ver empresa',
            'crear empresa',
            'editar empresa',
            'eliminar empresa',
            'ver sucursal',
            'crear sucursal',
            'editar sucursal',
            'eliminar sucursal',
            'ver serie de documento',
            'crear serie de documento',
            'editar serie de documento',
            'eliminar serie de documento',
            'ver categoria',
            'crear categoria',
            'editar categoria',
            'eliminar categoria',
            'ver marca',
            'crear marca',
            'editar marca',
            'eliminar marca',
            'ver productos',
            'crear productos',
            'editar productos',
            'eliminar productos',
            'ver inventario',
            'crear inventario',
            'editar inventario',
            'eliminar inventario',
            'ver clientes',
            'crear clientes',
            'editar clientes',
            'eliminar clientes',
            'ver proveedores',
            'crear proveedores',
            'editar proveedores',
            'eliminar proveedores',
            'ver cajas',
            'abrir caja',
            'cerrar caja',
            'hacer ajustes de caja',
            'ver historial de cajas',
            'ver promociones',
            'crear promociones',
            'editar promociones',
            'eliminar promociones',
            'ver ventas',
            'crear ventas',
            'anular ventas',
        ];

        $this->command->info('Creando permisos...');
        
        // Crear permisos
        $createdPermissions = [];
        foreach ($permissions as $permission) {
            $perm = Permission::firstOrCreate(
                ['name' => $permission, 'guard_name' => 'web'],
                ['name' => $permission, 'guard_name' => 'web']
            );
            $createdPermissions[] = $perm;
        }

        $this->command->info('✓ ' . count($createdPermissions) . ' permisos creados/verificados.');

        // Crear rol Admin
        $this->command->info('Creando rol Admin...');
        $adminRole = Role::firstOrCreate(
            ['name' => 'Admin', 'guard_name' => 'web'],
            ['name' => 'Admin', 'guard_name' => 'web']
        );

        // Asignar todos los permisos al rol Admin
        $adminRole->syncPermissions($createdPermissions);
        $this->command->info('✓ Rol Admin creado con todos los permisos asignados.');

        // Crear usuario admin
        $this->command->info('Creando usuario admin...');
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@puntoventa.com'],
            [
                'name' => 'Administrador',
                'email' => 'admin@puntoventa.com',
                'password' => Hash::make('password'), // Cambiar esta contraseña después
            ]
        );

        // Asignar rol Admin al usuario
        if (!$adminUser->hasRole('Admin')) {
            $adminUser->assignRole('Admin');
        }

        $this->command->info('✓ Usuario admin creado con el rol Admin asignado.');
        $this->command->info('');
        $this->command->info('═══════════════════════════════════════════════════════');
        $this->command->info('  Credenciales de acceso:');
        $this->command->info('  Email: admin@puntoventa.com');
        $this->command->info('  Password: password');
        $this->command->info('═══════════════════════════════════════════════════════');
    }
}

