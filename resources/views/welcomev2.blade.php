<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <title>MiEntreno · Más que números, tu historia running</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Favicons -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

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

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
            background: radial-gradient(circle at top, #111827 0, #05060A 45%, #020308 100%);
            color: var(--text-main);
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }

        /* Animated background gradient orbs */
        body::before,
        body::after {
            content: '';
            position: fixed;
            border-radius: 50%;
            filter: blur(120px);
            opacity: 0.15;
            z-index: 0;
            pointer-events: none;
        }

        body::before {
            width: 600px;
            height: 600px;
            background: linear-gradient(135deg, var(--accent-primary), #FF4FA3);
            top: -200px;
            right: -150px;
            animation: float 20s ease-in-out infinite;
        }

        body::after {
            width: 500px;
            height: 500px;
            background: linear-gradient(135deg, var(--accent-secondary), #22C55E);
            bottom: -100px;
            left: -100px;
            animation: float 15s ease-in-out infinite reverse;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(30px, -30px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        .app-shell {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            position: relative;
            z-index: 1;
        }

        .nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1.25rem 2rem;
            border-bottom: 1px solid var(--border-subtle);
            backdrop-filter: blur(20px) saturate(180%);
            background: rgba(5, 6, 10, 0.75);
            position: sticky;
            top: 0;
            z-index: 100;
            transition: all 0.3s ease;
        }

        .nav.scrolled {
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.4);
        }

        .logo {
            display: flex;
            align-items: center;
            gap: .5rem;
            transition: transform 0.3s ease;
        }

        .logo:hover {
            transform: scale(1.05);
        }

        .logo-mark {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: .25rem .6rem;
            border-radius: 999px;
            background: linear-gradient(135deg, var(--accent-primary), #FF4FA3);
            font-family: 'Space Grotesk', system-ui, sans-serif;
            font-size: .8rem;
            letter-spacing: .06em;
            text-transform: uppercase;
            box-shadow: 0 4px 16px rgba(255, 59, 92, 0.35);
        }

        .logo-text {
            font-family: 'Space Grotesk', system-ui, sans-serif;
            font-weight: 600;
            letter-spacing: .06em;
            text-transform: uppercase;
            font-size: .95rem;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 1.25rem;
            font-size: .9rem;
        }

        .nav-links a {
            color: var(--text-muted);
            transition: all 0.2s ease;
            position: relative;
        }

        .nav-links a:not(.btn-primary):not(.btn-ghost):hover {
            color: var(--text-main);
        }

        .nav-links a:not(.btn-primary):not(.btn-ghost)::after {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 50%;
            transform: translateX(-50%) scaleX(0);
            width: 100%;
            height: 2px;
            background: linear-gradient(90deg, var(--accent-primary), var(--accent-secondary));
            transition: transform 0.3s ease;
        }

        .nav-links a:not(.btn-primary):not(.btn-ghost):hover::after {
            transform: translateX(-50%) scaleX(1);
        }

        .btn-primary, .btn-secondary, .btn-ghost {
            border-radius: 999px;
            padding: .6rem 1.4rem;
            font-size: .85rem;
            font-weight: 500;
            border: 1px solid transparent;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
            transition: all .25s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--accent-primary), #FF4FA3);
            color: #0B0C12;
            box-shadow: 0 8px 24px rgba(255, 59, 92, .4);
            font-weight: 600;
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, #FF4FA3, var(--accent-primary));
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .btn-primary:hover::before {
            opacity: 1;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 32px rgba(255, 59, 92, .5);
        }

        .btn-primary span {
            position: relative;
            z-index: 1;
        }

        .btn-secondary {
            background: transparent;
            color: var(--text-main);
            border-color: var(--accent-secondary);
        }

        .btn-secondary:hover {
            background: rgba(45, 227, 142, .12);
            box-shadow: 0 0 20px rgba(45, 227, 142, .2);
        }

        .btn-ghost {
            background: transparent;
            color: var(--text-muted);
            border-color: transparent;
        }

        .btn-ghost:hover {
            background: rgba(15, 23, 42, .95);
            color: var(--text-main);
        }

        .main {
            flex: 1;
            padding: 3.5rem 2rem;
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
        }

        .footer {
            border-top: 1px solid var(--border-subtle);
            padding: 2rem 2rem;
            font-size: .85rem;
            color: var(--text-muted);
            background: rgba(5, 6, 10, 0.85);
            backdrop-filter: blur(20px);
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 2fr 1fr 1fr;
            gap: 3rem;
        }

        .footer-section h3 {
            font-size: .9rem;
            color: var(--text-main);
            margin-bottom: .75rem;
            font-family: 'Space Grotesk', sans-serif;
        }

        .footer-section ul {
            list-style: none;
            display: grid;
            gap: .5rem;
        }

        .footer-section a {
            color: var(--text-muted);
            transition: color 0.2s ease;
        }

        .footer-section a:hover {
            color: var(--accent-secondary);
        }

        .footer-bottom {
            max-width: 1200px;
            margin: 2rem auto 0;
            padding-top: 1.5rem;
            border-top: 1px solid var(--border-subtle);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            padding: .25rem .8rem;
            border-radius: 999px;
            background: rgba(15, 23, 42, .95);
            border: 1px solid rgba(148, 163, 184, .5);
            font-size: .72rem;
            text-transform: uppercase;
            letter-spacing: .14em;
            font-weight: 500;
            backdrop-filter: blur(10px);
        }

        .badge::before {
            content: '';
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--accent-secondary);
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; box-shadow: 0 0 0 0 rgba(45, 227, 142, 0.4); }
            50% { opacity: 0.8; box-shadow: 0 0 0 6px rgba(45, 227, 142, 0); }
        }

        .hero-title {
            font-family: 'Space Grotesk', system-ui, sans-serif;
            font-size: 3.2rem;
            line-height: 1.1;
            margin-bottom: 1.25rem;
            font-weight: 700;
            letter-spacing: -0.02em;
        }

        .hero-gradient-text {
            background: linear-gradient(135deg, #FF3B5C, #FF4FA3, #FF6B9D);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            display: inline-block;
            animation: gradientShift 5s ease infinite;
            background-size: 200% 200%;
        }

        @keyframes gradientShift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }

        .dashboard-preview {
            background: linear-gradient(135deg, rgba(11, 12, 18, 0.9) 0%, rgba(15, 23, 42, 0.8) 100%);
            border-radius: 1.75rem;
            padding: 1.75rem;
            border: 1px solid rgba(148, 163, 184, 0.4);
            box-shadow:
                0 20px 60px rgba(15, 23, 42, 0.8),
                0 0 0 1px rgba(255, 255, 255, 0.05) inset;
            max-width: 480px;
            margin-left: auto;
            backdrop-filter: blur(20px);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
        }

        .dashboard-preview::before {
            content: '';
            position: absolute;
            inset: -2px;
            border-radius: 1.75rem;
            padding: 2px;
            background: linear-gradient(135deg, rgba(255, 59, 92, 0.3), rgba(45, 227, 142, 0.3));
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            opacity: 0;
            transition: opacity 0.4s ease;
        }

        .dashboard-preview:hover::before {
            opacity: 1;
        }

        .dashboard-preview:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow:
                0 30px 80px rgba(15, 23, 42, 0.9),
                0 0 0 1px rgba(255, 255, 255, 0.1) inset;
        }

        .stat-card {
            padding: 1rem;
            border-radius: 1rem;
            background: linear-gradient(135deg, rgba(5, 8, 20, 0.95), rgba(17, 24, 39, 0.8));
            border: 1px solid rgba(17, 24, 39, 0.8);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255, 59, 92, 0.1), rgba(45, 227, 142, 0.1));
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .stat-card:hover::before {
            opacity: 1;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            border-color: rgba(148, 163, 184, 0.3);
        }

        .feature-card {
            padding: 1.75rem;
            border-radius: 1.25rem;
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.9), rgba(11, 12, 18, 0.85));
            border: 1px solid var(--border-subtle);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255, 59, 92, 0.05), rgba(45, 227, 142, 0.05));
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .feature-card:hover::before {
            opacity: 1;
        }

        .feature-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5);
            border-color: rgba(148, 163, 184, 0.4);
        }

        .feature-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: linear-gradient(135deg, rgba(255, 59, 92, 0.15), rgba(255, 79, 163, 0.15));
            border: 1px solid rgba(255, 59, 92, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
        }

        .feature-card:hover .feature-icon {
            transform: scale(1.1) rotate(5deg);
            box-shadow: 0 8px 24px rgba(255, 59, 92, 0.3);
        }

        .feature-card:nth-child(2) .feature-icon {
            background: linear-gradient(135deg, rgba(45, 227, 142, 0.15), rgba(34, 197, 94, 0.15));
            border-color: rgba(45, 227, 142, 0.3);
        }

        .feature-card:nth-child(2):hover .feature-icon {
            box-shadow: 0 8px 24px rgba(45, 227, 142, 0.3);
        }

        .stats-section {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 2rem;
            padding: 3rem 0;
            margin: 3rem 0;
            border-top: 1px solid var(--border-subtle);
            border-bottom: 1px solid var(--border-subtle);
        }

        .stat-item {
            text-align: center;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            font-family: 'Space Grotesk', sans-serif;
            background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            font-size: 0.9rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }

        @media (max-width: 968px) {
            .stats-section {
                grid-template-columns: repeat(2, 1fr);
                gap: 1.5rem;
            }

            .footer-content {
                grid-template-columns: 1fr;
                gap: 2rem;
            }

            .hero-title {
                font-size: 2.4rem;
            }
        }

        @media (max-width: 768px) {
            .nav {
                padding: 1rem;
            }

            .main {
                padding: 2rem 1rem;
            }

            .hero-grid {
                grid-template-columns: minmax(0, 1fr) !important;
            }

            .nav-links a.hide-on-mobile {
                display: none;
            }

            .hero-title {
                font-size: 2rem;
            }

            #features {
                grid-template-columns: minmax(0, 1fr) !important;
            }

            .stats-section {
                grid-template-columns: 1fr;
                gap: 1.5rem;
                padding: 2rem 0;
            }

            .dashboard-preview {
                margin-left: 0;
                margin-top: 2rem;
            }
        }

        /* Smooth scrolling */
        html {
            scroll-behavior: smooth;
        }

        /* Selection color */
        ::selection {
            background: rgba(255, 59, 92, 0.3);
            color: var(--text-main);
        }
    </style>
