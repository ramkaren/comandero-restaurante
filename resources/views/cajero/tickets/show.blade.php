@extends('cajero.layout')

@section('title', 'Ticket '.$ticket->numero)

@section('heading')
<h1 class="screen-only">Ticket</h1>
<p class="muted screen-only">{{ $ticket->numero }}</p>
@endsection

@section('content')
<div class="ticket-actions screen-only">
	<button type="button" onclick="window.print()">🖨️ Imprimir ticket</button>
	<a class="secondary" href="{{ route('cajero.tickets.index') }}">Volver a tickets</a>
</div>

<article class="ticket-sheet">
	<header class="ticket-header">
		<span class="ticket-mark">✦</span>
		<p class="ticket-brand">Cafecito caliente</p>
		<p class="ticket-kind">Ticket de consumo</p>
		<span class="ticket-rule" aria-hidden="true">· · · · · · · · · ·</span>
	</header>

	<section class="ticket-meta" aria-label="Información del ticket">
		<div><span>Ticket</span><strong>{{ $ticket->numero }}</strong></div>
		<div><span>Fecha</span><strong>{{ $ticket->emitido_at->format('d/m/Y') }}</strong></div>
		<div><span>Hora</span><strong>{{ $ticket->emitido_at->format('H:i') }}</strong></div>
		<div><span>Mesa</span><strong>{{ $ticket->cuenta->mesa->numero }}</strong></div>
		<div class="wide"><span>Mesero</span><strong>{{ $ticket->cuenta->comanda->mesero->name }}</strong></div>
	</section>

	<div class="ticket-divider" aria-hidden="true"></div>

	<section aria-label="Productos consumidos">
		<div class="ticket-columns"><span>Cant. / Producto</span><span>Importe</span></div>
		@foreach ($ticket->cuenta->comanda->detalles as $detalle)
			<div class="ticket-item">
				<div class="ticket-product">
					<strong>{{ $detalle->cantidad }} × {{ $detalle->producto->nombre }}</strong>
					<span>${{ number_format((float) $detalle->precio_unitario, 2) }} c/u</span>
				</div>
				<strong>${{ number_format((float) $detalle->subtotal, 2) }}</strong>
			</div>
		@endforeach
	</section>

	<div class="ticket-divider" aria-hidden="true"></div>

	<section class="ticket-totals" aria-label="Totales">
		<div><span>Subtotal</span><strong>${{ number_format((float) $ticket->cuenta->subtotal, 2) }}</strong></div>
		<div class="ticket-total"><span>Total</span><strong>${{ number_format((float) $ticket->cuenta->total, 2) }}</strong></div>
	</section>

	<section class="ticket-payment" aria-label="Información del pago">
		<div><span>Método de pago</span><strong>{{ ucfirst($ticket->pago->metodo) }}</strong></div>
		@if ($ticket->pago->metodo === 'efectivo' && $ticket->pago->monto_recibido !== null)
			<div><span>Efectivo recibido</span><strong>${{ number_format((float) $ticket->pago->monto_recibido, 2) }}</strong></div>
			<div><span>Cambio</span><strong>${{ number_format((float) ($ticket->pago->cambio ?? 0), 2) }}</strong></div>
		@endif
		@if ($ticket->pago->metodo === 'transferencia' && $ticket->pago->referencia)
			<div><span>Referencia</span><strong>{{ $ticket->pago->referencia }}</strong></div>
		@endif
	</section>

	<footer class="ticket-footer">
		<span class="ticket-rule" aria-hidden="true">· · · · · · · · · ·</span>
		<p>Gracias por tu visita <span aria-hidden="true">♡</span></p>
		<small>Conserva este ticket para cualquier aclaración.</small>
	</footer>
</article>

