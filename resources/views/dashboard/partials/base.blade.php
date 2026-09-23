<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} | Comandero</title>
    <style>
        :root { --ink:#382b3d; --paper:#fff7f8; --pink:#a95778; --soft:#f6e2e9; --line:#ead7de; }
        body { margin:0; min-height:100vh; display:grid; place-items:center; padding:24px; background:radial-gradient(circle at 90% 10%, #f4dce5 0, transparent 25rem), var(--paper); color:var(--ink); font-family:Georgia, 'Times New Roman', serif; }
        main { width:min(100%, 700px); padding:42px; border:1px solid var(--line); border-radius:22px; background:#fffdfd; box-shadow:0 18px 50px rgba(92,51,73,.11); } h1 { margin:8px 0; font-weight:500; font-size:2.7rem; } .eyebrow { color:var(--pink); font:700 .75rem system-ui,sans-serif; letter-spacing:.1em; text-transform:uppercase; } .description { color:#806f79; } .links { display:flex; gap:10px; flex-wrap:wrap; margin:28px 0; } a, button { border:0; border-radius:10px; padding:11px 15px; background:var(--pink); color:white; text-decoration:none; font:600 .9rem system-ui,sans-serif; cursor:pointer; } button { background:var(--soft); color:var(--ink); }
    </style>
</head>
<body>
    <main>
        <span class="eyebrow">Comandero</span>
        <h1>{{ $title }}</h1>
        <p class="description">{{ $description }}</p>
        <p>Usuario: {{ $user->name }}</p>
        <p>Rol: {{ $role }}</p>

        <div class="links">
            @foreach ($links ?? [] as $link)
                <a href="{{ $link['url'] }}">{{ $link['label'] }}</a>
            @endforeach
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit">Cerrar sesion</button>
        </form>
    </main>
</body>
</html>