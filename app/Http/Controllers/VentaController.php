<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use Illuminate\View\View;

class VentaController extends Controller
{
    public function index(): View
    {
        $pagos = Pago::with(['cuenta.mesa', 'cajero'])
            ->where('estado', 'confirmado')
            ->whereDate('pagado_at', today())
            ->latest('pagado_at')
            ->get();

        return view('cajero.ventas.index', ['pagos' => $pagos, 'total' => $pagos->sum('monto')]);
    }
}