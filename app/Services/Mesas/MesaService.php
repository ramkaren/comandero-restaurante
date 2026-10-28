<?php

namespace App\Services\Mesas;

use App\Models\Cuenta;
use App\Models\Mesa;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class MesaService
{
    /** Estados de comanda que cuentan como "cuenta en curso" de una mesa ocupada. */
    private const COMANDA_ACTIVA = ['abierta', 'borrador', 'guardada'];

    public function listar(bool $incluirInactivas = false): Collection
    {
        return Mesa::query()
            ->when(!$incluirInactivas, fn ($q) => $q->where('activa', true))
            ->with([
                'mesero:id,name',
                'comandas' => fn ($q) => $q->whereIn('estado', self::COMANDA_ACTIVA)
                    ->latest('id')
                    ->withCount('detalles'),
            ])
            ->orderBy('numero')
            ->get()
            ->map(fn (Mesa $m) => $this->resumen($m));
    }

    public function detalle(Mesa $mesa): array
    {
        $mesa->load('mesero:id,name');

        $comanda = $mesa->estado === 'ocupada'
            ? $mesa->comandas()
                ->whereIn('estado', self::COMANDA_ACTIVA)
                ->latest('id')
                ->with(['mesero:id,name', 'detalles.producto:id,nombre', 'cuenta.pago'])
                ->first()
            : null;

        $ultimoPago = Cuenta::where('mesa_id', $mesa->id)
            ->whereHas('pago')
            ->with('pago')
            ->latest('id')
            ->first()?->pago;

        return $this->base($mesa) + [
            'pedido' => $comanda ? [
                'id'              => $comanda->id,
                'estado'          => $comanda->estado,
                'mesero'          => $comanda->mesero?->name,
                'creado_en'       => $comanda->created_at,
                'minutos'         => $this->minutos($comanda->created_at),
                'items'           => $comanda->detalles->map(fn ($d) => [
                    'producto'        => $d->producto?->nombre,
                    'cantidad'        => $d->cantidad,
                    'precio_unitario' => $d->precio_unitario,
                    'subtotal'        => $d->subtotal,
                ])->values(),
                'cuenta'          => $comanda->cuenta ? [
                    'estado'   => $comanda->cuenta->estado,
                    'subtotal' => $comanda->cuenta->subtotal,
                    'total'    => $comanda->cuenta->total,
                ] : null,
                'pago'            => [
                    'estado' => $comanda->cuenta?->pago?->estado === 'confirmado' ? 'pagado' : 'pendiente',
                    'metodo' => $comanda->cuenta?->pago?->metodo,
                ],
            ] : null,
            'ultimo_pago' => $ultimoPago ? [
                'metodo'    => $ultimoPago->metodo,
                'monto'     => $ultimoPago->monto,
                'pagado_at' => $ultimoPago->pagado_at,
            ] : null,
        ];
    }

    public function crear(array $data): array
    {
        $mesa = Mesa::create([
            'numero'    => $data['numero'],
            'capacidad' => $data['capacidad'],
            'estado'    => $data['estado'] ?? 'disponible',
            'activa'    => $data['activa'] ?? true,
        ]);

        return $this->base($mesa->load('mesero:id,name')) + ['pedido' => null];
    }

    public function actualizar(Mesa $mesa, array $data): array
    {
        if ($mesa->estado === 'ocupada') {
            if (array_key_exists('estado', $data)) {
                throw ValidationException::withMessages([
                    'estado' => ['No se puede cambiar el estado de una mesa ocupada.'],
                ]);
            }

            if (array_key_exists('activa', $data) && !$data['activa']) {
                throw ValidationException::withMessages([
                    'activa' => ['No se puede desactivar una mesa ocupada.'],
                ]);
            }
        }

        $mesa->update($data);

        return $this->base($mesa->fresh('mesero:id,name')) + ['pedido' => null];
    }

    /** @return string "eliminada" o "desactivada" */
    public function eliminar(Mesa $mesa): string
    {
        if ($mesa->estado === 'ocupada') {
            throw ValidationException::withMessages([
                'mesa' => ['No se puede eliminar una mesa ocupada.'],
            ]);
        }

        // Con historial de comandas se desactiva, para no dejarlas huérfanas.
        if ($mesa->comandas()->exists()) {
            $mesa->update(['activa' => false]);

            return 'desactivada';
        }

        $mesa->delete();

        return 'eliminada';
    }

    private function resumen(Mesa $mesa): array
    {
        $comanda = $mesa->estado === 'ocupada' ? $mesa->comandas->first() : null;

        return $this->base($mesa) + [
            'pedido' => $comanda ? [
                'id'          => $comanda->id,
                'total'       => $comanda->total,
                'items_count' => $comanda->detalles_count,
                'minutos'     => $this->minutos($comanda->created_at),
            ] : null,
        ];
    }

    private function base(Mesa $mesa): array
    {
        return [
            'id'        => $mesa->id,
            'numero'    => $mesa->numero,
            'capacidad' => $mesa->capacidad,
            'estado'    => $mesa->estado,
            'activa'    => $mesa->activa,
            'mesero'    => $mesa->mesero?->name,
        ];
    }

    private function minutos($desde): int
    {
        return (int) $desde->diffInMinutes(now(), true);
    }
}