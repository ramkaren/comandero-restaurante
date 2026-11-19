<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;

class LimpiarVentasDemoSeeder extends VentasDemoSeeder
{
    public function run(): void
    {
        DB::transaction(fn () => $this->limpiar());

        $this->command->info('Ventas de ejemplo eliminadas.');
    }
}