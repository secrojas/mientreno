<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MiEntreno · Tu running, tu salud y tus zapatillas en un solo lugar</title>
    <meta name="description" content="Registrá tus entrenamientos, llevá tu historia médica deportiva y controlá los kilómetros de tus zapatillas. Reportes compartibles para tu coach y tu médico.">
    <meta property="og:title" content="MiEntreno · Entrená. Cuidate. Rendí.">
    <meta property="og:description" content="Entrenamientos, salud médica y zapatillas en una sola app pensada para runners.">
    <meta property="og:type" content="website">
    <meta name="theme-color" content="#05060A">

    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg-main: #05060A;
            --bg-card: #0B0C12;
            --bg-elevated: #0F1119;
            --border-subtle: #161B26;
            --border-strong: #232A3A;
            --text-main: #F9FAFB;
            --text-muted: #9CA3AF;
            --text-dim: #6B7280;
            --accent-primary: #FF3B5C;
            --accent-pink: #FF4FA3;
            --accent-secondary: #2DE38E;
            --accent-blue: #60A5FA;
            --accent-amber: #F59E0B;
            --radius-lg: 1.25rem;
            --radius-md: .9rem;
            --radius-sm: .6rem;
            --container: 1180px;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html { scroll-behavior: smooth; scroll-padding-top: 5rem; }

        body {
            font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--bg-main);
            color: var(--text-main);
            line-height: 1.6;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }

        a { color: inherit; text-decoration: none; }
        img, svg { display: block; }

        .container { width: 100%; max-width: var(--container); margin: 0 auto; padding: 0 1.25rem; }

        .display { font-family: 'Space Grotesk', system-ui, sans-serif; letter-spacing: -.02em; }
        .gradient-text {
            background: linear-gradient(120deg, var(--accent-primary) 0%, var(--accent-pink) 45%, var(--accent-secondary) 100%);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        .text-green { color: var(--accent-secondary); }
        .text-red { color: var(--accent-primary); }
        .text-muted { color: var(--text-muted); }

        /* ---------- Background ---------- */
        .bg-glow {
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 0;
            background:
                radial-gradient(900px 600px at 85% -10%, rgba(255, 59, 92, .16), transparent 60%),
                radial-gradient(700px 500px at -10% 30%, rgba(45, 227, 142, .10), transparent 60%),
                radial-gradient(circle at top, #0E1220 0, var(--bg-main) 55%);
        }
        .bg-grid {
            position: absolute;
            inset: 0;
            pointer-events: none;
            background-image:
                linear-gradient(rgba(255, 255, 255, .035) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, .035) 1px, transparent 1px);
            background-size: 56px 56px;
            mask-image: radial-gradient(ellipse at 50% 0%, #000 20%, transparent 70%);
            -webkit-mask-image: radial-gradient(ellipse at 50% 0%, #000 20%, transparent 70%);
        }
        .page { position: relative; z-index: 1; }

        /* ---------- Buttons ---------- */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
            padding: .8rem 1.4rem;
            border-radius: 999px;
            font-size: .92rem;
            font-weight: 600;
            border: 1px solid transparent;
            cursor: pointer;
            transition: transform .2s ease, box-shadow .2s ease, background .2s ease, border-color .2s ease;
            white-space: nowrap;
        }
        .btn:focus-visible { outline: 2px solid var(--accent-secondary); outline-offset: 3px; }
        .btn-primary {
            background: linear-gradient(135deg, var(--accent-primary), var(--accent-pink));
            color: #fff;
            box-shadow: 0 10px 30px -8px rgba(255, 59, 92, .6);
        }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 16px 40px -8px rgba(255, 59, 92, .7); }
        .btn-outline { border-color: var(--border-strong); background: rgba(255, 255, 255, .02); color: var(--text-main); }
        .btn-outline:hover { border-color: var(--accent-secondary); color: var(--accent-secondary); }
        .btn-ghost { color: var(--text-muted); padding-left: .9rem; padding-right: .9rem; }
        .btn-ghost:hover { color: var(--text-main); }
        .btn-lg { padding: 1rem 1.8rem; font-size: 1rem; }

        /* ---------- Nav ---------- */
        .nav {
            position: sticky;
            top: 0;
            z-index: 50;
            border-bottom: 1px solid transparent;
            transition: background .3s ease, border-color .3s ease;
        }
        .nav.scrolled { background: rgba(5, 6, 10, .8); backdrop-filter: blur(18px) saturate(160%); -webkit-backdrop-filter: blur(18px) saturate(160%); border-color: var(--border-subtle); }
        .nav-inner { display: flex; align-items: center; justify-content: space-between; gap: 1rem; height: 4.5rem; }
        .nav-links { display: flex; align-items: center; gap: 1.75rem; font-size: .9rem; color: var(--text-muted); }
        .nav-links a:hover { color: var(--text-main); }
        .nav-actions { display: flex; align-items: center; gap: .5rem; }
        .nav-toggle { display: none; background: none; border: 1px solid var(--border-strong); color: var(--text-main); border-radius: .6rem; width: 2.5rem; height: 2.5rem; align-items: center; justify-content: center; cursor: pointer; }
        .mobile-menu { display: none; }

        /* ---------- Hero ---------- */
        .hero { padding: 4.5rem 0 3rem; position: relative; }
        .hero-grid { display: grid; grid-template-columns: 1.05fr .95fr; gap: 3.5rem; align-items: center; }
        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: .6rem;
            padding: .35rem .9rem .35rem .4rem;
            border-radius: 999px;
            border: 1px solid var(--border-strong);
            background: rgba(255, 255, 255, .03);
            font-size: .8rem;
            color: var(--text-muted);
            margin-bottom: 1.5rem;
        }
        .eyebrow b {
            background: rgba(45, 227, 142, .12);
            color: var(--accent-secondary);
            border-radius: 999px;
            padding: .15rem .55rem;
            font-size: .7rem;
            letter-spacing: .06em;
            text-transform: uppercase;
        }
        .hero h1 { font-size: clamp(2.5rem, 5.6vw, 4.4rem); line-height: 1.02; font-weight: 700; margin-bottom: 1.5rem; }
        .hero-lead { font-size: 1.15rem; color: var(--text-muted); max-width: 34rem; margin-bottom: 2.25rem; }
        .hero-ctas { display: flex; flex-wrap: wrap; gap: .85rem; margin-bottom: 2.5rem; }
        .hero-proof { display: flex; flex-wrap: wrap; gap: 1.75rem; font-size: .85rem; color: var(--text-muted); }
        .hero-proof div { display: flex; align-items: center; gap: .5rem; }
        .hero-proof svg { color: var(--accent-secondary); flex-shrink: 0; }

        /* Hero visual */
        .hero-visual { position: relative; min-height: 500px; }
        .mock {
            background: linear-gradient(180deg, rgba(20, 24, 36, .95), rgba(11, 12, 18, .95));
            border: 1px solid var(--border-strong);
            border-radius: var(--radius-lg);
            box-shadow: 0 40px 80px -30px rgba(0, 0, 0, .8), 0 0 0 1px rgba(255, 255, 255, .02) inset;
        }
        .mock-main { position: absolute; inset: 5rem 3rem 6.5rem 0; padding: 1.4rem; }
        .mock-head { display: flex; align-items: center; gap: .75rem; margin-bottom: 1.1rem; }
        .mock-title { font-size: .8rem; color: var(--text-muted); }
        .mock-chip { font-size: .72rem; padding: .2rem .6rem; border-radius: 999px; background: rgba(45, 227, 142, .12); color: var(--accent-secondary); font-weight: 600; }
        .kpis { display: grid; grid-template-columns: repeat(3, 1fr); gap: .65rem; margin-bottom: 1.1rem; }
        .kpis > .kpi { padding: .75rem .8rem; border-radius: var(--radius-sm); background: rgba(255, 255, 255, .03); border: 1px solid var(--border-subtle); }
        .kpi small { display: block; font-size: .68rem; color: var(--text-muted); margin-bottom: .15rem; }
        .kpi strong { font-family: 'Space Grotesk', sans-serif; font-size: 1.35rem; }
        .bars { display: flex; align-items: flex-end; gap: .45rem; height: 100px; padding: .5rem .25rem 0; border-bottom: 1px solid var(--border-subtle); }
        .bars span { flex: 1; border-radius: .35rem .35rem 0 0; background: linear-gradient(180deg, var(--accent-secondary), rgba(45, 227, 142, .15)); transform-origin: bottom; animation: grow 1.2s cubic-bezier(.2, .8, .2, 1) both; }
        .bars span:nth-child(odd) { animation-delay: .1s; }
        .bars span.hot { background: linear-gradient(180deg, var(--accent-primary), rgba(255, 59, 92, .15)); }
        .bar-labels { display: flex; gap: .45rem; padding: .35rem .25rem 0; font-size: .62rem; color: var(--text-dim); }
        .bar-labels span { flex: 1; text-align: center; }
        @keyframes grow { from { transform: scaleY(0); } to { transform: scaleY(1); } }

        .float-card { position: absolute; padding: .9rem 1rem; border-radius: var(--radius-md); animation: bob 6s ease-in-out infinite; }
        .float-shoe { right: 0; top: 0; width: 240px; }
        .float-health { right: -.5rem; bottom: 0; width: 255px; animation-delay: -3s; }
        .float-appointment { left: -1.5rem; bottom: 1.25rem; width: 215px; animation-delay: -1.5s; }
        @keyframes bob { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-8px); } }
        .float-label { font-size: .68rem; text-transform: uppercase; letter-spacing: .08em; color: var(--text-dim); margin-bottom: .45rem; display: flex; align-items: center; gap: .4rem; }
        .progress { height: 8px; border-radius: 999px; background: rgba(255, 255, 255, .06); overflow: hidden; }
        .progress > span { display: block; height: 100%; border-radius: 999px; }
        .status-pill { white-space: nowrap; display: inline-flex; align-items: center; gap: .35rem; font-size: .7rem; font-weight: 600; padding: .15rem .55rem; border-radius: 999px; }
        .pill-green { background: rgba(45, 227, 142, .12); color: var(--accent-secondary); }
        .pill-amber { background: rgba(245, 158, 11, .14); color: var(--accent-amber); }
        .pill-red { background: rgba(255, 59, 92, .14); color: var(--accent-primary); }
        .pill-blue { background: rgba(96, 165, 250, .14); color: var(--accent-blue); }
        .dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; }

        /* ---------- Marquee ---------- */
        .marquee { border-top: 1px solid var(--border-subtle); border-bottom: 1px solid var(--border-subtle); padding: 1.1rem 0; overflow: hidden; margin: 2rem 0 0; background: rgba(255, 255, 255, .01); }
        .marquee-track { display: flex; gap: 3rem; width: max-content; animation: marquee 40s linear infinite; }
        .marquee-track span { display: inline-flex; align-items: center; gap: .75rem; font-family: 'Space Grotesk', sans-serif; font-size: 1rem; color: var(--text-muted); white-space: nowrap; }
        .marquee-track span::before { content: ''; width: 7px; height: 7px; border-radius: 50%; background: var(--accent-primary); }
        .marquee-track span:nth-child(3n+2)::before { background: var(--accent-secondary); }
        .marquee-track span:nth-child(3n)::before { background: var(--accent-pink); }
        @keyframes marquee { to { transform: translateX(-50%); } }

        /* ---------- Sections ---------- */
        section.block { padding: 5.5rem 0; position: relative; }
        .section-head { max-width: 46rem; margin-bottom: 3.5rem; }
        .section-head.center { margin-left: auto; margin-right: auto; text-align: center; }
        .kicker { display: inline-flex; align-items: center; gap: .5rem; font-size: .78rem; font-weight: 600; letter-spacing: .14em; text-transform: uppercase; margin-bottom: 1rem; }
        .kicker::before { content: ''; width: 18px; height: 2px; border-radius: 2px; background: currentColor; }
        .section-head h2 { font-size: clamp(2rem, 4vw, 3.1rem); line-height: 1.08; font-weight: 700; margin-bottom: 1.1rem; }
        .section-head p { font-size: 1.08rem; color: var(--text-muted); }

        .card {
            background: linear-gradient(180deg, rgba(17, 20, 30, .9), rgba(11, 12, 18, .9));
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-lg);
            padding: 1.75rem;
            position: relative;
            overflow: hidden;
            transition: border-color .3s ease, transform .3s ease;
        }
        .card:hover { border-color: var(--border-strong); }
        .card h3 { font-family: 'Space Grotesk', sans-serif; font-size: 1.25rem; font-weight: 600; margin-bottom: .5rem; letter-spacing: -.01em; }
        .card p { color: var(--text-muted); font-size: .95rem; }
        .icon-badge {
            width: 44px; height: 44px;
            border-radius: .8rem;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 1.25rem;
            border: 1px solid var(--border-strong);
        }
        .icon-red { background: rgba(255, 59, 92, .1); color: var(--accent-primary); }
        .icon-green { background: rgba(45, 227, 142, .1); color: var(--accent-secondary); }
        .icon-pink { background: rgba(255, 79, 163, .1); color: var(--accent-pink); }
        .icon-blue { background: rgba(96, 165, 250, .1); color: var(--accent-blue); }
        .icon-amber { background: rgba(245, 158, 11, .1); color: var(--accent-amber); }

        /* Pillars */
        .pillars { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.25rem; }
        .pillar { padding: 2rem; }
        .pillar .num { position: absolute; top: 1.25rem; right: 1.5rem; font-family: 'Space Grotesk', sans-serif; font-size: 3.5rem; font-weight: 700; color: rgba(255, 255, 255, .04); line-height: 1; }
        .pillar ul { list-style: none; margin-top: 1.25rem; display: grid; gap: .55rem; font-size: .9rem; color: var(--text-muted); }
        .pillar li { display: flex; gap: .6rem; align-items: flex-start; }
        .pillar li svg { flex-shrink: 0; margin-top: .25rem; }
        .pillar-link { display: inline-flex; align-items: center; gap: .35rem; margin-top: 1.5rem; font-size: .88rem; font-weight: 600; }
        .pillar-link:hover { gap: .6rem; }

        /* Split feature */
        .split { display: grid; grid-template-columns: 1fr 1fr; gap: 4rem; align-items: center; }
        .split.reverse > :first-child { order: 2; }
        .feature-list { list-style: none; display: grid; gap: 1.1rem; margin: 2rem 0 2.25rem; }
        .feature-list li { display: grid; grid-template-columns: 40px 1fr; gap: 1rem; align-items: start; }
        .feature-list .icon-badge { width: 40px; height: 40px; margin: 0; }
        .feature-list strong { display: block; font-size: 1rem; margin-bottom: .15rem; }
        .feature-list span { color: var(--text-muted); font-size: .92rem; }

        /* Health visuals */
        .health-stack { position: relative; display: grid; gap: 1rem; }
        .report-card { padding: 1.5rem; }
        .report-head { display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem; padding-bottom: 1rem; margin-bottom: 1rem; border-bottom: 1px solid var(--border-subtle); }
        .report-head h4 { font-family: 'Space Grotesk', sans-serif; font-size: 1.05rem; }
        .report-head small { color: var(--text-muted); font-size: .78rem; }
        .study-row { display: flex; justify-content: space-between; align-items: center; gap: .75rem; padding: .6rem .75rem; border-radius: var(--radius-sm); background: rgba(255, 255, 255, .025); border: 1px solid var(--border-subtle); font-size: .85rem; margin-bottom: .5rem; }
        .study-row span:first-child { display: flex; align-items: center; gap: .6rem; }
        .study-dot { width: 8px; height: 8px; border-radius: 2px; }
        .mini-kpis { display: grid; grid-template-columns: repeat(4, 1fr); gap: .5rem; margin-top: 1rem; }
        .mini-kpis > div { padding: .6rem; border-radius: var(--radius-sm); background: rgba(255, 255, 255, .025); border-left: 3px solid; }
        .mini-kpis small { display: block; font-size: .65rem; color: var(--text-muted); }
        .mini-kpis strong { font-family: 'Space Grotesk', sans-serif; font-size: 1rem; }
        .share-link { display: flex; align-items: center; justify-content: space-between; gap: .75rem; margin-top: 1rem; padding: .65rem .85rem; border-radius: var(--radius-sm); border: 1px dashed var(--border-strong); font-size: .8rem; color: var(--text-muted); font-family: ui-monospace, SFMono-Regular, Menlo, monospace; }
        .appointment-card { padding: 1.25rem 1.5rem; margin-left: 2.5rem; }
        .task { display: flex; align-items: center; gap: .7rem; font-size: .88rem; padding: .35rem 0; }
        .check { width: 16px; height: 16px; border-radius: 4px; border: 1.5px solid var(--text-dim); flex-shrink: 0; display: flex; align-items: center; justify-content: center; }
        .check.done { background: var(--accent-secondary); border-color: var(--accent-secondary); color: var(--bg-main); }
        .task.done > span:last-child { color: var(--text-dim); text-decoration: line-through; }

        .steps { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.25rem; margin-top: 4rem; counter-reset: step; }
        .step { padding: 1.5rem; border-radius: var(--radius-md); border: 1px solid var(--border-subtle); background: rgba(255, 255, 255, .015); }
        .step::before { counter-increment: step; content: '0' counter(step); display: block; font-family: 'Space Grotesk', sans-serif; font-size: .85rem; font-weight: 700; color: var(--accent-secondary); margin-bottom: .6rem; }
        .step strong { display: block; margin-bottom: .3rem; }
        .step span { color: var(--text-muted); font-size: .9rem; }

        /* Shoes */
        .shoes { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; }
        .shoe { padding: 1.4rem; }
        .shoe-top { display: flex; justify-content: space-between; align-items: flex-start; gap: .5rem; margin-bottom: 1.25rem; }
        .shoe-name { font-family: 'Space Grotesk', sans-serif; font-weight: 600; font-size: 1.05rem; line-height: 1.2; }
        .shoe-brand { font-size: .75rem; color: var(--text-muted); }
        .ring { position: relative; width: 120px; height: 120px; margin: 0 auto 1.25rem; }
        .ring svg { width: 120px; height: 120px; transform: rotate(-90deg); }
        .ring circle { fill: none; stroke-width: 10; }
        .ring .track { stroke: rgba(255, 255, 255, .06); }
        .ring .value { stroke-linecap: round; stroke-dasharray: 327; transition: stroke-dashoffset 1.6s cubic-bezier(.2, .8, .2, 1); }
        .ring-label { position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; }
        .ring-label strong { font-family: 'Space Grotesk', sans-serif; font-size: 1.5rem; line-height: 1; }
        .ring-label small { font-size: .7rem; color: var(--text-muted); }
        .shoe-stats { display: grid; grid-template-columns: 1fr 1fr; gap: .5rem; font-size: .78rem; }
        .shoe-stats > div { padding: .5rem .6rem; border-radius: var(--radius-sm); background: rgba(255, 255, 255, .025); }
        .shoe-stats small { display: block; color: var(--text-dim); font-size: .68rem; }
        .shoe-alert { display: flex; gap: .85rem; align-items: center; margin-top: 1rem; padding: 1rem 1.25rem; border-radius: var(--radius-md); background: linear-gradient(90deg, rgba(255, 59, 92, .12), rgba(255, 59, 92, .02)); border: 1px solid rgba(255, 59, 92, .3); font-size: .9rem; }
        .shoe-alert svg { color: var(--accent-primary); flex-shrink: 0; }
        .shoe-features { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-top: 3rem; }
        .shoe-features .card { padding: 1.4rem; }
        .shoe-features h3 { font-size: 1.02rem; }
        .shoe-features p { font-size: .88rem; }

        /* Bento (reports + coach) */
        .bento { display: grid; grid-template-columns: repeat(6, 1fr); gap: 1.25rem; }
        .span-4 { grid-column: span 4; }
        .span-3 { grid-column: span 3; }
        .span-2 { grid-column: span 2; }
        .week-compare { display: grid; grid-template-columns: repeat(4, 1fr); gap: .75rem; margin-top: 1.5rem; }
        .week-compare > div { padding: .8rem; border-radius: var(--radius-sm); background: rgba(255, 255, 255, .025); border: 1px solid var(--border-subtle); }
        .week-compare small { display: block; font-size: .7rem; color: var(--text-muted); }
        .week-compare strong { font-family: 'Space Grotesk', sans-serif; font-size: 1.2rem; }
        .trend-up { font-size: .72rem; color: var(--accent-secondary); }
        .trend-down { font-size: .72rem; color: var(--accent-primary); }
        .insight { display: flex; gap: .75rem; align-items: center; padding: .7rem .85rem; border-radius: var(--radius-sm); background: rgba(255, 255, 255, .025); font-size: .88rem; margin-top: .6rem; }
        .avatars { display: flex; margin-top: 1.5rem; }
        .avatars span { width: 38px; height: 38px; font-size: .68rem !important; border-radius: 50%; border: 2px solid var(--bg-card); margin-left: -8px; display: flex; align-items: center; justify-content: center; font-size: .75rem; font-weight: 700; color: var(--bg-main); }
        .avatars span:first-child { margin-left: 0; }
        .code {
            margin-top: 1.25rem;
            padding: 1rem 1.1rem;
            border-radius: var(--radius-sm);
            background: #05070D;
            border: 1px solid var(--border-subtle);
            font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
            font-size: .8rem;
            line-height: 1.8;
            color: var(--text-muted);
            overflow-x: auto;
        }
        .code .k { color: var(--accent-pink); }
        .code .s { color: var(--accent-secondary); }
        .code .c { color: var(--text-dim); }

        /* Numbers */
        .numbers { display: grid; grid-template-columns: repeat(4, 1fr); border: 1px solid var(--border-subtle); border-radius: var(--radius-lg); overflow: hidden; background: rgba(255, 255, 255, .015); }
        .numbers > div { padding: 2rem 1.5rem; text-align: center; border-right: 1px solid var(--border-subtle); }
        .numbers > div:last-child { border-right: none; }
        .numbers strong { display: block; font-family: 'Space Grotesk', sans-serif; font-size: 2.5rem; line-height: 1.1; }
        .numbers span { font-size: .85rem; color: var(--text-muted); }

        /* FAQ */
        .faq { max-width: 50rem; margin: 0 auto; display: grid; gap: .75rem; }
        .faq details { border: 1px solid var(--border-subtle); border-radius: var(--radius-md); background: rgba(255, 255, 255, .015); transition: border-color .2s ease; }
        .faq details[open] { border-color: var(--border-strong); background: rgba(255, 255, 255, .03); }
        .faq summary { list-style: none; cursor: pointer; padding: 1.2rem 1.4rem; font-weight: 600; display: flex; justify-content: space-between; align-items: center; gap: 1rem; }
        .faq summary::-webkit-details-marker { display: none; }
        .faq summary::after { content: '+'; font-size: 1.4rem; font-weight: 400; color: var(--accent-secondary); transition: transform .2s ease; }
        .faq details[open] summary::after { transform: rotate(45deg); }
        .faq details p { padding: 0 1.4rem 1.3rem; color: var(--text-muted); font-size: .95rem; }

        /* CTA */
        .cta {
            position: relative;
            overflow: hidden;
            text-align: center;
            padding: 5rem 2rem;
            border-radius: 2rem;
            border: 1px solid var(--border-strong);
            background:
                radial-gradient(600px 300px at 20% 0%, rgba(255, 59, 92, .25), transparent 70%),
                radial-gradient(600px 300px at 80% 100%, rgba(45, 227, 142, .18), transparent 70%),
                var(--bg-card);
        }
        .cta h2 { font-size: clamp(2.2rem, 5vw, 3.6rem); line-height: 1.05; margin-bottom: 1.25rem; }
        .cta p { color: var(--text-muted); font-size: 1.1rem; max-width: 36rem; margin: 0 auto 2.25rem; }

        /* Footer */
        footer { border-top: 1px solid var(--border-subtle); margin-top: 6rem; padding: 3.5rem 0 2rem; }
        .footer-grid { display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 2.5rem; margin-bottom: 3rem; }
        .footer-grid h4 { font-size: .8rem; letter-spacing: .12em; text-transform: uppercase; color: var(--text-dim); margin-bottom: 1rem; }
        .footer-grid ul { list-style: none; display: grid; gap: .6rem; font-size: .9rem; color: var(--text-muted); }
        .footer-grid a:hover { color: var(--text-main); }
        .footer-bottom { display: flex; justify-content: space-between; flex-wrap: wrap; gap: 1rem; font-size: .82rem; color: var(--text-dim); padding-top: 1.5rem; border-top: 1px solid var(--border-subtle); }

        /* Reveal */
        .reveal { opacity: 0; transform: translateY(24px); transition: opacity .8s ease, transform .8s cubic-bezier(.2, .8, .2, 1); }
        .reveal.visible { opacity: 1; transform: none; }
        .reveal-delay-1 { transition-delay: .1s; }
        .reveal-delay-2 { transition-delay: .2s; }
        .reveal-delay-3 { transition-delay: .3s; }

        /* ---------- Responsive ---------- */
        @media (max-width: 1024px) {
            .hero-grid, .split { grid-template-columns: 1fr; gap: 3rem; }
            .split.reverse > :first-child { order: 0; }
            .hero-visual { max-width: 560px; width: 100%; margin: 0 auto; }
            .pillars { grid-template-columns: 1fr; }
            .shoe-features { grid-template-columns: repeat(2, 1fr); }
            .bento { grid-template-columns: 1fr; }
            .span-4, .span-3, .span-2 { grid-column: auto; }
            .nav-links { display: none; }
            .nav-toggle { display: inline-flex; }
            .mobile-menu.open { display: grid; gap: .25rem; padding: .5rem 1.25rem 1.25rem; border-bottom: 1px solid var(--border-subtle); background: rgba(5, 6, 10, .96); }
            .mobile-menu a { padding: .75rem 0; color: var(--text-muted); border-bottom: 1px solid var(--border-subtle); }
        }

        @media (max-width: 720px) {
            section.block { padding: 4.5rem 0; }
            .hero { padding-top: 2.5rem; }
            .hero-visual { min-height: 520px; }
            .mock-main { inset: 6rem 0 7rem 0; }
            .float-shoe { width: 220px; right: 0; top: 0; }
            .float-health { width: 225px; right: 0; bottom: 0; }
            .float-appointment { left: 0; bottom: 3.75rem; width: 190px; display: none; }
            .kpi strong { font-size: 1.1rem; }
            .shoes { grid-template-columns: 1fr; }
            .shoe-features { grid-template-columns: 1fr 1fr; gap: .75rem; }
            .shoe-features .card { padding: 1.1rem; }
            .shoe-features .icon-badge { width: 36px; height: 36px; margin-bottom: .85rem; }
            .shoe-features h3 { font-size: .95rem; }
            .shoe-features p { font-size: .82rem; }
            .steps { grid-template-columns: 1fr; margin-top: 2.5rem; }
            .numbers { grid-template-columns: 1fr 1fr; }
            .numbers > div:nth-child(2) { border-right: none; }
            .numbers > div:nth-child(-n+2) { border-bottom: 1px solid var(--border-subtle); }
            .week-compare, .mini-kpis { grid-template-columns: 1fr 1fr; }
            .appointment-card { margin-left: 0; }
            .footer-grid { grid-template-columns: 1fr 1fr; }
            .footer-grid > :first-child { grid-column: 1 / -1; }
            .nav-actions .btn-ghost { display: none; }
            .nav-actions .btn-primary { padding: .6rem 1rem; font-size: .85rem; }
            .cta { padding: 3.5rem 1.25rem; }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { animation: none !important; transition: none !important; }
            .reveal { opacity: 1; transform: none; }
            html { scroll-behavior: auto; }
        }
    </style>
