<x-app-layout>
    <header style="display:flex;justify-content:space-between;align-items:center;gap:1rem;margin-bottom:1.5rem;">
        <div style="display:flex;flex-direction:column;gap:.2rem;">
            <h1 style="font-family:'Space Grotesk',system-ui,sans-serif;font-size:1.6rem;">Dashboard</h1>
            <p style="font-size:.9rem;color:var(--text-muted);">Resumen de tus entrenamientos y actividad reciente.</p>
        </div>
        <div style="display:flex;align-items:center;gap:.5rem;">
            <a href="{{ route('workouts.create') }}" class="btn-primary" style="border-radius:999px;padding:.45rem .9rem;font-size:.8rem;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px;">
                    <path d="M12 5v14M5 12h14"/>
                </svg>
                Nuevo entreno
            </a>
        </div>
    </header>

    <!-- METRIC CARDS -->
    <section style="display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:1rem;margin-bottom:1.5rem;">
        <div style="padding:1rem;border-radius:.9rem;background:rgba(15,23,42,.95);border:1px solid var(--border-subtle);">
            <div style="font-size:.75rem;color:var(--text-muted);margin-bottom:.25rem;">Km esta semana</div>
            <div style="font-size:1.4rem;font-weight:600;font-family:'Space Grotesk',monospace;">
                {{ number_format($weekStats['total_distance'], 1) }}
            </div>
            <div style="font-size:.78rem;color:var(--accent-secondary);margin-top:.2rem;">
                {{ $weekStats['total_workouts'] }} {{ $weekStats['total_workouts'] === 1 ? 'sesión' : 'sesiones' }}
            </div>
        </div>
        <div style="padding:1rem;border-radius:.9rem;background:rgba(15,23,42,.95);border:1px solid var(--border-subtle);">
            <div style="font-size:.75rem;color:var(--text-muted);margin-bottom:.25rem;">Tiempo total</div>
            <div style="font-size:1.4rem;font-weight:600;font-family:'Space Grotesk',monospace;">
                @php
                    $hours = floor($weekStats['total_duration'] / 3600);
                    $minutes = floor(($weekStats['total_duration'] % 3600) / 60);
                @endphp
                {{ $hours > 0 ? $hours . 'h ' : '' }}{{ $minutes }}m
            </div>
            <div style="font-size:.78rem;color:var(--accent-secondary);margin-top:.2rem;">
                Semana {{ now()->weekOfYear }}
            </div>
        </div>
        <div style="padding:1rem;border-radius:.9rem;background:rgba(15,23,42,.95);border:1px solid var(--border-subtle);">
            <div style="font-size:.75rem;color:var(--text-muted);margin-bottom:.25rem;">Pace medio</div>
            <div style="font-size:1.4rem;font-weight:600;font-family:'Space Grotesk',monospace;">
                @if($weekStats['avg_pace'])
                    @php
                        $avgMinutes = floor($weekStats['avg_pace'] / 60);
                        $avgSeconds = $weekStats['avg_pace'] % 60;
                    @endphp
                    {{ $avgMinutes }}:{{ str_pad($avgSeconds, 2, '0', STR_PAD_LEFT) }}
                @else
                    –
                @endif
            </div>
            <div style="font-size:.78rem;color:var(--accent-secondary);margin-top:.2rem;">
                {{ $weekStats['avg_pace'] ? 'min/km' : 'Sin entrenamientos' }}
            </div>
        </div>
        <div style="padding:1rem;border-radius:.9rem;background:rgba(15,23,42,.95);border:1px solid var(--border-subtle);">
            <div style="font-size:.75rem;color:var(--text-muted);margin-bottom:.25rem;">Próxima carrera</div>
            <div style="font-size:1.4rem;font-weight:600;">—</div>
            <div style="font-size:.78rem;color:var(--accent-secondary);margin-top:.2rem;">Agregá una carrera</div>
        </div>
    </section>

    <!-- CONTENT -->
    <section style="display:grid;grid-template-columns:minmax(0,2fr) minmax(0,1.2fr);gap:1.5rem;">
        <!-- Entrenamientos -->
        <div style="padding:1rem;border-radius:.9rem;background:rgba(15,23,42,.95);border:1px solid var(--border-subtle);">
            <div style="display:flex;justify-content:space-between;align-items:center;gap:.5rem;margin-bottom:.75rem;">
                <div>
                    <div style="font-size:1rem;">Entrenamientos recientes</div>
                    <div style="font-size:.8rem;color:var(--text-muted);">Tus últimas 5 sesiones.</div>
                </div>
                <a href="{{ route('workouts.index') }}" style="font-size:.8rem;color:var(--accent-secondary);">Ver todos</a>
            </div>

            @if($recentWorkouts->count() > 0)
                <div style="display:grid;gap:.5rem;">
                    @foreach($recentWorkouts as $workout)
                        <a href="{{ route('workouts.edit', $workout) }}" style="
                            display:grid;
                            grid-template-columns:80px 1fr 80px 80px;
                            gap:.75rem;
                            padding:.75rem;
                            border-radius:.7rem;
                            background:rgba(5,8,20,.9);
                            border:1px solid rgba(31,41,55,.7);
                            align-items:center;
                            transition:all .15s ease-out;
                        " onmouseover="this.style.background='rgba(5,8,20,.7)';this.style.borderColor='rgba(255,59,92,.3)';" onmouseout="this.style.background='rgba(5,8,20,.9)';this.style.borderColor='rgba(31,41,55,.7)';">
                            <div style="font-family:'Space Grotesk',monospace;font-size:.8rem;color:var(--text-muted);">
                                {{ $workout->date->format('d/m') }}
                            </div>
                            <div>
                                <div style="font-size:.85rem;font-weight:500;">{{ $workout->type_label }}</div>
                                @if($workout->notes)
                                    <div style="font-size:.75rem;color:var(--text-muted);margin-top:.15rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                        {{ Str::limit($workout->notes, 40) }}
                                    </div>
                                @endif
                            </div>
                            <div style="font-family:'Space Grotesk',monospace;font-size:.8rem;">
                                <strong>{{ $workout->distance }}</strong> km
                            </div>
                            <div style="font-family:'Space Grotesk',monospace;font-size:.8rem;color:var(--accent-secondary);">
                                {{ $workout->formatted_pace }}
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div style="font-size:.85rem;color:var(--text-muted);text-align:center;padding:2rem 1rem;">
                    No hay entrenamientos cargados todavía.<br>
                    <br>
                    <a href="{{ route('workouts.create') }}" style="display:inline-flex;align-items:center;gap:.35rem;padding:.4rem .8rem;border-radius:999px;font-size:.8rem;border:1px solid var(--accent-secondary);color:var(--accent-secondary);background:rgba(45,227,142,.05);">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:14px;height:14px;">
                            <path d="M12 5v14M5 12h14"/>
                        </svg>
                        Crear primer entreno
                    </a>
                </div>
            @endif
        </div>

        <!-- Panel Coach / Info -->
        <div style="padding:1rem;border-radius:.9rem;background:rgba(15,23,42,.95);border:1px solid var(--border-subtle);">
            <div style="margin-bottom:.75rem;">
                <div style="font-size:1rem;">Resumen</div>
                <div style="font-size:.8rem;color:var(--text-muted);">Estadísticas generales</div>
            </div>

            <div style="display:grid;gap:.75rem;">
                <div style="padding:.75rem;border-radius:.6rem;background:rgba(5,8,20,.9);border:1px solid rgba(31,41,55,.7);">
                    <div style="font-size:.75rem;color:var(--text-muted);margin-bottom:.25rem;">Total entrenamientos</div>
                    <div style="font-size:1.2rem;font-weight:600;">{{ auth()->user()->workouts()->count() }}</div>
                </div>
                <div style="padding:.75rem;border-radius:.6rem;background:rgba(5,8,20,.9);border:1px solid rgba(31,41,55,.7);">
                    <div style="font-size:.75rem;color:var(--text-muted);margin-bottom:.25rem;">Total kilómetros</div>
                    <div style="font-size:1.2rem;font-weight:600;font-family:'Space Grotesk',monospace;">
                        {{ number_format(auth()->user()->workouts()->sum('distance'), 1) }} km
                    </div>
                </div>
                <div style="padding:.75rem;border-radius:.6rem;background:rgba(5,8,20,.9);border:1px solid rgba(31,41,55,.7);">
                    <div style="font-size:.75rem;color:var(--text-muted);margin-bottom:.25rem;">Miembro desde</div>
                    <div style="font-size:1.2rem;font-weight:600;">
                        {{ auth()->user()->created_at->format('M Y') }}
                    </div>
                </div>
            </div>
        </div>
    </section>

    <style>
        @media (max-width: 1024px) {
            header {
                flex-direction: column;
                align-items: flex-start;
            }
            header > div:last-child {
                width: 100%;
            }
            section:first-of-type {
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            }
            section:last-of-type {
                grid-template-columns: minmax(0, 1fr) !important;
            }
        }

        @media (max-width: 600px) {
            section:first-of-type {
                grid-template-columns: minmax(0, 1fr) !important;
            }
        }
    </style>
</x-app-layout>
