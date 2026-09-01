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
        'easy_run'     => 'Fondo Suave',
        'intervals'    => 'Series/Intervalos',
        'tempo'        => 'Ritmo Sostenido',
        'long_run'     => 'Fondo Largo',
        'recovery'     => 'Recuperación',
        'race'         => 'Carrera',
        'training_run' => 'Entrenamiento',
    ];
@endphp

@php
    $reportTitle = 'Reporte Médico — ' . $share->user->name;
    $reportSubtitle = $firstWorkout
        ? 'Desde ' . $firstWorkout->date->locale('es')->isoFormat('MMMM YYYY')
        : 'Historial completo';
@endphp

<x-public-layout
    :title="$reportTitle"
    :subtitle="$reportSubtitle"
>
    {{-- Aviso compartido --}}
    <div class="public-notice">
        <strong>Reporte Médico</strong> de {{ $share->user->name }}<br>
        <span style="font-size:0.85rem;">
            Generado el {{ $report['generated_at']->format('d/m/Y H:i') }} &bull;
            Expira: {{ $share->expires_at->format('d/m/Y H:i') }} &bull;
            Vistas: {{ $share->view_count }}
        </span>
    </div>

    @if($share->user->health_insurance_provider || $share->user->health_insurance_plan || $share->user->health_insurance_member_number)
        <div style="border:1px solid var(--border-subtle);border-radius:0.5rem;padding:0.85rem 1.25rem;margin-bottom:1.5rem;font-size:0.85rem;color:#94A3B8;">
            <strong style="color:var(--text-main);">Obra Social:</strong>
            {{ $share->user->health_insurance_provider ?: '—' }}
            @if($share->user->health_insurance_plan) &bull; Plan {{ $share->user->health_insurance_plan }} @endif
            @if($share->user->health_insurance_member_number) &bull; N° Credencial: {{ $share->user->health_insurance_member_number }} @endif
        </div>
    @endif

    {{-- TOTALES --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:1rem;margin-bottom:1.5rem;">
        <x-metric-card
            label="Kilómetros totales"
            :value="number_format($global['total_distance'], 1) . ' km'"
            accent="#2DE38E"
        />
        <x-metric-card
            label="Tiempo total"
            :value="($totalHours > 0 ? $totalHours . 'h ' : '') . $totalMins . 'm'"
        />
        <x-metric-card
            label="Sesiones"
            :value="$global['total_sessions']"
            :subtitle="'Pace: ' . $global['formatted_pace'] . '/km'"
        />
        <x-metric-card
            label="Entrenando desde"
            :value="$firstWorkout ? $firstWorkout->date->locale('es')->isoFormat('MMM YYYY') : '—'"
            :subtitle="$firstWorkout ? $firstWorkout->date->diffForHumans(now(), true) : ''"
        />
    </div>

    {{-- FC --}}
    @if(!empty($hrStats))
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:0.75rem;margin-bottom:1.5rem;">
            <x-metric-card label="FC Promedio" :value="$hrStats['overall_avg'] . ' bpm'" accent="#EF4444" />
            <x-metric-card label="FC Máxima"   :value="$hrStats['max']         . ' bpm'" accent="#F97316" />
            <x-metric-card label="FC Mínima"   :value="$hrStats['min']         . ' bpm'" accent="#10B981" />
        </div>
    @endif

    {{-- APTO --}}
    @if($fitnessCertificate)
        <div style="margin-bottom:1.5rem;">
            <h3 style="font-size:0.75rem;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:#94A3B8;margin-bottom:0.5rem;">
                Apto Médico
            </h3>
            @php
                $certExpired = $fitnessCertificate->isExpired();
                $certSoon    = $fitnessCertificate->isExpiringSoon();
                $certBorder  = $certExpired ? '#EF4444' : ($certSoon ? '#F59E0B' : '#10B981');
                $certBadge   = $certExpired ? 'background:#FEF2F2;color:#DC2626;border:1px solid #FECACA;' : ($certSoon ? 'background:#FFFBEB;color:#D97706;border:1px solid #FDE68A;' : 'background:#F0FDF4;color:#16A34A;border:1px solid #BBF7D0;');
                $certLabel   = $certExpired ? 'VENCIDO' : ($certSoon ? 'POR VENCER' : 'VIGENTE');
            @endphp
            <div style="border:1px solid {{ $certBorder }}33;border-left:4px solid {{ $certBorder }};padding:0.75rem 1rem;display:flex;align-items:center;justify-content:space-between;gap:1rem;">
                <div>
                    <strong>{{ $fitnessCertificate->title }}</strong>
                    <span style="{{ $certBadge }} display:inline-block;padding:1px 8px;border-radius:20px;font-size:0.7rem;font-weight:700;margin-left:8px;">{{ $certLabel }}</span>
                </div>
                <div style="font-size:0.8rem;color:#64748B;text-align:right;">
                    @if($fitnessCertificate->issued_at) Emitido: {{ $fitnessCertificate->issued_at->format('d/m/Y') }}<br> @endif
                    @if($fitnessCertificate->expires_at) Vence: {{ $fitnessCertificate->expires_at->format('d/m/Y') }} @endif
                </div>
            </div>
        </div>
    @endif

    {{-- PROGRESIÓN MES A MES --}}
    <div style="margin-bottom:1.5rem;">
        <h3 style="font-size:0.75rem;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:#94A3B8;margin-bottom:0.75rem;">
            Progresión Mes a Mes
        </h3>

        @foreach($monthly as $year => $months)
            <div style="margin-bottom:1rem;">
                <div style="font-size:0.8rem;font-weight:700;color:#0F172A;margin-bottom:0.25rem;">{{ $year }}</div>
                <div style="overflow-x:auto;">
                    <table style="width:100%;border-collapse:collapse;font-size:0.8rem;">
                        <thead>
                            <tr style="background:#F8FAFC;border-bottom:2px solid #E2E8F0;">
                                <th style="padding:6px 10px;text-align:left;font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:#94A3B8;">Mes</th>
                                <th style="padding:6px 10px;text-align:right;font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:#94A3B8;">Sesiones</th>
                                <th style="padding:6px 10px;text-align:right;font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:#2DE38E;">Km</th>
                                <th style="padding:6px 10px;text-align:right;font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:#94A3B8;">Tiempo</th>
                                <th style="padding:6px 10px;text-align:right;font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:#94A3B8;">Pace</th>
                                <th style="padding:6px 10px;text-align:right;font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:#EF4444;">FC</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $yDist = 0; $ySess = 0; $yDur = 0; @endphp
                            @foreach($months as $m)
                                @php $yDist += $m['total_distance']; $ySess += $m['total_sessions']; $yDur += $m['total_duration']; @endphp
                                <tr style="border-bottom:1px solid #F1F5F9;">
                                    <td style="padding:6px 10px;">{{ $m['month_name'] }}</td>
                                    <td style="padding:6px 10px;text-align:right;color:#64748B;">{{ $m['total_sessions'] }}</td>
                                    <td style="padding:6px 10px;text-align:right;font-weight:700;color:#2DE38E;">{{ number_format($m['total_distance'], 1) }}</td>
                                    <td style="padding:6px 10px;text-align:right;color:#64748B;">{{ $m['formatted_duration'] }}</td>
                                    <td style="padding:6px 10px;text-align:right;color:#64748B;">{{ $m['formatted_pace'] }}/km</td>
                                    <td style="padding:6px 10px;text-align:right;{{ $m['avg_heart_rate_month'] ? 'color:#EF4444;font-weight:600;' : 'color:#CBD5E1;' }}">
                                        {{ $m['avg_heart_rate_month'] ? $m['avg_heart_rate_month'] . ' bpm' : '—' }}
                                    </td>
                                </tr>
                            @endforeach
                            <tr style="background:#F0FDF4;border-top:2px solid #2DE38E;">
                                <td style="padding:6px 10px;font-weight:700;font-size:0.75rem;">Total {{ $year }}</td>
                                <td style="padding:6px 10px;text-align:right;font-weight:700;">{{ $ySess }}</td>
                                <td style="padding:6px 10px;text-align:right;font-weight:700;color:#2DE38E;">{{ number_format($yDist, 1) }} km</td>
                                <td style="padding:6px 10px;text-align:right;font-weight:700;">
                                    @php $yh=floor($yDur/3600);$ym=floor(($yDur%3600)/60); @endphp
                                    {{ $yh > 0 ? $yh.'h ' : '' }}{{ $ym }}m
                                </td>
                                <td colspan="2"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
    </div>

    {{-- TOP 10 --}}
    <div style="margin-bottom:1.5rem;">
        <h3 style="font-size:0.75rem;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:#94A3B8;margin-bottom:0.75rem;">
            Top 10 Recorridos por Distancia
        </h3>
        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;font-size:0.8rem;">
                <thead>
                    <tr style="background:#F8FAFC;border-bottom:2px solid #E2E8F0;">
                        <th style="padding:6px 10px;text-align:center;width:40px;font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:#94A3B8;">#</th>
                        <th style="padding:6px 10px;text-align:left;font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:#94A3B8;">Fecha</th>
                        <th style="padding:6px 10px;text-align:left;font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:#94A3B8;">Tipo</th>
                        <th style="padding:6px 10px;text-align:right;font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:#D97706;">Distancia</th>
                        <th style="padding:6px 10px;text-align:right;font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:#94A3B8;">Tiempo</th>
                        <th style="padding:6px 10px;text-align:right;font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:#94A3B8;">Pace</th>
                        <th style="padding:6px 10px;text-align:right;font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:#EF4444;">FC</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topWorkouts as $i => $workout)
                        <tr style="border-bottom:1px solid #F1F5F9;{{ $i % 2 !== 0 ? 'background:#F8FAFC;' : '' }}">
                            <td style="padding:6px 10px;text-align:center;font-weight:700;{{ $i === 0 ? 'color:#D97706' : ($i === 1 ? 'color:#94A3B8' : ($i === 2 ? 'color:#B45309' : 'color:#CBD5E1')) }}">{{ $i + 1 }}</td>
                            <td style="padding:6px 10px;">{{ $workout->date->locale('es')->isoFormat('D MMM YYYY') }}</td>
                            <td style="padding:6px 10px;color:#64748B;">{{ $typeColors[$workout->type] ?? $workout->type_label }}</td>
                            <td style="padding:6px 10px;text-align:right;font-weight:700;color:#D97706;">{{ number_format($workout->distance, 2) }} km</td>
                            <td style="padding:6px 10px;text-align:right;color:#64748B;">{{ $workout->formatted_duration }}</td>
                            <td style="padding:6px 10px;text-align:right;color:#64748B;">{{ $workout->formatted_pace }}/km</td>
                            <td style="padding:6px 10px;text-align:right;{{ $workout->avg_heart_rate ? 'color:#EF4444;font-weight:600;' : 'color:#CBD5E1;' }}">
                                {{ $workout->avg_heart_rate ? $workout->avg_heart_rate . ' bpm' : '—' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</x-public-layout>
