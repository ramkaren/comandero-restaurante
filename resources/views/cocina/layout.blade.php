<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Cocina') | Comandero</title>
    <style>
        :root { --ink:#382b3d; --paper:#fff7f8; --panel:#fffdfd; --pink:#a95778; --pink-soft:#f6e2e9; --sage:#62806e; --sage-soft:#e4f0e8; --clay:#b66566; --line:#ead7de; }
        * { box-sizing:border-box; }
        body { margin:0; min-height:100vh; background:radial-gradient(circle at 90% 0, #f9e5eb 0, transparent 28rem), var(--paper); color:var(--ink); font-family:Georgia, 'Times New Roman', serif; }
        .shell { max-width:1220px; margin:auto; padding:clamp(24px, 5vw, 58px); }
        .topbar { display:flex; justify-content:space-between; align-items:center; gap:16px; flex-wrap:wrap; margin-bottom:34px; }
        .brand { color:var(--pink); font:700 .75rem system-ui,sans-serif; letter-spacing:.12em; text-transform:uppercase; }
        h1 { margin:8px 0; font-size:clamp(2.1rem, 5vw, 3.8rem); font-weight:500; line-height:1; } h2 { font-size:1.35rem; font-weight:500; }
        .muted { color:#806f79; } .grid { display:grid; grid-template-columns:repeat(auto-fit, minmax(270px, 1fr)); gap:18px; }
        .card { background:rgba(255,253,253,.94); border:1px solid var(--line); border-radius:16px; padding:22px; box-shadow:0 10px 28px rgba(92,51,73,.08); }
        .eyebrow { color:var(--pink); font:700 .74rem system-ui,sans-serif; letter-spacing:.1em; text-transform:uppercase; }
        .badge { display:inline-block; border-radius:999px; padding:6px 10px; background:var(--pink-soft); color:var(--pink); font:700 .74rem system-ui,sans-serif; text-transform:uppercase; }
        .badge.ready { background:var(--sage-soft); color:#416247; } .badge.busy { background:#f5e3d6; color:#8d5e3f; }
        .item { display:flex; justify-content:space-between; gap:12px; padding:13px 0; border-bottom:1px solid var(--line); } .item:last-child { border-bottom:0; }
        .actions { display:flex; gap:9px; flex-wrap:wrap; margin-top:20px; } a, button { border:0; border-radius:10px; padding:11px 15px; background:var(--pink); color:white; text-decoration:none; font:600 .9rem system-ui,sans-serif; cursor:pointer; } button.secondary, a.secondary { background:var(--pink-soft); color:var(--ink); }
        .flash { padding:12px 14px; margin-bottom:20px; border-radius:10px; background:var(--sage-soft); color:#416247; }
    </style>
</head>
<body>
<main class="shell">
    <div class="topbar"><div><span class="brand">Comandero · cocina</span>@yield('heading')</div><a class="secondary" href="{{ route('dashboard') }}">Volver al perfil</a></div>
    @yield('content')
</main>
</body>
</html>