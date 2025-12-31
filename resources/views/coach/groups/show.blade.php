<x-app-layout>
    <div class="mb-6">
        <div class="flex items-center gap-2 mb-2">
            <a href="{{ businessRoute('coach.groups.index') }}" class="text-text-muted inline-flex items-center gap-1.5 text-sm hover:text-text-main transition-colors">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
                Volver a Grupos
            </a>
        </div>
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-start gap-4">
            <div>
                <h1 class="font-display text-responsive-2xl mb-2">
                    {{ $group->name }}
                </h1>
                <div class="flex items-center gap-3 flex-wrap">
                    <span class="inline-block px-3 py-1.5 rounded-full text-sm font-semibold
                        {{ $group->level === 'beginner' ? 'bg-accent-secondary/10 text-accent-secondary' : '' }}
                        {{ $group->level === 'intermediate' ? 'bg-blue-400/10 text-blue-400' : '' }}
                        {{ $group->level === 'advanced' ? 'bg-accent-primary/10 text-accent-primary' : '' }}
                    ">
                        {{ $group->level_label }}
                    </span>
                    @if(!$group->is_active)
                        <span class="px-3 py-1.5 text-sm bg-border-subtle text-text-muted rounded-full">Inactivo</span>
                    @endif
                </div>
            </div>
            <a href="{{ businessRoute('coach.groups.edit', ['group' => $group]) }}" class="px-4 py-2.5 bg-blue-400/10 text-blue-400 rounded-card font-medium text-sm border border-blue-400/20 hover:bg-blue-400/20 transition-colors inline-flex items-center gap-2 min-h-touch w-full sm:w-auto justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Editar
            </a>
        </div>
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

    <!-- Estadísticas -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-bg-card rounded-card p-4 border border-border-subtle">
            <div class="text-sm text-text-muted mb-1">Total Miembros</div>
            <div class="text-3xl font-bold font-display">{{ $group->members->count() }}</div>
            @if($group->max_members)
                <div class="text-xs text-text-muted mt-1">de {{ $group->max_members }} máximo</div>
            @endif
        </div>

        <div class="bg-bg-card rounded-card p-4 border border-border-subtle">
            <div class="text-sm text-text-muted mb-1">Miembros Activos</div>
            <div class="text-3xl font-bold text-accent-secondary font-display">{{ $group->activeMembers->count() }}</div>
        </div>

        <div class="bg-bg-card rounded-card p-4 border border-border-subtle">
            <div class="text-sm text-text-muted mb-1">Entrenamientos</div>
            <div class="text-3xl font-bold font-display">{{ $groupWorkouts }}</div>
        </div>

        <div class="bg-bg-card rounded-card p-4 border border-border-subtle">
            <div class="text-sm text-text-muted mb-1">Kilómetros Totales</div>
            <div class="text-3xl font-bold font-display">{{ number_format($totalDistance, 1) }}</div>
        </div>
    </div>

    @if($group->description)
        <x-card title="Descripción" class="mb-6">
            <p class="text-text-muted leading-relaxed text-responsive-sm">{{ $group->description }}</p>
        </x-card>
    @endif

    <!-- Miembros del Grupo -->
    <x-card title="Miembros del Grupo ({{ $group->members->count() }})">
        <x-slot name="headerAction">
            @if(!$group->isFull() && $availableStudents->isNotEmpty())
                <button onclick="document.getElementById('addMemberModal').style.display='flex'" class="px-3.5 py-2 bg-gradient-to-br from-accent-primary to-pink-500 text-bg-main border-none rounded-card font-medium text-sm cursor-pointer inline-flex items-center gap-2 hover:opacity-90 transition-opacity min-h-touch">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Agregar Alumno
                </button>
            @endif
        </x-slot>

        @if($group->members->isEmpty())
            <div class="text-center py-8">
                <div class="w-16 h-16 bg-bg-main rounded-full mx-auto mb-4 flex items-center justify-center">
                    <svg class="w-8 h-8 text-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
                <p class="text-text-muted text-responsive-sm">No hay miembros en este grupo aún</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @foreach($group->members as $member)
                    <div class="p-3.5 rounded-card bg-bg-main border border-border-subtle/50 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-accent-primary to-pink-500 rounded-full flex items-center justify-center text-bg-main font-bold text-base">
                                {{ strtoupper(substr($member->name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="font-semibold text-responsive-sm">{{ $member->name }}</div>
                                <div class="text-text-muted text-xs">{{ $member->email }}</div>
                            </div>
                        </div>
                        <form method="POST" action="{{ businessRoute('coach.groups.removeMember', ['group' => $group, 'user' => $member]) }}" onsubmit="return confirm('¿Remover a {{ $member->name }} del grupo?')" class="m-0">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 bg-transparent border-none text-text-muted cursor-pointer hover:text-accent-primary transition-colors rounded-md min-h-touch min-w-touch">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        @endif
    </x-card>

    <!-- Modal para agregar miembro -->
    <div id="addMemberModal" class="hidden fixed inset-0 bg-black/80 items-center justify-center z-50" onclick="if(event.target === this) this.style.display='none'">
        <div class="bg-bg-card rounded-card p-6 max-w-lg w-11/12 border border-border-subtle">
            <h3 class="text-xl font-semibold mb-4">Agregar Alumno al Grupo</h3>

            @if($group->isFull())
                <p class="text-text-muted text-responsive-sm">El grupo ha alcanzado su límite de {{ $group->max_members }} miembros.</p>
            @elseif($availableStudents->isEmpty())
                <p class="text-text-muted text-responsive-sm">No hay alumnos disponibles. Todos los alumnos de tu negocio ya están en este grupo.</p>
            @else
                <form method="POST" action="{{ businessRoute('coach.groups.addMember', ['group' => $group]) }}">
                    @csrf
                    <div class="mb-5">
                        <label for="user_id" class="form-label">
                            Selecciona un alumno
                        </label>
                        <select
                            name="user_id"
                            id="user_id"
                            required
                            class="form-select"
                        >
                            <option value="">Selecciona...</option>
                            @foreach($availableStudents as $student)
                                <option value="{{ $student->id }}">{{ $student->name }} ({{ $student->email }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3">
                        <button
                            type="submit"
                            class="btn-primary flex-1 min-h-touch justify-center"
                        >
                            Agregar
                        </button>
                        <button
                            type="button"
                            onclick="document.getElementById('addMemberModal').style.display='none'"
                            class="btn-ghost min-h-touch justify-center"
                        >
                            Cancelar
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</x-app-layout>
