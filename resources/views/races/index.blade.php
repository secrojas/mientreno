<x-app-layout>
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="font-display text-responsive-2xl mb-1">
                Mis Carreras
            </h1>
            <p class="text-responsive-sm text-text-muted">
                Gestiona tus carreras próximas y pasadas.
            </p>
        </div>
        <a href="{{ route('races.create') }}" class="btn-primary w-full sm:w-auto justify-center min-h-touch">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                <path d="M12 5v14M5 12h14"/>
            </svg>
            Nueva Carrera
        </a>
    </div>

    @if (session('success'))
        <div class="px-4 py-3 bg-accent-secondary/10 border border-accent-secondary/30 rounded-btn text-sm text-accent-secondary mb-4">
            {{ session('success') }}
        </div>
    @endif

    <!-- Próximas carreras -->
    @if($upcomingRaces->count() > 0)
        <x-card title="Próximas Carreras" :subtitle="$upcomingRaces->count() . ' carrera(s)'" class="mb-6">
            <div class="grid gap-3">
                @foreach($upcomingRaces as $race)
                    <div class="p-4 rounded-card bg-bg-sidebar border border-border-subtle">
                        <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                            <div class="flex-1 min-w-0">
                                <div class="text-lg font-semibold mb-1">{{ $race->name }}</div>
                                <div class="text-sm text-text-muted">
                                    <span class="font-mono">{{ $race->distance_label }}</span>
                                    @if($race->location)
                                        · {{ $race->location }}
                                    @endif
                                    · {{ $race->date->format('d/m/Y') }}
                                    @if($race->days_until >= 0)
                                        · <span class="text-accent-secondary">en {{ $race->days_until }} días</span>
                                    @endif
                                </div>
                                @if($race->target_time)
                                    <div class="text-xs text-text-muted mt-1">
                                        Objetivo: <span class="font-mono">{{ $race->formatted_target_time }}</span>
                                    </div>
                                @endif
                            </div>
                            <a href="{{ route('races.edit', $race) }}"
                               class="btn-ghost justify-center min-h-touch text-sm">
                                Editar
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-card>
    @endif

    <!-- Carreras pasadas -->
    <x-card title="Carreras Pasadas" :subtitle="$pastRaces->total() . ' carrera(s)'">
        @if($pastRaces->count() > 0)
            <!-- Desktop: tabla horizontal -->
            <div class="hidden md:grid gap-2">
                @foreach($pastRaces as $race)
                    <div class="grid grid-cols-[100px_1fr_150px_120px] gap-4 items-center p-3 border-b border-bg-main last:border-0">
                        <div class="font-mono text-text-muted text-sm">
                            {{ $race->date->format('d/m/Y') }}
                        </div>
                        <div>
                            <div class="font-medium">{{ $race->name }}</div>
                            <div class="text-xs text-text-muted">
                                {{ $race->distance_label }}
                                @if($race->location)
                                    · {{ $race->location }}
                                @endif
                            </div>
                        </div>
                        <div class="font-mono text-sm">
                            @if($race->actual_time)
                                <span class="text-accent-secondary">{{ $race->formatted_actual_time }}</span>
                            @elseif($race->target_time)
                                <span class="text-text-muted">{{ $race->formatted_target_time }}</span>
                            @else
                                –
                            @endif
                        </div>
                        <div class="flex gap-2 justify-end">
                            <a href="{{ route('races.edit', $race) }}"
                               class="btn-ghost text-sm px-2 py-1">
                                Editar
                            </a>
                            <form method="POST" action="{{ route('races.destroy', $race) }}"
                                  class="inline"
                                  onsubmit="return confirm('¿Eliminar esta carrera?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="px-2 py-1 rounded-btn bg-accent-primary/10 border border-accent-primary/30
                                               text-red-400 text-sm hover:bg-accent-primary/20 transition-colors">
                                    Eliminar
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Mobile: cards -->
            <div class="md:hidden grid gap-3">
                @foreach($pastRaces as $race)
                    <div class="p-4 rounded-card bg-bg-sidebar border border-border-subtle">
                        <div class="mb-3">
                            <div class="font-semibold mb-1">{{ $race->name }}</div>
                            <div class="text-sm text-text-muted">
                                {{ $race->distance_label }}
                                @if($race->location)
                                    · {{ $race->location }}
                                @endif
                            </div>
                            <div class="font-mono text-xs text-text-muted mt-1">
                                {{ $race->date->format('d/m/Y') }}
                            </div>
                            @if($race->actual_time || $race->target_time)
                                <div class="font-mono text-sm mt-2">
                                    @if($race->actual_time)
                                        <span class="text-accent-secondary">{{ $race->formatted_actual_time }}</span>
                                    @elseif($race->target_time)
                                        <span class="text-text-muted">{{ $race->formatted_target_time }}</span>
                                    @endif
                                </div>
                            @endif
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('races.edit', $race) }}"
                               class="btn-ghost flex-1 justify-center min-h-touch text-sm">
                                Editar
                            </a>
                            <form method="POST" action="{{ route('races.destroy', $race) }}"
                                  class="flex-1"
                                  onsubmit="return confirm('¿Eliminar esta carrera?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="w-full px-3 py-2 rounded-btn bg-accent-primary/10 border border-accent-primary/30
                                               text-red-400 text-sm hover:bg-accent-primary/20 transition-colors min-h-touch">
                                    Eliminar
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-4">
                {{ $pastRaces->links() }}
            </div>
        @else
            <div class="text-center py-8 px-4 text-text-muted">
                No hay carreras pasadas registradas.
            </div>
        @endif
    </x-card>
</x-app-layout>
