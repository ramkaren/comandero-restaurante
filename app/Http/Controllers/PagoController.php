<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePagoRequest;
use App\Models\Cuenta;
use App\Models\Pago;
use App\Models\Ticket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PagoController extends Controller
{
    public function store(StorePagoRequest $request, Cuenta $cuenta): RedirectResponse
    {
        $data = $request->validated();

        try {
            $ticket = DB::transaction(function () use ($data, $cuenta): Ticket {
                $lockedAccount = Cuenta::query()->whereKey($cuenta->id)->lockForUpdate()->firstOrFail();

                if ($lockedAccount->estado !== 'pendiente' || $lockedAccount->pago()->exists()) {
                    throw ValidationException::withMessages(['cuenta' => 'La cuenta ya no está pendiente de pago.']);
                }

                $comanda = $lockedAccount->comanda()->with('detalles')->firstOrFail();
                if ($comanda->estado !== 'lista') {
                    throw ValidationException::withMessages(['cuenta' => 'Solo se pueden cobrar comandas listas.']);
                }

                $expectedTotal = round((float) $comanda->detalles->sum('subtotal'), 2);
                if ($expectedTotal <= 0 || round((float) $lockedAccount->total, 2) !== $expectedTotal) {
                    throw ValidationException::withMessages(['cuenta' => 'El total de la cuenta no es válido.']);
                }

                $amount = round((float) $data['monto'], 2);
                if ($amount !== round((float) $lockedAccount->total, 2)) {
                    throw ValidationException::withMessages(['monto' => 'El monto debe coincidir con el total de la cuenta.']);
                }

                $received = null;
                $change = null;
                if ($data['metodo'] === 'efectivo') {
                    $received = round((float) $data['monto_recibido'], 2);
                    if ($received < $amount) {
                        throw ValidationException::withMessages(['monto_recibido' => 'El monto recibido es insuficiente.']);
                    }
                    $change = round($received - $amount, 2);
                }

                $payment = Pago::create([
                    'cuenta_id' => $lockedAccount->id,
                    'cajero_id' => auth()->id(),
                    'metodo' => $data['metodo'],
                    'monto' => $amount,
                    'monto_recibido' => $received,
                    'cambio' => $change,
                    'referencia' => $data['referencia'] ?? null,
                    'estado' => 'confirmado',
                    'pagado_at' => now(),
                ]);

                $lockedAccount->update(['estado' => 'pagada', 'pagada_at' => now()]);

                $mesa = $lockedAccount->mesa()->lockForUpdate()->firstOrFail();
                $mesa->update(['estado' => 'disponible', 'mesero_id' => null]);

                return Ticket::create([
                    'cuenta_id' => $lockedAccount->id,
                    'pago_id' => $payment->id,
                    'numero' => 'T-'.now()->format('YmdHis').'-'.random_int(1000, 9999),
                    'emitido_at' => now(),
                ]);
            });
        } catch (ValidationException $exception) {
            return back()->withErrors($exception->errors())->withInput();
        }

        return redirect()->route('cajero.tickets.show', $ticket)->with('status', 'Pago confirmado correctamente.');
    }

    public function index(): \Illuminate\View\View
    {
        return view('cajero.pagos.index', ['pagos' => Pago::with(['cuenta.mesa', 'cajero'])->latest('pagado_at')->get()]);
    }
}