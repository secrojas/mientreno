<x-app-layout>
    <header class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div class="flex flex-col gap-1">
            <h1 class="font-display text-responsive-2xl">{{ $business->name }}</h1>
            <p class="text-responsive-sm text-text-muted">Gestión de tu negocio de coaching.</p>
        </div>
        <a href="{{ businessRoute('coach.business.edit') }}" class="btn-ghost min-h-touch w-full sm:w-auto justify-center">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
            </svg>
            Editar
        </a>
    </header>

    <!-- Información del negocio -->
    <x-card title="Información del Negocio" subtitle="Detalles de configuración">
        <div class="grid gap-5">
            <!-- Nombre y slug -->
            <div class="grid grid-cols-1 sm:grid-cols-[140px_1fr] gap-2 sm:gap-4 items-start">
                <span class="text-sm text-text-muted">Nombre:</span>
                <span class="text-responsive-sm font-medium">{{ $business->name }}</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-[140px_1fr] gap-2 sm:gap-4 items-start">
                <span class="text-sm text-text-muted">URL (slug):</span>
                <span class="text-responsive-sm font-mono text-accent-secondary">{{ $business->slug }}</span>
            </div>

            @if($business->description)
            <div class="grid grid-cols-1 sm:grid-cols-[140px_1fr] gap-2 sm:gap-4 items-start">
                <span class="text-sm text-text-muted">Descripción:</span>
                <span class="text-responsive-sm">{{ $business->description }}</span>
            </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-[140px_1fr] gap-2 sm:gap-4 items-start">
                <span class="text-sm text-text-muted">Nivel objetivo:</span>
                <span class="px-2.5 py-1 rounded-md bg-accent-secondary/10 border border-accent-secondary/30 text-xs inline-block w-fit">
                    {{ $business->level_label }}
                </span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-[140px_1fr] gap-2 sm:gap-4 items-start">
                <span class="text-sm text-text-muted">Estado:</span>
                <span class="px-2.5 py-1 rounded-md {{ $business->is_active ? 'bg-accent-secondary/10 border-accent-secondary/30' : 'bg-yellow-500/10 border-yellow-500/30' }} border text-xs inline-block w-fit">
                    {{ $business->is_active ? 'Activo' : 'Inactivo' }}
                </span>
            </div>
        </div>
    </x-card>

    <!-- Estadísticas -->
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-6">
        <x-metric-card
            label="Total Alumnos"
            :value="$business->runners->count()"
            subtitle="Runners activos"
        />

        <x-metric-card
            label="Grupos"
            value="0"
            subtitle="Próximamente (SPRINT 3)"
            accent="secondary"
        />

        <x-metric-card
            label="Creado"
            :value="$business->created_at->format('d/m/Y')"
            :subtitle="$business->created_at->diffForHumans()"
        />
    </section>

    <!-- Alumnos del business -->
    <x-card title="Alumnos" subtitle="Runners inscritos en tu negocio" class="mt-6">
        <x-slot:headerAction>
            <a href="#" class="text-sm text-accent-secondary hover:underline">Invitar alumno</a>
        </x-slot:headerAction>

        @if($business->runners->count() > 0)
            <div class="grid gap-2">
                @foreach($business->runners as $runner)
                    <div class="grid grid-cols-1 md:grid-cols-[1fr_120px_100px] gap-3 md:gap-4 p-3 rounded-card bg-bg-main border border-border-subtle items-start md:items-center">
                        <div>
                            <div class="text-responsive-sm font-medium">{{ $runner->name }}</div>
                            <div class="text-xs text-text-muted mt-0.5">{{ $runner->email }}</div>
                        </div>
                        <div class="text-sm text-text-muted">
                            {{ $runner->workouts()->count() }} entrenos
                        </div>
                        <div class="md:text-right">
                            <a href="#" class="text-xs text-accent-secondary hover:underline">Ver perfil</a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-sm text-text-muted text-center py-8 px-4">
                No hay alumnos inscritos todavía.
                <br>
                <a href="#" class="inline-flex items-center gap-2 px-3 py-2 rounded-full text-xs border border-accent-secondary text-accent-secondary bg-accent-secondary/5 mt-3 hover:bg-accent-secondary/10 transition-colors">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                        <path d="M12 5v14M5 12h14"/>
                    </svg>
                    Invitar primer alumno
                </a>
            </div>
        @endif
    </x-card>

    <!-- Horarios (placeholder) -->
    <x-card title="Horarios de Entrenamientos" subtitle="Próximamente" class="mt-6">
        <div class="text-center py-6 text-text-muted text-sm">
            La configuración de horarios estará disponible en la próxima versión.
        </div>
    </x-card>
</x-app-layout>
