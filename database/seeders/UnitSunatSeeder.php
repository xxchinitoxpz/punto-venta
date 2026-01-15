<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnitSunatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = [
            ['code' => 'ZZ', 'description' => 'Servicio'],
            ['code' => 'BX', 'description' => 'Caja'],
            ['code' => 'GLL', 'description' => 'Galon'],
            ['code' => 'KGM', 'description' => 'Kilos'],
            ['code' => 'NIU', 'description' => 'Unidad'],
            ['code' => 'PK', 'description' => 'Paquete'],
            ['code' => 'SA', 'description' => 'Saco'],
        ];

        foreach ($units as $unit) {
            DB::table('unit_sunat')->updateOrInsert(
                ['code' => $unit['code']],
                [
                    'code' => $unit['code'],
                    'description' => $unit['description'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
