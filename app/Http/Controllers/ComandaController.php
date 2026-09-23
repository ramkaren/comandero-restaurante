<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreComandaDetailRequest;
use App\Models\Categoria;
use App\Models\Comanda;
use App\Models\DetalleComanda;
use App\Models\Producto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ComandaController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $query = Comanda::with('mesa')->latest();

        if (! $user->hasRole('super_usuario')) {
            $query->where('mesero_id', $user->id);
        }

        return view('mesero.comandas.index', ['comandas' => $query->get()]);
    }

    public function edit(Comanda $comanda): View
    {
        $this->authorizeComanda($comanda);

        return view('mesero.comandas.edit', [
            'comanda' => $comanda->load('mesa', 'detalles.producto'),
            'categorias' => Categoria::where('activa', true)->with(['productos' => fn ($query) => $query->where('activo', true)->where('disponible', true)->orderBy('nombre')])->orderBy('nombre')->get(),
        ]);
    }

    public function agregar(StoreComandaDetailRequest $request, Comanda $comanda): RedirectResponse
    {
        $this->authorizeComanda($comanda);
        abort_if($comanda->estado === 'cancelada', 409, 'La comanda ya fue cancelada.');

        $product = Producto::query()->whereKey($request->integer('producto_id'))->where('activo', true)->where('disponible', true)->firstOrFail();
        $quantity = $request->integer('cantidad');

        DB::transaction(function () use ($comanda, $product, $quantity) {
            $detail = DetalleComanda::firstOrNew(['comanda_id' => $comanda->id, 'producto_id' => $product->id]);
            $detail->cantidad = ($detail->exists ? $detail->cantidad : 0) + $quantity;
            $detail->precio_unitario = $product->precio;
            $detail->subtotal = $detail->cantidad * $product->precio;
            $detail->save();
            $comanda->recalcularTotal();
        });

        return back()->with('status', 'Producto agregado a la comanda.');
    }

    public function ajustar(Comanda $comanda, DetalleComanda $detalle): RedirectResponse
    {
        $this->authorizeComanda($comanda);
        abort_unless($detalle->comanda_id === $comanda->id, 404);
        $quantity = max(0, $detalle->cantidad + request()->integer('cambio'));

        DB::transaction(function () use ($comanda, $detalle, $quantity) {
            if ($quantity === 0) {
                $detalle->delete();
            } else {
                $detalle->update(['cantidad' => $quantity, 'subtotal' => $quantity * $detalle->precio_unitario]);
            }
            $comanda->recalcularTotal();
        });

        return back();
    }

    public function eliminar(Comanda $comanda, DetalleComanda $detalle): RedirectResponse
    {
        $this->authorizeComanda($comanda);
        abort_unless($detalle->comanda_id === $comanda->id, 404);
        $detalle->delete();
        $comanda->recalcularTotal();

        return back();
    }

    public function guardar(Comanda $comanda): RedirectResponse
    {
        $this->authorizeComanda($comanda);
        $comanda->update(['estado' => 'guardada']);

        return redirect()->route('mesero.comandas')->with('status', 'Comanda guardada correctamente.');
    }

    private function authorizeComanda(Comanda $comanda): void
    {
        $user = auth()->user();
        abort_unless($user->hasRole('super_usuario') || $comanda->mesero_id === $user->id, 403);
    }
}