</head>
<body>
<div class="bg-glow" aria-hidden="true"></div>

<div class="page">
    {{-- NAV --}}
    <header class="nav" id="nav">
        <div class="container nav-inner">
            <a href="{{ route('welcome.v3') }}" aria-label="MiEntreno — inicio">
                <img src="{{ asset('images/logo-horizontal.svg') }}" alt="MiEntreno" style="height:36px;width:auto;">
            </a>

            <nav class="nav-links" aria-label="Secciones">
                <a href="#entrenamientos">Entrenamientos</a>
                <a href="#salud">Salud</a>
                <a href="#zapatillas">Zapatillas</a>
                <a href="#coaches">Coaches</a>
                <a href="#faq">FAQ</a>
            </nav>

            <div class="nav-actions">
                <a href="{{ route('login') }}" class="btn btn-ghost">Ingresar</a>
                <a href="{{ route('register') }}" class="btn btn-primary">Crear cuenta</a>
                <button class="nav-toggle" id="navToggle" aria-label="Abrir menú" aria-expanded="false" aria-controls="mobileMenu">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="4" y1="7" x2="20" y2="7"/><line x1="4" y1="12" x2="20" y2="12"/><line x1="4" y1="17" x2="20" y2="17"/></svg>
                </button>
            </div>
        </div>
        <div class="mobile-menu" id="mobileMenu">
            <a href="#entrenamientos">Entrenamientos</a>
            <a href="#salud">Salud</a>
            <a href="#zapatillas">Zapatillas</a>
            <a href="#coaches">Coaches</a>
            <a href="#faq">FAQ</a>
            <a href="{{ route('login') }}">Ingresar</a>
        </div>
    </header>

    <main>
        {{-- HERO --}}
        <section class="hero">
            <div class="bg-grid" aria-hidden="true"></div>
            <div class="container hero-grid">
                <div>
                    <div class="eyebrow reveal"><b>Nuevo</b> Salud médica + control de zapatillas</div>

                    <h1 class="display reveal reveal-delay-1">
                        Entrená.<br>
                        Cuidate.<br>
                        <span class="gradient-text">Rendí más.</span>
                    </h1>

                    <p class="hero-lead reveal reveal-delay-2">
                        Tus entrenamientos, tu historia médica deportiva y los kilómetros de cada zapatilla, en un solo lugar.
                        Con reportes listos para compartir con tu coach y con tu médico.
                    </p>

                    <div class="hero-ctas reveal reveal-delay-3">
                        <a href="{{ route('register') }}" class="btn btn-primary btn-lg">
                            Empezar gratis
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                        </a>
                        <a href="#salud" class="btn btn-outline btn-lg">Ver cómo funciona</a>
                    </div>

                    <div class="hero-proof reveal reveal-delay-3">
                        <div>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                            Tus estudios, privados
                        </div>
                        <div>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                            Links que vencen solos
                        </div>
                        <div>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                            Desde el celular
                        </div>
                    </div>
                </div>

                {{-- Product collage --}}
                <div class="hero-visual reveal reveal-delay-2" aria-hidden="true">
                    <div class="mock mock-main">
                        <div class="mock-head">
                            <span class="mock-title">Esta semana</span>
                            <span class="mock-chip">+18% vs. anterior</span>
                        </div>
                        <div class="kpis">
                            <div class="kpi"><small>Kilómetros</small><strong>42.6</strong></div>
                            <div class="kpi"><small>Pace medio</small><strong>5:08</strong></div>
                            <div class="kpi"><small>Sesiones</small><strong>5</strong></div>
                        </div>
                        <div class="bars">
                            <span style="height:45%"></span>
                            <span style="height:0%;opacity:0"></span>
                            <span class="hot" style="height:70%"></span>
                            <span style="height:38%"></span>
                            <span style="height:55%"></span>
                            <span style="height:0%;opacity:0"></span>
                            <span style="height:100%"></span>
                        </div>
                        <div class="bar-labels"><span>L</span><span>M</span><span>X</span><span>J</span><span>V</span><span>S</span><span>D</span></div>
                    </div>

                    <div class="mock float-card float-shoe">
                        <div class="float-label">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 17h17a3 3 0 0 0 3-3v-1l-6-2-3-5H8L6 9 2 10z"/><line x1="2" y1="20" x2="22" y2="20"/></svg>
                            Zapatillas
                        </div>
                        <div style="display:flex;justify-content:space-between;align-items:baseline;margin-bottom:.5rem;">
                            <strong style="font-size:.92rem;">Pegasus 41</strong>
                            <span style="font-size:.78rem;color:var(--text-muted);"><b style="color:var(--text-main);">612</b> / 800 km</span>
                        </div>
                        <div class="progress"><span style="width:76%;background:linear-gradient(90deg,var(--accent-secondary),var(--accent-amber));"></span></div>
                        <div style="margin-top:.6rem;"><span class="status-pill pill-amber"><span class="dot"></span>188 km restantes</span></div>
                    </div>

                    <div class="mock float-card float-health">
                        <div class="float-label">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78L12 21.23l8.84-8.84a5.5 5.5 0 0 0 0-7.78z"/></svg>
                            Salud
                        </div>
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.55rem;">
                            <strong style="font-size:.9rem;">Apto médico</strong>
                            <span class="status-pill pill-green"><span class="dot"></span>Vigente</span>
                        </div>
                        <div style="display:flex;justify-content:space-between;align-items:center;font-size:.8rem;color:var(--text-muted);">
                            <span>Reporte para Dr. Gómez</span>
                            <span class="status-pill pill-blue">Link 7 días</span>
                        </div>
                    </div>

                    <div class="mock float-card float-appointment">
                        <div class="float-label">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            Próximo turno
                        </div>
                        <strong style="font-size:.88rem;display:block;">Cardiología</strong>
                        <span style="font-size:.78rem;color:var(--accent-secondary);">Mié 7 · 16:00 · en 3 días</span>
                    </div>
                </div>
            </div>

            <div class="marquee" aria-hidden="true">
                <div class="marquee-track">
                    @foreach (range(1, 2) as $loopIndex)
                        <span>Registro de entrenamientos</span>
                        <span>Reportes semanales y mensuales</span>
                        <span>Estudios médicos</span>
                        <span>Reporte para tu médico</span>
                        <span>Turnos e indicaciones</span>
                        <span>Km por zapatilla</span>
                        <span>Alertas de recambio</span>
                        <span>Carreras y objetivos</span>
                        <span>Modo coach</span>
                        <span>Links compartibles</span>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- PILLARS --}}
        <section class="block" id="entrenamientos">
            <div class="container">
                <div class="section-head center reveal">
                    <div class="kicker text-green">Una app, tres frentes</div>
                    <h2 class="display">Todo lo que hace que sigas corriendo, <span class="gradient-text">en un solo lugar.</span></h2>
                    <p>Los kilómetros son solo una parte. MiEntreno junta tu entrenamiento, tu salud y tu equipamiento para que veas el cuadro completo.</p>
                </div>

                <div class="pillars">
                    <article class="card pillar reveal">
                        <span class="num">01</span>
                        <div class="icon-badge icon-red">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                        </div>
                        <h3>Entrenamientos</h3>
                        <p>Cada sesión con distancia, tiempo, ritmo, frecuencia cardíaca, desnivel y sensaciones.</p>
                        <ul>
                            <li><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--accent-secondary)" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>Fondos, series, tempo, recuperación y carreras</li>
                            <li><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--accent-secondary)" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>Planificado vs. realizado</li>
                            <li><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--accent-secondary)" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>Carreras, objetivos y marcas personales</li>
                        </ul>
                        <a href="#reportes" class="pillar-link text-red">Ver reportes →</a>
                    </article>

                    <article class="card pillar reveal reveal-delay-1">
                        <span class="num">02</span>
                        <div class="icon-badge icon-green">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78L12 21.23l8.84-8.84a5.5 5.5 0 0 0 0-7.78z"/></svg>
                        </div>
                        <h3>Salud médica</h3>
                        <p>Tus estudios, apto físico, turnos y órdenes, ordenados y listos para mostrar.</p>
                        <ul>
                            <li><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--accent-secondary)" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>Apto médico con aviso de vencimiento</li>
                            <li><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--accent-secondary)" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>Reporte para tu médico con tus entrenamientos</li>
                            <li><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--accent-secondary)" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>Turnos con indicaciones y pendientes</li>
                        </ul>
                        <a href="#salud" class="pillar-link text-green">Conocer Salud →</a>
                    </article>

                    <article class="card pillar reveal reveal-delay-2">
                        <span class="num">03</span>
                        <div class="icon-badge icon-pink">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 17h17a3 3 0 0 0 3-3v-1l-6-2-3-5H8L6 9 2 10z"/><line x1="2" y1="20" x2="22" y2="20"/></svg>
                        </div>
                        <h3>Zapatillas</h3>
                        <p>Cada par suma sus kilómetros solo. Sabés cuánto le queda antes de que te lo diga una lesión.</p>
                        <ul>
                            <li><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--accent-secondary)" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>Km acumulados por par</li>
                            <li><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--accent-secondary)" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>Alerta cuando se acerca el recambio</li>
                            <li><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--accent-secondary)" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>Rotación y costo por kilómetro</li>
                        </ul>
                        <a href="#zapatillas" class="pillar-link" style="color:var(--accent-pink);">Ver zapatillas →</a>
                    </article>
                </div>
            </div>
        </section>

        {{-- HEALTH --}}
        <section class="block" id="salud">
            <div class="container">
                <div class="split">
                    <div class="reveal">
                        <div class="kicker text-green">Salud médica</div>
                        <h2 class="display" style="font-size:clamp(2rem,4vw,3.1rem);line-height:1.08;margin-bottom:1.1rem;">
                            Tu médico ve tus estudios <span class="gradient-text">y cómo venís entrenando.</span>
                        </h2>
                        <p class="text-muted" style="font-size:1.08rem;">
                            Llevá tu historia médica deportiva al día. Cuando tengas consulta, armás un reporte con los estudios que elijas,
                            le sumás el detalle de tus últimos meses de entrenamiento y lo compartís con un link.
                        </p>

                        <ul class="feature-list">
                            <li>
                                <div class="icon-badge icon-green"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></div>
                                <div><strong>Estudios y apto físico</strong><span>Laboratorio, ECG, ergometría, ecocardiograma, imágenes y más. El apto te avisa antes de vencer.</span></div>
                            </li>
                            <li>
                                <div class="icon-badge icon-blue"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg></div>
                                <div><strong>Reporte compartible para tu médico</strong><span>Link sin login que vence a los 7 días, con descarga en ZIP y el detalle del último mes, 3 o 6 meses de entrenamiento.</span></div>
                            </li>
                            <li>
                                <div class="icon-badge icon-amber"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div>
                                <div><strong>Turnos con memoria</strong><span>Después de cada consulta anotás qué te dijeron y qué quedó pendiente: medicación, derivaciones, estudios. Y lo agregás a tu calendario.</span></div>
                            </li>
                            <li>
                                <div class="icon-badge icon-pink"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg></div>
                                <div><strong>Órdenes y médicos</strong><span>Sacale una foto a la orden en papel. Tus médicos, especialidades y obra social, siempre a mano.</span></div>
                            </li>
                        </ul>

                        <a href="{{ route('register') }}" class="btn btn-outline">Ordenar mi historia médica</a>
                    </div>

                    <div class="health-stack reveal reveal-delay-1" aria-hidden="true">
                        <div class="card report-card">
                            <div class="report-head">
                                <div>
                                    <small>Reporte de estudios</small>
                                    <h4>Pre-consulta cardiología</h4>
                                    <small>Para: Dra. Méndez · Cardiología</small>
                                </div>
                                <span class="status-pill pill-green"><span class="dot"></span>Compartido</span>
                            </div>
                            <div class="study-row"><span><i class="study-dot" style="background:#F87171"></i>Análisis de sangre</span><span class="text-muted">08/09</span></div>
                            <div class="study-row"><span><i class="study-dot" style="background:#60A5FA"></i>Electrocardiograma</span><span class="text-muted">08/09</span></div>
                            <div class="study-row"><span><i class="study-dot" style="background:#C084FC"></i>Ecocardiograma Doppler</span><span class="text-muted">10/09</span></div>

                            <div style="margin-top:1.1rem;font-size:.72rem;letter-spacing:.1em;text-transform:uppercase;color:var(--text-dim);">+ Entrenamientos · últimos 3 meses</div>
                            <div class="mini-kpis">
                                <div style="border-color:var(--accent-secondary)"><small>Km</small><strong>636</strong></div>
                                <div style="border-color:var(--accent-blue)"><small>Tiempo</small><strong>59h</strong></div>
                                <div style="border-color:var(--accent-amber)"><small>Sesiones</small><strong>46</strong></div>
                                <div style="border-color:#EF4444"><small>FC prom.</small><strong>153</strong></div>
                            </div>

                            <div class="share-link">
                                <span>mientreno.app/share/k7Qp…</span>
                                <span class="text-green" style="font-family:Inter,sans-serif;font-weight:600;">Copiar</span>
                            </div>
                        </div>

                        <div class="card appointment-card">
                            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.75rem;gap:.5rem;">
                                <strong style="font-size:.95rem;">Clínica médica · ¿Cómo te fue?</strong>
                                <span class="status-pill pill-amber">3 pendientes</span>
                            </div>
                            <div class="task done"><span class="check done"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4"><polyline points="20 6 9 17 4 12"/></svg></span><span>💊 Atorvastatina 10 mg por la noche</span></div>
                            <div class="task"><span class="check"></span><span>👨‍⚕️ Consultar cirujano — <span class="text-green">Agendar turno</span></span></div>
                            <div class="task"><span class="check"></span><span>🔬 Repetir perfil lipídico en 3 meses</span></div>
                        </div>
                    </div>
                </div>

                <div class="steps">
                    <div class="step reveal"><strong>Cargá tus estudios</strong><span>PDF o foto, con fecha y el médico que los pidió.</span></div>
                    <div class="step reveal reveal-delay-1"><strong>Armá el reporte</strong><span>Elegí los estudios y si querés sumar tus entrenamientos.</span></div>
                    <div class="step reveal reveal-delay-2"><strong>Compartí el link</strong><span>Tu médico lo abre sin registrarse. En 7 días expira solo.</span></div>
                </div>
            </div>
        </section>

        {{-- SHOES --}}
        <section class="block" id="zapatillas">
            <div class="container">
                <div class="section-head reveal">
                    <div class="kicker" style="color:var(--accent-pink);">Zapatillas</div>
                    <h2 class="display">Cada kilómetro cuenta. <span class="gradient-text">También para tus zapatillas.</span></h2>
                    <p>Asigná un par a cada entrenamiento y MiEntreno lleva la cuenta. Sabés cuándo rotar, cuándo cambiar y cuánto te rinde cada par.</p>
                </div>

                <div class="shoes">
                    <article class="card shoe reveal">
                        <div class="shoe-top">
                            <div><div class="shoe-name">Pegasus 41</div><div class="shoe-brand">Nike · Entrenamiento diario</div></div>
                            <span class="status-pill pill-amber"><span class="dot"></span>Cerca del límite</span>
                        </div>
                        <div class="ring" data-progress="0.76">
                            <svg viewBox="0 0 120 120"><circle class="track" cx="60" cy="60" r="52"/><circle class="value" cx="60" cy="60" r="52" stroke="url(#ringAmber)" style="stroke-dashoffset:327"/></svg>
                            <div class="ring-label"><strong>612</strong><small>de 800 km</small></div>
                        </div>
                        <div class="shoe-stats">
                            <div><small>Sesiones</small>74</div>
                            <div><small>Costo por km</small>$ 245</div>
                        </div>
                    </article>

                    <article class="card shoe reveal reveal-delay-1">
                        <div class="shoe-top">
                            <div><div class="shoe-name">Endorphin Speed 4</div><div class="shoe-brand">Saucony · Series y tempo</div></div>
                            <span class="status-pill pill-green"><span class="dot"></span>En forma</span>
                        </div>
                        <div class="ring" data-progress="0.31">
                            <svg viewBox="0 0 120 120"><circle class="track" cx="60" cy="60" r="52"/><circle class="value" cx="60" cy="60" r="52" stroke="url(#ringGreen)" style="stroke-dashoffset:327"/></svg>
                            <div class="ring-label"><strong>186</strong><small>de 600 km</small></div>
                        </div>
                        <div class="shoe-stats">
                            <div><small>Sesiones</small>21</div>
                            <div><small>Costo por km</small>$ 890</div>
                        </div>
                    </article>

                    <article class="card shoe reveal reveal-delay-2">
                        <div class="shoe-top">
                            <div><div class="shoe-name">Ghost 15</div><div class="shoe-brand">Brooks · Fondos largos</div></div>
                            <span class="status-pill pill-red"><span class="dot"></span>Para retirar</span>
                        </div>
                        <div class="ring" data-progress="1">
                            <svg viewBox="0 0 120 120"><circle class="track" cx="60" cy="60" r="52"/><circle class="value" cx="60" cy="60" r="52" stroke="url(#ringRed)" style="stroke-dashoffset:327"/></svg>
                            <div class="ring-label"><strong>812</strong><small>de 750 km</small></div>
                        </div>
                        <div class="shoe-stats">
                            <div><small>Sesiones</small>96</div>
                            <div><small>Costo por km</small>$ 160</div>
                        </div>
                    </article>
                </div>

                <svg width="0" height="0" style="position:absolute" aria-hidden="true">
                    <defs>
                        <linearGradient id="ringGreen" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#2DE38E"/><stop offset="1" stop-color="#22C55E"/></linearGradient>
                        <linearGradient id="ringAmber" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#2DE38E"/><stop offset="1" stop-color="#F59E0B"/></linearGradient>
                        <linearGradient id="ringRed" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#FF4FA3"/><stop offset="1" stop-color="#FF3B5C"/></linearGradient>
                    </defs>
                </svg>

                <div class="shoe-alert reveal">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                    <span><strong>Tus Ghost 15 superaron su vida útil.</strong> <span class="text-muted">Una zapatilla gastada pierde amortiguación y es una causa frecuente de molestias. Es momento de retirarlas.</span></span>
                </div>

                <div class="shoe-features">
                    <div class="card reveal">
                        <div class="icon-badge icon-green"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg></div>
                        <h3>Km automáticos</h3>
                        <p>Elegís el par al cargar el entrenamiento y los kilómetros se suman solos.</p>
                    </div>
                    <div class="card reveal reveal-delay-1">
                        <div class="icon-badge icon-amber"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg></div>
                        <h3>Vida útil y alertas</h3>
                        <p>Definí el límite de cada par y recibí el aviso cuando se acerca el recambio.</p>
                    </div>
                    <div class="card reveal reveal-delay-2">
                        <div class="icon-badge icon-blue"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg></div>
                        <h3>Rotación inteligente</h3>
                        <p>Mirá qué par usás para cada tipo de entrenamiento y repartí el desgaste.</p>
                    </div>
                    <div class="card reveal reveal-delay-3">
                        <div class="icon-badge icon-pink"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg></div>
                        <h3>Costo por km</h3>
                        <p>Cargá lo que pagaste y descubrí qué modelo te rinde de verdad.</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- REPORTS + COACH --}}
        <section class="block" id="reportes">
            <div class="container">
                <div class="section-head center reveal">
                    <div class="kicker text-red">Datos que se entienden</div>
                    <h2 class="display">Reportes claros para vos, <span class="gradient-text">tu coach y tu equipo.</span></h2>
                    <p>Resumen semanal y mensual, comparativas, insights y links para compartir. Y si entrenás gente, un panel entero para vos.</p>
                </div>

                <div class="bento">
                    <article class="card span-4 reveal">
                        <div class="icon-badge icon-red"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg></div>
                        <h3>Reportes semanales y mensuales</h3>
                        <p>Comparás contra el período anterior, ves la distribución por tipo de entrenamiento y exportás a PDF.</p>
                        <div class="week-compare">
                            <div><small>Distancia</small><strong>158 km</strong><div class="trend-up">▲ 12.4%</div></div>
                            <div><small>Sesiones</small><strong>18</strong><div class="trend-up">▲ 3</div></div>
                            <div><small>Tiempo</small><strong>14h 47m</strong><div class="trend-up">▲ 9.8%</div></div>
                            <div><small>Pace</small><strong>5:36</strong><div class="trend-down">▼ 0:05</div></div>
                        </div>
                    </article>

                    <article class="card span-2 reveal reveal-delay-1">
                        <div class="icon-badge icon-amber"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg></div>
                        <h3>Insights</h3>
                        <p>Lo importante, sin buscarlo.</p>
                        <div class="insight">🏆 <span>Mejor entreno: 21.5 km</span></div>
                        <div class="insight">⚡ <span>Mejor pace: 5:07/km</span></div>
                        <div class="insight">🔥 <span>Racha: 5 días</span></div>
                    </article>

                    <article class="card span-3 reveal" id="coaches">
                        <div class="icon-badge icon-blue"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg></div>
                        <h3>Modo coach</h3>
                        <p>Tu team con su propio espacio: grupos por nivel, alumnos, suscripciones y el rendimiento de cada uno en un panel.</p>
                        <div class="avatars">
                            <span style="background:var(--accent-secondary)">LM</span>
                            <span style="background:var(--accent-primary)">JP</span>
                            <span style="background:var(--accent-blue)">AR</span>
                            <span style="background:var(--accent-amber)">SG</span>
                            <span style="background:var(--border-strong);color:var(--text-main)">+24</span>
                        </div>
                    </article>

                    <article class="card span-3 reveal reveal-delay-1">
                        <div class="icon-badge icon-green"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg></div>
                        <h3>Entrená como corrés. Registrá como programás.</h3>
                        <p>Hecho por un runner que programa: rápido, minimalista y sin vueltas.</p>
                        <div class="code">
                            <div><span class="c">// hoy</span></div>
                            <div><span class="k">run</span>(<span class="s">'fondo'</span>, { km: <span class="s">18</span>, pace: <span class="s">'5:32'</span>, fc: <span class="s">148</span> })</div>
                            <div><span class="k">shoe</span>(<span class="s">'Pegasus 41'</span>).km += <span class="s">18</span> <span class="c">// 630 / 800</span></div>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        {{-- NUMBERS --}}
        <section style="padding:1rem 0 5rem;">
            <div class="container">
                <div class="numbers reveal">
                    <div><strong class="gradient-text">10</strong><span>tipos de estudios médicos</span></div>
                    <div><strong class="text-green">7</strong><span>días de validez de cada link</span></div>
                    <div><strong class="text-red">3</strong><span>frentes: entreno, salud y zapatillas</span></div>
                    <div><strong style="color:var(--accent-pink);">100%</strong><span>tus archivos, privados</span></div>
                </div>
            </div>
        </section>

        {{-- FAQ --}}
        <section class="block" id="faq" style="padding-top:2rem;">
            <div class="container">
                <div class="section-head center reveal">
                    <div class="kicker text-green">Preguntas frecuentes</div>
                    <h2 class="display">Lo que nos suelen preguntar</h2>
                </div>

                <div class="faq reveal">
                    <details>
                        <summary>¿Quién puede ver mis estudios médicos?</summary>
                        <p>Solo vos. Los archivos se guardan de forma privada y nunca quedan públicos. Si generás un reporte para tu médico, solo ve lo que elegiste compartir, y el link vence solo a los 7 días.</p>
                    </details>
                    <details>
                        <summary>¿Qué ve el médico cuando abre el link?</summary>
                        <p>Los estudios que seleccionaste (con vista previa y descarga en ZIP), tus datos de obra social y, si lo activás, un resumen y el detalle de tus entrenamientos del último mes, 3 o 6 meses. No necesita crear cuenta.</p>
                    </details>
                    <details>
                        <summary>¿Cómo se cuentan los kilómetros de las zapatillas?</summary>
                        <p>Al cargar un entrenamiento elegís qué par usaste y la distancia se suma sola. Vos definís la vida útil de cada par y MiEntreno te avisa cuando se acerca el recambio.</p>
                    </details>
                    <details>
                        <summary>¿Sirve si entreno solo, sin coach?</summary>
                        <p>Sí. La app está pensada primero para el runner. El modo coach es un extra para entrenadores y teams que quieren seguir a sus alumnos.</p>
                    </details>
                    <details>
                        <summary>¿Puedo usarla desde el celular?</summary>
                        <p>Sí, toda la app está diseñada mobile first: cargás el entreno apenas terminás y le sacás una foto a la orden médica en el consultorio.</p>
                    </details>
                </div>
            </div>
        </section>

        {{-- CTA --}}
        <section style="padding:2rem 0 0;">
            <div class="container">
                <div class="cta reveal">
                    <div class="kicker text-green" style="justify-content:center;">Empezá hoy</div>
                    <h2 class="display">Más que números.<br><span class="gradient-text">Tu historia running.</span></h2>
                    <p>Creá tu cuenta en un minuto y empezá a registrar entrenamientos, estudios y zapatillas.</p>
                    <div style="display:flex;flex-wrap:wrap;gap:.85rem;justify-content:center;">
                        <a href="{{ route('register') }}" class="btn btn-primary btn-lg">Crear cuenta gratis</a>
                        <a href="{{ route('login') }}" class="btn btn-outline btn-lg">Ya tengo cuenta</a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <div class="container">
            <div class="footer-grid">
                <div>
                    <img src="{{ asset('images/logo-horizontal.svg') }}" alt="MiEntreno" style="height:34px;width:auto;margin-bottom:1rem;">
                    <p class="text-muted" style="max-width:22rem;font-size:.92rem;">Entrenamientos, salud médica y zapatillas en una sola app, pensada por y para runners.</p>
                </div>
                <div>
                    <h4>Producto</h4>
                    <ul>
                        <li><a href="#entrenamientos">Entrenamientos</a></li>
                        <li><a href="#salud">Salud médica</a></li>
                        <li><a href="#zapatillas">Zapatillas</a></li>
                        <li><a href="#coaches">Modo coach</a></li>
                    </ul>
                </div>
                <div>
                    <h4>Cuenta</h4>
                    <ul>
                        <li><a href="{{ route('register') }}">Crear cuenta</a></li>
                        <li><a href="{{ route('login') }}">Ingresar</a></li>
                        <li><a href="#faq">Preguntas frecuentes</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <span>© {{ date('Y') }} MiEntreno · Más que números, tu historia running</span>
                <span>Hecho con ❤️ por <a href="https://srojasweb.dev" target="_blank" rel="noopener noreferrer" class="text-green">srojasweb.dev</a></span>
            </div>
        </div>
    </footer>
