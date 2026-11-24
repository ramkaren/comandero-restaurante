<?php

namespace App\Http\Controllers\Cajero;

use App\Http\Controllers\Controller;
use App\Http\Requests\PagarCuentaRequest;
use App\Models\Cuenta;
use App\Models\Ticket;
use App\Services\Cajero\CajeroService;
use Illuminate\Http\JsonResponse;

class CajeroController extends Controller
{
    public function __construct(private CajeroService $service)
    {
    }

    public function index(): JsonResponse
    {
        return response()->json(['data' => $this->service->cuentasPendientes()]);
    }

    public function pay(PagarCuentaRequest $request, Cuenta $cuenta): JsonResponse
    {
        $ticket = $this->service->cobrar($cuenta->id, $request->validated(), $request->user()->id);

        return response()->json(['data' => $ticket], 201);
    }

    public function summaryToday(): JsonResponse
    {
        return response()->json(['data' => $this->service->resumenHoy()]);
    }

    public function ticket(Ticket $ticket): JsonResponse
    {
        return response()->json(['data' => $this->service->ticket($ticket->id)]);
    }
}