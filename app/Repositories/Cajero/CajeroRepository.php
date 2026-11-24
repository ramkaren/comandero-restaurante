<?php

namespace App\Repositories\Cajero;

use App\Models\Cuenta;
use App\Models\Pago;
use App\Models\Ticket;
use Illuminate\Support\Collection;

class CajeroRepository
{
    public function cuentasPendientes(): Collection
    {
        return Cuenta::with(['mesa:id,numero', 'comanda.detalles.producto:id,nombre'])
            ->where('estado', 'pendiente')
            ->where('total', '>', 0)
            ->orderBy('created_at')
            ->get();
    }

    public function bloquearCuenta(int $id): Cuenta
    {
        return Cuenta::with('comanda')->lockForUpdate()->findOrFail($id);
    }

    public function crearPago(Cuenta $cuenta, array $datos): Pago
    {
        return $cuenta->pago()->create($datos);
    }

    public function marcarPagada(Cuenta $cuenta): void
    {
        $cuenta->update(['estado' => 'pagada', 'pagada_at' => now()]);
        $cuenta->comanda->update(['estado' => 'cerrada']);
    }

    public function crearTicket(Cuenta $cuenta, Pago $pago): Ticket
    {
        return Ticket::create([
            'cuenta_id'  => $cuenta->id,
            'pago_id'    => $pago->id,
            'numero'     => 'T-' . str_pad($pago->id, 6, '0', STR_PAD_LEFT),
            'emitido_at' => now(),
        ]);
    }

    public function ticketConRelaciones(int $id): Ticket
    {
        return Ticket::with([
            'pago',
            'cuenta.mesa:id,numero',
            'cuenta.comanda.detalles.producto:id,nombre',
        ])->findOrFail($id);
    }

    public function pagosDeHoy(): Collection
    {
        return Pago::with(['cuenta.mesa:id,numero', 'ticket:id,pago_id,numero'])
            ->where('estado', 'confirmado')
            ->whereDate('pagado_at', today())
            ->orderByDesc('pagado_at')
            ->get();
    }
}