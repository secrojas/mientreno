<x-app-layout>
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="font-display text-responsive-2xl mb-1">
                Grupos de Entrenamiento
            </h1>
            <p class="text-responsive-sm text-text-muted">
                Gestiona tus grupos y asigna alumnos
            </p>
        </div>
        <a href="{{ businessRoute('coach.groups.create') }}" class="btn-primary min-h-touch w-full sm:w-auto justify-center">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                <path d="M12 5v14M5 12h14"/>
            </svg>
            Crear Grupo
        </a>
    </div>

    @if(session('success'))
        <div class="px-4 py-3 bg-accent-secondary/10 border border-accent-secondary/30 rounded-card text-sm text-accent-secondary mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="px-4 py-3 bg-accent-primary/10 border border-accent-primary/30 rounded-card text-sm text-red-400 mb-4">
            {{ session('error') }}
        </div>
    @endif

    @if($groups->isEmpty())
        <div class="bg-bg-card rounded-card border border-border-subtle py-12 px-8 sm:px-12 text-center">
            <div class="w-20 h-20 bg-bg-main rounded-full mx-auto mb-6 flex items-center justify-center">
                <svg class="w-10 h-10 text-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
            <h3 class="text-xl font-semibold mb-2">No tienes grupos creados</h3>
            <p class="text-text-muted mb-6 text-responsive-sm">Crea tu primer grupo de entrenamiento para organizar a tus alumnos</p>
            <a href="{{ businessRoute('coach.groups.create') }}" class="btn-primary inline-flex min-h-touch">
                Crear Primer Grupo
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($groups as $group)
                <div class="bg-bg-card rounded-card border {{ $group->is_active ? 'border-border-subtle' : 'border-border-subtle/50' }} p-5 transition-all {{ $group->is_active ? '' : 'opacity-60' }}">
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold mb-2">{{ $group->name }}</h3>
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="inline-block px-2.5 py-1 rounded-full text-xs font-semibold
                                    {{ $group->level === 'beginner' ? 'bg-accent-secondary/10 text-accent-secondary' : '' }}
                                    {{ $group->level === 'intermediate' ? 'bg-blue-400/10 text-blue-400' : '' }}
                                    {{ $group->level === 'advanced' ? 'bg-accent-primary/10 text-accent-primary' : '' }}
                                ">
                                    {{ $group->level_label }}
                                </span>
                                @if(!$group->is_active)
                                    <span class="px-2.5 py-1 text-xs bg-border-subtle text-text-muted rounded-full">Inactivo</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if($group->description)
                        <p class="text-text-muted text-sm mb-4 line-clamp-2">{{ $group->description }}</p>
                    @endif

                    <div class="flex items-center gap-4 mb-4 text-sm text-text-muted">
                        <div class="flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                            <span>{{ $group->members_count }} miembro{{ $group->members_count !== 1 ? 's' : '' }}</span>
                        </div>
                        @if($group->max_members)
                            <div class="flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                                <span>Máx: {{ $group->max_members }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="flex gap-2">
                        <a href="{{ businessRoute('coach.groups.show', ['group' => $group]) }}" class="flex-1 px-4 py-2 bg-bg-main text-text-main text-center rounded-card font-medium text-sm border border-border-subtle hover:border-border-main transition-colors">
                            Ver Detalle
                        </a>
                        <a href="{{ businessRoute('coach.groups.edit', ['group' => $group]) }}" class="px-3.5 py-2 bg-blue-400/10 text-blue-400 rounded-card font-medium text-sm border border-blue-400/20 hover:bg-blue-400/20 transition-colors">
                            Editar
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-app-layout>