</div>

<script>
    (function () {
        const nav = document.getElementById('nav');
        const toggle = document.getElementById('navToggle');
        const menu = document.getElementById('mobileMenu');

        const onScroll = () => nav.classList.toggle('scrolled', window.scrollY > 10);
        onScroll();
        window.addEventListener('scroll', onScroll, { passive: true });

        toggle.addEventListener('click', () => {
            const isOpen = menu.classList.toggle('open');
            toggle.setAttribute('aria-expanded', isOpen);
        });
        menu.querySelectorAll('a').forEach((link) => link.addEventListener('click', () => {
            menu.classList.remove('open');
            toggle.setAttribute('aria-expanded', 'false');
        }));

        const fillRing = (ring) => {
            const circle = ring.querySelector('.value');
            const progress = Math.min(parseFloat(ring.dataset.progress), 1);
            circle.style.strokeDashoffset = 327 * (1 - progress);
        };

        if (!('IntersectionObserver' in window)) {
            document.querySelectorAll('.reveal').forEach((el) => el.classList.add('visible'));
            document.querySelectorAll('.ring').forEach(fillRing);
            return;
        }

        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) {
                    return;
                }
                entry.target.classList.add('visible');
                entry.target.querySelectorAll('.ring').forEach(fillRing);
                observer.unobserve(entry.target);
            });
        }, { threshold: 0.15, rootMargin: '0px 0px -40px 0px' });

        document.querySelectorAll('.reveal').forEach((el) => observer.observe(el));
    })();
</script>
</body>
</html>
