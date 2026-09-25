@php
    $reportTitle = $group->title;
    $reportSubtitle = 'Estudios médicos de '.$group->user->name;
@endphp

<x-public-layout
    :title="$reportTitle"
    :subtitle="$reportSubtitle"
>
    {{-- Aviso compartido --}}
    <div class="public-notice">
        <strong>Reporte de Estudios</strong> de {{ $group->user->name }}<br>
        <span style="font-size:0.85rem;">
            @if($group->doctor)
                Para: Dr. {{ $group->doctor->name }}{{ $group->doctor->specialty ? ' — '.$group->doctor->specialty : '' }} &bull;
            @endif
            Expira: {{ $share->expires_at->format('d/m/Y H:i') }} &bull;
            Vistas: {{ $share->view_count }}
        </span>
    </div>

    @if($group->user->health_insurance_provider || $group->user->health_insurance_plan || $group->user->health_insurance_member_number)
        <div style="border:1px solid var(--border-subtle);border-radius:0.5rem;padding:0.85rem 1.25rem;margin-bottom:1.5rem;font-size:0.85rem;color:#94A3B8;">
            <strong style="color:var(--text-main);">Obra Social:</strong>
            {{ $group->user->health_insurance_provider ?: '—' }}
            @if($group->user->health_insurance_plan) &bull; Plan {{ $group->user->health_insurance_plan }} @endif
            @if($group->user->health_insurance_member_number) &bull; N° Credencial: {{ $group->user->health_insurance_member_number }} @endif
        </div>
    @endif

    @if($group->notes)
        <div style="margin-bottom:1.5rem;font-size:0.9rem;color:#64748B;">
            {{ $group->notes }}
        </div>
    @endif

    <div style="margin-bottom:1.5rem;">
        <a href="{{ route('medical.groups.shared-zip', $share->token) }}"
           style="display:inline-flex;align-items:center;gap:0.5rem;background:var(--accent-secondary);color:#05060A;font-weight:700;padding:0.65rem 1.25rem;border-radius:0.5rem;font-size:0.85rem;">
            ⬇ Descargar todo (ZIP)
        </a>
    </div>

    <h3 style="font-size:0.75rem;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:#94A3B8;margin-bottom:0.75rem;">
        Estudios ({{ $group->documents->count() }})
    </h3>

    @foreach($group->documents as $document)
        <div style="border:1px solid var(--border-subtle);border-radius:0.5rem;padding:1rem 1.25rem;margin-bottom:0.75rem;display:flex;align-items:center;justify-content:space-between;gap:1rem;flex-wrap:wrap;">
            <div style="min-width:0;">
                <div style="font-weight:600;margin-bottom:0.25rem;">{{ $document->title }}</div>
                <div style="font-size:0.8rem;color:#94A3B8;">
                    {{ $document->type->label() }}
                    @if($document->issued_at)
                        &bull; {{ $document->issued_at->format('d/m/Y') }}
                    @endif
                </div>
            </div>
            <div style="display:flex;gap:0.5rem;flex-shrink:0;">
                <a href="{{ route('medical.groups.shared-document-preview', [$share->token, $document]) }}"
                   target="_blank"
                   style="display:inline-flex;align-items:center;gap:0.4rem;border:1px solid var(--border-subtle);color:var(--text-main);padding:0.5rem 1rem;border-radius:0.5rem;font-size:0.8rem;font-weight:600;">
                    Ver
                </a>
                @if($document->images_url)
                    <a href="{{ $document->images_url }}"
                       target="_blank" rel="noopener noreferrer"
                       style="display:inline-flex;align-items:center;gap:0.4rem;border:1px solid var(--border-subtle);color:var(--text-main);padding:0.5rem 1rem;border-radius:0.5rem;font-size:0.8rem;font-weight:600;">
                        Imágenes
                    </a>
                @endif
            </div>
        </div>
    @endforeach

    @if($trainingReport)
        @php
            $trainingSummary = $trainingReport['summary'];
            $trainingHrStats = $trainingReport['hr_stats'];
            $trainingHours = floor($trainingSummary['total_duration'] / 3600);
            $trainingMins = floor(($trainingSummary['total_duration'] % 3600) / 60);
            $workoutTypeLabels = \App\Models\Workout::typeLabels();
        @endphp

        <div style="margin-top:2.5rem;padding-top:1.5rem;border-top:2px solid var(--border-subtle);">
            <h3 style="font-size:0.75rem;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:#94A3B8;margin-bottom:0.25rem;">
                Entrenamientos — {{ $trainingReport['period']->label() }}
            </h3>
            <p style="font-size:0.8rem;color:#64748B;margin-bottom:1rem;">
                Del {{ $trainingReport['from']->format('d/m/Y') }} al {{ $trainingReport['to']->format('d/m/Y') }} &bull; Solo entrenamientos completados
            </p>

            @if($trainingReport['workouts']->isEmpty())
                <div style="border:1px dashed var(--border-subtle);border-radius:0.5rem;padding:1rem 1.25rem;font-size:0.85rem;color:#94A3B8;">
                    No hay entrenamientos registrados en este período.
                </div>
            @else
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:1rem;margin-bottom:1.5rem;">
                    <x-metric-card label="Kilómetros" :value="number_format($trainingSummary['total_distance'], 1) . ' km'" />
                    <x-metric-card label="Tiempo total" :value="($trainingHours > 0 ? $trainingHours . 'h ' : '') . $trainingMins . 'm'" />
                    <x-metric-card label="Sesiones" :value="$trainingSummary['total_sessions']" />
                    <x-metric-card label="Pace promedio" :value="$trainingSummary['formatted_pace']" />
                </div>

                @if(! empty($trainingHrStats))
                    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:0.75rem;margin-bottom:1.5rem;">
                        <x-metric-card label="FC Promedio" :value="$trainingHrStats['overall_avg'] . ' bpm'" />
                        <x-metric-card label="FC Máxima" :value="$trainingHrStats['max'] . ' bpm'" />
                        <x-metric-card label="FC Mínima" :value="$trainingHrStats['min'] . ' bpm'" />
                    </div>
                @endif

                <h4 style="font-size:0.75rem;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:#94A3B8;margin-bottom:0.5rem;">
                    Volumen semanal
                </h4>
                <div style="overflow-x:auto;margin-bottom:1.5rem;">
                    <table style="width:100%;border-collapse:collapse;font-size:0.8rem;">
                        <thead>
                            <tr style="background:#F8FAFC;border-bottom:2px solid #E2E8F0;">
                                <th style="padding:6px 10px;font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;text-align:left;color:#94A3B8;">Semana</th>
                                <th style="padding:6px 10px;font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;text-align:right;color:#94A3B8;">Sesiones</th>
                                <th style="padding:6px 10px;font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;text-align:right;color:#2DE38E;">Km</th>
                                <th style="padding:6px 10px;font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;text-align:right;color:#94A3B8;">Tiempo</th>
                                <th style="padding:6px 10px;font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;text-align:right;color:#94A3B8;">Pace</th>
                                <th style="padding:6px 10px;font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;text-align:right;color:#EF4444;">FC</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($trainingReport['weekly_breakdown'] as $week)
                                <tr style="border-bottom:1px solid #F1F5F9;">
                                    <td style="padding:6px 10px;white-space:nowrap;">{{ $week['week_start']->locale('es')->isoFormat('D MMM') }} – {{ $week['week_end']->locale('es')->isoFormat('D MMM') }}</td>
                                    <td style="padding:6px 10px;text-align:right;color:#64748B;">{{ $week['total_sessions'] }}</td>
                                    <td style="padding:6px 10px;text-align:right;font-weight:700;color:#2DE38E;">{{ number_format($week['total_distance'], 1) }}</td>
                                    <td style="padding:6px 10px;text-align:right;color:#64748B;">{{ $week['formatted_duration'] }}</td>
                                    <td style="padding:6px 10px;text-align:right;color:#64748B;">{{ $week['formatted_pace'] }}</td>
                                    <td style="padding:6px 10px;text-align:right;{{ $week['avg_heart_rate_week'] ? 'color:#EF4444;font-weight:600;' : 'color:#CBD5E1;' }}">
                                        {{ $week['avg_heart_rate_week'] ? $week['avg_heart_rate_week'] . ' bpm' : '—' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <h4 style="font-size:0.75rem;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:#94A3B8;margin-bottom:0.5rem;">
                    Detalle de entrenamientos ({{ $trainingReport['workouts']->count() }})
                </h4>
                <div style="overflow-x:auto;">
                    <table style="width:100%;border-collapse:collapse;font-size:0.8rem;">
                        <thead>
                            <tr style="background:#F8FAFC;border-bottom:2px solid #E2E8F0;">
                                <th style="padding:6px 10px;font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;text-align:left;color:#94A3B8;">Fecha</th>
                                <th style="padding:6px 10px;font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;text-align:left;color:#94A3B8;">Tipo</th>
                                <th style="padding:6px 10px;font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;text-align:right;color:#D97706;">Distancia</th>
                                <th style="padding:6px 10px;font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;text-align:right;color:#94A3B8;">Tiempo</th>
                                <th style="padding:6px 10px;font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;text-align:right;color:#94A3B8;">Pace</th>
                                <th style="padding:6px 10px;font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;text-align:right;color:#EF4444;">FC</th>
                                <th style="padding:6px 10px;font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;text-align:right;color:#94A3B8;">Esfuerzo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($trainingReport['workouts'] as $i => $workout)
                                <tr style="border-bottom:1px solid #F1F5F9;{{ $i % 2 !== 0 ? 'background:#F8FAFC;' : '' }}">
                                    <td style="padding:6px 10px;white-space:nowrap;">{{ $workout->date->locale('es')->isoFormat('ddd D MMM YYYY') }}</td>
                                    <td style="padding:6px 10px;color:#64748B;">{{ $workoutTypeLabels[$workout->type] ?? $workout->type }}{{ $workout->is_race ? ' 🏁' : '' }}</td>
                                    <td style="padding:6px 10px;text-align:right;font-weight:700;color:#D97706;">{{ number_format($workout->distance, 2) }} km</td>
                                    <td style="padding:6px 10px;text-align:right;color:#64748B;">{{ $workout->formatted_duration }}</td>
                                    <td style="padding:6px 10px;text-align:right;color:#64748B;">{{ $workout->formatted_pace }}</td>
                                    <td style="padding:6px 10px;text-align:right;{{ $workout->avg_heart_rate ? 'color:#EF4444;font-weight:600;' : 'color:#CBD5E1;' }}">
                                        {{ $workout->avg_heart_rate ? $workout->avg_heart_rate . ' bpm' : '—' }}
                                    </td>
                                    <td style="padding:6px 10px;text-align:right;color:#64748B;">{{ $workout->difficulty ? $workout->difficulty . '/5' : '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @endif
</x-public-layout>
