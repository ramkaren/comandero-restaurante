<?php

namespace App\Http\Controllers\Pedidos;

use App\Http\Controllers\Controller;
use App\Models\Comanda;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PedidoController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'estado'   => 'nullable|string|max:50',
            'fecha'    => 'nullable|date_format:Y-m-d',
            'search'   => 'nullable|string|max:100',
            'per_page' => 'nullable|integer|min:1|max:50',
        ]);

        $query = Comanda::query()
            ->with([
                'mesa:id,numero',
                'mesero:id,name',
                'detalles.producto:id,nombre',
                'cuenta.pago.cajero:id,name',
            ])
            ->when(
                $request->filled('estado'),
                fn ($q) => $q->where('estado', $request->estado),
                fn ($q) => $q->where('estado', '!=', 'borrador')
            )
            ->when($request->filled('fecha'), fn ($q) => $q->whereDate('created_at', $request->fecha))
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = trim($request->search);

                $q->where(function ($w) use ($term) {
                    if (ctype_digit($term)) {
                        $w->where('id', (int) $term)
                          ->orWhereHas('mesa', fn ($m) => $m->where('numero', (int) $term));
                    } else {
                        $w->whereHas('mesero', fn ($u) => $u->where('name', 'like', "%{$term}%"));
                    }
                });
            })
            ->orderByDesc('created_at')
            ->orderByDesc('id');

        $pedidos = $query
            ->paginate((int) $request->input('per_page', 10))
            ->through(fn (Comanda $c) => $this->formatear($c));

        return response()->json([
            'message' => 'success',
            'status'  => 200,
            'data'    => $pedidos,
        ]);
    }

    private function formatear(Comanda $c): array
    {
        $cuenta = $c->cuenta;
        $pago   = $cuenta?->pago;

        if ($pago && $pago->estado === 'confirmado') {
            $estadoPago = 'pagado';
        } elseif ($cuenta) {
            $estadoPago = 'pendiente';
        } else {
            $estadoPago = 'sin_cuenta';
        }

        return [
            'id'     => $c->id,
            'mesa'   => $c->mesa?->numero,
            'estado' => $c->estado,
            'mesero' => $c->mesero?->name,
            'items'  => $c->detalles->map(fn ($d) => [
                'producto' => $d->producto?->nombre,
                'cantidad' => $d->cantidad,
                'subtotal' => $d->subtotal,
            ])->values(),
            'total'  => $c->total,
            'pago'   => [
                'estado'    => $estadoPago,
                'metodo'    => $pago?->metodo,
                'cajero'    => $pago?->cajero?->name,
                'pagado_at' => $pago?->pagado_at,
            ],
            'creado_en' => $c->created_at,
        ];
    }
}

