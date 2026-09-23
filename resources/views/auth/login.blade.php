<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrar | Comandero</title>
    <style>
        :root { --ink:#382b3d; --paper:#fff7f8; --panel:#fffdfd; --pink:#a95778; --soft:#f6e2e9; --line:#ead7de; --muted:#806f79; }
        * { box-sizing:border-box; }
        body { margin:0; min-height:100vh; display:grid; place-items:center; padding:24px; color:var(--ink); background:radial-gradient(circle at 15% 10%, #f4dce5 0, transparent 25rem), radial-gradient(circle at 90% 90%, #f7e9d9 0, transparent 24rem), var(--paper); font-family:Georgia, 'Times New Roman', serif; }
        main { width:min(100%, 430px); padding:42px; border:1px solid var(--line); border-radius:22px; background:rgba(255,253,253,.9); box-shadow:0 18px 50px rgba(92,51,73,.13); }
        .mark { color:var(--pink); font:700 .75rem system-ui,sans-serif; letter-spacing:.12em; text-transform:uppercase; }
        h1 { margin:8px 0 8px; font-size:2.4rem; font-weight:500; } .intro { color:var(--muted); margin:0 0 28px; }
        form { display:grid; gap:9px; } label { font:600 .86rem system-ui,sans-serif; margin-top:8px; } input { width:100%; padding:13px; border:1px solid var(--line); border-radius:10px; background:#fffafb; color:var(--ink); } input:focus { outline:3px solid var(--soft); border-color:var(--pink); } button { margin-top:15px; padding:13px; border:0; border-radius:10px; background:var(--pink); color:white; font:700 .95rem system-ui,sans-serif; cursor:pointer; } .error { padding:12px; border-radius:10px; background:#f5dfdd; color:#8f4d4d; font: .9rem system-ui,sans-serif; }
    </style>
</head>
<body>
    <main>
        <span class="mark">Comandero · sala</span>
        <h1>Bienvenida de nuevo</h1>
        <p class="intro">Entra para organizar tu turno con calma y claridad.</p>

        @if ($errors->any())
            <div class="error" role="alert">
                <p>{{ $errors->first() }}</p>
            </div>
        @endif

        <form method="POST" action="{{ route('login.store') }}">
            @csrf

            <label for="email">Correo electronico</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus>

            <label for="password">Contrasena</label>
            <input id="password" name="password" type="password" required>

            <button type="submit">Entrar al comandero</button>
        </form>
    </main>
</body>
</html>