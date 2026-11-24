<?php

namespace App\Services\Cocina;

use App\Models\Comanda;
use App\Repositories\Cocina\CocinaRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CocinaService
{
    private const SIGUIENTE = [
        'guardada'       => 'en_preparacion',
        'pendiente'      => 'en_preparacion',
        'en_preparacion' => 'lista',
    ];

    public function __construct(private CocinaRepository $repo)
    {
    }

    public function ordenes(): array
    {
        return $this->repo->ordenes()->map(fn (Comanda $c) => $this->formatear($c))->values()->all();
    }

    public function cambiarEstado(int $id, string $nuevo): array
    {
        return DB::transaction(function () use ($id, $nuevo) {
            $comanda = $this->repo->bloquear($id);

            if ((self::SIGUIENTE[$comanda->estado_cocina] ?? null) !== $nuevo) {
                throw ValidationException::withMessages([
                    'estado' => ['La comanda no puede pasar de ese estado al solicitado.'],
                ]);
            }

            $datos = ['estado_cocina' => $nuevo];

            if ($nuevo === 'lista') {
                $datos['finalizada_en'] = now();
            }

            $comanda->update($datos);

            return $this->formatear($this->repo->conRelaciones($comanda));
        });
    }

    private function formatear(Comanda $c): array
    {
        return [
            'id'            => $c->id,
            'mesa'          => $c->mesa?->numero,
            'estado'        => $c->estado_cocina,
            'creado_en'     => $c->created_at?->toIso8601String(),
            'finalizada_en' => $c->finalizada_en?->toIso8601String(),
            'items'         => $c->detalles->map(fn ($d) => [
                'producto_id' => $d->producto_id,
                'producto'    => $d->producto?->nombre,
                'cantidad'    => $d->cantidad,
                'subtotal'    => $d->subtotal,
            ])->values(),
        ];
    }
}