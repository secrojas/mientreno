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
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 9.5pt;
            line-height: 1.3;
            color: #1f2937;
            padding: 15px;
        }

        .header {
            display: table;
            width: 100%;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #FF3B5C;
        }

        .header-left {
            display: table-cell;
            width: 70%;
            vertical-align: middle;
        }

        .header-right {
            display: table-cell;
            width: 30%;
            text-align: right;
            vertical-align: middle;
        }

        .logo-img {
            height: 35px;
            margin-bottom: 5px;
        }

        .report-title {
            font-size: 16pt;
            font-weight: bold;
            color: #FF3B5C;
            margin-bottom: 3px;
        }

        .report-period {
            font-size: 10pt;
            color: #6b7280;
        }

        .athlete-info {
            font-size: 9pt;
            color: #6b7280;
            text-align: right;
        }

        .url {
            font-size: 8pt;
            color: #FF3B5C;
            font-weight: 600;
            margin-top: 2px;
        }

        .section {
            margin-bottom: 12px;
        }

        .section-title {
            font-size: 11pt;
            font-weight: bold;
            margin-bottom: 6px;
            padding-bottom: 3px;
            border-bottom: 1px solid #e5e7eb;
            color: #111827;
        }

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
            width: 50%;
            padding: 8px;
            text-align: center;
            border: 1px solid #e5e7eb;
            background: #f9fafb;
        }

        .metric-label {
            font-size: 8pt;
            color: #6b7280;
            text-transform: uppercase;
            margin-bottom: 3px;
        }

        .metric-value {
            font-size: 14pt;
            font-weight: bold;
            color: #FF3B5C;
        }

        .metric-subtitle {
            font-size: 7pt;
            color: #9ca3af;
        }

        .two-columns {
            display: table;
            width: 100%;
        }

        .column-left {
            display: table-cell;
            width: 58%;
            vertical-align: top;
            padding-right: 8px;
        }

        .column-right {
            display: table-cell;
            width: 42%;
            vertical-align: top;
            padding-left: 8px;
        }

        .comparison-compact {
            background: #f9fafb;
            padding: 8px;
            border-left: 3px solid #2DE38E;
            font-size: 8.5pt;
            margin-bottom: 8px;
        }

        .comparison-compact-title {
            font-weight: bold;
            margin-bottom: 5px;
            color: #111827;
        }

        .comparison-item {
            margin-bottom: 3px;
            display: table;
            width: 100%;
        }

        .comparison-label {
            display: table-cell;
            width: 40%;
            color: #6b7280;
        }

        .comparison-value {
            display: table-cell;
            font-weight: 600;
        }

        .trend-up {
            color: #10b981;
        }

        .trend-down {
            color: #ef4444;
        }

        .insights-compact {
            background: #fff7ed;
            border-left: 3px solid #FF3B5C;
            padding: 8px;
            font-size: 8pt;
        }

        .insights-compact-title {
            font-weight: bold;
            margin-bottom: 5px;
            color: #FF3B5C;
        }

        .insight-item {
            margin-bottom: 3px;
            padding-left: 12px;
            position: relative;
        }

        .insight-item::before {
            content: "▸";
            position: absolute;
            left: 0;
            color: #FF3B5C;
            font-weight: bold;
        }

        .distribution-compact {
            font-size: 8.5pt;
            margin-bottom: 6px;
        }

        .distribution-item-inline {
            display: inline-block;
            margin-right: 12px;
            margin-bottom: 4px;
        }

        .distribution-type {
            font-weight: 600;
            color: #111827;
        }

        .distribution-stats {
            color: #6b7280;
            font-size: 7.5pt;
        }

        .workout-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8pt;
        }

        .workout-table th,
        .workout-table td {
            padding: 4px;
            border: 1px solid #e5e7eb;
            text-align: left;
        }

        .workout-table th {
            background: #f3f4f6;
            font-weight: bold;
            font-size: 8pt;
        }

        .workout-type-badge {
            display: inline-block;
            padding: 1px 5px;
            background: #fce7f3;
            color: #FF3B5C;
            border-radius: 3px;
            font-size: 7pt;
            font-weight: 600;
        }

        .difficulty-dots {
            color: #f59e0b;
            font-size: 10pt;
        }

        .workout-notes {
            font-style: italic;
            color: #6b7280;
            font-size: 7pt;
            padding: 3px 4px;
            background: #f9fafb;
        }

        .footer {
            margin-top: 10px;
            padding-top: 8px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 7pt;
            color: #9ca3af;
        }

        .footer-url {
            color: #FF3B5C;
            font-weight: 600;
            font-size: 8pt;
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
            <img src="{{ public_path('images/logo-horizontal.svg') }}" alt="MiEntreno" class="logo-img">
            <div class="report-title">Reporte Semanal</div>
            <div class="report-period">
                {{ $period['label'] }} • {{ $period['start_date']->format('d/m') }} - {{ $period['end_date']->format('d/m/Y') }}
            </div>
        </div>
        <div class="header-right">
            <div class="athlete-info">
                <strong>{{ auth()->user()->name }}</strong><br>
                {{ now()->format('d/m/Y H:i') }}
            </div>
            <div class="url">mientreno.app</div>
        </div>
    </div>

    {{-- Métricas Principales - Grid 2x2 --}}
    <div class="section">
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
            </div>
            <div class="metric-row">
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
    </div>

    {{-- Dos columnas: Comparativa + Insights --}}
    <div class="two-columns">
        <div class="column-left">
            {{-- Comparativa Compacta --}}
            @if($comparison['distance']['previous'] > 0)
                <div class="comparison-compact">
                    <div class="comparison-compact-title">📊 vs Semana Anterior</div>
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
                                ({{ abs($comparison['pace']['diff']) }} seg)
                            </span>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Distribución por Tipo --}}
            @if(!empty($distribution))
                <div class="section">
                    <div class="section-title">Distribución por Tipo</div>
                    <div class="distribution-compact">
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
                                <span class="distribution-stats">{{ $data['count'] }}x ({{ number_format($data['distance'], 1) }}km)</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <div class="column-right">
            {{-- Insights Compactos --}}
            @if(!empty($insights))
                <div class="insights-compact">
                    <div class="insights-compact-title">✨ Highlights</div>
                    @foreach($insights as $insight)
                        <div class="insight-item">{{ $insight['message'] }}</div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Detalle de Entrenamientos --}}
    <div class="section">
        <div class="section-title">Detalle de Entrenamientos ({{ $workouts->count() }})</div>
        @if($workouts->isEmpty())
            <p style="text-align:center;color:#6b7280;padding:15px;font-size:9pt;">
                No hay entrenamientos registrados en este período
            </p>
        @else
            <table class="workout-table">
                <thead>
                    <tr>
                        <th style="width:15%;">Fecha</th>
                        <th style="width:25%;">Tipo</th>
                        <th style="width:15%;">Distancia</th>
                        <th style="width:15%;">Duración</th>
                        <th style="width:15%;">Pace</th>
                        <th style="width:15%;">Dificultad</th>
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
                                @if($workout->difficulty)
                                    <span class="difficulty-dots">
                                        @for($i = 1; $i <= $workout->difficulty; $i++)●@endfor
                                    </span>
                                @else
                                    <span style="color:#9ca3af;">-</span>
                                @endif
                            </td>
                        </tr>
                        @if($workout->notes)
                            <tr>
                                <td colspan="6" class="workout-notes">
                                    💭 {{ $workout->notes }}
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    {{-- Footer --}}
    <div class="footer">
        Reporte generado el {{ now()->format('d/m/Y') }} a las {{ now()->format('H:i') }} •
        <span class="footer-url">mientreno.app</span>
    </div>
</body>
</html>
