<?php

namespace Database\Seeders;

use App\Models\Comanda;
use App\Models\Cuenta;
use App\Models\DetalleComanda;
use App\Models\Mesa;
use App\Models\Pago;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PedidosDemoSeeder extends Seeder
{
    public function run(): void
    {
        $mesero    = User::first();
        $mesas     = Mesa::orderBy('numero')->take(6)->get()->values();
        $productos = Producto::where('activo', true)->take(5)->get();

        if (!$mesero || $mesas->isEmpty() || $productos->count() < 3) {
            $this->command?->warn('Faltan usuarios, mesas o productos. Ejecuta antes los otros seeders.');
            return;
        }

        // [estado de la comanda, ¿tiene cuenta?, método de pago o null]
        $casos = [
            ['en_preparacion', false, null],
            ['lista',          true,  null],
            ['entregada',      true,  'efectivo'],
            ['entregada',      true,  'tarjeta'],
            ['cancelada',      false, null],
            ['lista',          false, null],
        ];

        foreach ($casos as $i => [$estado, $conCuenta, $metodo]) {
            $mesa = $mesas[$i % $mesas->count()];

            $comanda = Comanda::create([
                'mesa_id'   => $mesa->id,
                'mesero_id' => $mesero->id,
                'estado'    => $estado,
                'total'     => 0,
            ]);

            foreach ($productos->shuffle()->take(rand(2, 3)) as $producto) {
                $cantidad = rand(1, 3);

                DetalleComanda::create([
                    'comanda_id'      => $comanda->id,
                    'producto_id'     => $producto->id,
                    'cantidad'        => $cantidad,
                    'precio_unitario' => $producto->precio,
                    'subtotal'        => $producto->precio * $cantidad,
                ]);
            }

            $comanda->recalcularTotal();
            $comanda->refresh();

            if (!$conCuenta) {
                continue;
            }

            $cuenta = Cuenta::create([
                'comanda_id' => $comanda->id,
                'mesa_id'    => $mesa->id,
                'estado'     => $metodo ? 'pagada' : 'pendiente',
                'subtotal'   => $comanda->total,
                'total'      => $comanda->total,
                'pagada_at'  => $metodo ? now() : null,
            ]);

            if ($metodo) {
                Pago::create([
                    'cuenta_id' => $cuenta->id,
                    'cajero_id' => $mesero->id,
                    'metodo'    => $metodo,
                    'monto'     => $comanda->total,
                    'estado'    => 'confirmado',
                    'pagado_at' => now(),
                ]);
            }
        }
    }
}
   
