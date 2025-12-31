<x-app-layout>
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="font-display text-responsive-2xl mb-1">
                Mis Entrenamientos
            </h1>
            <p class="text-responsive-sm text-text-muted">
                Historial completo de tus sesiones de running.
            </p>
        </div>
        <a href="{{ route('workouts.create') }}" class="btn-primary w-full sm:w-auto justify-center">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                <path d="M12 5v14M5 12h14"/>
            </svg>
            Nuevo Entreno
        </a>
    </div>

    @if (session('success'))
        <div class="px-4 py-3 bg-accent-secondary/10 border border-accent-secondary/30 rounded-btn text-sm text-accent-secondary mb-4">
            {{ session('success') }}
        </div>
    @endif

    <!-- Filtros: Desktop horizontal, Mobile accordion -->
    <div class="mb-4">
        <!-- Desktop: Grid horizontal (visible md+) -->
        <div class="hidden md:block card p-4">
            <form method="GET" action="{{ route('workouts.index') }}">
                <div class="grid grid-cols-5 gap-3 mb-3">
                    <!-- Búsqueda -->
                    <div>
                        <label class="form-label">Buscar en notas</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar..." class="form-input">
                    </div>

                    <!-- Tipo -->
                    <div>
                        <label class="form-label">Tipo</label>
                        <select name="type" class="form-select">
                            <option value="">Todos los tipos</option>
                            @foreach($types as $key => $label)
                                <option value="{{ $key }}" {{ request('type') === $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Estado -->
                    <div>
                        <label class="form-label">Estado</label>
                        <select name="status" class="form-select">
                            <option value="">Todos</option>
                            <option value="planned" {{ request('status') === 'planned' ? 'selected' : '' }}>Planificado</option>
                            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completado</option>
                            <option value="skipped" {{ request('status') === 'skipped' ? 'selected' : '' }}>Saltado</option>
                        </select>
                    </div>

                    <!-- Fecha desde -->
                    <div>
                        <label class="form-label">Desde</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-input">
                    </div>

                    <!-- Fecha hasta -->
                    <div>
                        <label class="form-label">Hasta</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-input">
                    </div>
                </div>

                <!-- Botones -->
                <div class="flex gap-2">
                    <button type="submit" class="btn-secondary">
                        Filtrar
                    </button>
                    @if(request()->anyFilled(['search', 'type', 'status', 'date_from', 'date_to']))
                        <a href="{{ route('workouts.index') }}" class="btn-ghost">
                            Limpiar
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Mobile: Accordion (visible <md) -->
        <div class="md:hidden">
            <x-filter-accordion title="Filtros y búsqueda" :defaultOpen="request()->anyFilled(['search', 'type', 'status', 'date_from', 'date_to'])">
                <form method="GET" action="{{ route('workouts.index') }}" class="space-y-3">
                    <!-- Búsqueda -->
                    <div>
                        <label class="form-label">Buscar en notas</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar..." class="form-input">
                    </div>

                    <!-- Tipo -->
                    <div>
                        <label class="form-label">Tipo</label>
                        <select name="type" class="form-select">
                            <option value="">Todos los tipos</option>
                            @foreach($types as $key => $label)
                                <option value="{{ $key }}" {{ request('type') === $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Estado -->
                    <div>
                        <label class="form-label">Estado</label>
                        <select name="status" class="form-select">
                            <option value="">Todos</option>
                            <option value="planned" {{ request('status') === 'planned' ? 'selected' : '' }}>Planificado</option>
                            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completado</option>
                            <option value="skipped" {{ request('status') === 'skipped' ? 'selected' : '' }}>Saltado</option>
                        </select>
                    </div>

                    <!-- Fechas en grid -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="form-label">Desde</label>
                            <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-input">
                        </div>
                        <div>
                            <label class="form-label">Hasta</label>
                            <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-input">
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="flex flex-col gap-2 pt-2">
                        <button type="submit" class="btn-secondary w-full justify-center">
                            Filtrar
                        </button>
                        @if(request()->anyFilled(['search', 'type', 'status', 'date_from', 'date_to']))
                            <a href="{{ route('workouts.index') }}" class="btn-ghost w-full justify-center">
                                Limpiar filtros
                            </a>
                        @endif
                    </div>
                </form>
            </x-filter-accordion>
        </div>
    </div>

    @if($workouts->count() > 0)
        <!-- Desktop: Table view -->
        <div class="hidden md:block card overflow-hidden p-0">
            <!-- Header -->
            <div class="grid grid-cols-[110px_1fr_100px_100px_100px_80px_260px] gap-4 px-4 py-3 border-b border-border-subtle
                        text-xs uppercase tracking-wider text-text-muted">
                <div>Fecha</div>
                <div>Tipo</div>
                <div>Distancia</div>
                <div>Duración</div>
                <div>Pace</div>
                <div>Dif.</div>
                <div class="text-right">Acciones</div>
            </div>

            <!-- Rows -->
            @foreach($workouts as $workout)
                <div class="grid grid-cols-[110px_1fr_100px_100px_100px_80px_260px] gap-4 px-4 py-4
                            border-b border-border-subtle/50 text-sm items-center
                            hover:bg-border-subtle/30 transition-colors">
                    <div class="font-mono text-text-muted">
                        {{ $workout->date->format('d/m/Y') }}
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center gap-2 mb-1 flex-wrap">
                            <span class="font-medium">{{ $workout->type_label }}</span>
                            @if($workout->isPlanned())
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] bg-blue-500/10 border border-blue-500/30 text-blue-400">
                                    Planificado
                                </span>
                            @elseif($workout->isSkipped())
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] bg-yellow-500/10 border border-yellow-500/30 text-yellow-400">
                                    Saltado
                                </span>
                            @endif
                        </div>
                        @if($workout->notes)
                            <div class="text-xs text-text-muted truncate">
                                {{ Str::limit($workout->notes, 50) }}
                            </div>
                        @elseif($workout->skip_reason)
                            <div class="text-xs text-text-muted truncate">
                                Razón: {{ Str::limit($workout->skip_reason, 50) }}
                            </div>
                        @endif
                    </div>
                    <div>
                        <span class="font-mono font-semibold">{{ $workout->distance }}</span>
                        <span class="text-xs text-text-muted"> km</span>
                    </div>
                    <div class="font-mono">
                        {{ $workout->formatted_duration }}
                    </div>
                    <div class="font-mono text-accent-secondary">
                        {{ $workout->formatted_pace }}
                    </div>
                    <div>
                        @if($workout->isCompleted())
                            <div class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-accent-secondary/10 border border-accent-secondary/30">
                                {{ $workout->difficulty }}/5
                            </div>
                        @else
                            <span class="text-text-muted text-xs">–</span>
                        @endif
                    </div>
                    <div class="flex gap-1.5 justify-end">
                        @if($workout->isPlanned())
                            <a href="{{ route('workouts.mark-completed', $workout) }}"
                               class="px-2.5 py-1.5 rounded text-xs bg-accent-secondary/10 border border-accent-secondary/30 text-accent-secondary
                                      hover:bg-accent-secondary/20 transition-colors inline-flex items-center gap-1">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-3 h-3">
                                    <path d="M20 6L9 17l-5-5"/>
                                </svg>
                                Completar
                            </a>
                            <form method="POST" action="{{ route('workouts.mark-skipped', $workout) }}" class="inline"
                                  onsubmit="return confirm('¿Marcar este entrenamiento como saltado?');">
                                @csrf
                                <button type="submit"
                                        class="px-2.5 py-1.5 rounded text-xs bg-yellow-500/10 border border-yellow-500/30 text-yellow-400
                                               hover:bg-yellow-500/20 transition-colors inline-flex items-center gap-1">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-3 h-3">
                                        <path d="M18 6L6 18M6 6l12 12"/>
                                    </svg>
                                    Saltar
                                </button>
                            </form>
                        @else
                            <a href="{{ route('workouts.edit', $workout) }}"
                               class="px-2.5 py-1.5 rounded text-xs bg-bg-sidebar border border-border-subtle text-text-muted
                                      hover:bg-border-subtle hover:text-text-main transition-colors inline-flex items-center gap-1">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-3 h-3">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                </svg>
                                Editar
                            </a>
                            <form method="POST" action="{{ route('workouts.destroy', $workout) }}" class="inline"
                                  onsubmit="return confirm('¿Eliminar este entrenamiento?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="px-2.5 py-1.5 rounded text-xs bg-accent-primary/10 border border-accent-primary/30 text-red-400
                                               hover:bg-accent-primary/20 transition-colors">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-3 h-3">
                                        <path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                    </svg>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Mobile: Card view -->
        <div class="md:hidden space-y-3">
            @foreach($workouts as $workout)
                <x-workout-card
                    :workout="$workout"
                    :date="$workout->date->format('d/m/Y')"
                    :type="$workout->type_label . ($workout->isPlanned() ? ' (Planificado)' : ($workout->isSkipped() ? ' (Saltado)' : ''))"
                    :distance="$workout->distance"
                    :duration="$workout->formatted_duration"
                    :pace="$workout->formatted_pace"
                    :notes="$workout->notes ?? $workout->skip_reason"
                    :editUrl="!$workout->isPlanned() ? route('workouts.edit', $workout) : null"
                    :deleteUrl="!$workout->isPlanned() ? route('workouts.destroy', $workout) : null"
                >
                    <!-- Custom actions for planned workouts -->
                    @if($workout->isPlanned())
                        <div class="flex gap-2 mt-3 pt-3 border-t border-border-subtle/50">
                            <a href="{{ route('workouts.mark-completed', $workout) }}"
                               class="flex-1 px-3 py-2 rounded-lg text-sm font-medium text-center
                                      bg-accent-secondary/10 border border-accent-secondary/30 text-accent-secondary
                                      hover:bg-accent-secondary/20 transition-colors min-h-touch flex items-center justify-center gap-1.5">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                                    <path d="M20 6L9 17l-5-5"/>
                                </svg>
                                Completar
                            </a>
                            <form method="POST" action="{{ route('workouts.mark-skipped', $workout) }}" class="flex-1"
                                  onsubmit="return confirm('¿Marcar como saltado?');">
                                @csrf
                                <button type="submit"
                                        class="w-full px-3 py-2 rounded-lg text-sm font-medium
                                               bg-yellow-500/10 border border-yellow-500/30 text-yellow-400
                                               hover:bg-yellow-500/20 transition-colors min-h-touch flex items-center justify-center gap-1.5">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                                        <path d="M18 6L6 18M6 6l12 12"/>
                                    </svg>
                                    Saltar
                                </button>
                            </form>
                        </div>
                    @endif
                </x-workout-card>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $workouts->appends(request()->query())->links('vendor.pagination.custom') }}
        </div>
    @else
        <div class="card text-center py-12 px-4">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                 class="w-12 h-12 text-text-muted mx-auto mb-4">
                <path d="M4 19L9 10L13 15L20 5"/>
                <path d="M20 10V5H15"/>
            </svg>
            <h3 class="text-lg font-semibold mb-2">No hay entrenamientos registrados</h3>
            <p class="text-text-muted text-sm mb-6">
                Empezá a registrar tus sesiones de running para ver tu progreso.
            </p>
            <a href="{{ route('workouts.create') }}" class="btn-primary inline-flex">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                    <path d="M12 5v14M5 12h14"/>
                </svg>
                Crear Primer Entrenamiento
            </a>
        </div>
    @endif
</x-app-layout>
