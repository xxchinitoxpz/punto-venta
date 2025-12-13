<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Presentation;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creando productos con sus presentaciones...');

        // Obtener o crear categorías y marcas de ejemplo
        $categoriaBebidas = Category::firstOrCreate(['nombre' => 'Bebidas']);
        $categoriaGranos = Category::firstOrCreate(['nombre' => 'Granos']);
        $marcaCocaCola = Brand::firstOrCreate(['nombre' => 'Coca Cola']);
        $marcaGenerica = Brand::firstOrCreate(['nombre' => 'Genérico']);

        // Producto 1: Coca Cola
        $producto1 = Product::firstOrCreate(
            ['nombre' => 'Coca Cola'],
            [
                'nombre' => 'Coca Cola',
                'descripcion' => 'Refresco gaseoso de cola',
                'categoria_id' => $categoriaBebidas->id,
                'marca_id' => $marcaCocaCola->id,
            ]
        );

        // Presentaciones del Producto 1
        $presentaciones1 = [
            [
                'nombre' => 'Botella 500ml',
                'barcode' => '7702004000011',
                'precio_venta' => 3.50,
                'unidades' => 1,
            ],
            [
                'nombre' => 'Botella 1.5L',
                'barcode' => '7702004000028',
                'precio_venta' => 6.00,
                'unidades' => 1,
            ],
            [
                'nombre' => 'Pack 6 unidades 500ml',
                'barcode' => '7702004000035',
                'precio_venta' => 18.00,
                'unidades' => 6,
            ],
        ];

        foreach ($presentaciones1 as $presentacion) {
            Presentation::firstOrCreate(
                [
                    'product_id' => $producto1->id,
                    'barcode' => $presentacion['barcode'],
                ],
                [
                    'product_id' => $producto1->id,
                    'nombre' => $presentacion['nombre'],
                    'barcode' => $presentacion['barcode'],
                    'precio_venta' => $presentacion['precio_venta'],
                    'unidades' => $presentacion['unidades'],
                ]
            );
        }

        $this->command->info("✓ Producto creado: {$producto1->nombre} con " . count($presentaciones1) . " presentaciones");

        // Producto 2: Arroz
        $producto2 = Product::firstOrCreate(
            ['nombre' => 'Arroz Extra'],
            [
                'nombre' => 'Arroz Extra',
                'descripcion' => 'Arroz de grano largo',
                'categoria_id' => $categoriaGranos->id,
                'marca_id' => $marcaGenerica->id,
            ]
        );

        // Presentaciones del Producto 2
        $presentaciones2 = [
            [
                'nombre' => 'Bolsa 1kg',
                'barcode' => '7750123456789',
                'precio_venta' => 4.50,
                'unidades' => 1,
            ],
            [
                'nombre' => 'Bolsa 5kg',
                'barcode' => '7750123456796',
                'precio_venta' => 20.00,
                'unidades' => 5,
            ],
        ];

        foreach ($presentaciones2 as $presentacion) {
            Presentation::firstOrCreate(
                [
                    'product_id' => $producto2->id,
                    'barcode' => $presentacion['barcode'],
                ],
                [
                    'product_id' => $producto2->id,
                    'nombre' => $presentacion['nombre'],
                    'barcode' => $presentacion['barcode'],
                    'precio_venta' => $presentacion['precio_venta'],
                    'unidades' => $presentacion['unidades'],
                ]
            );
        }

        $this->command->info("✓ Producto creado: {$producto2->nombre} con " . count($presentaciones2) . " presentaciones");
        $this->command->info('');
        $this->command->info('═══════════════════════════════════════════════════════');
        $this->command->info('  Productos de ejemplo creados exitosamente:');
        $this->command->info('  1. Coca Cola (3 presentaciones)');
        $this->command->info('  2. Arroz Extra (2 presentaciones)');
        $this->command->info('═══════════════════════════════════════════════════════');
    }
}

