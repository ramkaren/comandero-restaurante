<?php

namespace App\Services\Cajero;

use App\Models\Pago;
use App\Models\Ticket;
use App\Repositories\Cajero\CajeroRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CajeroService
{
    public function __construct(private CajeroRepository $repo)
    {
    }

    public function cuentasPendientes(): array
    {
        return $this->repo->cuentasPendientes()->map(fn ($c) => [
            'id'         => $c->id,
            'estado'     => $c->estado,
            'mesa'       => $c->mesa?->numero,
            'comanda_id' => $c->comanda_id,
            'subtotal'   => (float) $c->subtotal,
            'total'      => (float) $c->total,
            'items'      => $c->comanda->detalles->map(fn ($d) => [
                'producto_id'     => $d->producto_id,
                'producto'        => $d->producto?->nombre,
                'cantidad'        => $d->cantidad,
                'precio_unitario' => (float) $d->precio_unitario,
                'subtotal'        => (float) $d->subtotal,
            ])->values(),
        ])->values()->all();
    }

    public function cobrar(int $cuentaId, array $data, int $cajeroId): array
    {
        return DB::transaction(function () use ($cuentaId, $data, $cajeroId) {
            $cuenta = $this->repo->bloquearCuenta($cuentaId);

            if ($cuenta->estado !== 'pendiente') {
                throw ValidationException::withMessages([
                    'cuenta' => ['La cuenta ya fue pagada o no está disponible.'],
                ]);
            }

            $total = (float) $cuenta->total;

            if ($total <= 0) {
                throw ValidationException::withMessages([
                    'cuenta' => ['La cuenta no tiene productos.'],
                ]);
            }

            if (abs((float) $data['monto'] - $total) > 0.009) {
                throw ValidationException::withMessages([
                    'monto' => ['El monto no coincide con el total de la cuenta.'],
                ]);
            }

            $recibido = null;
            $cambio = 0;

            if ($data['metodo'] === 'efectivo') {
                $recibido = (float) $data['monto_recibido'];

                if ($recibido + 0.009 < $total) {
                    throw ValidationException::withMessages([
                        'monto_recibido' => ['El efectivo recibido es menor al total.'],
                    ]);
                }

                $cambio = round($recibido - $total, 2);
            }

            $pago = $this->repo->crearPago($cuenta, [
                'cajero_id'      => $cajeroId,
                'metodo'         => $data['metodo'],
                'monto'          => $total,
                'monto_recibido' => $recibido,
                'cambio'         => $cambio,
                'referencia'     => $data['referencia'] ?? null,
                'estado'         => 'confirmado',
                'pagado_at'      => now(),
            ]);

            $this->repo->marcarPagada($cuenta);
            $ticket = $this->repo->crearTicket($cuenta, $pago);

            return $this->ticketPayload($this->repo->ticketConRelaciones($ticket->id));
        });
    }

    public function ticket(int $id): array
    {
        return $this->ticketPayload($this->repo->ticketConRelaciones($id));
    }

    public function resumenHoy(): array
    {
        $pagos = $this->repo->pagosDeHoy();
        $porMetodo = fn (string $m) => (float) $pagos->where('metodo', $m)->sum('monto');

        return [
            'fecha'               => today()->toDateString(),
            'ventas_total'        => (float) $pagos->sum('monto'),
            'pagos_efectivo'      => $porMetodo('efectivo'),
            'pagos_tarjeta'       => $porMetodo('tarjeta'),
            'pagos_transferencia' => $porMetodo('transferencia'),
            'transacciones'       => $pagos->count(),
            'pagos'               => $pagos->map(fn (Pago $p) => [
                'id'        => $p->id,
                'mesa'      => $p->cuenta?->mesa?->numero,
                'metodo'    => $p->metodo,
                'monto'     => (float) $p->monto,
                'referencia'=> $p->referencia,
                'pagado_at' => $p->pagado_at?->toIso8601String(),
                'ticket_id' => $p->ticket?->id,
                'folio'     => $p->ticket?->numero,
            ])->values(),
        ];
    }

    private function ticketPayload(Ticket $t): array
    {
        return [
            'id'           => $t->id,
            'restaurante'  => 'Cafecito Caliente',
            'folio'        => $t->numero,
            'emitido_at'   => $t->emitido_at?->toIso8601String(),
            'mesa'         => $t->cuenta->mesa?->numero,
            'comanda'      => $t->cuenta->comanda_id,
            'productos'    => $t->cuenta->comanda->detalles->map(fn ($d) => [
                'producto'        => $d->producto?->nombre,
                'cantidad'        => $d->cantidad,
                'precio_unitario' => (float) $d->precio_unitario,
                'subtotal'        => (float) $d->subtotal,
            ])->values(),
            'subtotal'     => (float) $t->cuenta->subtotal,
            'total_pagado' => (float) $t->pago->monto,
            'metodo_pago'  => $t->pago->metodo,
            'referencia'   => $t->pago->referencia,
        ];
    }
}