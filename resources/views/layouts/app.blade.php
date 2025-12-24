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
            max-width: 1600px;
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
            <a href="{{ businessRoute('dashboard') }}" style="display:flex;align-items:center;width:100%;">
                <img src="{{ asset('images/logo-horizontal.svg') }}" alt="MiEntreno" style="height:42px;width:auto;">
            </a>
        </div>

        <nav class="sidebar-nav">
            <div class="sidebar-section-label">Panel</div>

            @if(auth()->user()->role === 'coach' || auth()->user()->role === 'admin')
                @if(auth()->user()->business_id && auth()->user()->business)
                    <a href="{{ businessRoute('coach.dashboard') }}" class="sidebar-nav-link {{ request()->routeIs('coach.dashboard') || request()->routeIs('business.coach.dashboard') ? 'active' : '' }}">
                        <!-- Dashboard icon -->
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="7" height="7" rx="1.5"></rect>
                            <rect x="14" y="3" width="7" height="7" rx="1.5"></rect>
                            <rect x="3" y="14" width="7" height="7" rx="1.5"></rect>
                            <rect x="14" y="14" width="7" height="7" rx="1.5"></rect>
                        </svg>
                        <span class="sidebar-expanded-text">Dashboard Coach</span>
                    </a>
                @else
                    <a href="{{ route('coach.business.create') }}" class="sidebar-nav-link {{ request()->routeIs('coach.business.create') ? 'active' : '' }}">
                        <!-- Business icon -->
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                            <path d="M16 3h-8a2 2 0 0 0-2 2v2h12V5a2 2 0 0 0-2-2z"></path>
                        </svg>
                        <span class="sidebar-expanded-text">Crear Mi Negocio</span>
                    </a>
                @endif
            @else
                <a href="{{ businessRoute('dashboard') }}" class="sidebar-nav-link {{ request()->routeIs('dashboard') || request()->routeIs('business.dashboard') ? 'active' : '' }}">
                    <!-- Dashboard icon -->
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="7" height="7" rx="1.5"></rect>
                        <rect x="14" y="3" width="7" height="7" rx="1.5"></rect>
                        <rect x="3" y="14" width="7" height="7" rx="1.5"></rect>
                        <rect x="14" y="14" width="7" height="7" rx="1.5"></rect>
                    </svg>
                    <span class="sidebar-expanded-text">Dashboard</span>
                </a>

                <a href="{{ businessRoute('workouts.index') }}" class="sidebar-nav-link {{ request()->routeIs('workouts.*') || request()->routeIs('business.workouts.*') ? 'active' : '' }}">
                    <!-- Workouts icon (line chart) -->
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 19L9 10L13 15L20 5"></path>
                        <path d="M20 10V5H15"></path>
                    </svg>
                    <span class="sidebar-expanded-text">Entrenamientos</span>
                </a>

                <a href="{{ businessRoute('races.index') }}" class="sidebar-nav-link {{ request()->routeIs('races.*') || request()->routeIs('business.races.*') ? 'active' : '' }}">
                    <!-- Races icon (flag) -->
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M5 3v18"></path>
                        <path d="M6 4h9l-1.5 3L18 10H6z"></path>
                    </svg>
                    <span class="sidebar-expanded-text">Carreras</span>
                </a>

                <a href="{{ businessRoute('goals.index') }}" class="sidebar-nav-link {{ request()->routeIs('goals.*') || request()->routeIs('business.goals.*') ? 'active' : '' }}">
                    <!-- Goals icon (target) -->
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="8"></circle>
                        <circle cx="12" cy="12" r="4"></circle>
                        <path d="M12 8v2"></path>
                        <path d="M12 14v2"></path>
                    </svg>
                    <span class="sidebar-expanded-text">Objetivos</span>
                </a>

                <a href="{{ businessRoute('reports.index') }}" class="sidebar-nav-link {{ request()->routeIs('reports.*') || request()->routeIs('business.reports.*') ? 'active' : '' }}">
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
            @endif

            @if(auth()->user()->role === 'coach' || auth()->user()->role === 'admin')
            <div class="sidebar-section-label">Coaching</div>

            <a href="{{ businessRoute('coach.business.show') }}" class="sidebar-nav-link {{ request()->routeIs('coach.business.*') || request()->routeIs('business.coach.business.*') ? 'active' : '' }}">
                <!-- Business icon (briefcase) -->
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                    <path d="M16 3h-8a2 2 0 0 0-2 2v2h12V5a2 2 0 0 0-2-2z"></path>
                </svg>
                <span class="sidebar-expanded-text">Mi Negocio</span>
            </a>

            <a href="{{ businessRoute('coach.groups.index') }}" class="sidebar-nav-link {{ request()->routeIs('coach.groups.*') || request()->routeIs('business.coach.groups.*') ? 'active' : '' }}">
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
                <!-- Students icon (graduation cap) -->
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
                <span class="sidebar-expanded-text">Alumnos</span>
            </a>

            <a href="{{ businessRoute('coach.subscriptions.index') }}" class="sidebar-nav-link {{ request()->routeIs('coach.subscriptions.*') || request()->routeIs('business.coach.subscriptions.*') ? 'active' : '' }}">
                <!-- Subscription icon (credit card) -->
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="2" y="5" width="20" height="14" rx="2"></rect>
                    <path d="M2 10h20"></path>
                </svg>
                <span class="sidebar-expanded-text">Suscripción</span>
            </a>
            @endif

            <div class="sidebar-section-label">Cuenta</div>

            <a href="{{ route('profile.edit') }}" class="sidebar-nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                <!-- Profile icon -->
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
                <span class="sidebar-expanded-text">Mi Perfil</span>
            </a>

            <form method="POST" action="{{ route('logout') }}" style="display:inline;margin-top:.75rem;">
                @csrf
                <button type="submit" class="sidebar-nav-link" style="width:100%;text-align:left;background:none;border:none;cursor:pointer;padding:0;">
                    <!-- Logout icon -->
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H6a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h3"></path>
                        <path d="M16 17l5-5-5-5"></path>
                        <path d="M21 12H9"></path>
                    </svg>
                    <span class="sidebar-expanded-text">Salir</span>
                </button>
            </form>

        </nav>
    </aside>

    <!-- MAIN -->
    <main class="main">
        {{ $slot }}
    </main>
</div>
</body>
</html>
