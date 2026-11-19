<?php

namespace Database\Seeders;

use App\Models\Comanda;
use App\Models\Cuenta;
use App\Models\DetalleComanda;
use App\Models\Mesa;
use App\Models\Pago;
use App\Models\Producto;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

//Ventas de ejemplo
class VentasDemoSeeder extends Seeder
{
    protected const TZ = 'America/Mexico_City';
    protected const MARCA = 'DEMO-VENTAS';
    private const DIAS = 56;

    public function run(): void
    {
        if (app()->environment('production')) {
            $this->command->error('Este seeder es solo de pruebas y no se ejecuta con APP_ENV=production.');

            return;
        }

        $mesas     = Mesa::where('activa', true)->get()->values();
        $productos = Producto::where('activo', true)->where('disponible', true)->get()->values();
        $meseros   = $this->usuariosConRol('mesero');
        $cajeros   = $this->usuariosConRol('cajero');

        if ($meseros->isEmpty()) {
            $meseros = User::all()->values();
        }
        if ($cajeros->isEmpty()) {
            $cajeros = $meseros;
        }

        if ($mesas->isEmpty() || $productos->isEmpty() || $meseros->isEmpty()) {
            $this->command->error('Faltan mesas, productos o usuarios. Corre antes los seeders base.');

            return;
        }

        mt_srand(2026);

        $factorDia = [1 => 0.6, 2 => 0.6, 3 => 0.7, 4 => 0.8, 5 => 1.2, 6 => 1.5, 7 => 1.3];
        $horas     = [9, 10, 11, 13, 13, 14, 14, 15, 16, 19, 20, 20, 21];
        $metodos   = ['efectivo', 'efectivo', 'efectivo', 'efectivo', 'tarjeta', 'tarjeta', 'tarjeta', 'tarjeta', 'transferencia'];

        $hoy      = CarbonImmutable::now(self::TZ)->startOfDay();
        $ventas   = 0;
        $acumulado = 0.0;

        DB::transaction(function () use (
            $mesas, $productos, $meseros, $cajeros, $factorDia, $horas, $metodos, $hoy, &$ventas, &$acumulado
        ) {
            $this->limpiar();

            for ($i = self::DIAS; $i >= 1; $i--) {
                $dia         = $hoy->subDays($i);
                $crecimiento = 1 + (self::DIAS - $i) / self::DIAS * 0.25;
                $ruido       = mt_rand(80, 120) / 100;
                $cantidad    = max(2, (int) round(14 * $factorDia[$dia->isoWeekday()] * $crecimiento * $ruido));

                for ($n = 0; $n < $cantidad; $n++) {
                    $acumulado += $this->crearVenta($dia, $mesas, $productos, $meseros, $cajeros, $horas, $metodos);
                    $ventas++;
                }
            }
        });

        $this->command->info(sprintf(
            'Se crearon %d ventas por $%s en %d días.',
            $ventas,
            number_format($acumulado, 2),
            self::DIAS
        ));
    }

    
    protected function limpiar(): void
    {
        $cuentaIds  = Pago::where('referencia', self::MARCA)->pluck('cuenta_id');
        $comandaIds = Cuenta::whereIn('id', $cuentaIds)->pluck('comanda_id');

        Pago::where('referencia', self::MARCA)->delete();
        Cuenta::whereIn('id', $cuentaIds)->delete();
        DetalleComanda::whereIn('comanda_id', $comandaIds)->delete();
        Comanda::whereIn('id', $comandaIds)->delete();
    }

    private function crearVenta(
        CarbonImmutable $dia,
        Collection $mesas,
        Collection $productos,
        Collection $meseros,
        Collection $cajeros,
        array $horas,
        array $metodos
    ): float {
        $tzApp  = config('app.timezone');
        $abre   = $dia->setTime($horas[array_rand($horas)], mt_rand(0, 59));
        $cobra  = $abre->addMinutes(mt_rand(20, 55));
        $creada = $abre->setTimezone($tzApp);
        $pagada = $cobra->setTimezone($tzApp);

        $mesa   = $mesas[mt_rand(0, $mesas->count() - 1)];
        $mesero = $meseros[mt_rand(0, $meseros->count() - 1)];
        $cajero = $cajeros[mt_rand(0, $cajeros->count() - 1)];

        $lineas = [];
        $total  = 0.0;
        foreach ($productos->shuffle()->take(mt_rand(1, 4)) as $producto) {
            $cantidad = mt_rand(1, 3);
            $precio   = (float) $producto->precio;
            $subtotal = round($precio * $cantidad, 2);
            $total   += $subtotal;

            $lineas[] = [
                'producto_id'     => $producto->id,
                'cantidad'        => $cantidad,
                'precio_unitario' => $precio,
                'subtotal'        => $subtotal,
            ];
        }
        $total = round($total, 2);

        $comanda = Comanda::forceCreate([
            'mesa_id'    => $mesa->id,
            'mesero_id'  => $mesero->id,
            'estado'     => 'entregada',
            'total'      => $total,
            'created_at' => $creada,
            'updated_at' => $pagada,
        ]);

        foreach ($lineas as $linea) {
            DetalleComanda::forceCreate(['comanda_id' => $comanda->id] + $linea);
        }

        $cuenta = Cuenta::forceCreate([
            'comanda_id' => $comanda->id,
            'mesa_id'    => $mesa->id,
            'estado'     => 'pagada',
            'subtotal'   => $total,
            'total'      => $total,
            'pagada_at'  => $pagada,
            'created_at' => $creada,
            'updated_at' => $pagada,
        ]);

        $metodo   = $metodos[array_rand($metodos)];
        $recibido = $metodo === 'efectivo' ? ceil($total / 50) * 50 : null;

        Pago::forceCreate([
            'cuenta_id'      => $cuenta->id,
            'cajero_id'      => $cajero->id,
            'metodo'         => $metodo,
            'monto'          => $total,
            'monto_recibido' => $recibido,
            'cambio'         => $recibido !== null ? round($recibido - $total, 2) : null,
            'referencia'     => self::MARCA,
            'estado'         => 'confirmado',
            'pagado_at'      => $pagada,
            'created_at'     => $pagada,
            'updated_at'     => $pagada,
        ]);

        return $total;
    }

    private function usuariosConRol(string $rol): Collection
    {
        try {
            return User::role($rol)->get()->values();
        } catch (\Throwable) {
            return collect();
        }
    }
}