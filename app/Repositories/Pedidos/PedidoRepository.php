<?php

namespace App\Repositories\Pedidos;

use App\Models\Comanda;
use App\Models\Mesa;
use App\Models\Producto;
use Illuminate\Support\Collection;

class PedidoRepository
{
    public function bloquearMesa(int $mesaId): Mesa
    {
        return Mesa::lockForUpdate()->findOrFail($mesaId);
    }

    public function bloquearComanda(int $comandaId): Comanda
    {
        return Comanda::with('cuenta')->lockForUpdate()->findOrFail($comandaId);
    }

    public function mesaTieneComandaAbierta(int $mesaId): bool
    {
        return Comanda::where('mesa_id', $mesaId)
            ->where('estado', 'abierta')
            ->exists();
    }

    public function productosPorIds(array $ids): Collection
    {
        return Producto::whereIn('id', $ids)->get()->keyBy('id');
    }

    public function crearComanda(array $datos): Comanda
    {
        return Comanda::create($datos);
    }

    public function crearCuenta(Comanda $comanda, array $datos)
    {
        return $comanda->cuenta()->create($datos);
    }

    public function crearDetalles(Comanda $comanda, array $filas): void
    {
        $comanda->detalles()->createMany($filas);
    }

    public function actualizarTotales(Comanda $comanda): void
    {
        $comanda->recalcularTotal();
        $comanda->cuenta()->update([
            'subtotal' => $comanda->total,
            'total'    => $comanda->total,
        ]);
    }

    public function conRelaciones(Comanda $comanda): Comanda
    {
        return $comanda->load([
            'mesa:id,numero',
            'mesero:id,name',
            'detalles.producto:id,nombre',
            'cuenta.pago.cajero:id,name',
        ]);
    }
}