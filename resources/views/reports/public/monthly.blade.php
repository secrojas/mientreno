@php
    $period = $report['period'];
    $summary = $report['summary'];
    $distribution = $report['distribution'];
    $comparison = $report['comparison'];
    $workouts = $report['workouts'];
    $insights = $report['insights'];
@endphp

<x-public-layout
    :title="'Reporte Mensual - ' . $period['label']"
    :subtitle="$period['start_date']->format('d/m') . ' - ' . $period['end_date']->format('d/m/Y')"
>
    {{-- Aviso de enlace compartido --}}
    <div class="public-notice">
        <strong>📤 Reporte compartido</strong> por {{ $share->user->name }}<br>
        <span style="font-size:0.85rem;">
            Compartido el {{ $share->created_at->format('d/m/Y') }} •
            Expira: {{ $share->expires_at->format('d/m/Y H:i') }} •
            Vistas: {{ $share->view_count }}
        </span>
    </div>

    {{-- Resumen General - Métricas principales --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:1rem;margin-bottom:2rem;">
        <x-metric-card
            label="Kilómetros"
            :value="number_format($summary['total_distance'], 2)"
            subtitle="km totales"
            accent="#2DE38E"
        />
        <x-metric-card
            label="Tiempo"
            :value="$summary['formatted_duration']"
            subtitle="en movimiento"
            accent="#60A5FA"
        />
        <x-metric-card
            label="Sesiones"
            :value="$summary['total_sessions']"
            subtitle="entrenamientos"
            accent="#F59E0B"
        />
        <x-metric-card
            label="Pace Promedio"
            :value="$summary['formatted_pace']"
            subtitle="min/km"
            accent="#FF3B5C"
        />
    </div>

    {{-- Métricas adicionales (solo en vista mensual) --}}
    @if($summary['avg_heart_rate'] || $summary['elevation_gain'])
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:1rem;margin-bottom:2rem;">
            @if($summary['avg_heart_rate'])
                <x-metric-card
                    label="FC Promedio"
                    :value="round($summary['avg_heart_rate'])"
                    subtitle="bpm"
                    accent="#EF4444"
                />
            @endif
            @if($summary['elevation_gain'])
                <x-metric-card
                    label="Desnivel"
                    :value="number_format($summary['elevation_gain'])"
                    subtitle="metros D+"
                    accent="#8B5CF6"
                />
            @endif
        </div>
    @endif

    {{-- Comparativa con mes anterior --}}
    @if($comparison['distance']['previous'] > 0)
        <x-report-card title="Comparativa con Mes Anterior">
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:1rem;">
                <x-metric-comparison
                    label="Distancia"
                    :current="$summary['total_distance']"
                    :previous="$comparison['distance']['previous']"
                    :diff="$comparison['distance']"
                    unit=" km"
                />
                <x-metric-comparison
                    label="Sesiones"
                    :current="$summary['total_sessions']"
                    :previous="$comparison['sessions']['previous']"
                    :diff="$comparison['sessions']"
                />
                <x-metric-comparison
                    label="Tiempo"
                    :current="$summary['formatted_duration']"
                    :previous="$comparison['duration']['formatted_diff']"
                    :diff="$comparison['duration']"
                />
                <x-metric-comparison
                    label="Pace Promedio"
                    :current="$summary['formatted_pace']"
                    :previous="$comparison['pace']['formatted_previous'] ?? '–'"
                    :diff="$comparison['pace']"
                    unit="/km"
                    :invertTrend="true"
                />
            </div>
        </x-report-card>
    @endif

    {{-- Distribución por Tipo --}}
    @if(!empty($distribution))
        <x-report-card title="Distribución por Tipo de Entrenamiento">
            <div style="display:grid;gap:1rem;">
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
                    <div style="display:flex;align-items:center;gap:1rem;">
                        <div style="flex:1;min-width:120px;">
                            <div style="font-size:.9rem;font-weight:500;margin-bottom:.25rem;">{{ $label }}</div>
                            <div style="font-size:.8rem;color:var(--text-muted);">
                                {{ $data['count'] }} {{ $data['count'] === 1 ? 'sesión' : 'sesiones' }}
                                • {{ number_format($data['distance'], 2) }} km
                            </div>
                        </div>
                        <div style="flex:2;display:flex;align-items:center;gap:.5rem;">
                            <div style="flex:1;height:8px;background:rgba(30,41,59,.5);border-radius:4px;overflow:hidden;">
                                <div style="height:100%;background:linear-gradient(90deg,#2DE38E,#60A5FA);width:{{ $data['percentage'] }}%;transition:width .3s;"></div>
                            </div>
                            <div style="min-width:50px;text-align:right;font-size:.85rem;font-weight:600;color:var(--accent-secondary);">
                                {{ $data['percentage'] }}%
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-report-card>
    @endif

    {{-- Insights --}}
    @if(!empty($insights))
        <x-report-card title="Insights del Mes">
            <div style="display:grid;gap:.75rem;">
                @foreach($insights as $insight)
                    <div style="display:flex;align-items:center;gap:.75rem;padding:.75rem;border-radius:.5rem;background:rgba(30,41,59,.3);">
                        <span style="font-size:1.5rem;">{{ $insight['icon'] }}</span>
                        <span style="font-size:.95rem;">{{ $insight['message'] }}</span>
                    </div>
                @endforeach
            </div>
        </x-report-card>
    @endif

    {{-- Detalle de Entrenamientos --}}
    <x-report-card title="Detalle de Entrenamientos" subtitle="{{ $workouts->count() }} {{ $workouts->count() === 1 ? 'sesión registrada' : 'sesiones registradas' }}">
        <x-workout-table :workouts="$workouts" :showActions="false" />
    </x-report-card>

</x-public-layout>
