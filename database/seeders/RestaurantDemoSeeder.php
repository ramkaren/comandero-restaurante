<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Mesa;
use App\Models\Producto;
use Illuminate\Database\Seeder;

class RestaurantDemoSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Alimentos' => ['Hamburguesa clasica', 'Tacos de la casa'],
            'Bebidas' => ['Agua fresca', 'Cafe de olla'],
        ];

        foreach ($categories as $categoryName => $products) {
            $category = Categoria::firstOrCreate(['nombre' => $categoryName], ['activa' => true]);

            foreach ($products as $index => $productName) {
                Producto::firstOrCreate(
                    ['nombre' => $productName, 'categoria_id' => $category->id],
                    ['precio' => [129, 99, 35, 42][$index + ($categoryName === 'Bebidas' ? 2 : 0)], 'disponible' => true, 'activo' => true],
                );
            }
        }

        foreach (range(1, 8) as $number) {
            Mesa::firstOrCreate(['numero' => $number], ['capacidad' => $number <= 4 ? 4 : 6, 'estado' => 'disponible', 'activa' => true]);
        }
    }
}