</head>
<body>
<div class="app-shell">
    <header class="nav">
        <a href="{{ route('welcome') }}" class="logo">
            <img src="{{ asset('images/logo-horizontal.svg') }}" alt="MiEntreno" style="height:40px;width:auto;">
        </a>

        <nav class="nav-links">
            <a href="#features" class="hide-on-mobile">Features</a>
            <a href="#coaches" class="hide-on-mobile">Coaches</a>
            <a href="#faq" class="hide-on-mobile">FAQ</a>
            <a href="{{ route('login') }}" class="btn-ghost">Login</a>
            <a href="{{ route('register') }}" class="btn-primary">
                <span>Create account</span>
            </a>
        </nav>
    </header>

    <main class="main">
        <!-- HERO -->
        <section class="hero-grid" style="display:grid;grid-template-columns:minmax(0,2fr) minmax(0,1.6fr);gap:3rem;align-items:center;margin-bottom:1rem;">
            <div>
                <div class="badge" style="margin-bottom:1.5rem;">
                    <span>Running · Data · Code</span>
                </div>

                <h1 class="hero-title">
                    Entrená como corrés.<br>
                    <span class="hero-gradient-text">
                        Registrá como programás.
                    </span>
                </h1>

                <p style="color:var(--text-muted);max-width:34rem;margin-bottom:2rem;font-size:1.05rem;line-height:1.6;">
                    MiEntreno es tu bitácora de running: carga tus entrenamientos, ritmos y carreras.
                    Como un dashboard de developer, pero para tus kilómetros.
                </p>

                <div style="display:flex;flex-wrap:wrap;gap:1rem;margin-bottom:2.5rem;">
                    <a href="{{ route('register') }}" class="btn-primary">
                        <span>Empezar ahora</span>
                        <span style="font-size:1.1rem;">→</span>
                    </a>
                    <a href="#features" class="btn-secondary">
                        Ver cómo funciona
                    </a>
                </div>

                <div style="display:flex;flex-wrap:wrap;gap:1.5rem;font-size:.88rem;color:var(--text-muted);">
                    <div style="display:flex;align-items:center;gap:.5rem;">
                        <span style="font-size:1.2rem;">📊</span>
                        <div>
                            <strong style="color:var(--accent-secondary);display:block;font-size:.95rem;">+3</strong>
                            <span style="font-size:.75rem;">tipos de entrenamiento</span>
                        </div>
                    </div>
                    <div style="display:flex;align-items:center;gap:.5rem;">
                        <span style="font-size:1.2rem;">📈</span>
                        <div>
                            <strong style="color:var(--accent-secondary);display:block;font-size:.95rem;">Dashboard</strong>
                            <span style="font-size:.75rem;">semanal y mensual</span>
                        </div>
                    </div>
                    <div style="display:flex;align-items:center;gap:.5rem;">
                        <span style="font-size:1.2rem;">👥</span>
                        <div>
                            <strong style="color:var(--accent-secondary);display:block;font-size:.95rem;">Modo coach</strong>
                            <span style="font-size:.75rem;">para ver alumnos</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dashboard preview -->
            <div style="position:relative;">
                <div class="dashboard-preview">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.25rem;">
                        <span style="font-size:.85rem;color:var(--text-muted);font-weight:500;">This week</span>
                        <span style="font-size:.85rem;color:var(--accent-secondary);font-family:'Space Grotesk',monospace;font-weight:600;">
                            32.4 km · 3h 05m
                        </span>
                    </div>

                    <div style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:.85rem;margin-bottom:1.5rem;font-size:.85rem;">
                        <div class="stat-card">
                            <div style="color:var(--text-muted);margin-bottom:.35rem;font-size:.75rem;">Km semana</div>
                            <div style="font-weight:700;font-size:1.3rem;color:var(--text-main);">32.4</div>
                        </div>
                        <div class="stat-card">
                            <div style="color:var(--text-muted);margin-bottom:.35rem;font-size:.75rem;">Pace medio</div>
                            <div style="font-weight:700;font-size:1.3rem;color:var(--text-main);">5'12"</div>
                        </div>
                        <div class="stat-card">
                            <div style="color:var(--text-muted);margin-bottom:.35rem;font-size:.75rem;">Sesiones</div>
                            <div style="font-weight:700;font-size:1.3rem;color:var(--text-main);">5</div>
                        </div>
                    </div>

                    <div style="margin-bottom:1.25rem;padding:1rem;border-radius:1rem;background:rgba(5,8,20,0.6);border:1px solid rgba(17,24,39,0.6);">
                        <div style="display:flex;justify-content:space-between;font-size:.78rem;color:var(--text-muted);margin-bottom:.6rem;">
                            <span style="font-weight:500;">Próxima carrera</span>
                            <span style="color:var(--accent-secondary);font-weight:600;">15K · 08 Dic</span>
                        </div>
                        <div style="height:10px;border-radius:999px;background:#111827;overflow:hidden;position:relative;">
                            <div style="width:68%;height:100%;background:linear-gradient(90deg,var(--accent-secondary),#22C55E);box-shadow:0 0 12px rgba(45,227,142,0.5);"></div>
                        </div>
                    </div>

                    <div style="display:flex;justify-content:space-between;align-items:center;font-size:.85rem;">
                        <span style="color:var(--text-muted);">Plan: 10K sub 50'</span>
                        <span style="color:var(--accent-primary);font-family:'Space Grotesk',monospace;font-weight:600;">#run · #code</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- STATS SECTION -->
        <section class="stats-section">
            <div class="stat-item">
                <div class="stat-number">1000+</div>
                <div class="stat-label">Workouts tracked</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">50+</div>
                <div class="stat-label">Active runners</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">15K+</div>
                <div class="stat-label">Total kilometers</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">24/7</div>
                <div class="stat-label">Access anywhere</div>
            </div>
        </section>

        <!-- FEATURES -->
        <section id="features" style="margin-top:2rem;display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:2rem;">
            <div class="feature-card">
                <div class="feature-icon">
                    <span>🏃</span>
                </div>
                <h2 style="font-size:1.15rem;margin-bottom:.75rem;font-weight:600;font-family:'Space Grotesk',sans-serif;">Registro de entrenos</h2>
                <p style="font-size:.92rem;color:var(--text-muted);margin-bottom:1rem;line-height:1.6;">
                    Guarda tipo de entrenamiento, distancia, tiempo, ritmo promedio, dificultad y notas.
                </p>
                <ul style="font-size:.85rem;color:var(--text-muted);list-style:none;display:grid;gap:.4rem;">
                    <li style="display:flex;align-items:center;gap:.5rem;">
                        <span style="color:var(--accent-secondary);font-weight:700;">·</span>
                        Fondos, pasadas, carreras
                    </li>
                    <li style="display:flex;align-items:center;gap:.5rem;">
                        <span style="color:var(--accent-secondary);font-weight:700;">·</span>
                        Estadísticas por semana y mes
                    </li>
                    <li style="display:flex;align-items:center;gap:.5rem;">
                        <span style="color:var(--accent-secondary);font-weight:700;">·</span>
                        Histórico de carreras y marcas
                    </li>
                </ul>
            </div>

            <div id="coaches" class="feature-card">
                <div class="feature-icon">
                    <span>👨‍🏫</span>
                </div>
                <h2 style="font-size:1.15rem;margin-bottom:.75rem;font-weight:600;font-family:'Space Grotesk',sans-serif;">Modo Coach</h2>
                <p style="font-size:.92rem;color:var(--text-muted);margin-bottom:1rem;line-height:1.6;">
                    Entrenadores con acceso a panel de alumnos: entrenos, asistencias y rendimiento.
                </p>
                <ul style="font-size:.85rem;color:var(--text-muted);list-style:none;display:grid;gap:.4rem;">
                    <li style="display:flex;align-items:center;gap:.5rem;">
                        <span style="color:var(--accent-secondary);font-weight:700;">·</span>
                        Totalizadores por semana/mes
                    </li>
                    <li style="display:flex;align-items:center;gap:.5rem;">
                        <span style="color:var(--accent-secondary);font-weight:700;">·</span>
                        Planes de entrenamiento
                    </li>
                    <li style="display:flex;align-items:center;gap:.5rem;">
                        <span style="color:var(--accent-secondary);font-weight:700;">·</span>
                        Seguimiento de objetivos y carreras
                    </li>
                </ul>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <span>💻</span>
                </div>
                <h2 style="font-size:1.15rem;margin-bottom:.75rem;font-weight:600;font-family:'Space Grotesk',sans-serif;">Pensado por devs</h2>
                <p style="font-size:.92rem;color:var(--text-muted);margin-bottom:1rem;line-height:1.6;">
                    Minimalista, rápido y claro. Todo preparado para API, dashboards y vistas limpias.
                </p>
                <ul style="font-size:.85rem;color:var(--text-muted);list-style:none;display:grid;gap:.4rem;">
                    <li style="display:flex;align-items:center;gap:.5rem;">
                        <span style="color:var(--accent-secondary);font-weight:700;">·</span>
                        Backend Laravel listo
                    </li>
                    <li style="display:flex;align-items:center;gap:.5rem;">
                        <span style="color:var(--accent-secondary);font-weight:700;">·</span>
                        API para front (React, Vue, etc.)
                    </li>
                    <li style="display:flex;align-items:center;gap:.5rem;">
                        <span style="color:var(--accent-secondary);font-weight:700;">·</span>
                        Espacio para integrar Strava y relojes
                    </li>
                </ul>
            </div>
        </section>

        <!-- FAQ -->
        <section id="faq" style="margin-top:4rem;max-width:800px;margin-left:auto;margin-right:auto;">
            <h2 style="font-size:1.6rem;margin-bottom:1.75rem;font-family:'Space Grotesk',sans-serif;font-weight:700;text-align:center;">
                Preguntas <span style="color:var(--accent-primary);">rápidas</span>
            </h2>
            <div style="display:grid;gap:1.25rem;font-size:.92rem;">
                <div style="padding:1.5rem;border-radius:1rem;background:rgba(15,23,42,.85);border:1px solid var(--border-subtle);transition:all 0.3s ease;">
                    <strong style="color:var(--text-main);font-size:1rem;display:block;margin-bottom:.75rem;">¿MiEntreno es solo para running?</strong>
                    <p style="color:var(--text-muted);line-height:1.6;">
                        Por ahora el foco es 100% running, pero la base está pensada para ampliar a otras disciplinas.
                    </p>
                </div>
                <div style="padding:1.5rem;border-radius:1rem;background:rgba(15,23,42,.85);border:1px solid var(--border-subtle);transition:all 0.3s ease;">
                    <strong style="color:var(--text-main);font-size:1rem;display:block;margin-bottom:.75rem;">¿Hay roles diferentes?</strong>
                    <p style="color:var(--text-muted);line-height:1.6;">
                        Sí, la estructura contempla rol alumno y rol coach, con paneles distintos para cada uno.
                    </p>
                </div>
                <div style="padding:1.5rem;border-radius:1rem;background:rgba(15,23,42,.85);border:1px solid var(--border-subtle);transition:all 0.3s ease;">
                    <strong style="color:var(--text-main);font-size:1rem;display:block;margin-bottom:.75rem;">¿Puedo importar datos de otras apps?</strong>
                    <p style="color:var(--text-muted);line-height:1.6;">
                        La plataforma está preparada para futuras integraciones con Strava, Garmin y otros servicios populares.
                    </p>
                </div>
            </div>
        </section>
    </main>

    <footer class="footer">
        <div class="footer-content">
            <div class="footer-section">
                <h3>MiEntreno</h3>
                <p style="color:var(--text-muted);margin-bottom:1rem;line-height:1.6;">
                    La plataforma de registro de entrenamientos pensada por y para runners que también son developers.
                </p>
                <div style="display:flex;gap:1rem;margin-top:1rem;">
                    <a href="#" style="width:32px;height:32px;border-radius:50%;background:rgba(148,163,184,0.1);display:flex;align-items:center;justify-content:center;border:1px solid rgba(148,163,184,0.2);transition:all 0.3s ease;">
                        <span style="font-size:1rem;">𝕏</span>
                    </a>
                    <a href="#" style="width:32px;height:32px;border-radius:50%;background:rgba(148,163,184,0.1);display:flex;align-items:center;justify-content:center;border:1px solid rgba(148,163,184,0.2);transition:all 0.3s ease;">
                        <span style="font-size:1rem;">in</span>
                    </a>
                    <a href="https://github.com" target="_blank" style="width:32px;height:32px;border-radius:50%;background:rgba(148,163,184,0.1);display:flex;align-items:center;justify-content:center;border:1px solid rgba(148,163,184,0.2);transition:all 0.3s ease;">
                        <span style="font-size:1rem;">💻</span>
                    </a>
                </div>
            </div>
            <div class="footer-section">
                <h3>Platform</h3>
                <ul>
                    <li><a href="#features">Features</a></li>
                    <li><a href="#coaches">For Coaches</a></li>
                    <li><a href="#faq">FAQ</a></li>
                    <li><a href="{{ route('register') }}">Sign Up</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h3>Resources</h3>
                <ul>
                    <li><a href="#">Documentation</a></li>
                    <li><a href="#">API Reference</a></li>
                    <li><a href="#">Privacy Policy</a></li>
                    <li><a href="#">Terms of Service</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <span>© 2025 MiEntreno · Más que números, tu historia running</span>
            <span>Built with ❤️ by <a href="https://srojasweb.dev" target="_blank" rel="noopener noreferrer" style="color:var(--accent-secondary);font-weight:500;">srojasweb.dev</a></span>
        </div>
    </footer>
</div>

<script>
// Add scroll effect to navbar
window.addEventListener('scroll', function() {
    const nav = document.querySelector('.nav');
    if (window.scrollY > 50) {
        nav.classList.add('scrolled');
    } else {
        nav.classList.remove('scrolled');
    }
});
</script>
</body>
</html>
