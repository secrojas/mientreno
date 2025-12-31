<x-app-layout>
    <!-- Header -->
    <header class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div class="flex flex-col gap-1">
            <h1 class="font-display text-responsive-2xl">Dashboard Coach</h1>
            <p class="text-responsive-sm text-text-muted">Gestión y seguimiento de tus alumnos.</p>
        </div>

        @if(!isset($hasNoBusiness))
        <div class="w-full sm:w-auto">
            <a href="#" class="btn-primary w-full sm:w-auto justify-center">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                    <path d="M12 5v14M5 12h14"/>
                </svg>
                Invitar alumno
            </a>
        </div>
        @endif
    </header>

    @if(isset($hasNoBusiness) && $hasNoBusiness)
        <!-- No Business State -->
        <x-card title="Bienvenido a MiEntreno Coach" subtitle="Configurá tu negocio para empezar">
            <div class="text-center py-8 px-4">
                <div class="mb-6">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="w-16 h-16 mx-auto text-accent-secondary">
                        <rect x="3" y="10" width="9" height="5" rx="1.5"></rect>
                        <path d="M12 12h3.5a3.5 3.5 0 1 1-2.47 6"></path>
                        <circle cx="17.5" cy="10" r="1.2"></circle>
                    </svg>
                </div>
                <p class="text-responsive-sm text-text-muted mb-6 max-w-lg mx-auto">
                    Para empezar a gestionar tus alumnos y grupos de entrenamiento, primero necesitás crear tu negocio.
                </p>
                <a href="{{ route('coach.business.create') }}" class="btn-primary inline-flex">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                        <path d="M12 5v14M5 12h14"/>
                    </svg>
                    Crear mi negocio
                </a>
            </div>
        </x-card>
    @else
        <!-- Metric Cards -->
        <section class="grid-responsive-4 gap-4 mb-6">
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

        <!-- Main Content -->
        <section class="grid grid-cols-1 lg:grid-cols-[2fr_1.2fr] gap-6">
            <!-- Recent Activity -->
            <x-card title="Actividad Reciente" subtitle="Últimos entrenamientos de tus alumnos">
                <x-slot:headerAction>
                    <a href="#" class="text-sm text-accent-secondary hover:text-accent-secondary/80 transition-colors">Ver todos</a>
                </x-slot:headerAction>

                @if($recentActivity->count() > 0)
                    <div class="grid gap-2">
                        @foreach($recentActivity as $activity)
                            @php
                                $workout = (object) (array) $activity;
                                $pace = $workout->pace ?? 0;
                                $avgMinutes = $pace ? floor($pace / 60) : 0;
                                $avgSeconds = $pace ? $pace % 60 : 0;
                                $formattedPace = $pace ? $avgMinutes . ':' . str_pad($avgSeconds, 2, '0', STR_PAD_LEFT) : '-';
                            @endphp
                            <div class="grid grid-cols-[100px_1fr_80px_80px] gap-3 p-3 rounded-btn
                                        bg-bg-sidebar border border-border-subtle items-center">
                                <div class="font-mono text-xs text-text-muted">
                                    {{ \Carbon\Carbon::parse($workout->date)->format('d/m H:i') }}
                                </div>
                                <div class="min-w-0">
                                    <div class="text-sm font-medium text-accent-secondary truncate">{{ $workout->student_name }}</div>
                                    <div class="text-xs text-text-muted mt-0.5 truncate">
                                        {{ ucfirst($workout->type ?? 'running') }}
                                    </div>
                                </div>
                                <div class="font-mono text-sm">
                                    <strong>{{ $workout->distance }}</strong> km
                                </div>
                                <div class="font-mono text-sm text-accent-secondary">
                                    {{ $formattedPace }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 px-4 text-text-muted text-sm">
                        No hay actividad reciente de tus alumnos.
                    </div>
                @endif
            </x-card>

            <!-- Right Panel -->
            <div class="flex flex-col gap-6">
                <!-- Top Students -->
                <x-card title="Top Alumnos" subtitle="Más kilómetros esta semana">
                    @if($topStudents->count() > 0)
                        <div class="grid gap-3">
                            @foreach($topStudents as $index => $item)
                                <div class="p-3 rounded-btn bg-bg-sidebar border
                                            {{ $index === 0 ? 'border-accent-secondary/30' : 'border-border-subtle' }}">
                                    <div class="flex justify-between items-center mb-2">
                                        <div class="flex items-center gap-2">
                                            <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-semibold
                                                        {{ $index === 0 ? 'bg-accent-secondary text-bg-card' : 'bg-border-subtle text-text-muted' }}">
                                                #{{ $index + 1 }}
                                            </span>
                                            <span class="text-sm font-medium">{{ $item['student']->name }}</span>
                                        </div>
                                    </div>
                                    <div class="text-sm text-text-muted">
                                        <strong class="text-accent-secondary font-mono">{{ number_format($item['distance'], 1) }} km</strong>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-6 text-text-muted text-sm">
                            No hay datos de alumnos esta semana.
                        </div>
                    @endif
                </x-card>

                <!-- Inactive Students -->
                @if($inactiveStudents->count() > 0)
                <x-card title="Alumnos Inactivos" subtitle="Sin entrenamientos 2+ semanas">
                    <div class="grid gap-2">
                        @foreach($inactiveStudents as $student)
                            @php
                                $lastWorkout = $student->workouts()->latest('date')->first();
                                $daysInactive = $lastWorkout ? now()->diffInDays($lastWorkout->date) : null;
                            @endphp
                            <div class="p-2.5 rounded bg-bg-sidebar border border-yellow-500/30">
                                <div class="text-sm font-medium">{{ $student->name }}</div>
                                <div class="text-xs text-yellow-400 mt-1">
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

                <!-- Training Groups -->
                <x-card title="Grupos de Entrenamiento ({{ $trainingGroups->count() }})">
                    <x-slot name="headerAction">
                        <a href="{{ businessRoute('coach.groups.index') }}"
                           class="text-sm text-accent-primary hover:text-accent-primary/80 font-medium transition-colors">
                            Ver todos →
                        </a>
                    </x-slot>

                    @if($trainingGroups->isEmpty())
                        <div class="text-center py-6 text-text-muted text-sm">
                            No has creado grupos de entrenamiento aún.
                            <br>
                            <a href="{{ businessRoute('coach.groups.create') }}"
                               class="text-accent-primary hover:text-accent-primary/80 font-medium mt-2 inline-block">
                                Crear primer grupo
                            </a>
                        </div>
                    @else
                        <div class="flex flex-col gap-3">
                            @foreach($trainingGroups as $group)
                                <a href="{{ businessRoute('coach.groups.show', ['group' => $group]) }}"
                                   class="p-3 rounded bg-bg-sidebar border border-accent-primary/15
                                          flex justify-between items-center
                                          hover:border-accent-primary/30 transition-all duration-200">
                                    <div>
                                        <div class="text-sm font-semibold text-text-main mb-1">{{ $group->name }}</div>
                                        <div class="flex items-center gap-2 text-xs text-text-muted">
                                            <span class="inline-block px-2 py-0.5 rounded-full font-semibold
                                                {{ $group->level === 'beginner' ? 'bg-accent-secondary/10 text-accent-secondary' : '' }}
                                                {{ $group->level === 'intermediate' ? 'bg-blue-500/10 text-blue-400' : '' }}
                                                {{ $group->level === 'advanced' ? 'bg-accent-primary/10 text-accent-primary' : '' }}">
                                                {{ $group->level_label }}
                                            </span>
                                            <span>{{ $group->members_count }} miembro{{ $group->members_count !== 1 ? 's' : '' }}</span>
                                        </div>
                                    </div>
                                    <svg class="w-5 h-5 text-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </x-card>
            </div>
        </section>
    @endif
</x-app-layout>
