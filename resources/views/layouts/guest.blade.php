<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'MiEntreno' }} · MiEntreno</title>

    <!-- Favicons -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg-main: #05060A;
            --bg-card: #0B0C12;
            --border-subtle: #111827;
            --text-main: #F9FAFB;
            --text-muted: #9CA3AF;
            --accent-primary: #FF3B5C;
            --accent-secondary: #2DE38E;
        }
        * { box-sizing:border-box;margin:0;padding:0; }
        body {
            font-family:'Inter',system-ui,-apple-system,BlinkMacSystemFont,sans-serif;
            background: radial-gradient(circle at top, #111827 0, #05060A 45%, #020308 100%);
            color:var(--text-main);
            min-height:100vh;
        }
        a { color:inherit;text-decoration:none; }

        .app-shell { display:flex;flex-direction:column;min-height:100vh; }
        .nav {
            display:flex;align-items:center;justify-content:space-between;
            padding:1rem 1.5rem;border-bottom:1px solid var(--border-subtle);
            backdrop-filter:blur(16px);background:rgba(5,6,10,0.85);
            position:sticky;top:0;z-index:10;
        }
        .logo { display:flex;align-items:center;gap:.5rem; }
        .logo-mark {
            display:inline-flex;align-items:center;justify-content:center;
            padding:.25rem .6rem;border-radius:999px;
            background:linear-gradient(135deg,var(--accent-primary),#FF4FA3);
            font-family:'Space Grotesk',system-ui,sans-serif;
            font-size:.8rem;letter-spacing:.06em;text-transform:uppercase;
        }
        .logo-dev {
            font-size:.8rem;font-family:'Space Grotesk',monospace;
            color:var(--accent-secondary);
        }
        .logo-text {
            font-family:'Space Grotesk',system-ui,sans-serif;
            font-weight:600;letter-spacing:.06em;text-transform:uppercase;
            font-size:.95rem;
        }
        .nav-links { display:flex;align-items:center;gap:1rem;font-size:.9rem; }
        .nav-links a { color:var(--text-muted); }
        .nav-links a:hover { color:var(--text-main); }

        .btn-primary,.btn-ghost {
            border-radius:999px;padding:.5rem 1.1rem;font-size:.85rem;
            font-weight:500;border:1px solid transparent;cursor:pointer;
            display:inline-flex;align-items:center;justify-content:center;
            gap:.4rem;transition:all .18s ease-out;
        }
        .btn-primary {
            background:linear-gradient(135deg,var(--accent-primary),#FF4FA3);
            color:#0B0C12;box-shadow:0 8px 24px rgba(255,59,92,.35);
        }
        .btn-primary:hover { transform:translateY(-1px);box-shadow:0 12px 28px rgba(255,59,92,.45); }
        .btn-ghost { background:transparent;color:var(--text-muted);border-color:transparent; }
        .btn-ghost:hover { background:rgba(15,23,42,.85);color:var(--text-main); }

        .main { flex:1;display:flex;align-items:center;justify-content:center;padding:2.5rem 1.5rem; }
        .footer {
            border-top:1px solid var(--border-subtle);padding:1rem 1.5rem;
            font-size:.8rem;color:var(--text-muted);
            display:flex;justify-content:space-between;gap:1rem;
        }
        .badge {
            display:inline-flex;align-items:center;gap:.4rem;
            padding:.15rem .6rem;border-radius:999px;
            background:rgba(15,23,42,.9);border:1px solid rgba(148,163,184,.45);
            font-size:.7rem;text-transform:uppercase;letter-spacing:.12em;
        }

        input, select, button {
            font-family: inherit;
        }

        input:focus, select:focus {
            outline: none;
            border-color: var(--accent-primary);
        }

        @media (max-width:768px){
            .nav{padding-inline:1rem;}
            .main{padding-inline:1rem;padding-top:1.5rem;align-items:flex-start;}
            .footer{flex-direction:column;align-items:flex-start;}
            .nav-links a.hide-on-mobile{display:none;}
        }
    </style>
</head>
<body>
<div class="app-shell">
    <header class="nav">
        <a href="/" class="logo">
            <img src="{{ asset('images/logo-horizontal.svg') }}" alt="MiEntreno" style="height:36px;width:auto;">
        </a>

        <nav class="nav-links">
            <a href="/#features" class="hide-on-mobile">Features</a>
            <a href="/#coaches" class="hide-on-mobile">Coaches</a>
            <a href="/#faq" class="hide-on-mobile">FAQ</a>
            <a href="{{ route('login') }}" class="btn-ghost">Login</a>
            <a href="{{ route('register') }}" class="btn-primary">Create account</a>
        </nav>
    </header>

    <main class="main">
        {{ $slot }}
    </main>

    <footer class="footer">
        <span>© 2025 MiEntreno · Más que números, tu historia running</span>
        <span>By <a href="https://srojasweb.dev" target="_blank" rel="noopener noreferrer">srojasweb.dev</a></span>
    </footer>
</div>
</body>
</html>
