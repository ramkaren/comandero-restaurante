<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Comandero') | Comandero</title>
    <style>
        :root { --ink:#382b3d; --paper:#fff7f8; --panel:#fffdfd; --pink:#c97f9d; --pink-deep:#a95778; --pink-soft:#f6e2e9; --sage:#62806e; --sage-soft:#e4f0e8; --clay:#b66566; --gold:#b28a59; --line:#ead7de; --shadow:rgba(92, 51, 73, .09); }
        * { box-sizing:border-box; }
        body { margin:0; background:radial-gradient(circle at 92% 0%, #f9e5eb 0, transparent 28rem), var(--paper); color:var(--ink); font-family:Georgia, 'Times New Roman', serif; }
        a { color:inherit; }
        .shell { min-height:100vh; display:grid; grid-template-columns:230px 1fr; }
        .nav { padding:28px 20px; background:linear-gradient(160deg, #76556d, #65485f); color:#fff7f8; box-shadow:8px 0 24px rgba(92,51,73,.12); }
        .brand { font-size:1.35rem; margin:0 0 32px; letter-spacing:.02em; }
        .nav a { display:block; padding:11px 12px; margin:6px 0; border-radius:10px; text-decoration:none; color:#fff0f4; }
        .nav a:hover, .nav a.active { background:#f3d6df; color:#5b3d54; }
        .content { padding:clamp(24px, 5vw, 58px); max-width:1320px; width:100%; }
        .eyebrow { color:var(--pink-deep); font:700 .75rem/1.2 system-ui, sans-serif; letter-spacing:.08em; text-transform:uppercase; }
        h1 { font-size:clamp(2rem, 4vw, 3.4rem); line-height:1; margin:8px 0 10px; font-weight:500; }
        h2 { font-size:1.25rem; font-weight:500; }
        .muted { color:#6f7773; }
        .toolbar { display:flex; gap:12px; flex-wrap:wrap; align-items:center; margin:26px 0; }
        .grid { display:grid; grid-template-columns:repeat(auto-fit, minmax(210px, 1fr)); gap:16px; }
        .card { background:rgba(255,253,253,.92); border:1px solid var(--line); border-radius:16px; padding:20px; box-shadow:0 10px 28px var(--shadow); }
        .card h3 { margin:0 0 8px; font-size:1.35rem; }
        .status { display:inline-flex; align-items:center; border-radius:999px; padding:5px 10px; font:700 .74rem system-ui, sans-serif; text-transform:uppercase; letter-spacing:.04em; }
        .available { background:var(--sage-soft); color:#416247; } .busy { background:#f4e0d9; color:#934735; }
        button, .button { border:0; border-radius:10px; padding:10px 14px; background:var(--pink-deep); color:white; cursor:pointer; text-decoration:none; font:600 .9rem system-ui, sans-serif; box-shadow:0 5px 12px rgba(169,87,120,.18); }
        button:hover, .button:hover { background:#914866; } button.secondary, .button.secondary { background:var(--pink-soft); color:var(--ink); box-shadow:none; } button.danger { background:var(--clay); }
        .product { display:flex; justify-content:space-between; gap:14px; align-items:center; }
        .product form { display:flex; gap:7px; align-items:center; }
        input, select { border:1px solid var(--line); border-radius:9px; padding:10px; background:#fffafb; color:var(--ink); }
        .split { display:grid; grid-template-columns:minmax(0, 1.5fr) minmax(280px, .8fr); gap:22px; align-items:start; }
        .sticky { position:sticky; top:20px; }
        .row { display:flex; justify-content:space-between; gap:12px; align-items:center; padding:14px 0; border-bottom:1px solid var(--line); }
        .flash, .errors { border-radius:10px; padding:12px 14px; margin:16px 0; background:var(--sage-soft); color:#416247; }
        .errors { background:#f4e0d9; color:#934735; }
        .actions { display:flex; gap:8px; flex-wrap:wrap; }
        @media (max-width:760px) { .shell { display:block; } .nav { padding:16px; } .nav a { display:inline-block; } .content { padding:28px 18px; } .split { grid-template-columns:1fr; } .sticky { position:static; } }
    </style>
</head>
<body>
<div class="shell">
    <aside class="nav">
        <p class="brand">Comandero <span aria-hidden="true">·</span></p>
        <a href="{{ route('mesero.mesas') }}">Mesas</a>
        <a href="{{ route('mesero.comandas') }}">Mis comandas</a>
        <a href="{{ route('dashboard') }}">Perfil</a>
        <form method="POST" action="{{ route('logout') }}">@csrf<button class="secondary" type="submit">Cerrar sesión</button></form>
    </aside>
    <main class="content">@yield('content')</main>
</div>
</body>
</html>
