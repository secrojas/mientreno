<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Semanal - {{ $report['period']['label'] }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 8.5pt;
            line-height: 1.25;
            color: #1f2937;
            padding: 10px 12px;
        }

        /* Header */
        .header {
            display: table;
            width: 100%;
            margin-bottom: 10px;
            padding-bottom: 8px;
            border-bottom: 2.5px solid #FF3B5C;
        }

        .header-left {
            display: table-cell;
            width: 65%;
            vertical-align: middle;
        }

        .header-right {
            display: table-cell;
            width: 35%;
            text-align: right;
            vertical-align: middle;
        }

        .logo-text {
            font-family: 'Helvetica', sans-serif;
            font-size: 11pt;
            font-weight: bold;
            letter-spacing: 0.15em;
            color: #FF3B5C;
            text-transform: uppercase;
            margin-bottom: 2px;
        }

        .report-title {
            font-family: 'Helvetica', sans-serif;
            font-size: 15pt;
            font-weight: bold;
            color: #111827;
            margin-bottom: 2px;
        }

        .report-period {
            font-size: 9pt;
            color: #6b7280;
            font-weight: normal;
        }

        .athlete-info {
            font-size: 8.5pt;
            color: #6b7280;
            text-align: right;
            line-height: 1.4;
        }

        .athlete-name {
            font-weight: bold;
            color: #111827;
            font-size: 9pt;
        }

        .url {
            font-size: 8pt;
            color: #2DE38E;
            font-weight: bold;
            margin-top: 2px;
            letter-spacing: 0.03em;
        }

        /* Metrics Grid */
        .metrics-grid {
            display: table;
            width: 100%;
            margin-bottom: 8px;
        }

        .metric-row {
            display: table-row;
        }

        .metric-cell {
            display: table-cell;
            width: 25%;
            padding: 7px;
            text-align: center;
            border: 1px solid #e5e7eb;
            background: linear-gradient(to bottom, #fafafa 0%, #f9fafb 100%);
        }

        .metric-label {
            font-size: 7pt;
            color: #6b7280;
            text-transform: uppercase;
            margin-bottom: 3px;
            font-weight: bold;
            letter-spacing: 0.08em;
        }

        .metric-value {
            font-family: 'Helvetica', sans-serif;
            font-size: 13pt;
            font-weight: bold;
            color: #FF3B5C;
            line-height: 1;
            margin-bottom: 2px;
        }

        .metric-subtitle {
            font-size: 6.5pt;
            color: #9ca3af;
        }

        /* Two Columns Layout */
        .two-columns {
            display: table;
            width: 100%;
            margin-bottom: 8px;
        }

        .column-left {
            display: table-cell;
            width: 55%;
            vertical-align: top;
            padding-right: 6px;
        }

        .column-right {
            display: table-cell;
            width: 45%;
            vertical-align: top;
            padding-left: 6px;
        }

        /* Comparison Box */
        .comparison-box {
            background: #f0fdf4;
            padding: 7px;
            border-left: 3px solid #2DE38E;
            font-size: 8pt;
            margin-bottom: 8px;
        }

        .comparison-title {
            font-family: 'Helvetica', sans-serif;
            font-weight: bold;
            margin-bottom: 5px;
            color: #111827;
            font-size: 9pt;
        }

        .comparison-item {
            margin-bottom: 3px;
            display: table;
            width: 100%;
        }

        .comparison-label {
            display: table-cell;
            width: 38%;
            color: #6b7280;
            font-weight: 500;
        }

        .comparison-value {
            display: table-cell;
            font-weight: bold;
        }

        .trend-up {
            color: #10b981;
        }

        .trend-down {
            color: #ef4444;
        }

        .trend-stable {
            color: #6b7280;
        }

        /* Distribution Box */
        .distribution-box {
            background: #fafafa;
            border-left: 3px solid #60A5FA;
            padding: 7px;
            font-size: 7.5pt;
            margin-bottom: 8px;
        }

        .distribution-title {
            font-family: 'Helvetica', sans-serif;
            font-weight: bold;
            margin-bottom: 5px;
            color: #111827;
            font-size: 9pt;
        }

        .distribution-item-inline {
            display: inline-block;
            margin-right: 10px;
            margin-bottom: 3px;
        }

        .distribution-type {
            font-weight: bold;
            color: #111827;
        }

        .distribution-stats {
            color: #6b7280;
            font-size: 7pt;
        }

        /* Insights Box */
        .insights-box {
            background: #fff7ed;
            border-left: 3px solid #FF3B5C;
            padding: 7px;
            font-size: 7.5pt;
        }

        .insights-title {
            font-family: 'Helvetica', sans-serif;
            font-weight: bold;
            margin-bottom: 5px;
            color: #FF3B5C;
            font-size: 9pt;
        }

        .insight-item {
            margin-bottom: 3px;
            padding-left: 10px;
            position: relative;
            line-height: 1.3;
        }

        .insight-item::before {
            content: "\2022";
            position: absolute;
            left: 0;
            color: #FF3B5C;
            font-weight: bold;
            font-size: 10pt;
        }

        /* Section Title */
        .section-title {
            font-family: 'Helvetica', sans-serif;
            font-size: 10pt;
            font-weight: bold;
            margin-bottom: 6px;
            padding-bottom: 3px;
            border-bottom: 1px solid #e5e7eb;
            color: #111827;
        }

        /* Workout Table */
        .workout-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 7.5pt;
        }

        .workout-table th,
        .workout-table td {
            padding: 3px 4px;
            border: 1px solid #e5e7eb;
            text-align: left;
        }

        .workout-table th {
            background: #f3f4f6;
            font-weight: bold;
            font-size: 7pt;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #374151;
        }

        .workout-type-badge {
            display: inline-block;
            padding: 1px 4px;
            background: #fce7f3;
            color: #FF3B5C;
            border-radius: 3px;
            font-size: 6.5pt;
            font-weight: bold;
        }

        .difficulty-dots {
            color: #f59e0b;
            font-size: 9pt;
            letter-spacing: 1px;
        }

        .workout-notes {
            font-style: italic;
            color: #6b7280;
            font-size: 6.5pt;
            padding: 3px 4px;
            background: #fafafa;
        }

        /* Footer */
        .footer {
            margin-top: 8px;
            padding-top: 6px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 6.5pt;
            color: #9ca3af;
        }

        .footer-url {
            color: #FF3B5C;
            font-weight: bold;
            font-size: 7pt;
        }
    </style>
