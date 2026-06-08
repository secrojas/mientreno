<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte Médico — {{ $user->name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DejaVu Sans', Helvetica, Arial, sans-serif;
            font-size: 9pt;
            line-height: 1.45;
            color: #0F172A;
            background: #FFFFFF;
        }

        /* HEADER */
        .header { background: #0A0B0F; padding: 12px 20px 0; }
        .header-inner { display: table; width: 100%; }
        .header-left  { display: table-cell; width: 60%; vertical-align: middle; padding-bottom: 12px; }
        .header-right { display: table-cell; width: 40%; vertical-align: middle; text-align: right; padding-bottom: 12px; }
        .logo { font-size: 8.5pt; font-weight: bold; letter-spacing: 0.35em; color: #2DE38E; text-transform: uppercase; margin-bottom: 7px; }
        .logo-dot { color: #FF3B5C; }
        .report-type  { font-size: 17pt; font-weight: bold; color: #FFFFFF; line-height: 1; letter-spacing: -0.01em; margin-bottom: 5px; }
        .report-since { font-size: 8.5pt; color: #94A3B8; }
        .athlete-name { font-size: 11pt; font-weight: bold; color: #FFFFFF; margin-bottom: 3px; }
        .athlete-meta { font-size: 8pt; color: #64748B; }
        .header-accent { height: 3px; background: #FF3B5C; }

        /* HERO */
        .hero { padding: 12px 20px; border-bottom: 1px solid #E2E8F0; }
        .hero-inner { display: table; width: 100%; }
        .hero-main  { display: table-cell; width: 36%; vertical-align: middle; border-right: 1px solid #E2E8F0; padding-right: 20px; }
        .hero-km    { font-size: 34pt; font-weight: bold; color: #2DE38E; line-height: 1; letter-spacing: -0.02em; }
        .hero-unit  { font-size: 13pt; color: #94A3B8; font-weight: normal; margin-left: 4px; }
        .hero-label { font-size: 7.5pt; color: #94A3B8; text-transform: uppercase; letter-spacing: 0.12em; margin-top: 2px; margin-bottom: 5px; }
        .hero-stats { display: table-cell; width: 64%; vertical-align: middle; padding-left: 24px; }
        .stats-row  { display: table; width: 100%; margin-bottom: 6px; }
        .stat-col   { display: table-cell; text-align: center; vertical-align: middle; padding: 0 10px; border-left: 1px solid #E2E8F0; }
        .stat-col:first-child { border-left: none; padding-left: 0; }
        .stat-value      { font-size: 12pt; font-weight: bold; color: #0F172A; line-height: 1; margin-bottom: 3px; }
        .stat-value-pace { color: #FF3B5C; }
        .stat-value-hr   { color: #EF4444; }
        .stat-label      { font-size: 7pt; color: #94A3B8; text-transform: uppercase; letter-spacing: 0.1em; }

        /* HR CARDS */
        .hr-row { display: table; width: 100%; margin-top: 6px; }
        .hr-col { display: table-cell; width: 33%; padding: 6px 8px; text-align: center; border: 1px solid #FEE2E2; background: #FFF5F5; }
        .hr-val { font-size: 11pt; font-weight: bold; color: #DC2626; }
        .hr-lbl { font-size: 7pt; color: #94A3B8; text-transform: uppercase; letter-spacing: 0.08em; }

        /* SECTION */
        .body-section { padding: 10px 20px 0; }
        .section-label {
            font-size: 7pt; font-weight: bold; text-transform: uppercase; letter-spacing: 0.15em;
            color: #94A3B8; margin-bottom: 7px; padding-bottom: 4px; border-bottom: 1px solid #E2E8F0;
        }

        /* MONTHLY TABLE */
        .month-table { width: 100%; border-collapse: collapse; font-size: 8pt; margin-bottom: 12px; }
        .month-table thead tr { background: #0A0B0F; }
        .month-table th { padding: 5px 8px; text-align: left; font-size: 7pt; font-weight: bold; text-transform: uppercase; letter-spacing: 0.08em; color: #94A3B8; }
        .month-table th.r { text-align: right; }
        .month-table tbody td { padding: 5px 8px; border-bottom: 1px solid #F1F5F9; color: #0F172A; }
        .month-table tbody td.r { text-align: right; }
        .month-table tbody tr:nth-child(odd) td  { background: #FFFFFF; }
        .month-table tbody tr:nth-child(even) td { background: #F8FAFC; }
        .month-table tfoot td { padding: 5px 8px; background: #F0FDF4; border-top: 2px solid #2DE38E; font-weight: bold; font-size: 8pt; }
        .month-table tfoot td.r { text-align: right; color: #2DE38E; }
        .year-label { font-size: 8pt; font-weight: bold; color: #0F172A; text-transform: uppercase; letter-spacing: 0.08em; margin: 10px 0 4px; }

        /* TOP 10 TABLE */
        .top-table { width: 100%; border-collapse: collapse; font-size: 8pt; }
        .top-table thead tr { background: #0A0B0F; }
        .top-table th { padding: 5px 6px; text-align: left; font-size: 7pt; font-weight: bold; text-transform: uppercase; letter-spacing: 0.08em; color: #94A3B8; }
        .top-table th.r { text-align: right; }
        .top-table tbody td { padding: 5px 6px; border-bottom: 1px solid #F1F5F9; color: #0F172A; }
        .top-table tbody td.r { text-align: right; }
        .top-table tbody tr:nth-child(odd) td  { background: #FFFFFF; }
        .top-table tbody tr:nth-child(even) td { background: #F8FAFC; }
        .rank-1 { color: #D97706; font-weight: bold; }
        .rank-2 { color: #94A3B8; font-weight: bold; }
        .rank-3 { color: #B45309; font-weight: bold; }
        .km-val { font-weight: bold; color: #D97706; }
        .hr-cell { color: #DC2626; }

        /* DISTRIBUTION */
        .distrib-card { background: #F8FAFC; border: 1px solid #E2E8F0; border-top: 3px solid #FF3B5C; padding: 8px 10px; }
        .card-title { font-size: 8pt; font-weight: bold; color: #0F172A; margin-bottom: 7px; text-transform: uppercase; letter-spacing: 0.08em; }
        .distrib-row { display: table; width: 100%; margin-bottom: 6px; }
        .distrib-row:last-child { margin-bottom: 0; }
        .d-dot  { display: table-cell; width: 12px; vertical-align: middle; }
        .d-name { display: table-cell; width: 36%; font-size: 8pt; color: #0F172A; vertical-align: middle; padding-right: 8px; }
        .d-bar  { display: table-cell; vertical-align: middle; padding-right: 8px; }
        .d-pct  { display: table-cell; width: 32px; font-size: 8pt; font-weight: bold; text-align: right; vertical-align: middle; }
        .d-info { display: table-cell; width: 80px; font-size: 7pt; color: #94A3B8; text-align: right; vertical-align: middle; }
        .bar-track { width: 100%; border-collapse: collapse; }

        /* PAGE */
        .page-break { page-break-before: always; }
        .page2-header { display: table; width: 100%; padding: 8px 20px; background: #0A0B0F; margin-bottom: 10px; }
        .p2h-left  { display: table-cell; vertical-align: middle; }
        .p2h-right { display: table-cell; vertical-align: middle; text-align: right; }
        .p2-logo   { font-size: 8pt; font-weight: bold; letter-spacing: 0.25em; color: #2DE38E; }
        .p2-period { font-size: 8pt; color: #64748B; }

        /* FOOTER */
        .footer { display: table; width: 100%; margin-top: 10px; padding: 8px 20px 0; border-top: 1px solid #E2E8F0; }
        .footer-left  { display: table-cell; font-size: 7pt; color: #94A3B8; vertical-align: middle; }
        .footer-right { display: table-cell; font-size: 7pt; color: #94A3B8; text-align: right; vertical-align: middle; }
        .footer-brand { font-weight: bold; color: #2DE38E; }

        /* APTO */
        .cert-row { display: table; width: 100%; padding: 7px 10px; border: 1px solid #E2E8F0; background: #F8FAFC; }
        .cert-left { display: table-cell; vertical-align: middle; font-size: 8.5pt; color: #0F172A; }
        .cert-right { display: table-cell; vertical-align: middle; text-align: right; font-size: 8pt; color: #64748B; }
        .badge-ok      { display: inline-block; padding: 2px 7px; border-radius: 20px; font-size: 7pt; font-weight: bold; background: #F0FDF4; color: #16A34A; border: 1px solid #BBF7D0; }
        .badge-warn    { display: inline-block; padding: 2px 7px; border-radius: 20px; font-size: 7pt; font-weight: bold; background: #FFFBEB; color: #D97706; border: 1px solid #FDE68A; }
        .badge-expired { display: inline-block; padding: 2px 7px; border-radius: 20px; font-size: 7pt; font-weight: bold; background: #FEF2F2; color: #DC2626; border: 1px solid #FECACA; }
        .two-col { display: table; width: 100%; }
        .tc-left  { display: table-cell; width: 50%; vertical-align: top; padding-right: 10px; }
        .tc-right { display: table-cell; width: 50%; vertical-align: top; padding-left: 10px; }
    </style>
</head>
<body>
@php
    $global = $report['global_summary'];
    $monthly = $report['monthly_breakdown'];
    $topWorkouts = $report['top_workouts'];
    $hrStats = $report['hr_stats'];
    $distribution = $report['distribution'];
    $firstWorkout = $report['first_workout'];

    $totalHours = floor($global['total_duration'] / 3600);
    $totalMins  = floor(($global['total_duration'] % 3600) / 60);

    $typeColors = [
        'easy_run'     => ['label' => 'Fondo Suave',         'hex' => '#2563EB'],
        'intervals'    => ['label' => 'Series/Intervalos',   'hex' => '#FF3B5C'],
        'tempo'        => ['label' => 'Ritmo Sostenido',     'hex' => '#D97706'],
        'long_run'     => ['label' => 'Fondo Largo',         'hex' => '#7C3AED'],
        'recovery'     => ['label' => 'Recuperación',        'hex' => '#059669'],
        'race'         => ['label' => 'Carrera',             'hex' => '#DB2777'],
        'training_run' => ['label' => 'Entrenamiento',       'hex' => '#0891B2'],
    ];
@endphp

{{-- HEADER --}}
<div class="header">
    <div class="header-inner">
        <div class="header-left">
            <div class="logo">MI<span class="logo-dot">&middot;</span>ENTRENO</div>
            <div class="report-type">Reporte Médico</div>
            <div class="report-since">
                Historial completo
                @if($firstWorkout)
                    &nbsp;&bull;&nbsp;
                    Desde {{ $firstWorkout->date->locale('es')->isoFormat('MMMM YYYY') }}
                @endif
            </div>
        </div>
        <div class="header-right">
            <div class="athlete-name">{{ $user->name }}</div>
            <div class="athlete-meta">{{ now()->locale('es')->isoFormat('D MMMM YYYY') }}</div>
            <div class="athlete-meta" style="margin-top: 4px; color: #FF3B5C; font-size: 7.5pt;">mientreno.app</div>
        </div>
    </div>
    <div class="header-accent"></div>
</div>

{{-- HERO --}}
<div class="hero">
    <div class="hero-inner">
        <div class="hero-main">
            <div>
                <span class="hero-km">{{ number_format($global['total_distance'], 1) }}</span>
                <span class="hero-unit">km</span>
            </div>
            <div class="hero-label">Kilómetros totales</div>
        </div>
        <div class="hero-stats">
            <div class="stats-row">
                <div class="stat-col">
                    <div class="stat-value">{{ ($totalHours > 0 ? $totalHours . 'h ' : '') . $totalMins . 'm' }}</div>
                    <div class="stat-label">Tiempo total</div>
                </div>
                <div class="stat-col">
                    <div class="stat-value">{{ $global['total_sessions'] }}</div>
                    <div class="stat-label">Sesiones</div>
                </div>
                <div class="stat-col">
                    <div class="stat-value stat-value-pace">{{ $global['formatted_pace'] }}</div>
                    <div class="stat-label">Pace prom.</div>
                </div>
                @if(!empty($hrStats))
                    <div class="stat-col">
                        <div class="stat-value stat-value-hr">{{ $hrStats['overall_avg'] }}</div>
                        <div class="stat-label">FC prom. bpm</div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- HR CARDS --}}
@if(!empty($hrStats))
    <div class="body-section">
        <div class="hr-row">
            <div class="hr-col">
                <div class="hr-val">{{ $hrStats['overall_avg'] }} bpm</div>
                <div class="hr-lbl">FC Promedio</div>
            </div>
            <div class="hr-col" style="border-left: none;">
                <div class="hr-val">{{ $hrStats['max'] }} bpm</div>
                <div class="hr-lbl">FC Máxima</div>
            </div>
            <div class="hr-col" style="border-left: none;">
                <div class="hr-val">{{ $hrStats['min'] }} bpm</div>
                <div class="hr-lbl">FC Mínima</div>
            </div>
        </div>
    </div>
@endif

{{-- APTO MÉDICO --}}
@if($fitnessCertificate)
    <div class="body-section" style="margin-top: 8px;">
        <div class="section-label">Apto Médico</div>
        <div class="cert-row">
            <div class="cert-left">
                <strong>{{ $fitnessCertificate->title }}</strong>
                @if($fitnessCertificate->isExpired())
                    <span class="badge-expired">VENCIDO</span>
                @elseif($fitnessCertificate->isExpiringSoon())
                    <span class="badge-warn">POR VENCER</span>
                @else
                    <span class="badge-ok">VIGENTE</span>
                @endif
            </div>
            <div class="cert-right">
                @if($fitnessCertificate->issued_at) Emitido: {{ $fitnessCertificate->issued_at->format('d/m/Y') }} &nbsp; @endif
                @if($fitnessCertificate->expires_at) Vence: {{ $fitnessCertificate->expires_at->format('d/m/Y') }} @endif
            </div>
        </div>
    </div>
@endif

{{-- DISTRIBUTION --}}
@if(!empty($distribution))
    <div class="body-section" style="margin-top: 8px;">
        <div class="two-col">
            <div class="tc-left">
                <div class="distrib-card">
                    <div class="card-title">Distribución por Tipo</div>
                    @foreach($distribution as $type => $data)
                        @php $color = $typeColors[$type] ?? ['label' => $type, 'hex' => '#94A3B8']; @endphp
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
            </div>
            <div class="tc-right">
                <div style="background: #F8FAFC; border: 1px solid #E2E8F0; border-top: 3px solid #2DE38E; padding: 8px 10px;">
                    <div class="card-title">Resumen Global</div>
                    <table style="width: 100%; font-size: 8pt; border-collapse: collapse;">
                        <tr>
                            <td style="padding: 3px 0; color: #64748B;">Total km</td>
                            <td style="padding: 3px 0; text-align: right; font-weight: bold; color: #2DE38E;">{{ number_format($global['total_distance'], 1) }} km</td>
                        </tr>
                        <tr>
                            <td style="padding: 3px 0; color: #64748B;">Total tiempo</td>
                            <td style="padding: 3px 0; text-align: right; font-weight: bold;">{{ ($totalHours > 0 ? $totalHours . 'h ' : '') . $totalMins . 'm' }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 3px 0; color: #64748B;">Total sesiones</td>
                            <td style="padding: 3px 0; text-align: right; font-weight: bold;">{{ $global['total_sessions'] }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 3px 0; color: #64748B;">Pace promedio</td>
                            <td style="padding: 3px 0; text-align: right; font-weight: bold; color: #FF3B5C;">{{ $global['formatted_pace'] }}/km</td>
                        </tr>
                        @if($firstWorkout)
                            <tr>
                                <td style="padding: 3px 0; color: #64748B;">Entrenando desde</td>
                                <td style="padding: 3px 0; text-align: right; font-weight: bold;">{{ $firstWorkout->date->locale('es')->isoFormat('MMM YYYY') }}</td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
@endif

{{-- ═══ PÁGINA 2: MES A MES ═══ --}}
<div class="page-break"></div>

<div class="page2-header">
    <div class="p2h-left"><span class="p2-logo">MI&middot;ENTRENO</span></div>
    <div class="p2h-right"><span class="p2-period">Progresión Mes a Mes</span></div>
</div>

<div style="padding: 0 20px;">
    @forelse($monthly as $year => $months)
        <div class="year-label">{{ $year }}</div>
        <table class="month-table">
            <thead>
                <tr>
                    <th>Mes</th>
                    <th class="r">Sesiones</th>
                    <th class="r" style="color: #2DE38E;">Km</th>
                    <th class="r">Tiempo</th>
                    <th class="r">Pace prom.</th>
                    <th class="r" style="color: #EF4444;">FC prom.</th>
                </tr>
            </thead>
            <tbody>
                @php $yearDist = 0; $yearSess = 0; $yearDur = 0; @endphp
                @foreach($months as $m)
                    @php $yearDist += $m['total_distance']; $yearSess += $m['total_sessions']; $yearDur += $m['total_duration']; @endphp
                    <tr>
                        <td>{{ $m['month_name'] }}</td>
                        <td class="r">{{ $m['total_sessions'] }}</td>
                        <td class="r" style="font-weight: bold; color: #2DE38E;">{{ number_format($m['total_distance'], 1) }}</td>
                        <td class="r">{{ $m['formatted_duration'] }}</td>
                        <td class="r">{{ $m['formatted_pace'] }}/km</td>
                        <td class="r" style="{{ $m['avg_heart_rate_month'] ? 'color: #DC2626;' : 'color: #D1D5DB;' }}">
                            {{ $m['avg_heart_rate_month'] ? $m['avg_heart_rate_month'] . ' bpm' : '&mdash;' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
            @php $yh = floor($yearDur / 3600); $ym = floor(($yearDur % 3600) / 60); @endphp
            <tfoot>
                <tr>
                    <td><strong>Total {{ $year }}</strong></td>
                    <td class="r">{{ $yearSess }}</td>
                    <td class="r">{{ number_format($yearDist, 1) }} km</td>
                    <td class="r">{{ $yh > 0 ? $yh . 'h ' : '' }}{{ $ym }}m</td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
        </table>
    @empty
        <p style="text-align: center; color: #94A3B8; padding: 20px;">Sin entrenamientos registrados.</p>
    @endforelse
</div>

{{-- ═══ PÁGINA 3: TOP 10 ═══ --}}
<div class="page-break"></div>

<div class="page2-header">
    <div class="p2h-left"><span class="p2-logo">MI&middot;ENTRENO</span></div>
    <div class="p2h-right"><span class="p2-period">Top 10 Recorridos por Distancia</span></div>
</div>

<div style="padding: 0 20px;">
    <table class="top-table">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 14%;">Fecha</th>
                <th style="width: 22%;">Tipo</th>
                <th class="r" style="width: 13%; color: #D97706;">Distancia</th>
                <th class="r" style="width: 13%;">Tiempo</th>
                <th class="r" style="width: 12%;">Pace</th>
                <th class="r" style="width: 11%; color: #EF4444;">FC</th>
                <th class="r" style="width: 10%;">Desnivel</th>
            </tr>
        </thead>
        <tbody>
            @foreach($topWorkouts as $i => $workout)
                @php
                    $rankClass = $i === 0 ? 'rank-1' : ($i === 1 ? 'rank-2' : ($i === 2 ? 'rank-3' : ''));
                    $rowBg = $i % 2 === 0 ? 'background: #FFFFFF;' : 'background: #F8FAFC;';
                @endphp
                <tr style="{{ $rowBg }}">
                    <td class="{{ $rankClass }}" style="text-align: center;">{{ $i + 1 }}</td>
                    <td>{{ $workout->date->locale('es')->isoFormat('D MMM YYYY') }}</td>
                    <td>{{ $workout->type_label }}</td>
                    <td class="r km-val">{{ number_format($workout->distance, 2) }} km</td>
                    <td class="r">{{ $workout->formatted_duration }}</td>
                    <td class="r">{{ $workout->formatted_pace }}/km</td>
                    <td class="r {{ $workout->avg_heart_rate ? 'hr-cell' : '' }}" style="{{ !$workout->avg_heart_rate ? 'color: #D1D5DB;' : '' }}">
                        {{ $workout->avg_heart_rate ? $workout->avg_heart_rate . ' bpm' : '&mdash;' }}
                    </td>
                    <td class="r" style="{{ !$workout->elevation_gain ? 'color: #D1D5DB;' : '' }}">
                        {{ $workout->elevation_gain ? number_format($workout->elevation_gain) . ' m' : '&mdash;' }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- FOOTER --}}
<div class="footer" style="margin-top: 20px;">
    <div class="footer-left">
        <span class="footer-brand">MI&middot;ENTRENO</span>
        &nbsp;&mdash;&nbsp;
        Generado el {{ now()->locale('es')->isoFormat('D MMMM YYYY') }} a las {{ now()->format('H:i') }}
    </div>
    <div class="footer-right">
        mientreno.app &nbsp;&mdash;&nbsp; Reporte para uso médico
    </div>
</div>

</body>
</html>
