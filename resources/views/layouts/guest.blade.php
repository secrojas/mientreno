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

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<div class="flex flex-col min-h-screen">
    <!-- Header / Nav -->
    <header class="sticky top-0 z-10 flex items-center justify-between px-4 sm:px-6 py-4
                   border-b border-border-subtle backdrop-blur-2xl bg-bg-main/85">
        <a href="/" class="flex items-center gap-2">
            <img src="{{ asset('images/logo-horizontal.svg') }}" alt="MiEntreno" class="h-8 sm:h-9 w-auto">
        </a>

        <nav class="flex items-center gap-3 sm:gap-4 text-sm">
            <a href="/#features" class="hidden sm:block text-text-muted hover:text-text-main transition-colors">Features</a>
            <a href="/#coaches" class="hidden sm:block text-text-muted hover:text-text-main transition-colors">Coaches</a>
            <a href="/#faq" class="hidden sm:block text-text-muted hover:text-text-main transition-colors">FAQ</a>
            <a href="{{ route('login') }}" class="btn-ghost">Login</a>
            <a href="{{ route('register') }}" class="btn-primary">Create account</a>
        </nav>
    </header>

    <!-- Main content -->
    <main class="flex-1 flex items-center justify-center px-4 sm:px-6 py-6 sm:py-10">
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="border-t border-border-subtle px-4 sm:px-6 py-4
                   text-xs sm:text-sm text-text-muted
                   flex flex-col sm:flex-row justify-between gap-3 sm:gap-4">
        <span>© 2025 MiEntreno · Más que números, tu historia running</span>
        <span>By <a href="https://srojasweb.dev" target="_blank" rel="noopener noreferrer" class="hover:text-accent-secondary transition-colors">srojasweb.dev</a></span>
    </footer>
</div>
</body>
</html>
