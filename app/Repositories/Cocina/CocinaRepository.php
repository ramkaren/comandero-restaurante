<?php

namespace App\Repositories\Cocina;

use App\Models\Comanda;
use Illuminate\Support\Collection;

class CocinaRepository
{
    public function ordenes(): Collection
    {
        return Comanda::with(['mesa:id,numero', 'detalles.producto:id,nombre'])
            ->where('estado', '!=', 'borrador')
            ->where(function ($q) {
                $q->where('estado_cocina', '!=', 'lista')
                  ->orWhereDate('finalizada_en', today());
            })
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();
    }

    public function bloquear(int $id): Comanda
    {
        return Comanda::lockForUpdate()->findOrFail($id);
    }

    public function conRelaciones(Comanda $comanda): Comanda
    {
        return $comanda->load(['mesa:id,numero', 'detalles.producto:id,nombre']);
    }
}