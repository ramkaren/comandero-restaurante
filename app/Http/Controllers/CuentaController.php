<?php

namespace App\Http\Controllers;

use App\Models\Comanda;
use App\Models\Cuenta;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CuentaController extends Controller
{
    public function index(): View
    {
        Comanda::query()
            ->with('detalles')
            ->where('estado', 'lista')
            ->whereDoesntHave('cuenta')
            ->get()
            ->each(fn (Comanda $comanda) => $this->createFromComanda($comanda));

        return view('cajero.cuentas.index', [
            'cuentas' => Cuenta::with(['mesa', 'comanda.mesero'])->where('estado', 'pendiente')->latest()->get(),
        ]);
    }

    public function show(Cuenta $cuenta): View
    {
        return view('cajero.cuentas.show', [
            'cuenta' => $cuenta->load(['mesa', 'comanda.mesero', 'comanda.detalles.producto', 'pago', 'ticket']),
        ]);
    }

    private function createFromComanda(Comanda $comanda): Cuenta
    {
        return DB::transaction(function () use ($comanda): Cuenta {
            $lockedComanda = Comanda::query()->with('detalles')->whereKey($comanda->id)->lockForUpdate()->firstOrFail();
            abort_unless($lockedComanda->estado === 'lista', 409, 'La comanda aún no está lista.');

            $total = (float) $lockedComanda->detalles->sum('subtotal');
            abort_if($total <= 0, 422, 'La comanda no tiene productos cobrables.');

            return Cuenta::firstOrCreate(
                ['comanda_id' => $lockedComanda->id],
                ['mesa_id' => $lockedComanda->mesa_id, 'estado' => 'pendiente', 'subtotal' => $total, 'total' => $total],
            );
        });
    }
}