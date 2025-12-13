<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ExportRolesPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'roles:export {--format=array : Formato de salida (array, json, seeder)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Exporta los roles y permisos actuales de la base de datos';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $format = $this->option('format');
        
        // Obtener todos los permisos
        $permissions = Permission::all()->pluck('name')->toArray();
        
        // Obtener todos los roles con sus permisos
        $roles = Role::with('permissions')->get()->map(function ($role) {
            return [
                'name' => $role->name,
                'guard_name' => $role->guard_name,
                'permissions' => $role->permissions->pluck('name')->toArray(),
            ];
        })->toArray();

        switch ($format) {
            case 'json':
                $this->exportAsJson($permissions, $roles);
                break;
            case 'seeder':
                $this->exportAsSeeder($permissions, $roles);
                break;
            case 'array':
            default:
                $this->exportAsArray($permissions, $roles);
                break;
        }

        return Command::SUCCESS;
    }

    /**
     * Exporta como array PHP
     */
    private function exportAsArray($permissions, $roles)
    {
        $this->info('=== PERMISOS ===');
        $this->line('[');
        foreach ($permissions as $perm) {
            $this->line("    '{$perm}',");
        }
        $this->line(']');
        $this->newLine();

        $this->info('=== ROLES Y SUS PERMISOS ===');
        $this->line('[');
        foreach ($roles as $role) {
            $this->line("    '{$role['name']}' => [");
            foreach ($role['permissions'] as $perm) {
                $this->line("        '{$perm}',");
            }
            $this->line('    ],');
        }
        $this->line(']');
    }

    /**
     * Exporta como JSON
     */
    private function exportAsJson($permissions, $roles)
    {
        $data = [
            'permissions' => $permissions,
            'roles' => $roles,
        ];

        $this->line(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    /**
     * Exporta como código de seeder
     */
    private function exportAsSeeder($permissions, $roles)
    {
        $this->info('=== CÓDIGO PARA EL SEEDER ===');
        $this->newLine();
        
        // Permisos
        $this->line('// Permisos');
        $this->line('$permissions = [');
        foreach ($permissions as $perm) {
            $this->line("    '{$perm}',");
        }
        $this->line('];');
        $this->newLine();

        // Crear permisos
        $this->line('foreach ($permissions as $permission) {');
        $this->line("    Permission::firstOrCreate(");
        $this->line("        ['name' => \$permission],");
        $this->line("        ['name' => \$permission, 'guard_name' => 'web']");
        $this->line('    );');
        $this->line('}');
        $this->newLine();

        // Roles
        $this->line('// Roles y sus permisos');
        $this->line('$roles = [');
        foreach ($roles as $role) {
            $this->line("    '{$role['name']}' => [");
            foreach ($role['permissions'] as $perm) {
                $this->line("        '{$perm}',");
            }
            $this->line('    ],');
        }
        $this->line('];');
        $this->newLine();

        // Crear roles
        $this->line('foreach ($roles as $roleName => $rolePermissions) {');
        $this->line("    \$role = Role::firstOrCreate(");
        $this->line("        ['name' => \$roleName],");
        $this->line("        ['name' => \$roleName, 'guard_name' => 'web']");
        $this->line('    );');
        $this->newLine();
        $this->line('    $permissions = Permission::whereIn(\'name\', $rolePermissions)->get();');
        $this->line('    $role->syncPermissions($permissions);');
        $this->line('}');
    }
}
