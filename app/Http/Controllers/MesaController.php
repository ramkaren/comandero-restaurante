<?php

namespace App\Http\Controllers;

use App\Models\Comanda;
use App\Models\Mesa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class MesaController extends Controller
{
    public function index(): View
    {
        return view('mesero.mesas', ['mesas' => Mesa::with('mesero')->orderBy('numero')->get()]);
    }

    public function seleccionar(Mesa $mesa): RedirectResponse
    {
        try {
            $comanda = DB::transaction(function () use ($mesa) {
                $user = auth()->user();
                $lockedMesa = Mesa::query()->whereKey($mesa->id)->lockForUpdate()->firstOrFail();

                if (! $lockedMesa->activa) {
                    throw ValidationException::withMessages(['mesa' => 'Esta mesa esta inactiva.']);
                }

                if ($lockedMesa->estado !== 'disponible') {
                    if ($lockedMesa->mesero_id === $user->id) {
                        return Comanda::query()->where('mesa_id', $lockedMesa->id)
                            ->where('mesero_id', $user->id)->whereIn('estado', ['borrador', 'guardada'])
                            ->latest()->firstOrFail();
                    }

                    throw ValidationException::withMessages(['mesa' => 'La mesa ya esta siendo atendida por otro mesero.']);
                }

                $newComanda = Comanda::create([
                    'mesa_id' => $lockedMesa->id,
                    'mesero_id' => $user->id,
                    'estado' => 'borrador',
                    'total' => 0,
                ]);

                $lockedMesa->update(['estado' => 'ocupada', 'mesero_id' => $user->id]);

                return $newComanda;
            });
        } catch (ValidationException $exception) {
            return back()->withErrors($exception->errors());
        }

        return redirect()->route('mesero.comandas.edit', $comanda);
    }

    public function liberar(Mesa $mesa): RedirectResponse
    {
        DB::transaction(function () use ($mesa) {
            $user = auth()->user();
            $lockedMesa = Mesa::query()->whereKey($mesa->id)->lockForUpdate()->firstOrFail();

            abort_unless($lockedMesa->mesero_id === $user->id || $user->hasRole('super_usuario'), 403);

            $comanda = Comanda::query()->where('mesa_id', $lockedMesa->id)
                ->whereIn('estado', ['borrador', 'guardada'])->latest()->first();

            if ($comanda) {
                $comanda->update(['estado' => $comanda->detalles()->exists() ? 'guardada' : 'cancelada']);
            }

            $lockedMesa->update(['estado' => 'disponible', 'mesero_id' => null]);
        });

        return redirect()->route('mesero.mesas')->with('status', 'Mesa liberada correctamente.');
    }
}