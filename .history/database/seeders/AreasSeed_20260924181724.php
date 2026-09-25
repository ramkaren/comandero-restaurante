<?php

namespace Database\Seeders;

use App\Models\Areas;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AreasSeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $areas = [
            [
                'nombre' => 'Administración',
                'descripcion' => 'Área encargada de la gestión'
            ],
            [
                'nombre' => 'Cocina',
                'descripcion' => 'Área encargada de la cocina'
            ],
            [
                'nombre' => 'Piso',
                'descripcion' => 'Área encargada de la atención al cliente'
            ]
        ];

        foreach ($areas as $area) {
            Areas::firstOrCreate(
                [
                    'nombre' => $area['nombre']
                ],
                $area
            );
        }
    }
}
