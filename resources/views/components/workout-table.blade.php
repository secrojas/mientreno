@props([
    'workouts' => [],
    'showActions' => false,
])

@if($workouts->isEmpty())
    <div style="text-align:center;padding:2rem;color:var(--text-muted);">
        <div style="font-size:2rem;margin-bottom:.5rem;">📭</div>
        <p>No hay entrenamientos registrados en este período</p>
    </div>
@else
    <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;font-size:.9rem;">
            <thead>
                <tr style="border-bottom:2px solid var(--border-subtle);">
                    <th style="text-align:left;padding:.75rem .5rem;color:var(--text-muted);font-weight:500;">Fecha</th>
                    <th style="text-align:left;padding:.75rem .5rem;color:var(--text-muted);font-weight:500;">Tipo</th>
                    <th style="text-align:right;padding:.75rem .5rem;color:var(--text-muted);font-weight:500;">Distancia</th>
                    <th style="text-align:right;padding:.75rem .5rem;color:var(--text-muted);font-weight:500;">Duración</th>
                    <th style="text-align:right;padding:.75rem .5rem;color:var(--text-muted);font-weight:500;">Pace</th>
                    <th style="text-align:center;padding:.75rem .5rem;color:var(--text-muted);font-weight:500;">Dificultad</th>
                    @if($showActions)
                        <th style="text-align:right;padding:.75rem .5rem;color:var(--text-muted);font-weight:500;">Acciones</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach($workouts as $workout)
                    <tr style="border-bottom:1px solid var(--border-subtle);">
                        <td style="padding:.75rem .5rem;">
                            <div style="font-weight:500;">{{ $workout->date->format('d/m/Y') }}</div>
                            <div style="font-size:.75rem;color:var(--text-muted);">{{ $workout->date->locale('es')->dayName }}</div>
                        </td>
                        <td style="padding:.75rem .5rem;">
                            <span style="padding:.25rem .5rem;border-radius:.4rem;background:rgba(59,130,246,.1);color:rgb(96,165,250);font-size:.8rem;white-space:nowrap;">
                                {{ $workout->typeLabel }}
                            </span>
                        </td>
                        <td style="padding:.75rem .5rem;text-align:right;font-weight:500;">
                            {{ number_format($workout->distance, 2) }} km
                        </td>
                        <td style="padding:.75rem .5rem;text-align:right;">
                            {{ $workout->formattedDuration }}
                        </td>
                        <td style="padding:.75rem .5rem;text-align:right;font-family:monospace;">
                            {{ $workout->formattedPace }}/km
                        </td>
                        <td style="padding:.75rem .5rem;text-align:center;">
                            @if($workout->difficulty)
                                <div style="display:inline-flex;gap:.15rem;">
                                    @for($i = 1; $i <= 5; $i++)
                                        <span style="color:{{ $i <= $workout->difficulty ? 'rgb(251,191,36)' : 'rgba(251,191,36,.2)' }};">●</span>
                                    @endfor
                                </div>
                            @else
                                <span style="color:var(--text-muted);">–</span>
                            @endif
                        </td>
                        @if($showActions)
                            <td style="padding:.75rem .5rem;text-align:right;">
                                <a href="{{ route('workouts.edit', $workout) }}" style="color:var(--primary);text-decoration:none;font-size:.85rem;">
                                    Editar
                                </a>
                            </td>
                        @endif
                    </tr>
                    @if($workout->notes)
                        <tr>
                            <td colspan="{{ $showActions ? 7 : 6 }}" style="padding:.5rem .5rem .75rem .5rem;border-bottom:1px solid var(--border-subtle);">
                                <div style="font-size:.85rem;color:var(--text-muted);font-style:italic;padding-left:1rem;">
                                    💭 {{ $workout->notes }}
                                </div>
                            </td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>

    <div style="margin-top:1rem;padding-top:1rem;border-top:1px solid var(--border-subtle);font-size:.85rem;color:var(--text-muted);">
        <strong>Total:</strong> {{ $workouts->count() }} {{ $workouts->count() === 1 ? 'entrenamiento' : 'entrenamientos' }}
        • {{ number_format($workouts->sum('distance'), 2) }} km
        • {{ \Carbon\CarbonInterval::seconds($workouts->sum('duration'))->cascade()->forHumans(['short' => true]) }}
    </div>
@endif
