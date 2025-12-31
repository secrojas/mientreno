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

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<div x-data="mobileSidebar" class="min-h-screen">
    <!-- Mobile Header (visible only on <md) -->
    <header class="md:hidden sticky top-0 z-40 flex items-center justify-between
                   px-4 py-3 bg-bg-sidebar border-b border-border-subtle">
        <button @click="toggle" type="button" class="p-2 -ml-2 text-text-muted hover:text-text-main transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>

        <a href="{{ businessRoute('dashboard') }}" class="flex items-center">
            <img src="{{ asset('images/logo-horizontal.svg') }}" alt="MiEntreno" class="h-8 w-auto">
        </a>

        <div class="w-10"></div>
    </header>

    <!-- Backdrop overlay (mobile only) -->
    <div x-show="open"
         @click="close"
         x-transition:enter="transition-opacity ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="md:hidden fixed inset-0 bg-black/50 z-40"
         style="display: none;">
    </div>

    <div class="md:grid md:grid-cols-[260px_1fr] min-h-screen">
        <!-- Sidebar -->
        <aside x-bind:class="open ? 'translate-x-0' : '-translate-x-full'"
               class="fixed md:static inset-y-0 left-0 z-50 w-64 md:w-auto
                      transform md:transform-none transition-transform duration-200 ease-out
                      bg-bg-sidebar border-r border-border-subtle
                      flex flex-col p-4">

            <!-- Sidebar Header -->
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-border-subtle border-opacity-50">
                <a href="{{ businessRoute('dashboard') }}" class="flex items-center">
                    <img src="{{ asset('images/logo-horizontal.svg') }}" alt="MiEntreno" class="h-10 w-auto">
                </a>

                <!-- Close button (mobile only) -->
                <button @click="close" type="button" class="md:hidden p-2 -mr-2 text-text-muted hover:text-text-main">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Sidebar Navigation -->
            <nav class="flex-1 flex flex-col gap-1">
                <div class="text-xs uppercase tracking-wider text-text-muted px-2 py-2 mb-1">Panel</div>

                @if(auth()->user()->role === 'coach' || auth()->user()->role === 'admin')
                    @if(auth()->user()->business_id && auth()->user()->business)
                        <a href="{{ businessRoute('coach.dashboard') }}"
                           class="sidebar-link {{ request()->routeIs('coach.dashboard') || request()->routeIs('business.coach.dashboard') ? 'active' : '' }}"
                           @click="close">
                            <svg class="w-[18px] h-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="3" width="7" height="7" rx="1.5"></rect>
                                <rect x="14" y="3" width="7" height="7" rx="1.5"></rect>
                                <rect x="3" y="14" width="7" height="7" rx="1.5"></rect>
                                <rect x="14" y="14" width="7" height="7" rx="1.5"></rect>
                            </svg>
                            <span>Dashboard Coach</span>
                        </a>
                    @else
                        <a href="{{ route('coach.business.create') }}"
                           class="sidebar-link {{ request()->routeIs('coach.business.create') ? 'active' : '' }}"
                           @click="close">
                            <svg class="w-[18px] h-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                                <path d="M16 3h-8a2 2 0 0 0-2 2v2h12V5a2 2 0 0 0-2-2z"></path>
                            </svg>
                            <span>Crear Mi Negocio</span>
                        </a>
                    @endif
                @else
                    <a href="{{ businessRoute('dashboard') }}"
                       class="sidebar-link {{ request()->routeIs('dashboard') || request()->routeIs('business.dashboard') ? 'active' : '' }}"
                       @click="close">
                        <svg class="w-[18px] h-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="7" height="7" rx="1.5"></rect>
                            <rect x="14" y="3" width="7" height="7" rx="1.5"></rect>
                            <rect x="3" y="14" width="7" height="7" rx="1.5"></rect>
                            <rect x="14" y="14" width="7" height="7" rx="1.5"></rect>
                        </svg>
                        <span>Dashboard</span>
                    </a>

                    <a href="{{ businessRoute('workouts.index') }}"
                       class="sidebar-link {{ request()->routeIs('workouts.*') || request()->routeIs('business.workouts.*') ? 'active' : '' }}"
                       @click="close">
                        <svg class="w-[18px] h-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 19L9 10L13 15L20 5"></path>
                            <path d="M20 10V5H15"></path>
                        </svg>
                        <span>Entrenamientos</span>
                    </a>

                    <a href="{{ businessRoute('races.index') }}"
                       class="sidebar-link {{ request()->routeIs('races.*') || request()->routeIs('business.races.*') ? 'active' : '' }}"
                       @click="close">
                        <svg class="w-[18px] h-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 3v18"></path>
                            <path d="M6 4h9l-1.5 3L18 10H6z"></path>
                        </svg>
                        <span>Carreras</span>
                    </a>

                    <a href="{{ businessRoute('goals.index') }}"
                       class="sidebar-link {{ request()->routeIs('goals.*') || request()->routeIs('business.goals.*') ? 'active' : '' }}"
                       @click="close">
                        <svg class="w-[18px] h-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="8"></circle>
                            <circle cx="12" cy="12" r="4"></circle>
                            <path d="M12 8v2"></path>
                            <path d="M12 14v2"></path>
                        </svg>
                        <span>Objetivos</span>
                    </a>

                    <a href="{{ businessRoute('reports.index') }}"
                       class="sidebar-link {{ request()->routeIs('reports.*') || request()->routeIs('business.reports.*') ? 'active' : '' }}"
                       @click="close">
                        <svg class="w-[18px] h-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <path d="M14 2v6h6"></path>
                            <path d="M8 13h2"></path>
                            <path d="M8 17h8"></path>
                            <path d="M16 13l-4 4"></path>
                        </svg>
                        <span>Reportes</span>
                    </a>
                @endif

                @if(auth()->user()->role === 'coach' || auth()->user()->role === 'admin')
                    @if(auth()->user()->business_id && auth()->user()->business)
                        <div class="text-xs uppercase tracking-wider text-text-muted px-2 py-2 mt-3 mb-1">Coaching</div>

                        <a href="{{ businessRoute('coach.business.show') }}"
                           class="sidebar-link {{ request()->routeIs('coach.business.*') || request()->routeIs('business.coach.business.*') ? 'active' : '' }}"
                           @click="close">
                            <svg class="w-[18px] h-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                                <path d="M16 3h-8a2 2 0 0 0-2 2v2h12V5a2 2 0 0 0-2-2z"></path>
                            </svg>
                            <span>Mi Negocio</span>
                        </a>

                        <a href="{{ businessRoute('coach.groups.index') }}"
                           class="sidebar-link {{ request()->routeIs('coach.groups.*') || request()->routeIs('business.coach.groups.*') ? 'active' : '' }}"
                           @click="close">
                            <svg class="w-[18px] h-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="9" cy="9" r="2.5"></circle>
                                <circle cx="17" cy="9" r="2.5"></circle>
                                <path d="M4 19c.6-2.2 2.6-3.5 5-3.5s4.4 1.3 5 3.5"></path>
                                <path d="M12 15.5c.6-2.2 2.6-3.5 5-3.5 2.4 0 4.4 1.3 5 3.5"></path>
                            </svg>
                            <span>Grupos</span>
                        </a>

                        <a href="#" class="sidebar-link" @click="close">
                            <svg class="w-[18px] h-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                            <span>Alumnos</span>
                        </a>

                        <a href="{{ businessRoute('coach.subscriptions.index') }}"
                           class="sidebar-link {{ request()->routeIs('coach.subscriptions.*') || request()->routeIs('business.coach.subscriptions.*') ? 'active' : '' }}"
                           @click="close">
                            <svg class="w-[18px] h-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="2" y="5" width="20" height="14" rx="2"></rect>
                                <path d="M2 10h20"></path>
                            </svg>
                            <span>Suscripción</span>
                        </a>
                    @endif
                @endif

                <div class="text-xs uppercase tracking-wider text-text-muted px-2 py-2 mt-3 mb-1">Cuenta</div>

                <a href="{{ route('profile.edit') }}"
                   class="sidebar-link {{ request()->routeIs('profile.*') ? 'active' : '' }}"
                   @click="close">
                    <svg class="w-[18px] h-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                    <span>Mi Perfil</span>
                </a>

                <form method="POST" action="{{ route('logout') }}" class="mt-3">
                    @csrf
                    <button type="submit" class="sidebar-link w-full">
                        <svg class="w-[18px] h-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 21H6a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h3"></path>
                            <path d="M16 17l5-5-5-5"></path>
                            <path d="M21 12H9"></path>
                        </svg>
                        <span>Salir</span>
                    </button>
                </form>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="p-responsive flex flex-col gap-responsive max-w-[1600px] w-full mx-auto">
            {{ $slot }}
        </main>
    </div>
</div>
</body>
</html>
