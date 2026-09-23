@extends('mesero.layout')

@section('title', 'Comanda #'.$comanda->id)

@section('content')
<span class="eyebrow">Mesa {{ $comanda->mesa->numero }} · Comanda #{{ $comanda->id }}</span>
<h1>Arma la comanda</h1>
<p class="muted">Agrega productos y ajusta cantidades antes de guardarla.</p>
@if (session('status')) <div class="flash">{{ session('status') }}</div> @endif
@if ($errors->any()) <div class="errors">{{ $errors->first() }}</div> @endif

<div class="split">
    <section>
        <div class="toolbar">
            <button class="secondary filter" data-category="all" type="button">Todos</button>
            @foreach ($categorias as $categoria)
                <button class="secondary filter" data-category="category-{{ $categoria->id }}" type="button">{{ $categoria->nombre }}</button>
            @endforeach
        </div>
        @foreach ($categorias as $categoria)
            <section class="card product-group category-{{ $categoria->id }}" style="margin-bottom:16px">
                <h2>{{ $categoria->nombre }}</h2>
                @foreach ($categoria->productos as $producto)
                    <div class="product">
                        <div><strong>{{ $producto->nombre }}</strong><br><span class="muted">${{ number_format((float) $producto->precio, 2) }}</span></div>
                        <form method="POST" action="{{ route('mesero.comandas.add', $comanda) }}">@csrf<input type="hidden" name="producto_id" value="{{ $producto->id }}"><input name="cantidad" type="number" min="1" max="99" value="1" aria-label="Cantidad"><button type="submit">Agregar</button></form>
                    </div>
                @endforeach
            </section>
        @endforeach
    </section>

    <aside class="card sticky">
        <span class="eyebrow">Resumen</span>
        <h2>Tu pedido</h2>
        @forelse ($comanda->detalles as $detalle)
            <div class="row">
                <div><strong>{{ $detalle->producto->nombre }}</strong><br><span class="muted">{{ $detalle->cantidad }} × ${{ number_format((float) $detalle->precio_unitario, 2) }}</span></div>
                <div class="actions"><span>${{ number_format((float) $detalle->subtotal, 2) }}</span><form method="POST" action="{{ route('mesero.comandas.adjust', [$comanda, $detalle]) }}">@csrf @method('PATCH')<input type="hidden" name="cambio" value="-1"><button class="secondary" type="submit" aria-label="Disminuir cantidad">−</button></form><form method="POST" action="{{ route('mesero.comandas.adjust', [$comanda, $detalle]) }}">@csrf @method('PATCH')<input type="hidden" name="cambio" value="1"><button type="submit" aria-label="Aumentar cantidad">+</button></form><form method="POST" action="{{ route('mesero.comandas.delete', [$comanda, $detalle]) }}">@csrf @method('DELETE')<button class="danger" type="submit" aria-label="Eliminar producto">×</button></form></div>
            </div>
        @empty
            <p class="muted">Aún no agregas productos.</p>
        @endforelse
        <div class="row"><strong>Total</strong><strong>${{ number_format((float) $comanda->total, 2) }}</strong></div>
        <div class="actions" style="margin-top:18px"><form method="POST" action="{{ route('mesero.comandas.save', $comanda) }}">@csrf<button type="submit">Guardar comanda</button></form><a class="button secondary" href="{{ route('mesero.mesas') }}">Volver a mesas</a></div>
    </aside>
</div>
<script>
document.querySelectorAll('.filter').forEach((button) => button.addEventListener('click', () => {
    const category = button.dataset.category;
    document.querySelectorAll('.product-group').forEach((group) => { group.hidden = category !== 'all' && !group.classList.contains(category); });
}));
</script>
@endsection