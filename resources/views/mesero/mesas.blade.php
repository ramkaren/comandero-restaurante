@extends('mesero.layout')

@section('title', 'Mesas')

@section('content')
<span class="eyebrow">Sala de servicio</span>
<h1>Elige tu mesa</h1>
<p class="muted">Toma una mesa disponible para comenzar una comanda.</p>

@if (session('status')) <div class="flash">{{ session('status') }}</div> @endif
@if ($errors->any()) <div class="errors">{{ $errors->first() }}</div> @endif

<div class="grid">
    @forelse ($mesas as $mesa)
        <article class="card">
            <div class="actions" style="justify-content:space-between">
                <span class="eyebrow">Mesa {{ $mesa->numero }}</span>
                <span class="status {{ $mesa->estado === 'disponible' ? 'available' : 'busy' }}">{{ $mesa->estado }}</span>
            </div>
            <h3>{{ $mesa->capacidad }} personas</h3>
            @if ($mesa->mesero)
                <p class="muted">Atiende: {{ $mesa->mesero->name }}</p>
            @else
                <p class="muted">Lista para recibir clientes</p>
            @endif
            @if ($mesa->estado === 'disponible' && $mesa->activa)
                <form method="POST" action="{{ route('mesero.mesas.select', $mesa) }}">@csrf<button type="submit">Seleccionar mesa</button></form>
            @elseif ($mesa->mesero_id === auth()->id())
                <form method="POST" action="{{ route('mesero.mesas.release', $mesa) }}">@csrf<button class="secondary" type="submit">Liberar mesa</button></form>
            @endif
        </article>
    @empty
        <p>No hay mesas configuradas.</p>
    @endforelse
</div>
@endsection