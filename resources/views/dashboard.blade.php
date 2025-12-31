<x-app-layout>
    <!-- Header -->
    <header class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div class="flex flex-col gap-1">
            <h1 class="font-display text-responsive-2xl">Dashboard</h1>
            <p class="text-responsive-sm text-text-muted">Resumen de tus entrenamientos y actividad reciente.</p>
        </div>
        <div class="w-full sm:w-auto">
            <a href="{{ route('workouts.create') }}" class="btn-primary w-full sm:w-auto justify-center">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                    <path d="M12 5v14M5 12h14"/>
                </svg>
                Nuevo entreno
            </a>
        </div>
    </header>

    <!-- Metric Cards -->
    <section class="grid-responsive-4 gap-4 mb-6">
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
            :subtitle="'Semana ' . now()->isoWeek"
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

    <!-- Main Content -->
    <section class="grid grid-cols-1 lg:grid-cols-[2fr_1.2fr] gap-6">
        <!-- Recent Workouts -->
        <x-card title="Entrenamientos recientes" subtitle="Tus últimas 5 sesiones.">
            <x-slot:headerAction>
                <a href="{{ route('workouts.index') }}" class="text-sm text-accent-secondary hover:text-accent-secondary/80 transition-colors">Ver todos</a>
            </x-slot:headerAction>

            @if($recentWorkouts->count() > 0)
                <!-- Desktop: Table view -->
                <div class="hidden md:grid gap-2">
                    @foreach($recentWorkouts as $workout)
                        <a href="{{ route('workouts.edit', $workout) }}"
                           class="grid grid-cols-[80px_1fr_80px_80px] gap-3 p-3 rounded-btn
                                  bg-bg-sidebar border border-border-subtle
                                  hover:bg-bg-sidebar/70 hover:border-accent-primary/30
                                  transition-all duration-150 items-center">
                            <div class="font-mono text-sm text-text-muted">
                                {{ $workout->date->format('d/m') }}
                            </div>
                            <div class="min-w-0">
                                <div class="text-sm font-medium truncate">{{ $workout->type_label }}</div>
                                @if($workout->notes)
                                    <div class="text-xs text-text-muted mt-0.5 truncate">
                                        {{ Str::limit($workout->notes, 40) }}
                                    </div>
                                @endif
                            </div>
                            <div class="font-mono text-sm">
                                <strong>{{ $workout->distance }}</strong> km
                            </div>
                            <div class="font-mono text-sm text-accent-secondary">
                                {{ $workout->formatted_pace }}
                            </div>
                        </a>
                    @endforeach
                </div>

                <!-- Mobile: Card view -->
                <div class="md:hidden space-y-3">
                    @foreach($recentWorkouts as $workout)
                        <x-workout-card
                            :workout="$workout"
                            :editUrl="route('workouts.edit', $workout)"
                            :deleteUrl="route('workouts.destroy', $workout)"
                        />
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 px-4 text-text-muted text-sm">
                    No hay entrenamientos cargados todavía.
                    <br><br>
                    <a href="{{ route('workouts.create') }}" class="btn-secondary inline-flex">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-3.5 h-3.5">
                            <path d="M12 5v14M5 12h14"/>
                        </svg>
                        Crear primer entreno
                    </a>
                </div>
            @endif
        </x-card>

        <!-- Right Panel -->
        <div class="flex flex-col gap-6">
            <!-- Active Goals -->
            <x-card title="Objetivos Activos" :subtitle="$activeGoals->count() . ' objetivo(s)'">
                <x-slot:headerAction>
                    <a href="{{ route('goals.index') }}" class="text-sm text-accent-secondary hover:text-accent-secondary/80 transition-colors">Ver todos</a>
                </x-slot:headerAction>

                @if($activeGoals->count() > 0)
                    <div class="grid gap-3">
                        @foreach($activeGoals as $goal)
                            <div class="p-3 rounded-btn bg-bg-sidebar border border-accent-secondary/30">
                                <div class="flex justify-between items-start mb-2">
                                    <span class="px-2 py-1 rounded text-[10px] uppercase tracking-wider
                                                 bg-accent-secondary/10 border border-accent-secondary/30 text-accent-secondary">
                                        {{ $goal->type_label }}
                                    </span>
                                    @if($goal->target_date && $goal->days_until !== null)
                                        <span class="text-xs text-text-muted">
                                            {{ $goal->days_until >= 0 ? $goal->days_until . ' días' : 'Vencido' }}
                                        </span>
                                    @endif
                                </div>
                                <div class="text-sm font-medium mb-1">{{ $goal->title }}</div>
                                <div class="text-xs text-text-muted">{{ $goal->getTargetDescription() }}</div>
                                @if($goal->progress_percentage > 0)
                                    <div class="mt-2">
                                        <div class="w-full h-1 bg-border-subtle rounded-full overflow-hidden">
                                            <div class="h-full bg-accent-secondary transition-all duration-300"
                                                 style="width: {{ $goal->progress_percentage }}%"></div>
                                        </div>
                                        <div class="text-xs text-text-muted mt-1">{{ $goal->progress_percentage }}%</div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-6 text-text-muted text-sm">
                        No hay objetivos activos.
                        <br>
                        <a href="{{ route('goals.create') }}" class="text-accent-secondary hover:text-accent-secondary/80 mt-2 inline-block">Crear objetivo</a>
                    </div>
                @endif
            </x-card>

            <!-- Weekly Completion -->
            <x-card title="Cumplimiento Semanal" :subtitle="'Semana ' . now()->isoWeek">
                <x-slot:headerAction>
                    <a href="{{ route('workouts.index', ['status' => 'planned']) }}" class="text-sm text-accent-secondary hover:text-accent-secondary/80 transition-colors">Ver planificados</a>
                </x-slot:headerAction>

                @if($weeklyCompletion['total'] > 0)
                    <div class="p-3 rounded-btn bg-bg-sidebar border border-border-subtle">
                        <!-- Big percentage -->
                        <div class="text-center mb-4">
                            <div class="text-5xl font-bold font-mono text-accent-secondary">
                                {{ $weeklyCompletion['percentage'] }}%
                            </div>
                            <div class="text-xs text-text-muted mt-1">
                                {{ $weeklyCompletion['completed'] }} de {{ $weeklyCompletion['total'] }} entrenamientos completados
                            </div>
                        </div>

                        <!-- Progress bar -->
                        <div class="w-full h-1.5 bg-border-subtle rounded-full overflow-hidden mb-4">
                            <div class="h-full bg-accent-secondary transition-all duration-300"
                                 style="width: {{ $weeklyCompletion['percentage'] }}%"></div>
                        </div>

                        <!-- Breakdown -->
                        <div class="grid grid-cols-3 gap-2 text-xs">
                            <div class="text-center p-2 rounded bg-accent-secondary/5 border border-accent-secondary/20">
                                <div class="font-semibold text-lg text-accent-secondary">{{ $weeklyCompletion['completed'] }}</div>
                                <div class="text-text-muted mt-0.5">Completados</div>
                            </div>
                            <div class="text-center p-2 rounded bg-blue-500/5 border border-blue-500/20">
                                <div class="font-semibold text-lg text-blue-400">{{ $weeklyCompletion['planned'] }}</div>
                                <div class="text-text-muted mt-0.5">Planificados</div>
                            </div>
                            <div class="text-center p-2 rounded bg-yellow-500/5 border border-yellow-500/20">
                                <div class="font-semibold text-lg text-yellow-400">{{ $weeklyCompletion['skipped'] }}</div>
                                <div class="text-text-muted mt-0.5">Saltados</div>
                            </div>
                        </div>

                        @if($weeklyCompletion['planned'] > 0)
                            <div class="mt-3 p-2 rounded bg-blue-500/5 border border-blue-500/20 text-center">
                                <div class="text-xs text-blue-400">
                                    ¡Tenés {{ $weeklyCompletion['planned'] }} {{ $weeklyCompletion['planned'] === 1 ? 'entrenamiento planificado' : 'entrenamientos planificados' }} pendientes!
                                </div>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="text-center py-6 text-text-muted text-sm">
                        No hay entrenamientos esta semana.
                        <br>
                        <a href="{{ route('workouts.create') }}" class="text-accent-secondary hover:text-accent-secondary/80 mt-2 inline-block">Planificar entrenamientos</a>
                    </div>
                @endif
            </x-card>

            <!-- Summary -->
            <x-card title="Resumen" subtitle="Estadísticas generales">
                <div class="grid gap-3">
                    <div class="p-3 rounded-btn bg-bg-sidebar border border-border-subtle">
                        <div class="text-xs text-text-muted mb-1">Total entrenamientos</div>
                        <div class="text-xl font-semibold">{{ auth()->user()->workouts()->count() }}</div>
                    </div>
                    <div class="p-3 rounded-btn bg-bg-sidebar border border-border-subtle">
                        <div class="text-xs text-text-muted mb-1">Total kilómetros</div>
                        <div class="text-xl font-semibold font-mono">
                            {{ number_format(auth()->user()->workouts()->sum('distance'), 1) }} km
                        </div>
                    </div>
                </div>
            </x-card>
        </div>
    </section>
</x-app-layout>
