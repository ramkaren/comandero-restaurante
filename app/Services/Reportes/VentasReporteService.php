<?php

namespace App\Services\Reportes;

use App\Models\Pago;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class VentasReporteService
{
    
    private const TZ = 'America/Mexico_City';

    private const MAX_DIAS = 366;

   
    public function consultar(?string $desde, ?string $hasta): array
    {
        $hoy = CarbonImmutable::now(self::TZ)->startOfDay();
        $fin = $hasta ? CarbonImmutable::parse($hasta, self::TZ)->startOfDay() : $hoy;
        $ini = $desde ? CarbonImmutable::parse($desde, self::TZ)->startOfDay() : $fin->subDays(6);

        if ($ini->gt($fin)) {
            throw ValidationException::withMessages([
                'desde' => ['La fecha de inicio no puede ser posterior a la fecha fin.'],
            ]);
        }

        $dias = (int) round($ini->diffInDays($fin)) + 1;

        if ($dias > self::MAX_DIAS) {
            throw ValidationException::withMessages([
                'hasta' => ['El periodo no puede superar ' . self::MAX_DIAS . ' días.'],
            ]);
        }

        $granularidad = $dias <= 31 ? 'dia' : ($dias <= 120 ? 'semana' : 'mes');

        $pagos = $this->pagosEntre($ini, $fin->endOfDay());
        $serie = $this->construirSerie($ini, $fin, $granularidad, $pagos);

        $total  = round(array_sum(array_column($serie, 'ventas')), 2);
        $cobros = (int) array_sum(array_column($serie, 'cobros'));

        $mejor = null;
        if ($total > 0) {
            $max   = collect($serie)->sortByDesc('ventas')->first();
            $mejor = ['etiqueta' => $max['etiqueta'], 'ventas' => $max['ventas']];
        }

        $antFin   = $ini->subDay();
        $antIni   = $antFin->subDays($dias - 1);
        $anterior = $this->total($antIni, $antFin->endOfDay());

        return [
            'rango' => [
                'desde'        => $ini->toDateString(),
                'hasta'        => $fin->toDateString(),
                'dias'         => $dias,
                'granularidad' => $granularidad,
            ],
            'resumen' => [
                'total'           => $total,
                'cobros'          => $cobros,
                'ticket_promedio' => $cobros > 0 ? round($total / $cobros, 2) : 0.0,
                'promedio_diario' => round($total / $dias, 2),
                'mejor'           => $mejor,
            ],
            'comparacion' => [
                'anterior' => [
                    'desde' => $antIni->toDateString(),
                    'hasta' => $antFin->toDateString(),
                    'total' => $anterior,
                ],
            ] + $this->variacion($total, $anterior),
            'serie' => $serie,
        ];
    }

    public function comparaciones(): array
    {
        $ahora = CarbonImmutable::now(self::TZ);
        $hoy   = $ahora->startOfDay();

        $semana = $hoy->startOfWeek();
        $mes    = $hoy->startOfMonth();

        return [
            'hoy' => $this->tarjeta(
                'Hoy vs ayer',
                'Hoy hasta ahora',
                'Ayer a la misma hora',
                $this->total($hoy, $ahora),
                $this->total($hoy->subDay(), $ahora->subDay())
            ),
            'semana' => $this->tarjeta(
                'Esta semana vs la anterior',
                'Esta semana hasta hoy',
                'Semana anterior, mismo tramo',
                $this->total($semana, $ahora),
                $this->total($semana->subWeek(), $ahora->subWeek())
            ),
            'mes' => $this->tarjeta(
                'Este mes vs el anterior',
                'Este mes hasta hoy',
                'Mes anterior, mismo tramo',
                $this->total($mes, $ahora),
                $this->total($mes->subMonthNoOverflow(), $ahora->subMonthNoOverflow())
            ),
        ];
    }

    private function tarjeta(string $titulo, string $etActual, string $etAnterior, float $actual, float $anterior): array
    {
        return [
            'titulo'            => $titulo,
            'etiqueta_actual'   => $etActual,
            'etiqueta_anterior' => $etAnterior,
            'actual'            => $actual,
            'anterior'          => $anterior,
        ] + $this->variacion($actual, $anterior);
    }

    private function variacion(float $actual, float $anterior): array
    {
        $diferencia = round($actual - $anterior, 2);

        if ($anterior == 0.0) {
            return [
                'diferencia'    => $diferencia,
                'variacion_pct' => null,
                'tendencia'     => $actual > 0 ? 'sin_base' : 'igual',
            ];
        }

        return [
            'diferencia'    => $diferencia,
            'variacion_pct' => round($diferencia / $anterior * 100, 1),
            'tendencia'     => $diferencia > 0 ? 'sube' : ($diferencia < 0 ? 'baja' : 'igual'),
        ];
    }

    private function pagosEntre(CarbonImmutable $ini, CarbonImmutable $fin): Collection
    {
        $tzApp = config('app.timezone');

        return Pago::query()
            ->where('estado', 'confirmado')
            ->whereBetween('pagado_at', [$ini->setTimezone($tzApp), $fin->setTimezone($tzApp)])
            ->get(['id', 'monto', 'pagado_at']);
    }

    private function total(CarbonImmutable $ini, CarbonImmutable $fin): float
    {
        return round((float) $this->pagosEntre($ini, $fin)->sum('monto'), 2);
    }

    private function construirSerie(CarbonImmutable $desde, CarbonImmutable $hasta, string $gran, Collection $pagos): array
    {
        $buckets = [];
        $cursor  = $desde;

        while ($cursor->lte($hasta)) {
            [$ini, $fin] = $this->limites($cursor, $gran);
            $ini = $ini->lt($desde) ? $desde : $ini;
            $fin = $fin->gt($hasta) ? $hasta : $fin;

            $buckets[$this->clave($cursor, $gran)] = [
                'clave'    => $this->clave($cursor, $gran),
                'etiqueta' => $this->etiqueta($ini, $fin, $gran),
                'inicio'   => $ini->toDateString(),
                'fin'      => $fin->toDateString(),
                'ventas'   => 0.0,
                'cobros'   => 0,
            ];

            $cursor = $fin->addDay();
        }

        $tzApp = config('app.timezone');
        foreach ($pagos as $pago) {
            $fecha = CarbonImmutable::parse($pago->pagado_at, $tzApp)->setTimezone(self::TZ)->startOfDay();
            $key   = $this->clave($fecha, $gran);

            if (!isset($buckets[$key])) {
                continue;
            }

            $buckets[$key]['ventas'] += (float) $pago->monto;
            $buckets[$key]['cobros']++;
        }

        $serie = [];
        $previo = null;

        foreach ($buckets as $b) {
            $b['ventas']          = round($b['ventas'], 2);
            $b['ticket_promedio'] = $b['cobros'] > 0 ? round($b['ventas'] / $b['cobros'], 2) : 0.0;

            $v = $previo === null ? null : $this->variacion($b['ventas'], $previo);
            $b['variacion_pct'] = $v['variacion_pct'] ?? null;
            $b['tendencia']     = $v['tendencia'] ?? null;

            $previo  = $b['ventas'];
            $serie[] = $b;
        }

        return $serie;
    }

    private function limites(CarbonImmutable $c, string $gran): array
    {
        return match ($gran) {
            'semana' => [$c->startOfWeek(), $c->endOfWeek()->startOfDay()],
            'mes'    => [$c->startOfMonth(), $c->endOfMonth()->startOfDay()],
            default  => [$c, $c],
        };
    }

    private function clave(CarbonImmutable $c, string $gran): string
    {
        return match ($gran) {
            'semana' => $c->startOfWeek()->toDateString(),
            'mes'    => $c->format('Y-m'),
            default  => $c->toDateString(),
        };
    }

    private function etiqueta(CarbonImmutable $ini, CarbonImmutable $fin, string $gran): string
    {
        return match ($gran) {
            'semana' => $ini->locale('es')->isoFormat('D MMM') . ' – ' . $fin->locale('es')->isoFormat('D MMM'),
            'mes'    => $ini->locale('es')->isoFormat('MMM YYYY'),
            default  => $ini->locale('es')->isoFormat('ddd D MMM'),
        };
    }
}