<style>
	.ticket-actions { display:flex; gap:10px; flex-wrap:wrap; margin:0 auto 18px; max-width:480px; }
	.ticket-sheet { width:min(100%, 480px); margin:0 auto; padding:32px 30px 26px; color:#382b3d; background:#fffdfd; border:1px solid #ead7de; box-shadow:0 16px 38px rgba(92,51,73,.12); font-family:Georgia, 'Times New Roman', serif; }
	.ticket-header { text-align:center; }
	.ticket-mark { display:block; color:#b28a59; font-size:1.1rem; line-height:1; }
	.ticket-brand { margin:8px 0 2px; color:#a95778; font-size:2rem; font-weight:700; letter-spacing:.03em; }
	.ticket-kind { margin:0; color:#806f79; font:700 .68rem/1.4 system-ui,sans-serif; letter-spacing:.14em; text-transform:uppercase; }
	.ticket-rule { display:block; margin:16px 0 12px; color:#c97f9d; letter-spacing:.19em; overflow:hidden; white-space:nowrap; }
	.ticket-meta, .ticket-payment { display:grid; grid-template-columns:repeat(2, minmax(0, 1fr)); gap:13px 22px; font-size:.86rem; }
	.ticket-meta .wide { grid-column:1 / -1; }
	.ticket-meta span, .ticket-payment span { display:block; margin-bottom:3px; color:#806f79; font:700 .64rem/1.2 system-ui,sans-serif; letter-spacing:.08em; text-transform:uppercase; }
	.ticket-meta strong, .ticket-payment strong { display:block; overflow-wrap:anywhere; font-size:.88rem; font-weight:600; }
	.ticket-divider { height:1px; margin:22px 0 14px; border-top:1px dashed #d9b9c7; }
	.ticket-columns, .ticket-item { display:grid; grid-template-columns:minmax(0, 1fr) auto; gap:15px; align-items:start; }
	.ticket-columns { color:#a95778; font:700 .64rem/1.2 system-ui,sans-serif; letter-spacing:.08em; text-transform:uppercase; }
	.ticket-columns span:last-child { text-align:right; }
	.ticket-item { padding:12px 0; border-bottom:1px solid #f0e4e8; font-size:.9rem; }
	.ticket-product { min-width:0; }
	.ticket-product strong { display:block; overflow-wrap:anywhere; font-weight:600; }
	.ticket-product span { display:block; margin-top:4px; color:#806f79; font-size:.77rem; }
	.ticket-item > strong { white-space:nowrap; font-weight:600; }
	.ticket-totals > div { display:flex; justify-content:space-between; gap:15px; padding:7px 0; color:#806f79; font-size:.9rem; }
	.ticket-totals .ticket-total { margin-top:4px; padding:14px 0 2px; color:#382b3d; border-top:2px solid #a95778; font-size:1.18rem; }
	.ticket-total strong { color:#a95778; font-size:1.35rem; }
	.ticket-payment { margin-top:20px; padding:15px; background:#f6e2e9; border-radius:10px; }
	.ticket-footer { margin-top:25px; text-align:center; }
	.ticket-footer .ticket-rule { margin:0 0 13px; }
	.ticket-footer p { margin:0; color:#a95778; font-size:1.05rem; font-weight:700; }
	.ticket-footer small { display:block; margin-top:7px; color:#806f79; font-size:.72rem; }
	@media (max-width:520px) { .ticket-sheet { padding:26px 20px 22px; } .ticket-brand { font-size:1.75rem; } }
	@media print {
		@page { size:80mm auto; margin:0; }
		html, body { width:80mm; min-width:80mm; margin:0; background:#fff; }
		body { color:#000; }
		.screen-only, .topbar, .ticket-actions { display:none !important; }
		.shell { display:block; width:80mm; max-width:none; padding:0; margin:0; }
		.ticket-sheet { display:block; width:80mm; min-height:0; margin:0; padding:8mm 6mm 6mm; border:0; box-shadow:none; background:#fff; }
		.ticket-brand, .ticket-total strong, .ticket-footer p { color:#000; }
		.ticket-rule { color:#000; }
		.ticket-payment { background:#fff; border:1px solid #bbb; }
		.ticket-divider { border-color:#777; }
		.ticket-item { border-color:#ddd; }
		.ticket-meta span, .ticket-payment span, .ticket-product span, .ticket-footer small, .ticket-totals > div { color:#333; }
	}
</style>
@endsection