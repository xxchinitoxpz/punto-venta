<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Brand;
use Illuminate\Database\Seeder;

class CategoryBrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creando categorías...');

        // Categorías
        $categorias = [
            'Bebidas',
            'Lácteos',
            'Panadería',
            'Snacks',
            'Dulces y Chocolates',
            'Cereales y Desayunos',
            'Conservas',
            'Condimentos y Salsas',
            'Limpieza del Hogar',
            'Cuidado Personal',
            'Cigarrillos',
            'Congelados',
            'Frutas y Verduras',
            'Carnes y Embutidos',
            'Pescados y Mariscos',
            'Granos y Legumbres',
            'Aceites y Vinagres',
            'Harinas y Pastas',
            'Sopas y Caldos',
            'Golosinas',
            'Bebidas Alcohólicas',
            'Artículos de Papelería',
            'Productos para Bebés',
            'Mascotas',
            'Otros',
            'chicles',
            'galletas',
            'pilas',
            'Hielo',
            'Farmacia',
        ];

        $categoriasCreadas = 0;
        foreach ($categorias as $categoria) {
            Category::firstOrCreate(['nombre' => $categoria]);
            $categoriasCreadas++;
        }

        $this->command->info("✓ {$categoriasCreadas} categorías creadas/verificadas.");

        $this->command->info('Creando marcas...');

        // Marcas
        $marcas = [
            'Coca-Cola',
            'Pepsi',
            'Inca Kola',
            'Fanta',
            'Sprite',
            'San Mateo',
            'San Luis',
            'Cifrut',
            'Pulp',
            'Laive',
            'Nestlé',
            'Danone',
            'Yoplait',
            'Alpina',
            'Pura Vida',
            'Lácteos San Fernando',
            'Doritos',
            'Cheetos',
            'Ruffles',
            'Frito Lay',
            'Kraft',
            'Ferrero',
            'Cadbury',
            'Hershey\'s',
            'M&M\'s',
            'Snickers',
            'Twix',
            'Milky Way',
            'Kellogg\'s',
            'Quaker',
            'General Mills',
            'La Campiña',
            'San Fernando',
            'Maggi',
            'Knorr',
            'Ajinomoto',
            'Salsa Inglesa',
            'Ketchup Heinz',
            'Mayonesa Hellmann\'s',
            'Mostaza Heinz',
            'Ace',
            'Clorox',
            'Ajax',
            'Fabuloso',
            'Downy',
            'Ariel',
            'Tide',
            'Omo',
            'Rexona',
            'Dove',
            'Colgate',
            'Crest',
            'Oral-B',
            'Head & Shoulders',
            'Pantene',
            'Nivea',
            'Ponds',
            'Vaseline',
            'Marlboro',
            'Lucky Strike',
            'Kent',
            'Benson & Hedges',
            'Philip Morris',
            'D\'Onofrio',
            'Primor',
            'Cocinero',
            'Capullo',
            'Molitalia',
            'Barilla',
            'Don Vittorio',
            'Gloria',
            'Cristal',
            'Pilsen',
            'Cusqueña',
            'Corona',
            'Heineken',
            'Budweiser',
            'Johnnie Walker',
            'Jack Daniel\'s',
            'Bacardi',
            'Smirnoff',
            'Genérico',
            'Sin Marca',
            'Otros',
            'confiteca',
            'tic tac',
            'bimbo',
            'mondelez',
            'aje',
            'medifarma',
            'backus',
            'tabernero',
            'campomar',
            'yichang (seafrost sac)',
            'trululu',
            'alcohol',
            'Donofrio',
            'Inka chips',
            'ajinomen',
            'pringles',
            'winter',
            'galleta',
            'Four loko',
            'ayudin',
            'poett',
            'elite',
            'protex',
            'Nosotras',
            'Angel',
            'Mogul',
            'Nikolo',
            'Alacena',
            'Marsella',
            'Bolivar',
            'Bonobon',
            'Encendedor',
            'Kolynos',
            'Speed stick',
            'Schick',
            'Scott',
            'costeño',
            'dulfina',
            'altomayo',
            'Piel',
            'Ricocan',
            'panasonic',
            'Hielo',
            'Skittles',
            'Andrews',
        ];

        $marcasCreadas = 0;
        foreach ($marcas as $marca) {
            Brand::firstOrCreate(['nombre' => $marca]);
            $marcasCreadas++;
        }

        $this->command->info("✓ {$marcasCreadas} marcas creadas/verificadas.");
        $this->command->info('');
        $this->command->info('═══════════════════════════════════════════════════════');
        $this->command->info('  Categorías y marcas creadas exitosamente');
        $this->command->info('═══════════════════════════════════════════════════════');
    }
}

