@extends('mesero.layout')

@section('title', 'Mis comandas')

@section('content')
<span class="eyebrow">Seguimiento</span>
<h1>Mis comandas</h1>
<p class="muted">Consulta las comandas que estás atendiendo.</p>
@if (session('status')) <div class="flash">{{ session('status') }}</div> @endif

<div class="grid">
    @forelse ($comandas as $comanda)
        <article class="card">
            <span class="eyebrow">Comanda #{{ $comanda->id }}</span>
            <h3>Mesa {{ $comanda->mesa->numero }}</h3>
            <p class="muted">{{ $comanda->created_at->format('d/m/Y H:i') }}</p>
            <p><strong>Estado:</strong> {{ $comanda->estado }}</p>
            <p><strong>Total:</strong> ${{ number_format((float) $comanda->total, 2) }}</p>
            @if (in_array($comanda->estado, ['borrador', 'guardada']))
                <a class="button" href="{{ route('mesero.comandas.edit', $comanda) }}">Abrir comanda</a>
            @endif
        </article>
    @empty
        <p>Aún no tienes comandas.</p>
    @endforelse
</div>
@endsection