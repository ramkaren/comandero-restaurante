@extends('cocina.layout')

@section('title', 'Comandas')
@section('heading')<h1>Comandas del turno</h1><p class="muted">Prepara cada pedido con calma y claridad.</p>@endsection

@section('content')
@if (session('status')) <div class="flash">{{ session('status') }}</div> @endif
<div class="grid">
    @forelse ($comandas as $comanda)
        <article class="card">
            <div style="display:flex;justify-content:space-between;gap:12px;align-items:start"><div><span class="eyebrow">Comanda #{{ $comanda->id }}</span><h2>Mesa {{ $comanda->mesa->numero }}</h2></div><span class="badge {{ $comanda->estado === 'en_preparacion' ? 'busy' : ($comanda->estado === 'lista' ? 'ready' : '') }}">{{ $comanda->estado === 'guardada' ? 'pendiente' : str_replace('_', ' ', $comanda->estado) }}</span></div>
            @foreach ($comanda->detalles as $detalle)<div class="item"><span><strong>{{ $detalle->cantidad }} × {{ $detalle->producto->nombre }}</strong>@if ($detalle->observaciones)<br><span class="muted">{{ $detalle->observaciones }}</span>@endif</span></div>@endforeach
            <div class="actions"><a href="{{ route('cocina.show', $comanda) }}">Ver detalle</a>@if (in_array($comanda->estado, ['guardada', 'pendiente']))<form method="POST" action="{{ route('cocina.start', $comanda) }}">@csrf<button type="submit">En preparación</button></form>@elseif ($comanda->estado === 'en_preparacion')<form method="POST" action="{{ route('cocina.finish', $comanda) }}">@csrf<button type="submit">Marcar lista</button></form>@endif</div>
        </article>
    @empty
        <article class="card"><h2>No hay comandas pendientes</h2><p class="muted">Las nuevas comandas aparecerán aquí cuando estén guardadas.</p></article>
    @endforelse
</div>
@endsection