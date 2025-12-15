<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Reporte Compartido' }} · MiEntreno</title>

    <!-- Favicons -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon-16x16.png') }}">

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
            --primary: #FF3B5C;
            --success: #2DE38E;
            --danger: #ef4444;
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

        .public-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem 1.5rem;
        }

        .public-header {
            text-align: center;
            padding: 2rem 0 1rem;
            border-bottom: 1px solid var(--border-subtle);
            margin-bottom: 2rem;
        }

        .public-logo {
            height: 40px;
            margin-bottom: 1rem;
        }

        .public-title {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .public-subtitle {
            color: var(--text-muted);
            font-size: 0.95rem;
        }

        .public-notice {
            background: rgba(255, 59, 92, 0.1);
            border-left: 3px solid var(--accent-primary);
            padding: 1rem 1.25rem;
            border-radius: 0.5rem;
            margin-bottom: 2rem;
            font-size: 0.9rem;
        }

        .public-notice strong {
            color: var(--accent-primary);
        }

        .public-footer {
            text-align: center;
            padding: 2rem 0;
            margin-top: 3rem;
            border-top: 1px solid var(--border-subtle);
            color: var(--text-muted);
            font-size: 0.85rem;
        }

        .public-footer-logo {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--accent-secondary);
            margin-bottom: 0.5rem;
        }

        .public-footer-url {
            color: var(--accent-primary);
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="public-container">
        <!-- Header -->
        <div class="public-header">
            <img src="{{ asset('images/logo.png') }}" alt="MiEntreno" class="public-logo">
            <h1 class="public-title">{{ $title ?? 'Reporte Compartido' }}</h1>
            <p class="public-subtitle">{{ $subtitle ?? 'Reporte de entrenamiento compartido' }}</p>
        </div>

        <!-- Main Content -->
        {{ $slot }}

        <!-- Footer -->
        <div class="public-footer">
            <div class="public-footer-logo">MIENTRENO</div>
            <div>
                Compartido desde <span class="public-footer-url">mientreno.app</span>
            </div>
        </div>
    </div>
</body>
</html>
