@extends('cocina.layout')

@section('title', 'Comanda #'.$comanda->id)
@section('heading')<h1>Comanda #{{ $comanda->id }}</h1><p class="muted">Mesa {{ $comanda->mesa->numero }} · Mesero: {{ $comanda->mesero->name }}</p>@endsection

@section('content')
@if (session('status')) <div class="flash">{{ session('status') }}</div> @endif
<article class="card">
    <span class="badge {{ $comanda->estado === 'en_preparacion' ? 'busy' : '' }}">{{ $comanda->estado === 'guardada' ? 'pendiente' : str_replace('_', ' ', $comanda->estado) }}</span>
    @foreach ($comanda->detalles as $detalle)<div class="item"><span><strong>{{ $detalle->cantidad }} × {{ $detalle->producto->nombre }}</strong>@if ($detalle->observaciones)<br><span class="muted">{{ $detalle->observaciones }}</span>@endif</span></div>@endforeach
    <div class="actions"><a class="secondary" href="{{ route('cocina.index') }}">Volver a comandas</a>@if (in_array($comanda->estado, ['guardada', 'pendiente']))<form method="POST" action="{{ route('cocina.start', $comanda) }}">@csrf<button type="submit">Iniciar preparación</button></form>@elseif ($comanda->estado === 'en_preparacion')<form method="POST" action="{{ route('cocina.finish', $comanda) }}">@csrf<button type="submit">Marcar como lista</button></form>@endif</div>
</article>
@endsection