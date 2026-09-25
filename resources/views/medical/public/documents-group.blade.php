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
            $trainingStats = [
                ['label' => 'Kilómetros', 'value' => number_format($trainingSummary['total_distance'], 1), 'unit' => 'km', 'accent' => '#2DE38E'],
                ['label' => 'Tiempo', 'value' => ($trainingHours > 0 ? $trainingHours.'h ' : '').$trainingMins.'m', 'unit' => 'en movimiento', 'accent' => '#60A5FA'],
                ['label' => 'Sesiones', 'value' => $trainingSummary['total_sessions'], 'unit' => 'entrenamientos', 'accent' => '#F59E0B'],
                ['label' => 'Pace Promedio', 'value' => str_replace('/km', '', $trainingSummary['formatted_pace']), 'unit' => 'min/km', 'accent' => '#FF3B5C'],
            ];

            if (! empty($trainingHrStats)) {
                $trainingStats[] = ['label' => 'FC Promedio', 'value' => $trainingHrStats['overall_avg'], 'unit' => 'bpm', 'accent' => '#EF4444'];
                $trainingStats[] = ['label' => 'FC Máx / Mín', 'value' => $trainingHrStats['max'].' / '.$trainingHrStats['min'], 'unit' => 'bpm (promedio por sesión)', 'accent' => '#EF4444'];
            }
        @endphp

        <div style="margin-top:2.5rem;">
            <x-report-card
                :title="'Entrenamientos — '.$trainingReport['period']->label()"
                :subtitle="'Del '.$trainingReport['from']->format('d/m/Y').' al '.$trainingReport['to']->format('d/m/Y').' • Solo entrenamientos completados'"
            >
                @if($trainingReport['workouts']->isEmpty())
                    <div style="text-align:center;padding:1.5rem;color:var(--text-muted);font-size:.9rem;">
                        No hay entrenamientos registrados en este período.
                    </div>
                @else
                    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:.75rem;">
                        @foreach($trainingStats as $stat)
                            <div style="padding:.75rem 1rem;border-radius:.6rem;background:rgba(30,41,59,.3);border-left:3px solid {{ $stat['accent'] }};">
                                <div style="font-size:.75rem;color:var(--text-muted);margin-bottom:.25rem;">{{ $stat['label'] }}</div>
                                <div style="font-family:'Space Grotesk',sans-serif;font-size:1.35rem;font-weight:700;line-height:1.2;">{{ $stat['value'] }}</div>
                                <div style="font-size:.7rem;color:{{ $stat['accent'] }};margin-top:.15rem;">{{ $stat['unit'] }}</div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-report-card>

            @if($trainingReport['workouts']->isNotEmpty())
                <x-report-card title="Volumen Semanal" subtitle="De la semana más reciente a la más antigua">
                    <div style="overflow-x:auto;">
                        <table style="width:100%;border-collapse:collapse;font-size:.85rem;">
                            <thead>
                                <tr style="border-bottom:2px solid var(--border-subtle);">
                                    <th style="text-align:left;padding:.5rem;color:var(--text-muted);font-weight:500;font-size:.75rem;text-transform:uppercase;letter-spacing:.05em;">Semana</th>
                                    <th style="text-align:right;padding:.5rem;color:var(--text-muted);font-weight:500;font-size:.75rem;text-transform:uppercase;letter-spacing:.05em;">Sesiones</th>
                                    <th style="text-align:right;padding:.5rem;color:var(--text-muted);font-weight:500;font-size:.75rem;text-transform:uppercase;letter-spacing:.05em;">Distancia</th>
                                    <th style="text-align:right;padding:.5rem;color:var(--text-muted);font-weight:500;font-size:.75rem;text-transform:uppercase;letter-spacing:.05em;">Tiempo</th>
                                    <th style="text-align:right;padding:.5rem;color:var(--text-muted);font-weight:500;font-size:.75rem;text-transform:uppercase;letter-spacing:.05em;">Pace</th>
                                    <th style="text-align:right;padding:.5rem;color:var(--text-muted);font-weight:500;font-size:.75rem;text-transform:uppercase;letter-spacing:.05em;">FC</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($trainingReport['weekly_breakdown'] as $week)
                                    <tr style="border-bottom:1px solid var(--border-subtle);">
                                        <td style="padding:.5rem;white-space:nowrap;">{{ $week['week_start']->locale('es')->isoFormat('D MMM') }} – {{ $week['week_end']->locale('es')->isoFormat('D MMM') }}</td>
                                        <td style="padding:.5rem;text-align:right;color:var(--text-muted);">{{ $week['total_sessions'] }}</td>
                                        <td style="padding:.5rem;text-align:right;font-weight:600;color:var(--accent-secondary);white-space:nowrap;">{{ number_format($week['total_distance'], 1) }} km</td>
                                        <td style="padding:.5rem;text-align:right;">{{ $week['formatted_duration'] }}</td>
                                        <td style="padding:.5rem;text-align:right;font-family:monospace;white-space:nowrap;">{{ $week['formatted_pace'] }}</td>
                                        <td style="padding:.5rem;text-align:right;white-space:nowrap;color:{{ $week['avg_heart_rate_week'] ? '#F87171' : 'var(--text-muted)' }};">
                                            {{ $week['avg_heart_rate_week'] ? $week['avg_heart_rate_week'].' bpm' : '–' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </x-report-card>

                <x-report-card
                    title="Detalle de Entrenamientos"
                    :subtitle="$trainingReport['workouts']->count().' '.($trainingReport['workouts']->count() === 1 ? 'sesión registrada' : 'sesiones registradas')"
                >
                    <div style="overflow-x:auto;">
                        <table style="width:100%;border-collapse:collapse;font-size:.85rem;">
                            <thead>
                                <tr style="border-bottom:2px solid var(--border-subtle);">
                                    <th style="text-align:left;padding:.5rem;color:var(--text-muted);font-weight:500;font-size:.75rem;text-transform:uppercase;letter-spacing:.05em;">Fecha</th>
                                    <th style="text-align:left;padding:.5rem;color:var(--text-muted);font-weight:500;font-size:.75rem;text-transform:uppercase;letter-spacing:.05em;">Tipo</th>
                                    <th style="text-align:right;padding:.5rem;color:var(--text-muted);font-weight:500;font-size:.75rem;text-transform:uppercase;letter-spacing:.05em;">Distancia</th>
                                    <th style="text-align:right;padding:.5rem;color:var(--text-muted);font-weight:500;font-size:.75rem;text-transform:uppercase;letter-spacing:.05em;">Tiempo</th>
                                    <th style="text-align:right;padding:.5rem;color:var(--text-muted);font-weight:500;font-size:.75rem;text-transform:uppercase;letter-spacing:.05em;">Pace</th>
                                    <th style="text-align:right;padding:.5rem;color:var(--text-muted);font-weight:500;font-size:.75rem;text-transform:uppercase;letter-spacing:.05em;">FC</th>
                                    <th style="text-align:center;padding:.5rem;color:var(--text-muted);font-weight:500;font-size:.75rem;text-transform:uppercase;letter-spacing:.05em;">Esfuerzo</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($trainingReport['workouts'] as $workout)
                                    <tr style="border-bottom:1px solid var(--border-subtle);">
                                        <td style="padding:.5rem;white-space:nowrap;">
                                            {{ $workout->date->format('d/m/Y') }}
                                            <span style="font-size:.75rem;color:var(--text-muted);margin-left:.25rem;">{{ $workout->date->locale('es')->isoFormat('ddd') }}</span>
                                        </td>
                                        <td style="padding:.5rem;">
                                            <span style="padding:.2rem .5rem;border-radius:.4rem;background:rgba(59,130,246,.1);color:rgb(96,165,250);font-size:.75rem;white-space:nowrap;">
                                                {{ $workout->type_label }}{{ $workout->is_race ? ' 🏁' : '' }}
                                            </span>
                                        </td>
                                        <td style="padding:.5rem;text-align:right;font-weight:500;white-space:nowrap;">{{ number_format($workout->distance, 2) }} km</td>
                                        <td style="padding:.5rem;text-align:right;">{{ $workout->formatted_duration }}</td>
                                        <td style="padding:.5rem;text-align:right;font-family:monospace;white-space:nowrap;">{{ $workout->formatted_pace }}</td>
                                        <td style="padding:.5rem;text-align:right;white-space:nowrap;color:{{ $workout->avg_heart_rate ? '#F87171' : 'var(--text-muted)' }};">
                                            {{ $workout->avg_heart_rate ? $workout->avg_heart_rate.' bpm' : '–' }}
                                        </td>
                                        <td style="padding:.5rem;text-align:center;white-space:nowrap;">
                                            @if($workout->difficulty)
                                                @for($i = 1; $i <= 5; $i++)
                                                    <span style="font-size:.7rem;color:{{ $i <= $workout->difficulty ? 'rgb(251,191,36)' : 'rgba(251,191,36,.2)' }};">●</span>
                                                @endfor
                                            @else
                                                <span style="color:var(--text-muted);">–</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div style="margin-top:1rem;padding-top:1rem;border-top:1px solid var(--border-subtle);font-size:.85rem;color:var(--text-muted);">
                        <strong>Total:</strong> {{ $trainingSummary['total_sessions'] }} {{ $trainingSummary['total_sessions'] === 1 ? 'entrenamiento' : 'entrenamientos' }}
                        • {{ number_format($trainingSummary['total_distance'], 2) }} km
                        • {{ $trainingSummary['formatted_duration'] }}
                    </div>
                </x-report-card>
            @endif
        </div>
    @endif
</x-public-layout>