</head>
<body>
    @php
        $period = $report['period'];
        $summary = $report['summary'];
        $distribution = $report['distribution'];
        $comparison = $report['comparison'];
        $workouts = $report['workouts'];
        $insights = $report['insights'];
    @endphp

    {{-- Header --}}
    <div class="header">
        <div class="header-left">
            <div class="logo-text">MIENTRENO</div>
            <div class="report-title">Reporte Semanal</div>
            <div class="report-period">
                {{ $period['label'] }} • {{ $period['start_date']->format('d/m') }} - {{ $period['end_date']->format('d/m/Y') }}
            </div>
        </div>
        <div class="header-right">
            <div class="athlete-info">
                <div class="athlete-name">{{ auth()->user()->name }}</div>
                {{ now()->format('d/m/Y H:i') }}
            </div>
            <div class="url">mientreno.app</div>
        </div>
    </div>

    {{-- Métricas Principales - Grid 2x2 --}}
    <div class="metrics-grid">
        <div class="metric-row">
            <div class="metric-cell">
                <div class="metric-label">Kilómetros</div>
                <div class="metric-value">{{ number_format($summary['total_distance'], 1) }}</div>
                <div class="metric-subtitle">km totales</div>
            </div>
            <div class="metric-cell">
                <div class="metric-label">Tiempo</div>
                <div class="metric-value">{{ $summary['formatted_duration'] }}</div>
                <div class="metric-subtitle">en movimiento</div>
            </div>
            <div class="metric-cell">
                <div class="metric-label">Pace Promedio</div>
                <div class="metric-value">{{ $summary['formatted_pace'] }}</div>
                <div class="metric-subtitle">min/km</div>
            </div>
            <div class="metric-cell">
                <div class="metric-label">Sesiones</div>
                <div class="metric-value">{{ $summary['total_sessions'] }}</div>
                <div class="metric-subtitle">entrenamientos</div>
            </div>
        </div>
    </div>

    {{-- Dos columnas: Comparativa/Distribución + Insights --}}
    <div class="two-columns">
        <div class="column-left">
            {{-- Comparativa --}}
            @if($comparison['distance']['previous'] > 0)
                <div class="comparison-box">
                    <div class="comparison-title">vs Semana Anterior</div>
                    <div class="comparison-item">
                        <span class="comparison-label">Distancia:</span>
                        <span class="comparison-value trend-{{ $comparison['distance']['trend'] }}">
                            {{ $comparison['distance']['diff'] > 0 ? '+' : '' }}{{ number_format($comparison['distance']['diff'], 1) }} km
                            ({{ $comparison['distance']['percentage'] > 0 ? '+' : '' }}{{ $comparison['distance']['percentage'] }}%)
                        </span>
                    </div>
                    <div class="comparison-item">
                        <span class="comparison-label">Sesiones:</span>
                        <span class="comparison-value trend-{{ $comparison['sessions']['trend'] }}">
                            {{ $comparison['sessions']['diff'] > 0 ? '+' : '' }}{{ $comparison['sessions']['diff'] }}
                            ({{ $comparison['sessions']['percentage'] > 0 ? '+' : '' }}{{ $comparison['sessions']['percentage'] }}%)
                        </span>
                    </div>
                    @if($summary['avg_pace'] && $comparison['pace']['previous'])
                        <div class="comparison-item">
                            <span class="comparison-label">Pace:</span>
                            <span class="comparison-value trend-{{ $comparison['pace']['trend'] }}">
                                {{ $comparison['pace']['improved'] ? 'Mejora' : 'Declive' }}
                                ({{ abs($comparison['pace']['diff']) }}s)
                            </span>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Distribución --}}
            @if(!empty($distribution))
                <div class="distribution-box">
                    <div class="distribution-title">Distribución por Tipo</div>
                    @foreach($distribution as $type => $data)
                        @php
                            $typeLabels = [
                                'easy_run' => 'Fondo Suave',
                                'intervals' => 'Intervalos',
                                'tempo' => 'Tempo',
                                'long_run' => 'Tirada Larga',
                                'recovery' => 'Recuperación',
                                'race' => 'Carrera',
                                'training_run' => 'Entrenamiento',
                            ];
                            $label = $typeLabels[$type] ?? $type;
                        @endphp
                        <div class="distribution-item-inline">
                            <span class="distribution-type">{{ $label }}</span>:
                            <span class="distribution-stats">{{ $data['count'] }}x • {{ number_format($data['distance'], 1) }}km</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="column-right">
            {{-- Insights --}}
            @if(!empty($insights))
                <div class="insights-box">
                    <div class="insights-title">Highlights</div>
                    @foreach($insights as $insight)
                        <div class="insight-item">{{ $insight['message'] }}</div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Detalle de Entrenamientos --}}
    <div class="section-title">Detalle de Entrenamientos ({{ $workouts->count() }})</div>
    @if($workouts->isEmpty())
        <p style="text-align:center;color:#6b7280;padding:12px;font-size:8pt;">
            No hay entrenamientos registrados en este período
        </p>
    @else
        <table class="workout-table">
            <thead>
                <tr>
                    <th style="width:12%;">Fecha</th>
                    <th style="width:23%;">Tipo</th>
                    <th style="width:13%;">Distancia</th>
                    <th style="width:13%;">Duración</th>
                    <th style="width:13%;">Pace</th>
                    <th style="width:13%;">FC</th>
                    <th style="width:13%;">Dificultad</th>
                </tr>
            </thead>
            <tbody>
                @foreach($workouts as $workout)
                    <tr>
                        <td>{{ $workout->date->format('d/m') }}</td>
                        <td><span class="workout-type-badge">{{ $workout->typeLabel }}</span></td>
                        <td><strong>{{ number_format($workout->distance, 1) }} km</strong></td>
                        <td>{{ $workout->formattedDuration }}</td>
                        <td>{{ $workout->formattedPace }}</td>
                        <td>
                            @if($workout->avg_heart_rate)
                                {{ $workout->avg_heart_rate }} bpm
                            @else
                                <span style="color:#9ca3af;">-</span>
                            @endif
                        </td>
                        <td>
                            @if($workout->difficulty)
                                <span class="difficulty-dots">
                                    @for($i = 1; $i <= $workout->difficulty; $i++)&#9679;@endfor
                                </span>
                            @else
                                <span style="color:#9ca3af;">-</span>
                            @endif
                        </td>
                    </tr>
                    @if($workout->notes)
                        <tr>
                            <td colspan="7" class="workout-notes">
                                Notas: {{ $workout->notes }}
                            </td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    @endif

    {{-- Footer --}}
    <div class="footer">
        Reporte generado el {{ now()->format('d/m/Y') }} a las {{ now()->format('H:i') }} •
        <span class="footer-url">mientreno.app</span>
    </div>
</body>
</html>
