<?php

namespace App\Http\Controllers;

use App\Models\Comanda;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CocinaController extends Controller
{
    public function index(): View
    {
        $comandas = Comanda::query()
            ->with(['mesa', 'mesero', 'detalles.producto'])
            ->whereIn('estado', ['guardada', 'pendiente', 'en_preparacion'])
            ->latest()
            ->get();

        return view('cocina.index', compact('comandas'));
    }

    public function show(Comanda $comanda): View
    {
        abort_unless($this->isVisibleToKitchen($comanda), 404);

        return view('cocina.show', [
            'comanda' => $comanda->load(['mesa', 'mesero', 'detalles.producto']),
        ]);
    }

    public function start(Comanda $comanda): RedirectResponse
    {
        abort_unless($comanda->estado === 'guardada' || $comanda->estado === 'pendiente', 409, 'La comanda no puede iniciar preparacion.');

        $comanda->update(['estado' => 'en_preparacion']);

        return back()->with('status', 'La comanda esta en preparacion.');
    }

    public function finish(Comanda $comanda): RedirectResponse
    {
        abort_unless($comanda->estado === 'en_preparacion', 409, 'La comanda debe estar en preparacion.');

        $comanda->update(['estado' => 'lista']);

        return back()->with('status', 'La comanda esta lista.');
    }

    private function isVisibleToKitchen(Comanda $comanda): bool
    {
        return in_array($comanda->estado, ['guardada', 'pendiente', 'en_preparacion', 'lista'], true);
    }
}