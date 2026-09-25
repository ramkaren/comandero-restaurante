<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function index(): View
    {
        return view('cajero.tickets.index', ['tickets' => Ticket::with(['cuenta.mesa', 'pago'])->latest('emitido_at')->get()]);
    }

    public function show(Ticket $ticket): View
    {
        return view('cajero.tickets.show', ['ticket' => $ticket->load(['cuenta.mesa', 'cuenta.comanda.mesero', 'cuenta.comanda.detalles.producto', 'pago.cajero'])]);
    }
}