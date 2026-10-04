@extends('cajero.layout')
@section('title', 'Caja')
@section('heading')<h1>Cafecito caliente</h1><p class="muted">Todo listo para cerrar cuentas con calma.</p>@endsection
@section('content')
<div class="grid"><article class="card"><span class="eyebrow">Pendientes</span><h2>{{ $cuentasPendientes }} cuentas</h2><a href="{{ route('cajero.cuentas.index') }}">Revisar cuentas</a></article><article class="card"><span class="eyebrow">Pagos de hoy</span><h2>{{ $pagosHoy }} pagos</h2><a href="{{ route('cajero.pagos.index') }}">Ver pagos</a></article><article class="card"><span class="eyebrow">Venta del día</span><h2>${{ number_format((float) $ventasHoy, 2) }}</h2><a href="{{ route('cajero.ventas.index') }}">Ver ventas</a></article></div>
@endsection