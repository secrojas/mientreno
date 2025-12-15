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
            font-size: 11pt;
            line-height: 1.5;
            color: #1f2937;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #2DE38E;
        }

        .logo {
            font-size: 22pt;
            font-weight: bold;
            color: #2DE38E;
            margin-bottom: 5px;
        }

        .report-title {
            font-size: 18pt;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .report-period {
            font-size: 11pt;
            color: #6b7280;
        }

        .athlete-info {
            margin-bottom: 20px;
            padding: 10px;
            background: #f9fafb;
            border-left: 3px solid #2DE38E;
        }

        .section {
            margin-bottom: 20px;
        }

        .section-title {
            font-size: 13pt;
            font-weight: bold;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #e5e7eb;
            color: #111827;
        }

        .metrics-grid {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }

        .metric-row {
            display: table-row;
        }

        .metric-cell {
            display: table-cell;
            width: 25%;
            padding: 10px;
            text-align: center;
            border: 1px solid #e5e7eb;
            background: #f9fafb;
        }

        .metric-label {
            font-size: 9pt;
            color: #6b7280;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .metric-value {
            font-size: 16pt;
            font-weight: bold;
            color: #2DE38E;
        }

        .metric-subtitle {
            font-size: 8pt;
            color: #9ca3af;
        }

        .comparison-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .comparison-table th,
        .comparison-table td {
            padding: 8px;
            border: 1px solid #e5e7eb;
            text-align: left;
        }

        .comparison-table th {
            background: #f3f4f6;
            font-weight: bold;
            font-size: 10pt;
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

        .distribution-item {
            margin-bottom: 10px;
            padding: 8px;
            background: #f9fafb;
            border-left: 3px solid #60A5FA;
        }

        .distribution-header {
            font-weight: bold;
            margin-bottom: 3px;
        }

        .distribution-details {
            font-size: 9pt;
            color: #6b7280;
        }

        .workout-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
            margin-bottom: 15px;
        }

        .workout-table th,
        .workout-table td {
            padding: 6px;
            border: 1px solid #e5e7eb;
            text-align: left;
        }

        .workout-table th {
            background: #f3f4f6;
            font-weight: bold;
        }

        .workout-type {
            display: inline-block;
            padding: 2px 6px;
            background: #dbeafe;
            color: #1e40af;
            border-radius: 3px;
            font-size: 8pt;
        }

        .difficulty-dots {
            color: #f59e0b;
        }

        .insights-box {
            background: #f0fdf4;
            border-left: 3px solid #2DE38E;
            padding: 10px;
            margin-bottom: 15px;
        }

        .insight-item {
            margin-bottom: 5px;
            padding-left: 20px;
            position: relative;
        }

        .insight-item::before {
            content: "•";
            position: absolute;
            left: 5px;
            color: #2DE38E;
            font-weight: bold;
        }

        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 9pt;
            color: #6b7280;
        }

        .page-break {
            page-break-after: always;
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
        <div class="logo">MIENTRENO</div>
        <div class="report-title">Reporte Semanal</div>
        <div class="report-period">
            {{ $period['label'] }} <br>
            {{ $period['start_date']->format('d/m/Y') }} - {{ $period['end_date']->format('d/m/Y') }}
        </div>
    </div>

    {{-- Athlete Info --}}
    <div class="athlete-info">
        <strong>Atleta:</strong> {{ auth()->user()->name }}<br>
        <strong>Generado:</strong> {{ now()->format('d/m/Y H:i') }}
    </div>

    {{-- Resumen General --}}
    <div class="section">
        <div class="section-title">Resumen General</div>
        <div class="metrics-grid">
            <div class="metric-row">
                <div class="metric-cell">
                    <div class="metric-label">Kilómetros</div>
                    <div class="metric-value">{{ number_format($summary['total_distance'], 2) }}</div>
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
    </div>

    {{-- Comparativa --}}
    @if($comparison['distance']['previous'] > 0)
        <div class="section">
            <div class="section-title">Comparativa con Semana Anterior</div>
            <table class="comparison-table">
                <thead>
                    <tr>
                        <th>Métrica</th>
                        <th>Esta Semana</th>
                        <th>Semana Anterior</th>
                        <th>Diferencia</th>
                        <th>Tendencia</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Distancia</td>
                        <td>{{ number_format($summary['total_distance'], 2) }} km</td>
                        <td>{{ number_format($comparison['distance']['previous'], 2) }} km</td>
                        <td>{{ $comparison['distance']['diff'] > 0 ? '+' : '' }}{{ number_format($comparison['distance']['diff'], 2) }} km</td>
                        <td class="trend-{{ $comparison['distance']['trend'] }}">
                            {{ $comparison['distance']['percentage'] > 0 ? '+' : '' }}{{ $comparison['distance']['percentage'] }}%
                        </td>
                    </tr>
                    <tr>
                        <td>Sesiones</td>
                        <td>{{ $summary['total_sessions'] }}</td>
                        <td>{{ $comparison['sessions']['previous'] }}</td>
                        <td>{{ $comparison['sessions']['diff'] > 0 ? '+' : '' }}{{ $comparison['sessions']['diff'] }}</td>
                        <td class="trend-{{ $comparison['sessions']['trend'] }}">
                            {{ $comparison['sessions']['percentage'] > 0 ? '+' : '' }}{{ $comparison['sessions']['percentage'] }}%
                        </td>
                    </tr>
                    @if($summary['avg_pace'] && $comparison['pace']['previous'])
                        <tr>
                            <td>Pace Promedio</td>
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
            @foreach($distribution as $type => $data)
                @php
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
                <div class="distribution-item">
                    <div class="distribution-header">{{ $label }} - {{ $data['percentage'] }}%</div>
                    <div class="distribution-details">
                        {{ $data['count'] }} {{ $data['count'] === 1 ? 'sesión' : 'sesiones' }}
                        • {{ number_format($data['distance'], 2) }} km
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Insights --}}
    @if(!empty($insights))
        <div class="section">
            <div class="section-title">Insights de la Semana</div>
            <div class="insights-box">
                @foreach($insights as $insight)
                    <div class="insight-item">{{ $insight['message'] }}</div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Page break before workout table --}}
    <div class="page-break"></div>

    {{-- Detalle de Entrenamientos --}}
    <div class="section">
        <div class="section-title">Detalle de Entrenamientos</div>
        @if($workouts->isEmpty())
            <p style="text-align:center;color:#6b7280;padding:20px;">
                No hay entrenamientos registrados en este período
            </p>
        @else
            <table class="workout-table">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Tipo</th>
                        <th>Distancia</th>
                        <th>Duración</th>
                        <th>Pace</th>
                        <th>Dificultad</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($workouts as $workout)
                        <tr>
                            <td>{{ $workout->date->format('d/m/Y') }}</td>
                            <td><span class="workout-type">{{ $workout->typeLabel }}</span></td>
                            <td>{{ number_format($workout->distance, 2) }} km</td>
                            <td>{{ $workout->formattedDuration }}</td>
                            <td>{{ $workout->formattedPace }}/km</td>
                            <td>
                                @if($workout->difficulty)
                                    <span class="difficulty-dots">
                                        @for($i = 1; $i <= $workout->difficulty; $i++)●@endfor
                                    </span>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        @if($workout->notes)
                            <tr>
                                <td colspan="6" style="font-style:italic;color:#6b7280;font-size:9pt;">
                                    Notas: {{ $workout->notes }}
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>

            <div style="margin-top:10px;font-size:10pt;">
                <strong>Total:</strong> {{ $workouts->count() }} {{ $workouts->count() === 1 ? 'entrenamiento' : 'entrenamientos' }}
                • {{ number_format($workouts->sum('distance'), 2) }} km
                • {{ \Carbon\CarbonInterval::seconds($workouts->sum('duration'))->cascade()->forHumans(['short' => true]) }}
            </div>
        @endif
    </div>

    {{-- Footer --}}
    <div class="footer">
        Reporte generado por MiEntreno<br>
        {{ now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>
