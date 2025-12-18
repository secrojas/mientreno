<x-app-layout>
    <header style="display:flex;justify-content:space-between;align-items:center;gap:1rem;margin-bottom:1.5rem;">
        <div style="display:flex;flex-direction:column;gap:.2rem;">
            <h1 style="font-family:'Space Grotesk',system-ui,sans-serif;font-size:1.6rem;">Dashboard Coach</h1>
            <p style="font-size:.9rem;color:var(--text-muted);">Gestión y seguimiento de tus alumnos.</p>
        </div>

        @if(!isset($hasNoBusiness))
        <div style="display:flex;align-items:center;gap:.5rem;">
            <a href="#" class="btn-primary" style="border-radius:999px;padding:.45rem .9rem;font-size:.8rem;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px;">
                    <path d="M12 5v14M5 12h14"/>
                </svg>
                Invitar alumno
            </a>
        </div>
        @endif
    </header>

    @if(isset($hasNoBusiness) && $hasNoBusiness)
        <!-- Sin business -->
        <x-card title="Bienvenido a MiEntreno Coach" subtitle="Configurá tu negocio para empezar">
            <div style="text-align:center;padding:2rem 1rem;">
                <div style="margin-bottom:1.5rem;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="width:64px;height:64px;margin:0 auto;color:var(--accent-secondary);">
                        <rect x="3" y="10" width="9" height="5" rx="1.5"></rect>
                        <path d="M12 12h3.5a3.5 3.5 0 1 1-2.47 6"></path>
                        <circle cx="17.5" cy="10" r="1.2"></circle>
                    </svg>
                </div>
                <p style="font-size:.95rem;color:var(--text-muted);margin-bottom:1.5rem;max-width:500px;margin-left:auto;margin-right:auto;">
                    Para empezar a gestionar tus alumnos y grupos de entrenamiento, primero necesitás crear tu negocio.
                </p>
                <a href="#" style="display:inline-flex;align-items:center;gap:.5rem;padding:.6rem 1.2rem;border-radius:999px;font-size:.9rem;background:var(--accent-secondary);color:#050814;font-weight:500;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:18px;height:18px;">
                        <path d="M12 5v14M5 12h14"/>
                    </svg>
                    Crear mi negocio
                </a>
            </div>
        </x-card>
    @else
        <!-- METRIC CARDS -->
        <section style="display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:1rem;margin-bottom:1.5rem;">
            <x-metric-card
                label="Total Alumnos"
                :value="$totalStudents"
                :subtitle="$business->name"
            />

            <x-metric-card
                label="Activos esta semana"
                :value="$studentMetrics['active_students_this_week']"
                :subtitle="'de ' . $totalStudents . ' alumnos'"
                accent="secondary"
            />

            <x-metric-card
                label="Entrenamientos"
                :value="$studentMetrics['total_workouts_this_week']"
                subtitle="Esta semana"
            />

            <x-metric-card
                label="Kilómetros totales"
                :value="number_format($studentMetrics['total_distance_this_week'], 1)"
                subtitle="Esta semana"
                accent="primary"
            />
        </section>

        <!-- CONTENT -->
        <section style="display:grid;grid-template-columns:minmax(0,2fr) minmax(0,1.2fr);gap:1.5rem;">
            <!-- Actividad Reciente -->
            <x-card title="Actividad Reciente" subtitle="Últimos entrenamientos de tus alumnos">
                <x-slot:headerAction>
                    <a href="#" style="font-size:.8rem;color:var(--accent-secondary);">Ver todos</a>
                </x-slot:headerAction>

                @if($recentActivity->count() > 0)
                    <div style="display:grid;gap:.5rem;">
                        @foreach($recentActivity as $activity)
                            @php
                                $workout = (object) (array) $activity;
                                $pace = $workout->pace ?? 0;
                                $avgMinutes = $pace ? floor($pace / 60) : 0;
                                $avgSeconds = $pace ? $pace % 60 : 0;
                                $formattedPace = $pace ? $avgMinutes . ':' . str_pad($avgSeconds, 2, '0', STR_PAD_LEFT) : '-';
                            @endphp
                            <div style="
                                display:grid;
                                grid-template-columns:100px 1fr 80px 80px;
                                gap:.75rem;
                                padding:.75rem;
                                border-radius:.7rem;
                                background:rgba(5,8,20,.9);
                                border:1px solid rgba(31,41,55,.7);
                                align-items:center;
                            ">
                                <div style="font-family:'Space Grotesk',monospace;font-size:.75rem;color:var(--text-muted);">
                                    {{ \Carbon\Carbon::parse($workout->date)->format('d/m H:i') }}
                                </div>
                                <div>
                                    <div style="font-size:.85rem;font-weight:500;color:var(--accent-secondary);">{{ $workout->student_name }}</div>
                                    <div style="font-size:.75rem;color:var(--text-muted);margin-top:.15rem;">
                                        {{ ucfirst($workout->type ?? 'running') }}
                                    </div>
                                </div>
                                <div style="font-family:'Space Grotesk',monospace;font-size:.8rem;">
                                    <strong>{{ $workout->distance }}</strong> km
                                </div>
                                <div style="font-family:'Space Grotesk',monospace;font-size:.8rem;color:var(--accent-secondary);">
                                    {{ $formattedPace }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div style="font-size:.85rem;color:var(--text-muted);text-align:center;padding:2rem 1rem;">
                        No hay actividad reciente de tus alumnos.
                    </div>
                @endif
            </x-card>

            <!-- Panel derecho -->
            <div style="display:flex;flex-direction:column;gap:1.5rem;">
                <!-- Top Alumnos -->
                <x-card title="Top Alumnos" subtitle="Más kilómetros esta semana">
                    @if($topStudents->count() > 0)
                        <div style="display:grid;gap:.75rem;">
                            @foreach($topStudents as $index => $item)
                                <div style="padding:.75rem;border-radius:.6rem;background:rgba(5,8,20,.9);border:1px solid {{ $index === 0 ? 'rgba(45,227,142,.3)' : 'rgba(31,41,55,.7)' }};">
                                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.4rem;">
                                        <div style="display:flex;align-items:center;gap:.5rem;">
                                            <span style="
                                                width:24px;
                                                height:24px;
                                                border-radius:999px;
                                                background:{{ $index === 0 ? 'var(--accent-secondary)' : 'rgba(31,41,55,.7)' }};
                                                color:{{ $index === 0 ? '#050814' : 'var(--text-muted)' }};
                                                display:inline-flex;
                                                align-items:center;
                                                justify-content:center;
                                                font-size:.75rem;
                                                font-weight:600;
                                            ">#{{ $index + 1 }}</span>
                                            <span style="font-size:.85rem;font-weight:500;">{{ $item['student']->name }}</span>
                                        </div>
                                    </div>
                                    <div style="font-size:.8rem;color:var(--text-muted);">
                                        <strong style="color:var(--accent-secondary);font-family:'Space Grotesk',monospace;">{{ number_format($item['distance'], 1) }} km</strong>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div style="text-align:center;padding:1.5rem;color:var(--text-muted);font-size:.85rem;">
                            No hay datos de alumnos esta semana.
                        </div>
                    @endif
                </x-card>

                <!-- Alumnos Inactivos -->
                @if($inactiveStudents->count() > 0)
                <x-card title="Alumnos Inactivos" subtitle="Sin entrenamientos 2+ semanas">
                    <div style="display:grid;gap:.5rem;">
                        @foreach($inactiveStudents as $student)
                            @php
                                $lastWorkout = $student->workouts()->latest('date')->first();
                                $daysInactive = $lastWorkout ? now()->diffInDays($lastWorkout->date) : null;
                            @endphp
                            <div style="padding:.6rem;border-radius:.5rem;background:rgba(5,8,20,.9);border:1px solid rgba(234,179,8,.3);">
                                <div style="font-size:.85rem;font-weight:500;">{{ $student->name }}</div>
                                <div style="font-size:.7rem;color:rgba(234,179,8,.9);margin-top:.2rem;">
                                    @if($daysInactive)
                                        {{ $daysInactive }} días sin entrenar
                                    @else
                                        Sin entrenamientos
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </x-card>
                @endif

                <!-- Training Groups Placeholder -->
                <x-card title="Grupos de Entrenamiento" subtitle="Próximamente en SPRINT 3">
                    <div style="text-align:center;padding:1.5rem;color:var(--text-muted);font-size:.85rem;">
                        La gestión de grupos estará disponible pronto.
                        <br>
                        <a href="#" style="color:var(--accent-secondary);margin-top:.5rem;display:inline-block;">Más información</a>
                    </div>
                </x-card>
            </div>
        </section>
    @endif

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
