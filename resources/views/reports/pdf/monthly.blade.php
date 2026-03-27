<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte Mensual — {{ $report['period']['label'] }}</title>
    <style>
        /* ── RESET ────────────────────────────────── */
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DejaVu Sans', Helvetica, Arial, sans-serif;
            font-size: 9pt;
            line-height: 1.45;
            color: #0F172A;
            background: #FFFFFF;
        }

        /* ── HEADER DARK ──────────────────────────── */
        .header {
            background: #0A0B0F;
            padding: 12px 20px 0;
        }

        .header-inner {
            display: table;
            width: 100%;
        }

        .header-left {
            display: table-cell;
            width: 60%;
            vertical-align: middle;
            padding-bottom: 12px;
        }

        .header-right {
            display: table-cell;
            width: 40%;
            vertical-align: middle;
            text-align: right;
            padding-bottom: 12px;
        }

        .logo {
            font-size: 8.5pt;
            font-weight: bold;
            letter-spacing: 0.35em;
            color: #2DE38E;
            text-transform: uppercase;
            margin-bottom: 7px;
        }

        .logo-dot { color: #FF3B5C; }

        .report-type {
            font-size: 17pt;
            font-weight: bold;
            color: #FFFFFF;
            line-height: 1;
            letter-spacing: -0.01em;
            margin-bottom: 5px;
        }

        .report-period {
            font-size: 8.5pt;
            color: #94A3B8;
        }

        .athlete-name {
            font-size: 11pt;
            font-weight: bold;
            color: #FFFFFF;
            margin-bottom: 3px;
        }

        .athlete-meta {
            font-size: 8pt;
            color: #64748B;
        }

        .header-accent {
            height: 3px;
            background: #FF3B5C;
        }

        /* ── HERO ─────────────────────────────────── */
        .hero {
            padding: 12px 20px;
            border-bottom: 1px solid #E2E8F0;
        }

        .hero-inner {
            display: table;
            width: 100%;
        }

        .hero-main {
            display: table-cell;
            width: 36%;
            vertical-align: middle;
            border-right: 1px solid #E2E8F0;
            padding-right: 20px;
        }

        .hero-km {
            font-size: 34pt;
            font-weight: bold;
            color: #2DE38E;
            line-height: 1;
            letter-spacing: -0.02em;
        }

        .hero-unit {
            font-size: 13pt;
            color: #94A3B8;
            font-weight: normal;
            margin-left: 4px;
        }

        .hero-label {
            font-size: 7.5pt;
            color: #94A3B8;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            margin-top: 2px;
            margin-bottom: 5px;
        }

        .trend-badge {
            display: inline-block;
            font-size: 8pt;
            font-weight: bold;
            padding: 3px 10px;
            border-radius: 20px;
        }

        .trend-up     { background: #F0FDF4; color: #16A34A; border: 1px solid #BBF7D0; }
        .trend-down   { background: #FEF2F2; color: #DC2626; border: 1px solid #FECACA; }
        .trend-stable { background: #F8FAFC; color: #64748B; border: 1px solid #E2E8F0; }

        .hero-stats {
            display: table-cell;
            width: 64%;
            vertical-align: middle;
            padding-left: 24px;
        }

        .stats-grid-row1 {
            display: table;
            width: 100%;
            margin-bottom: 6px;
        }

        .stats-grid-row2 {
            display: table;
            width: 100%;
        }

        .stat-col {
            display: table-cell;
            text-align: center;
            vertical-align: middle;
            padding: 0 10px;
            border-left: 1px solid #E2E8F0;
        }

        .stat-col:first-child { border-left: none; padding-left: 0; }

        .stat-value {
            font-size: 12pt;
            font-weight: bold;
            color: #0F172A;
            line-height: 1;
            margin-bottom: 3px;
        }

        .stat-value-pace  { color: #FF3B5C; }
        .stat-value-hr    { color: #EF4444; }
        .stat-value-elev  { color: #8B5CF6; }

        .stat-label {
            font-size: 7pt;
            color: #94A3B8;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }

        /* ── BODY ─────────────────────────────────── */
        .body-section { padding: 10px 20px 0; }

        .section-label {
            font-size: 7pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            color: #94A3B8;
            margin-bottom: 7px;
            padding-bottom: 4px;
            border-bottom: 1px solid #E2E8F0;
        }

        .card-title {
            font-size: 8pt;
            font-weight: bold;
            color: #0F172A;
            margin-bottom: 7px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        /* ── COMPARISON TABLE ────────────────────── */
        .compare-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
        }

        .compare-table thead tr {
            background: #0A0B0F;
        }

        .compare-table th {
            padding: 6px 8px;
            text-align: left;
            font-size: 7pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #94A3B8;
        }

        .compare-table tbody td {
            padding: 6px 8px;
            border-bottom: 1px solid #F1F5F9;
            color: #0F172A;
        }

        .compare-table tbody tr:nth-child(odd) td  { background: #FFFFFF; }
        .compare-table tbody tr:nth-child(even) td { background: #F8FAFC; }

        .up     { color: #16A34A; font-weight: bold; }
        .down   { color: #DC2626; font-weight: bold; }
        .stable { color: #64748B; font-weight: bold; }

        /* ── TWO COL ──────────────────────────────── */
        .two-col-wrap { display: table; width: 100%; }
        .tc-left  { display: table-cell; width: 50%; vertical-align: top; padding-right: 12px; }
        .tc-right { display: table-cell; width: 50%; vertical-align: top; padding-left: 12px; }

        /* ── DISTRIBUTION ────────────────────────── */
        .distrib-card {
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-top: 3px solid #FF3B5C;
            padding: 8px 10px;
        }

        .distrib-row { display: table; width: 100%; margin-bottom: 7px; }
        .distrib-row:last-child { margin-bottom: 0; }
        .d-dot   { display: table-cell; width: 12px; vertical-align: middle; }
        .d-name  { display: table-cell; width: 36%; font-size: 8pt; color: #0F172A; vertical-align: middle; padding-right: 8px; }
        .d-bar   { display: table-cell; vertical-align: middle; padding-right: 8px; }
        .d-pct   { display: table-cell; width: 32px; font-size: 8pt; font-weight: bold; text-align: right; vertical-align: middle; }
        .d-info  { display: table-cell; width: 74px; font-size: 7pt; color: #94A3B8; text-align: right; vertical-align: middle; }

        .bar-track {
            width: 100%;
            border-collapse: collapse;
        }

        /* ── INSIGHTS ────────────────────────────── */
        .insights-card {
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-top: 3px solid #8B5CF6;
            padding: 8px 10px;
        }

        .insight-item {
            font-size: 8pt;
            color: #0F172A;
            margin-bottom: 5px;
            line-height: 1.4;
        }

        .insight-item:last-child { margin-bottom: 0; }

        /* ── PAGE 2: WORKOUT TABLE ───────────────── */
        .page-break { page-break-before: always; }

        .page2-header {
            display: table;
            width: 100%;
            margin-bottom: 10px;
            padding: 8px 20px;
            background: #0A0B0F;
        }

        .p2h-left {
            display: table-cell;
            vertical-align: middle;
        }

        .p2h-right {
            display: table-cell;
            vertical-align: middle;
            text-align: right;
        }

        .p2-logo {
            font-size: 8pt;
            font-weight: bold;
            letter-spacing: 0.25em;
            color: #2DE38E;
        }

        .p2-period {
            font-size: 8pt;
            color: #64748B;
        }

        .workout-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8pt;
        }

        .workout-table thead tr {
            background: #0A0B0F;
        }

        .workout-table th {
            padding: 6px 5px;
            text-align: left;
            font-size: 7pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.09em;
            color: #94A3B8;
        }

        .workout-table td {
            padding: 5px 5px;
            vertical-align: middle;
            color: #0F172A;
        }

        .row-odd  td { background: #FFFFFF; border-bottom: 1px solid #F1F5F9; }
        .row-even td { background: #F8FAFC; border-bottom: 1px solid #F1F5F9; }

        .row-notes td {
            padding: 3px 7px 9px;
            font-style: italic;
            font-size: 7.5pt;
            color: #64748B;
        }

        .type-badge {
            display: inline-block;
            padding: 2px 7px;
            border-radius: 4px;
            font-size: 7pt;
            font-weight: bold;
        }

        .diff-filled { color: #F59E0B; }
        .diff-empty  { color: #D1D5DB; }

        .table-total {
            display: table;
            width: 100%;
            margin-top: 12px;
            padding: 10px 12px;
            background: #F0FDF4;
            border-left: 4px solid #2DE38E;
        }

        /* ── FOOTER ──────────────────────────────── */
        .footer {
            display: table;
            width: 100%;
            margin-top: 10px;
            padding: 8px 20px 0;
            border-top: 1px solid #E2E8F0;
        }

        .footer-left {
            display: table-cell;
            font-size: 7pt;
            color: #94A3B8;
            vertical-align: middle;
        }

        .footer-right {
            display: table-cell;
            font-size: 7pt;
            color: #94A3B8;
            text-align: right;
            vertical-align: middle;
        }

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
            'easy_run'     => ['label' => 'Fondo Suave',         'hex' => '#2563EB', 'bg' => '#EFF6FF', 'text' => '#1D4ED8'],
            'intervals'    => ['label' => 'Series/Intervalos',   'hex' => '#FF3B5C', 'bg' => '#FFF1F2', 'text' => '#BE123C'],
            'tempo'        => ['label' => 'Ritmo Sostenido',     'hex' => '#D97706', 'bg' => '#FFFBEB', 'text' => '#B45309'],
            'long_run'     => ['label' => 'Fondo Largo',         'hex' => '#7C3AED', 'bg' => '#F5F3FF', 'text' => '#6D28D9'],
            'recovery'     => ['label' => 'Recuperación',        'hex' => '#059669', 'bg' => '#F0FDF4', 'text' => '#047857'],
            'race'         => ['label' => 'Carrera',             'hex' => '#DB2777', 'bg' => '#FDF2F8', 'text' => '#9D174D'],
            'training_run' => ['label' => 'Entrenamiento',       'hex' => '#0891B2', 'bg' => '#ECFEFF', 'text' => '#0E7490'],
        ];

        $hasTrend  = $comparison['distance']['previous'] > 0;
        $distTrend = $comparison['distance'];
        $rowIndex  = 0;
    @endphp

    {{-- ════════════════ HEADER ════════════════ --}}
    <div class="header">
        <div class="header-inner">
            <div class="header-left">
                <div class="logo">MI<span class="logo-dot">&middot;</span>ENTRENO</div>
                <div class="report-type">Reporte Mensual</div>
                <div class="report-period">
                    {{ $period['label'] }}
                    &nbsp;&bull;&nbsp;
                    {{ $period['start_date']->locale('es')->isoFormat('D MMM') }} &ndash; {{ $period['end_date']->locale('es')->isoFormat('D MMM YYYY') }}
                </div>
            </div>
            <div class="header-right">
                <div class="athlete-name">{{ auth()->user()->name }}</div>
                <div class="athlete-meta">{{ now()->locale('es')->isoFormat('D MMMM YYYY') }}</div>
                <div class="athlete-meta" style="margin-top: 4px; color: #FF3B5C; font-size: 7.5pt;">mientreno.app</div>
            </div>
        </div>
        <div class="header-accent"></div>
    </div>

    {{-- ════════════════ HERO ════════════════ --}}
    <div class="hero">
        <div class="hero-inner">
            <div class="hero-main">
                <div>
                    <span class="hero-km">{{ number_format($summary['total_distance'], 1) }}</span>
                    <span class="hero-unit">km</span>
                </div>
                <div class="hero-label">Total este mes</div>
                @if($hasTrend)
                    @php $pct = abs($distTrend['percentage']); @endphp
                    <span class="trend-badge trend-{{ $distTrend['trend'] }}">
                        @if($distTrend['trend'] === 'up') &#8593; +{{ $pct }}%
                        @elseif($distTrend['trend'] === 'down') &#8595; -{{ $pct }}%
                        @else &#8594; Sin cambio @endif
                        vs mes ant.
                    </span>
                @endif
            </div>
            <div class="hero-stats">
                <div class="stats-grid-row1">
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
                @if($summary['avg_heart_rate'] || $summary['elevation_gain'])
                    <div class="stats-grid-row2">
                        @if($summary['avg_heart_rate'])
                            <div class="stat-col">
                                <div class="stat-value stat-value-hr">{{ round($summary['avg_heart_rate']) }}</div>
                                <div class="stat-label">FC prom. bpm</div>
                            </div>
                        @endif
                        @if($summary['elevation_gain'])
                            <div class="stat-col">
                                <div class="stat-value stat-value-elev">{{ number_format($summary['elevation_gain']) }}</div>
                                <div class="stat-label">Desnivel m+</div>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ════════════════ COMPARISON TABLE ════════════════ --}}
    @if($hasTrend)
        <div class="body-section">
            <div class="section-label">Comparativa con mes anterior</div>
            <table class="compare-table">
                <thead>
                    <tr>
                        <th style="width: 22%;">Métrica</th>
                        <th style="width: 22%;">Este mes</th>
                        <th style="width: 22%;">Mes anterior</th>
                        <th style="width: 18%;">Diferencia</th>
                        <th style="width: 16%;">Tendencia</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Distancia</strong></td>
                        <td>{{ number_format($summary['total_distance'], 1) }} km</td>
                        <td>{{ number_format($comparison['distance']['previous'], 1) }} km</td>
                        <td>{{ $comparison['distance']['diff'] > 0 ? '+' : '' }}{{ number_format($comparison['distance']['diff'], 1) }} km</td>
                        <td class="{{ $comparison['distance']['trend'] === 'up' ? 'up' : ($comparison['distance']['trend'] === 'down' ? 'down' : 'stable') }}">
                            {{ $comparison['distance']['percentage'] > 0 ? '+' : '' }}{{ $comparison['distance']['percentage'] }}%
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Sesiones</strong></td>
                        <td>{{ $summary['total_sessions'] }}</td>
                        <td>{{ $comparison['sessions']['previous'] }}</td>
                        <td>{{ $comparison['sessions']['diff'] > 0 ? '+' : '' }}{{ $comparison['sessions']['diff'] }}</td>
                        <td class="{{ $comparison['sessions']['trend'] === 'up' ? 'up' : ($comparison['sessions']['trend'] === 'down' ? 'down' : 'stable') }}">
                            {{ $comparison['sessions']['percentage'] > 0 ? '+' : '' }}{{ $comparison['sessions']['percentage'] }}%
                        </td>
                    </tr>
                    @if($summary['avg_pace'] && ($comparison['pace']['previous'] ?? false))
                        <tr>
                            <td><strong>Pace promedio</strong></td>
                            <td>{{ $summary['formatted_pace'] }}/km</td>
                            <td>{{ $comparison['pace']['formatted_previous'] ?? '—' }}/km</td>
                            <td>{{ $comparison['pace']['diff'] > 0 ? '+' : '' }}{{ $comparison['pace']['diff'] ?? 0 }} seg</td>
                            <td class="{{ ($comparison['pace']['improved'] ?? false) ? 'up' : 'down' }}">
                                {{ ($comparison['pace']['improved'] ?? false) ? '↑ Mejora' : '↓ Declive' }}
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    @endif

    {{-- ════════════════ DISTRIBUTION + INSIGHTS ════════════════ --}}
    <div class="body-section">
        <div class="two-col-wrap">
            {{-- Distribución --}}
            <div class="tc-left">
                @if(!empty($distribution))
                    <div class="distrib-card">
                        <div class="card-title">Distribución por Tipo</div>
                        @foreach($distribution as $type => $data)
                            @php $color = $typeColors[$type] ?? ['label' => $type, 'hex' => '#94A3B8', 'bg' => '#F1F5F9', 'text' => '#475569']; @endphp
                            <div class="distrib-row">
                                <div class="d-dot">
                                    <div style="width: 8px; height: 8px; background: {{ $color['hex'] }};"></div>
                                </div>
                                <div class="d-name">{{ $color['label'] }}</div>
                                <div class="d-bar">
                                    <table class="bar-track" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td style="width: {{ $data['percentage'] }}%; background: {{ $color['hex'] }}; height: 7px; padding: 0;"></td>
                                            @if($data['percentage'] < 100)
                                                <td style="background: #E2E8F0; height: 7px; padding: 0;"></td>
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

            {{-- Insights --}}
            <div class="tc-right">
                @if(!empty($insights))
                    <div class="insights-card">
                        <div class="card-title">Highlights del Mes</div>
                        @foreach($insights as $insight)
                            <div class="insight-item">
                                <span style="color: #FF3B5C; font-weight: bold; margin-right: 6px;">&bull;</span>{{ $insight['message'] }}
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ════════════════ PAGE BREAK → WORKOUT TABLE ════════════════ --}}
    <div class="page-break"></div>

    {{-- Mini header página 2 --}}
    <div class="page2-header">
        <div class="p2h-left">
            <span class="p2-logo">MI&middot;ENTRENO</span>
        </div>
        <div class="p2h-right">
            <span class="p2-period">{{ $period['label'] }} &mdash; Detalle de Entrenamientos</span>
        </div>
    </div>

    {{-- Tabla --}}
    <div style="padding: 0 20px;">
        @if($workouts->isEmpty())
            <div style="text-align: center; color: #64748B; padding: 20px; background: #F8FAFC; border: 1px solid #E2E8F0; font-size: 9pt;">
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
                        <th style="width: 12%;">Pace</th>
                        <th style="width: 10%;">FC</th>
                        <th style="width: 10%;">Desnivel</th>
                        <th style="width: 13%;">Dificultad</th>
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
                                @if($workout->elevation_gain)
                                    {{ number_format($workout->elevation_gain) }}&thinsp;m
                                @else
                                    <span style="color: #D1D5DB;">&mdash;</span>
                                @endif
                            </td>
                            <td>
                                @if($filled)
                                    <span class="diff-filled">{{ str_repeat('●', $filled) }}</span><span class="diff-empty">{{ str_repeat('○', $empty) }}</span>
                                @else
                                    <span style="color: #D1D5DB;">&mdash;</span>
                                @endif
                            </td>
                        </tr>
                        @if($workout->notes)
                            <tr class="row-notes" style="{{ $rowIndex % 2 === 0 ? 'background: #F8FAFC;' : 'background: #FFFFFF;' }}">
                                <td colspan="8">&#9998;&nbsp;{{ mb_strimwidth($workout->notes, 0, 120, '…') }}</td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>

            <div class="table-total">
                <strong>Total del mes:</strong>
                {{ $workouts->count() }} {{ $workouts->count() === 1 ? 'entrenamiento' : 'entrenamientos' }}
                &nbsp;&bull;&nbsp;
                {{ number_format($workouts->sum('distance'), 1) }} km
                &nbsp;&bull;&nbsp;
                {{ \Carbon\CarbonInterval::seconds($workouts->sum('duration'))->cascade()->forHumans(['short' => true]) }}
            </div>
        @endif
    </div>

    {{-- ════════════════ FOOTER ════════════════ --}}
    <div class="footer">
        <div class="footer-left">
            <span class="footer-brand">MI&middot;ENTRENO</span>
            &nbsp;&mdash;&nbsp;
            Generado el {{ now()->locale('es')->isoFormat('D MMMM YYYY') }} a las {{ now()->format('H:i') }}
        </div>
        <div class="footer-right">
            mientreno.app
        </div>
    </div>

</body>
</html>
