<x-app-layout>
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="font-display text-responsive-2xl mb-1">
                Mis Objetivos
            </h1>
            <p class="text-responsive-sm text-text-muted">
                Gestiona tus metas de entrenamiento.
            </p>
        </div>
        <a href="{{ route('goals.create') }}" class="btn-primary w-full sm:w-auto justify-center min-h-touch">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                <path d="M12 5v14M5 12h14"/>
            </svg>
            Nuevo Objetivo
        </a>
    </div>

    @if (session('success'))
        <div class="px-4 py-3 bg-accent-secondary/10 border border-accent-secondary/30 rounded-btn text-sm text-accent-secondary mb-4">
            {{ session('success') }}
        </div>
    @endif

    <x-card>
        @if($goals->count() > 0)
            <div class="grid gap-3">
                @foreach($goals as $goal)
                    <div class="p-4 rounded-card bg-bg-sidebar border {{ $goal->status === 'active' ? 'border-accent-secondary/30' : 'border-border-subtle' }}">
                        <div class="flex flex-col lg:flex-row lg:items-center gap-4">
                            <!-- Información del objetivo -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-2 flex-wrap">
                                    <span class="px-2 py-1 rounded-btn bg-accent-secondary/10 border border-accent-secondary/30 text-xs uppercase tracking-wide">
                                        {{ $goal->type_label }}
                                    </span>
                                    <span class="px-2 py-1 rounded-btn bg-bg-main border border-border-subtle text-xs">
                                        {{ $goal->status_label }}
                                    </span>
                                </div>
                                <div class="text-base font-semibold mb-1">{{ $goal->title }}</div>
                                <div class="text-sm text-text-muted">
                                    {{ $goal->getTargetDescription() }}
                                    @if($goal->target_date)
                                        · Fecha límite: {{ $goal->target_date->format('d/m/Y') }}
                                        @if($goal->days_until !== null && $goal->days_until >= 0)
                                            <span class="text-accent-secondary">({{ $goal->days_until }} días)</span>
                                        @elseif($goal->isOverdue())
                                            <span class="text-red-400">(vencido)</span>
                                        @endif
                                    @endif
                                </div>
                                @if($goal->progress_percentage > 0)
                                    <div class="mt-3">
                                        <div class="w-full h-1 bg-bg-main rounded-full overflow-hidden">
                                            <div class="h-full bg-accent-secondary transition-all duration-300" style="width: {{ $goal->progress_percentage }}%"></div>
                                        </div>
                                        <div class="text-xs text-text-muted mt-1">
                                            {{ $goal->progress_percentage }}% completado
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Acciones -->
                            <div class="flex gap-2 lg:flex-col xl:flex-row">
                                <a href="{{ route('goals.edit', $goal) }}"
                                   class="btn-ghost flex-1 lg:flex-initial justify-center min-h-touch text-sm">
                                    Editar
                                </a>
                                <form method="POST" action="{{ route('goals.destroy', $goal) }}"
                                      class="flex-1 lg:flex-initial"
                                      onsubmit="return confirm('¿Eliminar este objetivo?');">
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
                    </div>
                @endforeach
            </div>

            <div class="mt-4">
                {{ $goals->links() }}
            </div>
        @else
            <div class="text-center py-12 px-4">
                <div class="text-lg mb-2">No hay objetivos registrados</div>
                <p class="text-text-muted mb-6">Empezá a definir tus metas de entrenamiento.</p>
                <a href="{{ route('goals.create') }}" class="btn-primary inline-flex">
                    Crear Primer Objetivo
                </a>
            </div>
        @endif
    </x-card>
</x-app-layout>
