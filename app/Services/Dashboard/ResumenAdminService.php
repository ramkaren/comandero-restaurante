<?php

namespace App\Services\Dashboard;

use App\Models\Actividad;
use App\Models\Comanda;
use App\Models\Mesa;
use App\Services\Reportes\VentasReporteService;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Schema;

class ResumenAdminService
{
    /** Zona en la que se cuenta "hoy": el día cambia a la medianoche local. */
    private const TZ = 'America/Mexico_City';

    public function __construct(private VentasReporteService $ventas)
    {
    }

    public function resumen(): array
    {
        $ahora = CarbonImmutable::now(self::TZ);
        $hoy   = $ahora->startOfDay();

        $semana = $this->ventas->consultar(null, null);

        return [
            'fecha'              => $hoy->toDateString(),
            'pedidos_hoy'        => $this->pedidosHoy($hoy, $ahora),
            'personal_activo'    => $this->personalActivo($hoy, $ahora),
            'mesas'              => $this->mesas(),
            'ventas_hoy'         => $this->ventas->comparaciones()['hoy'],
            'ventas_semana'      => [
                'total' => $semana['resumen']['total'],
                'serie' => $semana['serie'],
            ],
            'pedidos_por_estado' => $this->pedidosPorEstado($hoy, $ahora),
            'actividad_reciente' => $this->actividadReciente(),
        ];
    }

    private function pedidosHoy(CarbonImmutable $hoy, CarbonImmutable $ahora): array
    {
        $actual   = $this->contarPedidos($hoy, $ahora);
        $anterior = $this->contarPedidos($hoy->subDay(), $ahora->subDay());

        $diferencia = $actual - $anterior;

        if ($anterior === 0) {
            $pct       = null;
            $tendencia = $actual > 0 ? 'sin_base' : 'igual';
        } else {
            $pct       = round($diferencia / $anterior * 100, 1);
            $tendencia = $diferencia > 0 ? 'sube' : ($diferencia < 0 ? 'baja' : 'igual');
        }

        return [
            'actual'        => $actual,
            'anterior'      => $anterior,
            'variacion_pct' => $pct,
            'tendencia'     => $tendencia,
        ];
    }

    private function pedidosEntre(CarbonImmutable $ini, CarbonImmutable $fin)
    {
        $tzApp = config('app.timezone');

        return Comanda::query()
            ->where('estado', '!=', 'borrador')
            ->whereBetween('created_at', [$ini->setTimezone($tzApp), $fin->setTimezone($tzApp)]);
    }

    private function contarPedidos(CarbonImmutable $ini, CarbonImmutable $fin): int
    {
        return $this->pedidosEntre($ini, $fin)->count();
    }

    /** Usuarios que hicieron algo hoy, según el historial de actividad. */
    private function personalActivo(CarbonImmutable $hoy, CarbonImmutable $ahora): int
    {
        if (!Schema::hasTable('actividades')) {
            return 0;
        }

        $tzApp = config('app.timezone');

        return Actividad::query()
            ->whereNotNull('user_id')
            ->whereBetween('created_at', [$hoy->setTimezone($tzApp), $ahora->setTimezone($tzApp)])
            ->distinct()
            ->count('user_id');
    }

    private function mesas(): array
    {
        $porEstado = Mesa::query()
            ->where('activa', true)
            ->selectRaw('estado, count(*) as total')
            ->groupBy('estado')
            ->pluck('total', 'estado');

        $total    = (int) $porEstado->sum();
        $ocupadas = (int) ($porEstado['ocupada'] ?? 0);

        return [
            'total'             => $total,
            'ocupadas'          => $ocupadas,
            'disponibles'       => (int) ($porEstado['disponible'] ?? 0),
            'reservadas'        => (int) ($porEstado['reservada'] ?? 0),
            'fuera_de_servicio' => (int) ($porEstado['fuera_de_servicio'] ?? 0),
            'ocupacion_pct'     => $total > 0 ? (int) round($ocupadas / $total * 100) : 0,
        ];
    }

    private function pedidosPorEstado(CarbonImmutable $hoy, CarbonImmutable $ahora): array
    {
        $porEstado = $this->pedidosEntre($hoy, $ahora)
            ->selectRaw('estado, count(*) as total')
            ->groupBy('estado')
            ->pluck('total', 'estado')
            ->map(fn ($v) => (int) $v);

        return [
            'total'   => (int) $porEstado->sum(),
            'estados' => $porEstado->all(),
        ];
    }

    private function actividadReciente(): array
    {
        if (!Schema::hasTable('actividades')) {
            return [];
        }

        return Actividad::query()
            ->with('usuario:id,name')
            ->orderByDesc('id')
            ->limit(6)
            ->get()
            ->map(fn (Actividad $a) => [
                'id'          => $a->id,
                'modulo'      => $a->modulo,
                'accion'      => $a->accion,
                'descripcion' => $a->descripcion,
                'usuario'     => $a->usuario?->name,
                'fecha'       => $a->created_at,
            ])
            ->all();
    }
}