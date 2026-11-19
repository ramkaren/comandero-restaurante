<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Database\Seeder;

class MenuProductosSeeder extends Seeder
{
    public function run(): void
    {
        $productos = [
           ['Bebidas', 'Café americano', 'Taza pequeña', 30.00],
['Comida rápida', 'Hamburguesa Sencilla', 'Jitomate, cebolla, lechuga y queso', 55.00],
['Bebidas', 'Malteada de Vainilla', 'Helado de Vainilla', 55.00],
['Bebidas', 'Café de olla', 'Taza mediana', 25.00],
['Bebidas', 'Aguas Frescas', 'Limón, fresa, piña,  1/2 o litro', 37.00],
['Bebidas', 'Matcha', 'Medio o Litro', 43.00],
['Bebidas', 'Té', 'Taza mediana', 24.00],
['Bebidas', 'Frappé', '500 ml', 42.00],
['Bebidas', 'Agua Natural', 'Bonafont, Ciel', 26.00],
['Bebidas', 'Agua Mineral', '350 ml', 27.00],
['Bebidas', 'Mocka', 'Taza Mediana', 32.00],
['Bebidas', 'Jugo de naranja', 'Medio o Litro', 36.00],
['Bebidas', 'Champurrado', 'Medio o Litro', 30.00],
['Bebidas', 'Chocolate Caliente', 'Taza mediana', 28.00],
['Bebidas', 'Café espresso', 'Taza mediana', 26.00],
['Comida rápida', 'Hamburguesa c/papas a la francesa', 'Doble carne, queso y pepinillos', 74.00],
['Comida rápida', 'Papas a la francesa', 'Queso, Valentina, Catsupt', 34.00],
['Comida rápida', 'Pizza Pepperoni', 'Mediana, Grande, Familiar', 110.00],
['Comida rápida', 'Club Sandwich', 'SANDWICH medianos', 54.00],
['Comida rápida', 'Hot dogs Sencillos', '3 piezas', 55.00],
['Comida rápida', 'Hot dogs con tocino', '3 piezas', 65.00],
['Comida rápida', 'Tacos', 'BIstec 3 piezas', 50.00],
['Comida rápida', 'Alitas', 'Mediana, Grande', 84.00],
['Comida rápida', 'Boneless de pollo', 'BBQ, Habanero de mango', 84.00],
['Bebidas', 'Capuchino', 'Taza mediana', 54.00],
        ];

        foreach ($productos as [$categoriaNombre, $nombre, $descripcion, $precio]) {
            $categoria = Categoria::firstOrCreate(
                ['nombre' => $categoriaNombre],
                ['activa' => true]
            );

            Producto::firstOrCreate(
                ['nombre' => $nombre, 'categoria_id' => $categoria->id],
                ['descripcion' => $descripcion, 'precio' => $precio, 'disponible' => true, 'activo' => true]
            );
        }
    }
}
