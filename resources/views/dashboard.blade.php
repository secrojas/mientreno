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
        @php
            $hours = floor($weekStats['total_duration'] / 3600);
            $minutes = floor(($weekStats['total_duration'] % 3600) / 60);
            $avgMinutes = $weekStats['avg_pace'] ? floor($weekStats['avg_pace'] / 60) : 0;
            $avgSeconds = $weekStats['avg_pace'] ? $weekStats['avg_pace'] % 60 : 0;
        @endphp

        <x-metric-card
            label="Km esta semana"
            :value="number_format($weekStats['total_distance'], 1)"
            :subtitle="$weekStats['total_workouts'] . ' ' . ($weekStats['total_workouts'] === 1 ? 'sesión' : 'sesiones')"
        />

        <x-metric-card
            label="Tiempo total"
            :value="($hours > 0 ? $hours . 'h ' : '') . $minutes . 'm'"
            :subtitle="'Semana ' . now()->weekOfYear"
        />

        <x-metric-card
            label="Pace medio"
            :value="$weekStats['avg_pace'] ? $avgMinutes . ':' . str_pad($avgSeconds, 2, '0', STR_PAD_LEFT) : '–'"
            :subtitle="$weekStats['avg_pace'] ? 'min/km' : 'Sin entrenamientos'"
        />

        <x-metric-card
            label="Próxima carrera"
            :value="$nextRace ? $nextRace->name : '—'"
            :subtitle="$nextRace ? 'en ' . $nextRace->days_until . ' días (' . $nextRace->date->format('d/m') . ')' : 'Agregá una carrera'"
            accent="primary"
        />
    </section>

    <!-- CONTENT -->
    <section style="display:grid;grid-template-columns:minmax(0,2fr) minmax(0,1.2fr);gap:1.5rem;">
        <!-- Entrenamientos -->
        <x-card title="Entrenamientos recientes" subtitle="Tus últimas 5 sesiones.">
            <x-slot:headerAction>
                <a href="{{ route('workouts.index') }}" style="font-size:.8rem;color:var(--accent-secondary);">Ver todos</a>
            </x-slot:headerAction>

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
        </x-card>

        <!-- Panel derecho -->
        <div style="display:flex;flex-direction:column;gap:1.5rem;">
            <!-- Objetivos Activos -->
            <x-card title="Objetivos Activos" :subtitle="$activeGoals->count() . ' objetivo(s)'">
                <x-slot:headerAction>
                    <a href="{{ route('goals.index') }}" style="font-size:.8rem;color:var(--accent-secondary);">Ver todos</a>
                </x-slot:headerAction>

                @if($activeGoals->count() > 0)
                    <div style="display:grid;gap:.75rem;">
                        @foreach($activeGoals as $goal)
                            <div style="padding:.75rem;border-radius:.6rem;background:rgba(5,8,20,.9);border:1px solid rgba(45,227,142,.3);">
                                <div style="display:flex;justify-content:space-between;align-items:start;margin-bottom:.4rem;">
                                    <span style="padding:.12rem .4rem;border-radius:.3rem;background:rgba(45,227,142,.1);border:1px solid rgba(45,227,142,.3);font-size:.65rem;text-transform:uppercase;letter-spacing:.05em;">
                                        {{ $goal->type_label }}
                                    </span>
                                    @if($goal->target_date && $goal->days_until !== null)
                                        <span style="font-size:.7rem;color:var(--text-muted);">
                                            {{ $goal->days_until >= 0 ? $goal->days_until . ' días' : 'Vencido' }}
                                        </span>
                                    @endif
                                </div>
                                <div style="font-size:.85rem;font-weight:500;margin-bottom:.3rem;">{{ $goal->title }}</div>
                                <div style="font-size:.75rem;color:var(--text-muted);">{{ $goal->getTargetDescription() }}</div>
                                @if($goal->progress_percentage > 0)
                                    <div style="margin-top:.5rem;">
                                        <div style="width:100%;height:3px;background:rgba(15,23,42,.9);border-radius:999px;overflow:hidden;">
                                            <div style="width:{{ $goal->progress_percentage }}%;height:100%;background:var(--accent-secondary);"></div>
                                        </div>
                                        <div style="font-size:.7rem;color:var(--text-muted);margin-top:.25rem;">{{ $goal->progress_percentage }}%</div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div style="text-align:center;padding:1.5rem;color:var(--text-muted);font-size:.85rem;">
                        No hay objetivos activos.
                        <br>
                        <a href="{{ route('goals.create') }}" style="color:var(--accent-secondary);margin-top:.5rem;display:inline-block;">Crear objetivo</a>
                    </div>
                @endif
            </x-card>

            <!-- Cumplimiento Semanal -->
            <x-card title="Cumplimiento Semanal" subtitle="Semana {{ now()->weekOfYear }}">
                <x-slot:headerAction>
                    <a href="{{ route('workouts.index', ['status' => 'planned']) }}" style="font-size:.8rem;color:var(--accent-secondary);">Ver planificados</a>
                </x-slot:headerAction>

                @if($weeklyCompletion['total'] > 0)
                    <div style="padding:.75rem;border-radius:.6rem;background:rgba(5,8,20,.9);border:1px solid rgba(31,41,55,.7);">
                        <!-- Porcentaje grande -->
                        <div style="text-align:center;margin-bottom:1rem;">
                            <div style="font-size:2.5rem;font-weight:700;font-family:'Space Grotesk',monospace;color:var(--accent-secondary);">
                                {{ $weeklyCompletion['percentage'] }}%
                            </div>
                            <div style="font-size:.75rem;color:var(--text-muted);margin-top:.25rem;">
                                {{ $weeklyCompletion['completed'] }} de {{ $weeklyCompletion['total'] }} entrenamientos completados
                            </div>
                        </div>

                        <!-- Barra de progreso -->
                        <div style="width:100%;height:6px;background:rgba(15,23,42,.9);border-radius:999px;overflow:hidden;margin-bottom:1rem;">
                            <div style="width:{{ $weeklyCompletion['percentage'] }}%;height:100%;background:var(--accent-secondary);transition:width 0.3s ease;"></div>
                        </div>

                        <!-- Breakdown -->
                        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:.5rem;font-size:.75rem;">
                            <div style="text-align:center;padding:.5rem;border-radius:.4rem;background:rgba(45,227,142,.05);border:1px solid rgba(45,227,142,.2);">
                                <div style="font-weight:600;font-size:1.1rem;color:#2de38e;">{{ $weeklyCompletion['completed'] }}</div>
                                <div style="color:var(--text-muted);margin-top:.15rem;">Completados</div>
                            </div>
                            <div style="text-align:center;padding:.5rem;border-radius:.4rem;background:rgba(59,130,246,.05);border:1px solid rgba(59,130,246,.2);">
                                <div style="font-weight:600;font-size:1.1rem;color:#60a5fa;">{{ $weeklyCompletion['planned'] }}</div>
                                <div style="color:var(--text-muted);margin-top:.15rem;">Planificados</div>
                            </div>
                            <div style="text-align:center;padding:.5rem;border-radius:.4rem;background:rgba(234,179,8,.05);border:1px solid rgba(234,179,8,.2);">
                                <div style="font-weight:600;font-size:1.1rem;color:#facc15;">{{ $weeklyCompletion['skipped'] }}</div>
                                <div style="color:var(--text-muted);margin-top:.15rem;">Saltados</div>
                            </div>
                        </div>

                        @if($weeklyCompletion['planned'] > 0)
                            <div style="margin-top:.75rem;padding:.5rem;border-radius:.4rem;background:rgba(59,130,246,.05);border:1px solid rgba(59,130,246,.2);text-align:center;">
                                <div style="font-size:.75rem;color:#60a5fa;">
                                    ¡Tenés {{ $weeklyCompletion['planned'] }} {{ $weeklyCompletion['planned'] === 1 ? 'entrenamiento planificado' : 'entrenamientos planificados' }} pendientes!
                                </div>
                            </div>
                        @endif
                    </div>
                @else
                    <div style="text-align:center;padding:1.5rem;color:var(--text-muted);font-size:.85rem;">
                        No hay entrenamientos esta semana.
                        <br>
                        <a href="{{ route('workouts.create') }}" style="color:var(--accent-secondary);margin-top:.5rem;display:inline-block;">Planificar entrenamientos</a>
                    </div>
                @endif
            </x-card>

            <!-- Resumen -->
            <x-card title="Resumen" subtitle="Estadísticas generales">
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
                </div>
            </x-card>
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
