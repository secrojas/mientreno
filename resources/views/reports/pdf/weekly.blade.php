<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte Semanal — {{ $report['period']['label'] }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DejaVu Sans', Helvetica, Arial, sans-serif;
            font-size: 8.5pt;
            line-height: 1.35;
            color: #0F172A;
            background: #FFFFFF;
        }

        /* ── HEADER ─────────────────────────── */
        .header { background: #0A0B0F; padding: 12px 20px 0; }

        .header-inner { display: table; width: 100%; }
        .header-left  { display: table-cell; width: 60%; vertical-align: middle; padding-bottom: 12px; }
        .header-right { display: table-cell; width: 40%; vertical-align: middle; text-align: right; padding-bottom: 12px; }

        .logo { font-size: 7.5pt; font-weight: bold; letter-spacing: 0.32em; color: #2DE38E; text-transform: uppercase; margin-bottom: 5px; }
        .logo-dot { color: #FF3B5C; }
        .report-type { font-size: 17pt; font-weight: bold; color: #FFFFFF; line-height: 1; margin-bottom: 4px; }
        .report-period { font-size: 8pt; color: #94A3B8; }
        .athlete-name { font-size: 10pt; font-weight: bold; color: #FFFFFF; margin-bottom: 2px; }
        .athlete-meta { font-size: 7.5pt; color: #64748B; }
        .header-accent { height: 3px; background: #2DE38E; }

        /* ── HERO ───────────────────────────── */
        .hero { padding: 12px 20px; border-bottom: 1px solid #E2E8F0; }
        .hero-inner { display: table; width: 100%; }
        .hero-main {
            display: table-cell; width: 40%; vertical-align: middle;
            border-right: 1px solid #E2E8F0; padding-right: 18px;
        }
        .hero-km { font-size: 34pt; font-weight: bold; color: #2DE38E; line-height: 1; }
        .hero-unit { font-size: 11pt; color: #94A3B8; font-weight: normal; margin-left: 3px; }
        .hero-label { font-size: 7pt; color: #94A3B8; text-transform: uppercase; letter-spacing: 0.1em; margin-top: 3px; margin-bottom: 6px; }

        .trend-badge { display: inline-block; font-size: 7.5pt; font-weight: bold; padding: 2px 8px; border-radius: 20px; }
        .trend-up     { background: #F0FDF4; color: #16A34A; border: 1px solid #BBF7D0; }
        .trend-down   { background: #FEF2F2; color: #DC2626; border: 1px solid #FECACA; }
        .trend-stable { background: #F8FAFC; color: #64748B; border: 1px solid #E2E8F0; }

        .hero-stats { display: table-cell; width: 60%; vertical-align: middle; padding-left: 20px; }
        .stats-grid { display: table; width: 100%; }
        .stat-col { display: table-cell; text-align: center; vertical-align: middle; padding: 0 10px; border-left: 1px solid #E2E8F0; }
        .stat-col:first-child { border-left: none; padding-left: 0; }
        .stat-value { font-size: 13pt; font-weight: bold; color: #0F172A; line-height: 1; margin-bottom: 4px; }
        .stat-value-pace { color: #FF3B5C; }
        .stat-label { font-size: 6.5pt; color: #94A3B8; text-transform: uppercase; letter-spacing: 0.08em; }

        /* ── BODY ───────────────────────────── */
        .body-section { padding: 10px 20px 0; }

        .card-title { font-size: 7.5pt; font-weight: bold; color: #0F172A; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.07em; }

        /* ── TWO COL ────────────────────────── */
        .two-col-wrap { display: table; width: 100%; }
        .tc-left  { display: table-cell; width: 50%; vertical-align: top; padding-right: 8px; }
        .tc-right { display: table-cell; width: 50%; vertical-align: top; padding-left: 8px; }

        /* ── COMPARISON CARD ─────────────────── */
        .compare-card { background: #F8FAFC; border: 1px solid #E2E8F0; border-top: 3px solid #2DE38E; padding: 8px 10px; }
        .cmp-row { display: table; width: 100%; margin-bottom: 6px; }
        .cmp-row:last-child { margin-bottom: 0; }
        .cmp-metric  { display: table-cell; width: 32%; font-size: 7.5pt; color: #64748B; vertical-align: middle; }
        .cmp-current { display: table-cell; width: 28%; font-size: 8.5pt; font-weight: bold; color: #0F172A; vertical-align: middle; }
        .cmp-diff    { display: table-cell; font-size: 7.5pt; font-weight: bold; text-align: right; vertical-align: middle; }
        .up     { color: #16A34A; }
        .down   { color: #DC2626; }
        .stable { color: #64748B; }

        /* ── DISTRIBUTION ────────────────────── */
        .distrib-card { background: #F8FAFC; border: 1px solid #E2E8F0; border-top: 3px solid #FF3B5C; padding: 8px 10px; }
        .distrib-row { display: table; width: 100%; margin-bottom: 7px; }
        .distrib-row:last-child { margin-bottom: 0; }
        .d-dot  { display: table-cell; width: 12px; vertical-align: middle; }
        .d-name { display: table-cell; width: 36%; font-size: 7.5pt; color: #0F172A; vertical-align: middle; padding-right: 6px; }
        .d-bar  { display: table-cell; vertical-align: middle; padding-right: 6px; }
        .d-pct  { display: table-cell; width: 28px; font-size: 7.5pt; font-weight: bold; text-align: right; vertical-align: middle; }
        .d-info { display: table-cell; width: 64px; font-size: 6.5pt; color: #94A3B8; text-align: right; vertical-align: middle; }
        .bar-track { width: 100%; border-collapse: collapse; }

        /* ── INSIGHTS ────────────────────────── */
        .insights-card { background: #F8FAFC; border: 1px solid #E2E8F0; border-top: 3px solid #8B5CF6; padding: 8px 10px; margin-top: 10px; }
        .insight-item { font-size: 8pt; color: #0F172A; margin-bottom: 4px; line-height: 1.35; }
        .insight-item:last-child { margin-bottom: 0; }

        /* ── TABLE ───────────────────────────── */
        .table-label {
            font-size: 6.5pt; font-weight: bold; text-transform: uppercase;
            letter-spacing: 0.13em; color: #94A3B8;
            margin-bottom: 7px; padding-bottom: 5px; border-bottom: 1px solid #E2E8F0;
        }

        .workout-table { width: 100%; border-collapse: collapse; font-size: 7.5pt; }

        .workout-table thead tr { background: #0A0B0F; }
        .workout-table th {
            padding: 6px 6px; text-align: left;
            font-size: 6.5pt; font-weight: bold; text-transform: uppercase;
            letter-spacing: 0.08em; color: #94A3B8;
        }
        .workout-table td { padding: 5px 6px; vertical-align: middle; color: #0F172A; }

        .row-odd  td { background: #FFFFFF; border-bottom: 1px solid #F1F5F9; }
        .row-even td { background: #F8FAFC; border-bottom: 1px solid #F1F5F9; }
        .row-notes td {
            padding: 2px 6px 6px; font-style: italic;
            font-size: 7pt; color: #64748B;
        }

        .type-badge { display: inline-block; padding: 1px 5px; border-radius: 3px; font-size: 6.5pt; font-weight: bold; }

        /* ── FOOTER ──────────────────────────── */
        .footer { display: table; width: 100%; margin-top: 10px; padding: 8px 20px 0; border-top: 1px solid #E2E8F0; }
        .footer-left  { display: table-cell; font-size: 6.5pt; color: #94A3B8; vertical-align: middle; }
        .footer-right { display: table-cell; font-size: 6.5pt; color: #94A3B8; text-align: right; vertical-align: middle; }
        .footer-brand { font-weight: bold; color: #2DE38E; }
    </style>
</head>
<body>
    @php
        $period       = $report['period'];
        $summary      = $report['summary'];
        $distribution = $report['distribution'];
        $comparison   = $report['comparison'];
        $workouts     = $report['workouts'];
        $insights     = $report['insights'];

        $typeColors = [
            'easy_run'     => ['label' => 'Fondo Suave',       'hex' => '#2563EB', 'bg' => '#EFF6FF', 'text' => '#1D4ED8'],
            'intervals'    => ['label' => 'Series/Intervalos', 'hex' => '#FF3B5C', 'bg' => '#FFF1F2', 'text' => '#BE123C'],
            'tempo'        => ['label' => 'Ritmo Sostenido',   'hex' => '#D97706', 'bg' => '#FFFBEB', 'text' => '#B45309'],
            'long_run'     => ['label' => 'Fondo Largo',       'hex' => '#7C3AED', 'bg' => '#F5F3FF', 'text' => '#6D28D9'],
            'recovery'     => ['label' => 'Recuperación',      'hex' => '#059669', 'bg' => '#F0FDF4', 'text' => '#047857'],
            'race'         => ['label' => 'Carrera',           'hex' => '#DB2777', 'bg' => '#FDF2F8', 'text' => '#9D174D'],
            'training_run' => ['label' => 'Entrenamiento',     'hex' => '#0891B2', 'bg' => '#ECFEFF', 'text' => '#0E7490'],
        ];

        $hasTrend  = $comparison['distance']['previous'] > 0;
        $distTrend = $comparison['distance'];
        $rowIndex  = 0;
    @endphp

    {{-- HEADER --}}
    <div class="header">
        <div class="header-inner">
            <div class="header-left">
                <div class="logo">MI<span class="logo-dot">&middot;</span>ENTRENO</div>
                <div class="report-type">Reporte Semanal</div>
                <div class="report-period">
                    {{ $period['label'] }}
                    &nbsp;&bull;&nbsp;
                    {{ $period['start_date']->locale('es')->isoFormat('D MMM') }} &ndash; {{ $period['end_date']->locale('es')->isoFormat('D MMM YYYY') }}
                </div>
            </div>
            <div class="header-right">
                <div class="athlete-name">{{ auth()->user()->name }}</div>
                <div class="athlete-meta">{{ now()->locale('es')->isoFormat('D MMMM YYYY') }}</div>
                <div class="athlete-meta" style="margin-top: 3px; color: #2DE38E; font-size: 7pt;">mientreno.app</div>
            </div>
        </div>
        <div class="header-accent"></div>
    </div>

    {{-- HERO --}}
    <div class="hero">
        <div class="hero-inner">
            <div class="hero-main">
                <span class="hero-km">{{ number_format($summary['total_distance'], 1) }}</span><span class="hero-unit">km</span>
                <div class="hero-label">Total esta semana</div>
                @if($hasTrend)
                    @php $pct = abs($distTrend['percentage']); @endphp
                    <span class="trend-badge trend-{{ $distTrend['trend'] }}">
                        @if($distTrend['trend'] === 'up') ↑ +{{ $pct }}%
                        @elseif($distTrend['trend'] === 'down') ↓ -{{ $pct }}%
                        @else → Sin cambio @endif
                        vs sem. ant.
                    </span>
                @endif
            </div>
            <div class="hero-stats">
                <div class="stats-grid">
                    <div class="stat-col">
                        <div class="stat-value">{{ $summary['formatted_duration'] }}</div>
                        <div class="stat-label">Tiempo</div>
                    </div>
                    <div class="stat-col">
                        <div class="stat-value">{{ $summary['total_sessions'] }}</div>
                        <div class="stat-label">Sesiones</div>
                    </div>
                    <div class="stat-col">
                        <div class="stat-value stat-value-pace">{{ $summary['formatted_pace'] }}</div>
                        <div class="stat-label">Pace prom.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- COMPARISON + DISTRIBUTION --}}
    <div class="body-section">
        <div class="two-col-wrap">
            <div class="tc-left">
                @if($hasTrend)
                    <div class="compare-card">
                        <div class="card-title">VS Semana Anterior</div>
                        <div class="cmp-row">
                            <div class="cmp-metric">Distancia</div>
                            <div class="cmp-current">{{ number_format($summary['total_distance'], 1) }} km</div>
                            <div class="cmp-diff {{ $comparison['distance']['trend'] === 'up' ? 'up' : ($comparison['distance']['trend'] === 'down' ? 'down' : 'stable') }}">
                                {{ $comparison['distance']['diff'] > 0 ? '+' : '' }}{{ number_format($comparison['distance']['diff'], 1) }} km
                                ({{ $comparison['distance']['percentage'] > 0 ? '+' : '' }}{{ $comparison['distance']['percentage'] }}%)
                            </div>
                        </div>
                        <div class="cmp-row">
                            <div class="cmp-metric">Sesiones</div>
                            <div class="cmp-current">{{ $summary['total_sessions'] }}</div>
                            <div class="cmp-diff {{ $comparison['sessions']['trend'] === 'up' ? 'up' : ($comparison['sessions']['trend'] === 'down' ? 'down' : 'stable') }}">
                                {{ $comparison['sessions']['diff'] > 0 ? '+' : '' }}{{ $comparison['sessions']['diff'] }}
                                ({{ $comparison['sessions']['percentage'] > 0 ? '+' : '' }}{{ $comparison['sessions']['percentage'] }}%)
                            </div>
                        </div>
                        @if($summary['avg_pace'] && ($comparison['pace']['previous'] ?? false))
                            <div class="cmp-row">
                                <div class="cmp-metric">Pace</div>
                                <div class="cmp-current">{{ $summary['formatted_pace'] }}/km</div>
                                <div class="cmp-diff {{ ($comparison['pace']['improved'] ?? false) ? 'up' : 'down' }}">
                                    {{ ($comparison['pace']['improved'] ?? false) ? '↑ Mejora' : '↓ Declive' }}
                                    ({{ abs($comparison['pace']['diff'] ?? 0) }}s)
                                </div>
                            </div>
                        @endif
                    </div>
                @else
                    <div style="padding: 14px; text-align: center; color: #94A3B8; font-size: 7.5pt; background: #F8FAFC; border: 1px solid #E2E8F0;">
                        Sin datos de semana anterior.
                    </div>
                @endif
            </div>
            <div class="tc-right">
                @if(!empty($distribution))
                    <div class="distrib-card">
                        <div class="card-title">Distribución por Tipo</div>
                        @foreach($distribution as $type => $data)
                            @php $color = $typeColors[$type] ?? ['label' => $type, 'hex' => '#94A3B8', 'bg' => '#F1F5F9', 'text' => '#475569']; @endphp
                            <div class="distrib-row">
                                <div class="d-dot">
                                    <div style="width: 7px; height: 7px; background: {{ $color['hex'] }};"></div>
                                </div>
                                <div class="d-name">{{ $color['label'] }}</div>
                                <div class="d-bar">
                                    <table class="bar-track" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td style="width: {{ $data['percentage'] }}%; background: {{ $color['hex'] }}; height: 6px; padding: 0;"></td>
                                            @if($data['percentage'] < 100)
                                                <td style="background: #E2E8F0; height: 6px; padding: 0;"></td>
                                            @endif
                                        </tr>
                                    </table>
                                </div>
                                <div class="d-pct" style="color: {{ $color['hex'] }}">{{ $data['percentage'] }}%</div>
                                <div class="d-info">{{ $data['count'] }}x &middot; {{ number_format($data['distance'], 1) }}km</div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- INSIGHTS --}}
    @if(!empty($insights))
        <div class="body-section">
            <div class="insights-card">
                <div class="card-title">Highlights de la semana</div>
                <div style="display: table; width: 100%;">
                    @php $insightChunks = array_chunk($insights, 2); @endphp
                    @foreach($insightChunks as $chunk)
                        <div style="display: table-row;">
                            @foreach($chunk as $insight)
                                <div style="display: table-cell; width: 50%; padding-right: 10px; vertical-align: top; padding-bottom: 3px;">
                                    <span style="color: #2DE38E; font-weight: bold; margin-right: 4px;">&bull;</span>{{ $insight['message'] }}
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    {{-- TABLE --}}
    <div class="body-section" style="padding-top: 12px;">
        <div class="table-label">
            Detalle &mdash; {{ $workouts->count() }} {{ $workouts->count() === 1 ? 'sesión' : 'sesiones' }}
        </div>
        @if($workouts->isEmpty())
            <div style="text-align: center; color: #64748B; padding: 14px; background: #F8FAFC; border: 1px solid #E2E8F0; font-size: 8pt;">
                No hay entrenamientos registrados en este período.
            </div>
        @else
            <table class="workout-table">
                <thead>
                    <tr>
                        <th style="width: 10%;">Fecha</th>
                        <th style="width: 22%;">Tipo</th>
                        <th style="width: 11%;">Dist.</th>
                        <th style="width: 12%;">Tiempo</th>
                        <th style="width: 11%;">Pace</th>
                        <th style="width: 10%;">FC</th>
                        <th style="width: 24%;">Dificultad</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($workouts as $workout)
                        @php
                            $rowIndex++;
                            $typeC    = $typeColors[$workout->type] ?? ['label' => $workout->type_label, 'bg' => '#F1F5F9', 'text' => '#475569'];
                            $rowClass = $rowIndex % 2 === 0 ? 'row-even' : 'row-odd';
                            $filled   = $workout->difficulty ?? 0;
                            $empty    = max(0, 5 - $filled);
                        @endphp
                        <tr class="{{ $rowClass }}">
                            <td>{{ $workout->date->locale('es')->isoFormat('D MMM') }}</td>
                            <td>
                                <span class="type-badge" style="background: {{ $typeC['bg'] }}; color: {{ $typeC['text'] }};">
                                    {{ $typeC['label'] }}
                                </span>
                            </td>
                            <td><strong>{{ number_format($workout->distance, 1) }} km</strong></td>
                            <td>{{ $workout->formatted_duration }}</td>
                            <td>{{ $workout->formatted_pace }}</td>
                            <td>
                                @if($workout->avg_heart_rate)
                                    {{ $workout->avg_heart_rate }}&thinsp;bpm
                                @else
                                    <span style="color: #D1D5DB;">&mdash;</span>
                                @endif
                            </td>
                            <td>
                                @if($filled)
                                    <span style="color: #F59E0B; font-size: 9pt;">{{ str_repeat('●', $filled) }}</span><span style="color: #D1D5DB; font-size: 9pt;">{{ str_repeat('○', $empty) }}</span>
                                @else
                                    <span style="color: #D1D5DB;">&mdash;</span>
                                @endif
                            </td>
                        </tr>
                        @if($workout->notes)
                            <tr class="row-notes" style="{{ $rowIndex % 2 === 0 ? 'background:#F8FAFC;' : 'background:#FFFFFF;' }}">
                                <td colspan="7">&#x270E;&nbsp;{{ mb_strimwidth($workout->notes, 0, 120, '…') }}</td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    {{-- FOOTER --}}
    <div class="footer">
        <div class="footer-left">
            <span class="footer-brand">MI&middot;ENTRENO</span>
            &nbsp;&mdash;&nbsp;
            Generado el {{ now()->locale('es')->isoFormat('D MMMM YYYY') }} a las {{ now()->format('H:i') }}
        </div>
        <div class="footer-right">mientreno.app</div>
    </div>
</body>
</html>
