<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} · MiEntreno</title>

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
            --bg-sidebar: #050814;
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
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        /* Layout */

        .dashboard-shell {
            display: grid;
            grid-template-columns: 260px minmax(0, 1fr);
            min-height: 100vh;
        }

        @media (max-width: 900px) {
            .dashboard-shell {
                grid-template-columns: 80px minmax(0, 1fr);
            }

            .sidebar-expanded-text {
                display: none;
            }

            .sidebar-header-title {
                display: none;
            }

            .sidebar-footer-user {
                display: none;
            }

            .sidebar-nav-link {
                justify-content: center;
            }
        }

        /* Sidebar */

        .sidebar {
            background: var(--bg-sidebar);
            border-right: 1px solid var(--border-subtle);
            display: flex;
            flex-direction: column;
            padding: 1rem 1rem 1rem 1rem;
        }

        .sidebar-header {
            display: flex;
            align-items: center;
            gap: .5rem;
            padding: .25rem .25rem .75rem;
            border-bottom: 1px solid rgba(15, 23, 42, 0.9);
            margin-bottom: 1rem;
        }

        .logo-dev {
            font-size: .8rem;
            font-family: 'Space Grotesk', monospace;
            color: var(--accent-secondary);
        }

        .logo-mark {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: .2rem .55rem;
            border-radius: 999px;
            background: linear-gradient(135deg, var(--accent-primary), #FF4FA3);
            font-family: 'Space Grotesk', system-ui, sans-serif;
            font-size: .7rem;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .logo-text {
            font-family: 'Space Grotesk', system-ui, sans-serif;
            font-weight: 600;
            letter-spacing: .12em;
            text-transform: uppercase;
            font-size: .8rem;
        }

        .sidebar-nav {
            flex: 1;
            margin-top: .5rem;
            display: flex;
            flex-direction: column;
            gap: .25rem;
        }

        .sidebar-section-label {
            font-size: .7rem;
            text-transform: uppercase;
            letter-spacing: .14em;
            color: var(--text-muted);
            padding: .75rem .4rem .25rem;
        }

        .sidebar-nav-link {
            display: flex;
            align-items: center;
            gap: .6rem;
            padding: .55rem .6rem;
            border-radius: .7rem;
            font-size: .86rem;
            color: var(--text-muted);
            cursor: pointer;
            transition: background .15s ease-out, color .15s ease-out, transform .1s ease-out;
        }

        .sidebar-nav-link svg {
            flex-shrink: 0;
            width: 18px;
            height: 18px;
        }

        .sidebar-nav-link:hover {
            background: rgba(15, 23, 42, 0.95);
            color: var(--text-main);
            transform: translateY(-1px);
        }

        .sidebar-nav-link.active {
            background: linear-gradient(135deg, rgba(255, 59, 92, 0.16), rgba(255, 79, 163, 0.08));
            color: var(--text-main);
            border: 1px solid rgba(255, 59, 92, 0.5);
        }

        .sidebar-footer {
            border-top: 1px solid rgba(15, 23, 42, 0.9);
            padding-top: .75rem;
            margin-top: .5rem;
            display: flex;
            flex-direction: column;
            gap: .5rem;
            font-size: .8rem;
            color: var(--text-muted);
        }

        .sidebar-footer-user {
            display: flex;
            align-items: center;
            gap: .6rem;
        }

        .avatar {
            width: 26px;
            height: 26px;
            border-radius: 999px;
            background: radial-gradient(circle at 20% 20%, #FF4FA3, #05060A);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: .8rem;
            font-weight: 600;
        }

        .sidebar-footer-actions a {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            cursor: pointer;
        }

        .sidebar-footer-actions svg {
            width: 14px;
            height: 14px;
        }

        /* Main */

        .main {
            padding: 1.75rem 1.75rem 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            max-width: 1500px;
            width: 100%;
            margin: 0 auto;
        }

        @media (max-width: 600px) {
            .main {
                padding: 1.25rem 1rem 1rem;
            }
        }
    </style>
</head>
<body>
<div class="dashboard-shell">
    <!-- SIDEBAR -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <a href="{{ route('dashboard') }}" style="display:flex;align-items:center;width:100%;">
                <img src="{{ asset('images/logo.png') }}" alt="MiEntreno" style="height:42px;width:auto;">
            </a>
        </div>

        <nav class="sidebar-nav">
            <div class="sidebar-section-label">Panel</div>

            <a href="{{ route('dashboard') }}" class="sidebar-nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <!-- Dashboard icon -->
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="7" height="7" rx="1.5"></rect>
                    <rect x="14" y="3" width="7" height="7" rx="1.5"></rect>
                    <rect x="3" y="14" width="7" height="7" rx="1.5"></rect>
                    <rect x="14" y="14" width="7" height="7" rx="1.5"></rect>
                </svg>
                <span class="sidebar-expanded-text">Dashboard</span>
            </a>

            <a href="{{ route('workouts.index') }}" class="sidebar-nav-link {{ request()->routeIs('workouts.*') ? 'active' : '' }}">
                <!-- Workouts icon (line chart) -->
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 19L9 10L13 15L20 5"></path>
                    <path d="M20 10V5H15"></path>
                </svg>
                <span class="sidebar-expanded-text">Entrenamientos</span>
            </a>

            <a href="{{ route('races.index') }}" class="sidebar-nav-link {{ request()->routeIs('races.*') ? 'active' : '' }}">
                <!-- Races icon (flag) -->
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 3v18"></path>
                    <path d="M6 4h9l-1.5 3L18 10H6z"></path>
                </svg>
                <span class="sidebar-expanded-text">Carreras</span>
            </a>

            <a href="{{ route('goals.index') }}" class="sidebar-nav-link {{ request()->routeIs('goals.*') ? 'active' : '' }}">
                <!-- Goals icon (target) -->
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="8"></circle>
                    <circle cx="12" cy="12" r="4"></circle>
                    <path d="M12 8v2"></path>
                    <path d="M12 14v2"></path>
                </svg>
                <span class="sidebar-expanded-text">Objetivos</span>
            </a>

            <a href="{{ route('reports.index') }}" class="sidebar-nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                <!-- Reports icon (file/document with chart) -->
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <path d="M14 2v6h6"></path>
                    <path d="M8 13h2"></path>
                    <path d="M8 17h8"></path>
                    <path d="M16 13l-4 4"></path>
                </svg>
                <span class="sidebar-expanded-text">Reportes</span>
            </a>

            @if(auth()->user()->role === 'coach' || auth()->user()->role === 'admin')
            <div class="sidebar-section-label">Coaching</div>

            <a href="#" class="sidebar-nav-link">
                <!-- Groups icon (users/group) -->
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="9" cy="9" r="2.5"></circle>
                    <circle cx="17" cy="9" r="2.5"></circle>
                    <path d="M4 19c.6-2.2 2.6-3.5 5-3.5s4.4 1.3 5 3.5"></path>
                    <path d="M12 15.5c.6-2.2 2.6-3.5 5-3.5 2.4 0 4.4 1.3 5 3.5"></path>
                </svg>
                <span class="sidebar-expanded-text">Grupos</span>
            </a>

            <a href="#" class="sidebar-nav-link">
                <!-- Coach icon (whistle-ish) -->
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="10" width="9" height="5" rx="1.5"></rect>
                    <path d="M12 12h3.5a3.5 3.5 0 1 1-2.47 6"></path>
                    <circle cx="17.5" cy="10" r="1.2"></circle>
                </svg>
                <span class="sidebar-expanded-text">Alumnos</span>
            </a>
            @endif

            <div class="sidebar-section-label">Sistema</div>

            <a href="#" class="sidebar-nav-link">
                <!-- Settings icon -->
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="3"></circle>
                    <path d="M19.4 15a1.7 1.7 0 0 0 .34 1.87l.06.06a1.8 1.8 0 0 1-2.54 2.54l-.06-.06A1.7 1.7 0 0 0 15 19.4a1.7 1.7 0 0 0-1 .31 1.7 1.7 0 0 0-.8 1.44V21a1.8 1.8 0 0 1-3.6 0v-.15a1.7 1.7 0 0 0-.8-1.44 1.7 1.7 0 0 0-1-.31 1.7 1.7 0 0 0-1.87.34l-.06.06a1.8 1.8 0 0 1-2.54-2.54l.06-.06A1.7 1.7 0 0 0 4.6 15a1.7 1.7 0 0 0-.31-1 1.7 1.7 0 0 0-1.44-.8H3a1.8 1.8 0 0 1 0-3.6h.15a1.7 1.7 0 0 0 1.44-.8 1.7 1.7 0 0 0 .31-1 1.7 1.7 0 0 0-.34-1.87l-.06-.06a1.8 1.8 0 0 1 2.54-2.54l.06.06A1.7 1.7 0 0 0 9 4.6a1.7 1.7 0 0 0 1-.31 1.7 1.7 0 0 0 .8-1.44V3a1.8 1.8 0 0 1 3.6 0v.15a1.7 1.7 0 0 0 .8 1.44 1.7 1.7 0 0 0 1 .31 1.7 1.7 0 0 0 1.87-.34l.06-.06a1.8 1.8 0 0 1 2.54 2.54l-.06.06A1.7 1.7 0 0 0 19.4 9a1.7 1.7 0 0 0 .31 1 1.7 1.7 0 0 0 1.44.8H21a1.8 1.8 0 0 1 0 3.6h-.15a1.7 1.7 0 0 0-1.44.8 1.7 1.7 0 0 0-.01 1.8z"></path>
                </svg>
                <span class="sidebar-expanded-text">Configuración</span>
            </a>

        </nav>

        <div class="sidebar-footer">
            <div class="sidebar-footer-user">
                <div class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</div>
                <div>
                    <div style="font-size:.8rem;">{{ auth()->user()->name }}</div>
                    <div style="font-size:.75rem;color:var(--text-muted);">{{ ucfirst(auth()->user()->role) }}</div>
                </div>
            </div>

            <div class="sidebar-footer-actions">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" style="background:none;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:.35rem;color:inherit;padding:0;">
                        <!-- Logout icon -->
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 21H6a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h3"></path>
                            <path d="M16 17l5-5-5-5"></path>
                            <path d="M21 12H9"></path>
                        </svg>
                        <span>Salir</span>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- MAIN -->
    <main class="main">
        {{ $slot }}
    </main>
</div>
</body>
</html>
