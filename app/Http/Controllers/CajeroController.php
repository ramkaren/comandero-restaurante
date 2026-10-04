<?php

namespace App\Http\Controllers;

use App\Models\Cuenta;
use App\Models\Pago;
use Illuminate\View\View;

class CajeroController extends Controller
{
    public function index(): View
    {
        return view('cajero.index', [
            'cuentasPendientes' => Cuenta::where('estado', 'pendiente')->count(),
            'pagosHoy' => Pago::where('estado', 'confirmado')->whereDate('pagado_at', today())->count(),
            'ventasHoy' => Pago::where('estado', 'confirmado')->whereDate('pagado_at', today())->sum('monto'),
        ]);
    }
}