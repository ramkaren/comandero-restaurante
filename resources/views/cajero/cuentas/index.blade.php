@extends('cajero.layout')
@section('title', 'Cuentas pendientes')
@section('heading')<h1>Cuentas pendientes</h1><p class="muted">Revisa una comanda lista antes de cobrarla.</p>@endsection
@section('content')
@if ($errors->any())<div class="errors">{{ $errors->first() }}</div>@endif
<div class="grid">@forelse ($cuentas as $cuenta)<article class="card"><span class="eyebrow">Cuenta #{{ $cuenta->id }}</span><h2>Mesa {{ $cuenta->mesa->numero }}</h2><p class="muted">Mesero: {{ $cuenta->comanda->mesero->name }}</p><p><strong>Total:</strong> ${{ number_format((float) $cuenta->total, 2) }}</p><span class="badge">{{ $cuenta->estado }}</span><div class="actions"><a href="{{ route('cajero.cuentas.show', $cuenta) }}">Abrir cuenta</a></div></article>@empty<article class="card"><h2>No hay cuentas pendientes</h2><p class="muted">Las comandas listas aparecerán aquí.</p></article>@endforelse</div>
@endsection