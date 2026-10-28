<?php

namespace App\Http\Controllers\Actividad;

use App\Http\Controllers\Controller;
use App\Http\Requests\ActividadIndexRequest;
use App\Models\Actividad;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;

class ActividadController extends Controller
{
    
    private const TZ = 'America/Mexico_City';

    public function index(ActividadIndexRequest $request): JsonResponse
    {
        $f = $request->validated();

        $actividades = Actividad::query()
            ->with('usuario:id,name')
            ->when($f['modulo'] ?? null, fn ($q, $v) => $q->where('modulo', $v))
            ->when($f['user_id'] ?? null, fn ($q, $v) => $q->where('user_id', $v))
            ->when($f['fecha'] ?? null, function ($q, $fecha) {
                $ini = CarbonImmutable::parse($fecha, self::TZ)->startOfDay()->setTimezone(config('app.timezone'));
                $fin = CarbonImmutable::parse($fecha, self::TZ)->endOfDay()->setTimezone(config('app.timezone'));

                $q->whereBetween('created_at', [$ini, $fin]);
            })
            ->when($f['search'] ?? null, function ($q, $term) {
                $q->where(function ($w) use ($term) {
                    $w->where('descripcion', 'like', "%{$term}%")
                      ->orWhereHas('usuario', fn ($u) => $u->where('name', 'like', "%{$term}%"));
                });
            })
            ->orderByDesc('id')
            ->paginate((int) ($f['per_page'] ?? 20))
            ->through(fn (Actividad $a) => [
                'id'          => $a->id,
                'modulo'      => $a->modulo,
                'accion'      => $a->accion,
                'descripcion' => $a->descripcion,
                'usuario'     => $a->usuario?->name,
                'user_id'     => $a->user_id,
                'ip'          => $a->ip,
                'propiedades' => $a->propiedades,
                'fecha'       => $a->created_at,
            ]);

        return response()->json([
            'message' => 'success',
            'status'  => 200,
            'data'    => $actividades,
        ]);
    }
}