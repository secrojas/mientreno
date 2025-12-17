<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Mensual - {{ $report['period']['label'] }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 9pt;
            line-height: 1.3;
            color: #1f2937;
            padding: 12px 15px;
        }

        /* Header */
        .header {
            text-align: center;
            margin-bottom: 15px;
            padding-bottom: 12px;
            border-bottom: 3px solid #2DE38E;
        }

        .logo-text {
            font-family: 'Helvetica', sans-serif;
            font-size: 13pt;
            font-weight: bold;
            letter-spacing: 0.15em;
            color: #2DE38E;
            text-transform: uppercase;
            margin-bottom: 3px;
        }

        .report-title {
            font-family: 'Helvetica', sans-serif;
            font-size: 17pt;
            font-weight: bold;
            margin-bottom: 4px;
            color: #111827;
        }

        .report-period {
            font-size: 10pt;
            color: #6b7280;
            font-weight: normal;
        }

        /* Athlete Info Box */
        .athlete-box {
            display: table;
            width: 100%;
            margin-bottom: 12px;
            padding: 8px 10px;
            background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%);
            border-left: 4px solid #2DE38E;
            font-size: 8.5pt;
        }

        .athlete-row {
            display: table-row;
        }

        .athlete-cell {
            display: table-cell;
            padding: 2px 0;
        }

        .athlete-label {
            font-weight: bold;
            color: #111827;
            width: 20%;
        }

        .athlete-value {
            color: #374151;
        }

        /* Section Container */
        .section {
            margin-bottom: 12px;
        }

        .section-title {
            font-family: 'Helvetica', sans-serif;
            font-size: 11pt;
            font-weight: bold;
            margin-bottom: 8px;
            padding: 6px 0 6px 10px;
            border-left: 4px solid #FF3B5C;
            background: linear-gradient(to right, #fff7ed 0%, transparent 100%);
            color: #111827;
        }

        /* Metrics Grid */
        .metrics-grid {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }

        .metric-row {
            display: table-row;
        }

        .metric-cell {
            display: table-cell;
            width: 25%;
            padding: 9px;
            text-align: center;
            border: 1px solid #e5e7eb;
            background: linear-gradient(to bottom, #fafafa 0%, #f9fafb 100%);
        }

        .metric-label {
            font-size: 7.5pt;
            color: #6b7280;
            text-transform: uppercase;
            margin-bottom: 4px;
            font-weight: bold;
            letter-spacing: 0.08em;
        }

        .metric-value {
            font-family: 'Helvetica', sans-serif;
            font-size: 15pt;
            font-weight: bold;
            color: #2DE38E;
            line-height: 1;
            margin-bottom: 3px;
        }

        .metric-subtitle {
            font-size: 7pt;
            color: #9ca3af;
        }

        /* Additional Metrics Box */
        .additional-metrics {
            background: linear-gradient(135deg, #fef3c7 0%, #fef9c3 100%);
            border-left: 4px solid #f59e0b;
            padding: 8px 10px;
            margin-bottom: 12px;
            font-size: 8.5pt;
        }

        .additional-metrics strong {
            font-weight: bold;
            color: #111827;
        }

        /* Comparison Table */
        .comparison-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            font-size: 8.5pt;
        }

        .comparison-table th,
        .comparison-table td {
            padding: 6px 8px;
            border: 1px solid #e5e7eb;
            text-align: left;
        }

        .comparison-table th {
            background: linear-gradient(to bottom, #f3f4f6 0%, #f9fafb 100%);
            font-weight: bold;
            font-size: 8pt;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #374151;
        }

        .comparison-table td {
            background: #ffffff;
        }

        .trend-up {
            color: #10b981;
            font-weight: bold;
        }

        .trend-down {
            color: #ef4444;
            font-weight: bold;
        }

        .trend-stable {
            color: #6b7280;
            font-weight: bold;
        }

        /* Distribution Grid */
        .distribution-grid {
            display: table;
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }

        .distribution-row {
            display: table-row;
        }

        .distribution-cell {
            display: table-cell;
            width: 50%;
            padding: 7px;
            border: 1px solid #e5e7eb;
            background: #fafafa;
            font-size: 8pt;
        }

        .distribution-header {
            font-weight: bold;
            color: #111827;
            margin-bottom: 2px;
        }

        .distribution-percentage {
            font-family: 'Helvetica', sans-serif;
            font-size: 12pt;
            font-weight: bold;
            color: #60A5FA;
        }

        .distribution-details {
            font-size: 7.5pt;
            color: #6b7280;
            margin-top: 2px;
        }

        /* Insights Box */
        .insights-box {
            background: linear-gradient(135deg, #fef2f2 0%, #fff1f2 100%);
            border-left: 4px solid #FF3B5C;
            padding: 8px 10px;
            margin-bottom: 12px;
        }

        .insights-title {
            font-family: 'Helvetica', sans-serif;
            font-weight: bold;
            margin-bottom: 6px;
            color: #FF3B5C;
            font-size: 10pt;
        }

        .insight-item {
            margin-bottom: 4px;
            padding-left: 12px;
            position: relative;
            line-height: 1.4;
            font-size: 8pt;
        }

        .insight-item::before {
            content: "\2022";
            position: absolute;
            left: 0;
            color: #FF3B5C;
            font-weight: bold;
            font-size: 10pt;
        }

        /* Page Break */
        .page-break {
            page-break-before: always;
        }

        .page-break-section {
            margin-top: 20px;
            padding-top: 15px;
            border-top: 3px solid #FF3B5C;
        }

        /* Workout Table */
        .workout-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8pt;
            margin-bottom: 12px;
        }

        .workout-table th,
        .workout-table td {
            padding: 5px 6px;
            border: 1px solid #e5e7eb;
            text-align: left;
        }

        .workout-table th {
            background: linear-gradient(to bottom, #f3f4f6 0%, #f9fafb 100%);
            font-weight: bold;
            font-size: 7.5pt;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #374151;
        }

        .workout-type-badge {
            display: inline-block;
            padding: 2px 5px;
            background: #dbeafe;
            color: #1e40af;
            border-radius: 3px;
            font-size: 7pt;
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
            font-size: 7.5pt;
            padding: 4px 6px;
            background: #fafafa;
        }

        /* Table Summary */
        .table-summary {
            margin-top: 8px;
            padding: 8px 10px;
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border-left: 4px solid #60A5FA;
            font-size: 8.5pt;
        }

        .table-summary strong {
            font-weight: bold;
            color: #111827;
        }

        /* Footer */
        .footer {
            margin-top: 12px;
            padding-top: 8px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 7pt;
            color: #9ca3af;
        }

        .footer-url {
            color: #2DE38E;
            font-weight: bold;
            font-size: 7.5pt;
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
        <div class="logo-text">MIENTRENO</div>
        <div class="report-title">Reporte Mensual</div>
        <div class="report-period">
            {{ $period['label'] }}<br>
            {{ $period['start_date']->format('d/m/Y') }} - {{ $period['end_date']->format('d/m/Y') }}
        </div>
    </div>

    {{-- Athlete Info --}}
    <div class="athlete-box">
        <div class="athlete-row">
            <div class="athlete-cell athlete-label">Atleta:</div>
            <div class="athlete-cell athlete-value">{{ auth()->user()->name }}</div>
        </div>
        <div class="athlete-row">
            <div class="athlete-cell athlete-label">Generado:</div>
            <div class="athlete-cell athlete-value">{{ now()->format('d/m/Y H:i') }}</div>
        </div>
    </div>

    {{-- Resumen General --}}
    <div class="section">
        <div class="section-title">Resumen General</div>
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
                    <div class="metric-label">Sesiones</div>
                    <div class="metric-value">{{ $summary['total_sessions'] }}</div>
                    <div class="metric-subtitle">entrenamientos</div>
                </div>
                <div class="metric-cell">
                    <div class="metric-label">Pace Promedio</div>
                    <div class="metric-value">{{ $summary['formatted_pace'] }}</div>
                    <div class="metric-subtitle">min/km</div>
                </div>
            </div>
        </div>

        {{-- Métricas adicionales --}}
        @if($summary['avg_heart_rate'] || $summary['elevation_gain'])
            <div class="additional-metrics">
                <strong>Métricas Adicionales:</strong>
                @if($summary['avg_heart_rate'])
                    FC Promedio: {{ round($summary['avg_heart_rate']) }} bpm
                    @if($summary['elevation_gain']) • @endif
                @endif
                @if($summary['elevation_gain'])
                    Desnivel Acumulado: {{ number_format($summary['elevation_gain']) }} m D+
                @endif
            </div>
        @endif
    </div>

    {{-- Comparativa --}}
    @if($comparison['distance']['previous'] > 0)
        <div class="section">
            <div class="section-title">Comparativa con Mes Anterior</div>
            <table class="comparison-table">
                <thead>
                    <tr>
                        <th style="width:22%;">Métrica</th>
                        <th style="width:22%;">Este Mes</th>
                        <th style="width:22%;">Mes Anterior</th>
                        <th style="width:17%;">Diferencia</th>
                        <th style="width:17%;">Tendencia</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Distancia</strong></td>
                        <td>{{ number_format($summary['total_distance'], 1) }} km</td>
                        <td>{{ number_format($comparison['distance']['previous'], 1) }} km</td>
                        <td>{{ $comparison['distance']['diff'] > 0 ? '+' : '' }}{{ number_format($comparison['distance']['diff'], 1) }} km</td>
                        <td class="trend-{{ $comparison['distance']['trend'] }}">
                            {{ $comparison['distance']['percentage'] > 0 ? '+' : '' }}{{ $comparison['distance']['percentage'] }}%
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Sesiones</strong></td>
                        <td>{{ $summary['total_sessions'] }}</td>
                        <td>{{ $comparison['sessions']['previous'] }}</td>
                        <td>{{ $comparison['sessions']['diff'] > 0 ? '+' : '' }}{{ $comparison['sessions']['diff'] }}</td>
                        <td class="trend-{{ $comparison['sessions']['trend'] }}">
                            {{ $comparison['sessions']['percentage'] > 0 ? '+' : '' }}{{ $comparison['sessions']['percentage'] }}%
                        </td>
                    </tr>
                    @if($summary['avg_pace'] && $comparison['pace']['previous'])
                        <tr>
                            <td><strong>Pace Promedio</strong></td>
                            <td>{{ $summary['formatted_pace'] }}/km</td>
                            <td>{{ $comparison['pace']['formatted_previous'] }}/km</td>
                            <td>{{ $comparison['pace']['diff'] > 0 ? '+' : '' }}{{ $comparison['pace']['diff'] }} seg</td>
                            <td class="trend-{{ $comparison['pace']['trend'] }}">
                                {{ $comparison['pace']['improved'] ? 'Mejora' : 'Declive' }}
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    @endif

    {{-- Distribución por Tipo --}}
    @if(!empty($distribution))
        <div class="section">
            <div class="section-title">Distribución por Tipo de Entrenamiento</div>
            <div class="distribution-grid">
                @php
                    $distributionArray = array_values($distribution);
                    $totalTypes = count($distributionArray);
                @endphp
                @for($i = 0; $i < $totalTypes; $i += 2)
                    <div class="distribution-row">
                        @for($j = $i; $j < min($i + 2, $totalTypes); $j++)
                            @php
                                $type = array_keys($distribution)[$j];
                                $data = $distributionArray[$j];
                                $typeLabels = [
                                    'easy_run' => 'Fondo Suave',
                                    'intervals' => 'Intervalos',
                                    'tempo' => 'Tempo',
                                    'long_run' => 'Tirada Larga',
                                    'recovery' => 'Recuperación',
                                    'race' => 'Carrera',
                                    'training_run' => 'Entrenamiento General',
                                ];
                                $label = $typeLabels[$type] ?? $type;
                            @endphp
                            <div class="distribution-cell">
                                <div class="distribution-header">
                                    {{ $label }} <span class="distribution-percentage">{{ $data['percentage'] }}%</span>
                                </div>
                                <div class="distribution-details">
                                    {{ $data['count'] }} {{ $data['count'] === 1 ? 'sesión' : 'sesiones' }}
                                    • {{ number_format($data['distance'], 1) }} km
                                </div>
                            </div>
                        @endfor
                        @if($j < $totalTypes && ($totalTypes - $j) == 1)
                            <div class="distribution-cell" style="background:transparent;border:none;"></div>
                        @endif
                    </div>
                @endfor
            </div>
        </div>
    @endif

    {{-- Insights --}}
    @if(!empty($insights))
        <div class="insights-box">
            <div class="insights-title">Insights del Mes</div>
            @foreach($insights as $insight)
                <div class="insight-item">{{ $insight['message'] }}</div>
            @endforeach
        </div>
    @endif

    {{-- Page break before workout details --}}
    <div class="page-break"></div>

    {{-- Detalle de Entrenamientos (Nueva Página) --}}
    <div class="page-break-section">
        <div class="section-title">Detalle de Entrenamientos</div>
        @if($workouts->isEmpty())
            <p style="text-align:center;color:#6b7280;padding:15px;font-size:9pt;">
                No hay entrenamientos registrados en este período
            </p>
        @else
            <table class="workout-table">
                <thead>
                    <tr>
                        <th style="width:11%;">Fecha</th>
                        <th style="width:20%;">Tipo</th>
                        <th style="width:13%;">Distancia</th>
                        <th style="width:13%;">Duración</th>
                        <th style="width:13%;">Pace</th>
                        <th style="width:11%;">FC</th>
                        <th style="width:11%;">Desnivel</th>
                        <th style="width:8%;">Dif.</th>
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
                                    {{ $workout->avg_heart_rate }}
                                @else
                                    <span style="color:#9ca3af;">-</span>
                                @endif
                            </td>
                            <td>
                                @if($workout->elevation_gain)
                                    {{ $workout->elevation_gain }}m
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
                                <td colspan="8" class="workout-notes">
                                    Notas: {{ $workout->notes }}
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>

            <div class="table-summary">
                <strong>Total del Mes:</strong>
                {{ $workouts->count() }} {{ $workouts->count() === 1 ? 'entrenamiento' : 'entrenamientos' }}
                • {{ number_format($workouts->sum('distance'), 1) }} km
                • {{ \Carbon\CarbonInterval::seconds($workouts->sum('duration'))->cascade()->forHumans(['short' => true]) }}
            </div>
        @endif
    </div>

    {{-- Footer --}}
    <div class="footer">
        Reporte generado por <span class="footer-url">MiEntreno</span> •
        {{ now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>
