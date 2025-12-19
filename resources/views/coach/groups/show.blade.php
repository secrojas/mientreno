<x-app-layout>
    <div style="margin-bottom:1.5rem;">
        <div style="display:flex;align-items:center;gap:.5rem;margin-bottom:.5rem;">
            <a href="{{ route('coach.groups.index') }}" style="color:var(--text-muted);display:inline-flex;align-items:center;gap:.3rem;font-size:.85rem;text-decoration:none;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px;">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
                Volver a Grupos
            </a>
        </div>
        <div style="display:flex;justify-content:space-between;align-items:start;">
            <div>
                <h1 style="font-family:'Space Grotesk',system-ui,sans-serif;font-size:1.6rem;margin-bottom:.5rem;">
                    {{ $group->name }}
                </h1>
                <div style="display:flex;align-items:center;gap:.75rem;">
                    <span style="display:inline-block;padding:.3rem .7rem;border-radius:999px;font-size:.8rem;font-weight:600;
                        {{ $group->level === 'beginner' ? 'background:rgba(45,227,142,.1);color:#2DE38E;' : '' }}
                        {{ $group->level === 'intermediate' ? 'background:rgba(96,165,250,.1);color:#60A5FA;' : '' }}
                        {{ $group->level === 'advanced' ? 'background:rgba(255,59,92,.1);color:#FF3B5C;' : '' }}
                    ">
                        {{ $group->level_label }}
                    </span>
                    @if(!$group->is_active)
                        <span style="padding:.3rem .7rem;font-size:.8rem;background:rgba(31,41,55,.8);color:var(--text-muted);border-radius:999px;">Inactivo</span>
                    @endif
                </div>
            </div>
            <a href="{{ route('coach.groups.edit', $group) }}" style="padding:.6rem 1rem;background:rgba(96,165,250,.1);color:#60A5FA;border-radius:.6rem;font-weight:500;font-size:.85rem;text-decoration:none;border:1px solid rgba(96,165,250,.2);display:inline-flex;align-items:center;gap:.4rem;">
                <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Editar
            </a>
        </div>
    </div>

    @if(session('success'))
        <div style="padding:.75rem 1rem;background:rgba(45,227,142,.1);border:1px solid rgba(45,227,142,.3);border-radius:.6rem;font-size:.85rem;color:var(--accent-secondary);margin-bottom:1rem;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="padding:.75rem 1rem;background:rgba(255,59,92,.1);border:1px solid rgba(255,59,92,.3);border-radius:.6rem;font-size:.85rem;color:#ff6b6b;margin-bottom:1rem;">
            {{ session('error') }}
        </div>
    @endif

    <!-- Estadísticas -->
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:1rem;margin-bottom:1.5rem;">
        <div style="background:rgba(15,23,42,.95);border-radius:.9rem;padding:1rem;border:1px solid var(--border-subtle);">
            <div style="font-size:.8rem;color:var(--text-muted);margin-bottom:.3rem;">Total Miembros</div>
            <div style="font-size:1.8rem;font-weight:700;font-family:'Space Grotesk',monospace;">{{ $group->members->count() }}</div>
            @if($group->max_members)
                <div style="font-size:.75rem;color:var(--text-muted);margin-top:.2rem;">de {{ $group->max_members }} máximo</div>
            @endif
        </div>

        <div style="background:rgba(15,23,42,.95);border-radius:.9rem;padding:1rem;border:1px solid var(--border-subtle);">
            <div style="font-size:.8rem;color:var(--text-muted);margin-bottom:.3rem;">Miembros Activos</div>
            <div style="font-size:1.8rem;font-weight:700;color:var(--accent-secondary);font-family:'Space Grotesk',monospace;">{{ $group->activeMembers->count() }}</div>
        </div>

        <div style="background:rgba(15,23,42,.95);border-radius:.9rem;padding:1rem;border:1px solid var(--border-subtle);">
            <div style="font-size:.8rem;color:var(--text-muted);margin-bottom:.3rem;">Entrenamientos</div>
            <div style="font-size:1.8rem;font-weight:700;font-family:'Space Grotesk',monospace;">{{ $groupWorkouts }}</div>
        </div>

        <div style="background:rgba(15,23,42,.95);border-radius:.9rem;padding:1rem;border:1px solid var(--border-subtle);">
            <div style="font-size:.8rem;color:var(--text-muted);margin-bottom:.3rem;">Kilómetros Totales</div>
            <div style="font-size:1.8rem;font-weight:700;font-family:'Space Grotesk',monospace;">{{ number_format($totalDistance, 1) }}</div>
        </div>
    </div>

    @if($group->description)
        <x-card title="Descripción" style="margin-bottom:1.5rem;">
            <p style="color:var(--text-muted);line-height:1.6;font-size:.9rem;">{{ $group->description }}</p>
        </x-card>
    @endif

    <!-- Miembros del Grupo -->
    <x-card title="Miembros del Grupo ({{ $group->members->count() }})">
        <x-slot name="headerAction">
            @if(!$group->isFull() && $availableStudents->isNotEmpty())
                <button onclick="document.getElementById('addMemberModal').style.display='flex'" style="padding:.5rem .9rem;background:linear-gradient(135deg,var(--accent-primary),#FF4FA3);color:#0B0C12;border:none;border-radius:.6rem;font-weight:500;font-size:.85rem;cursor:pointer;display:inline-flex;align-items:center;gap:.4rem;">
                    <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Agregar Alumno
                </button>
            @endif
        </x-slot>

        @if($group->members->isEmpty())
            <div style="text-align:center;padding:2rem;">
                <div style="width:64px;height:64px;background:rgba(5,8,20,.9);border-radius:50%;margin:0 auto 1rem;display:flex;align-items:center;justify-content:center;">
                    <svg style="width:32px;height:32px;color:var(--text-muted);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
                <p style="color:var(--text-muted);font-size:.9rem;">No hay miembros en este grupo aún</p>
            </div>
        @else
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:.75rem;">
                @foreach($group->members as $member)
                    <div style="padding:.9rem;border-radius:.7rem;background:rgba(5,8,20,.9);border:1px solid rgba(31,41,55,.5);display:flex;align-items:center;justify-content:space-between;">
                        <div style="display:flex;align-items:center;gap:.75rem;">
                            <div style="width:40px;height:40px;background:linear-gradient(135deg,var(--accent-primary),#FF4FA3);border-radius:50%;display:flex;align-items:center;justify-content:center;color:#0B0C12;font-weight:700;font-size:1rem;">
                                {{ strtoupper(substr($member->name, 0, 1)) }}
                            </div>
                            <div>
                                <div style="font-weight:600;font-size:.9rem;">{{ $member->name }}</div>
                                <div style="color:var(--text-muted);font-size:.75rem;">{{ $member->email }}</div>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('coach.groups.removeMember', [$group, $member]) }}" onsubmit="return confirm('¿Remover a {{ $member->name }} del grupo?')" style="margin:0;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="padding:.4rem;background:transparent;border:none;color:var(--text-muted);cursor:pointer;transition:color .15s ease-out;border-radius:.4rem;">
                                <svg style="width:18px;height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
    <div id="addMemberModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.8);align-items:center;justify-content:center;z-index:1000;" onclick="if(event.target === this) this.style.display='none'">
        <div style="background:rgba(15,23,42,.98);border-radius:1rem;padding:1.5rem;max-width:480px;width:90%;border:1px solid rgba(31,41,55,.7);">
            <h3 style="font-size:1.3rem;font-weight:600;margin-bottom:1rem;">Agregar Alumno al Grupo</h3>

            @if($group->isFull())
                <p style="color:var(--text-muted);font-size:.9rem;">El grupo ha alcanzado su límite de {{ $group->max_members }} miembros.</p>
            @elseif($availableStudents->isEmpty())
                <p style="color:var(--text-muted);font-size:.9rem;">No hay alumnos disponibles. Todos los alumnos de tu negocio ya están en este grupo.</p>
            @else
                <form method="POST" action="{{ route('coach.groups.addMember', $group) }}">
                    @csrf
                    <div style="margin-bottom:1.25rem;">
                        <label for="user_id" style="display:block;font-size:.8rem;margin-bottom:.25rem;font-weight:500;">
                            Selecciona un alumno
                        </label>
                        <select
                            name="user_id"
                            id="user_id"
                            required
                            style="width:100%;padding:.6rem .75rem;border-radius:.6rem;border:1px solid #1F2937;background:#050814;color:var(--text-main);font-size:.9rem;"
                        >
                            <option value="">Selecciona...</option>
                            @foreach($availableStudents as $student)
                                <option value="{{ $student->id }}">{{ $student->name }} ({{ $student->email }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div style="display:flex;gap:.75rem;">
                        <button
                            type="submit"
                            style="flex:1;padding:.7rem 1.2rem;background:linear-gradient(135deg,var(--accent-primary),#FF4FA3);color:#0B0C12;border:none;border-radius:.6rem;font-weight:600;font-size:.9rem;cursor:pointer;"
                        >
                            Agregar
                        </button>
                        <button
                            type="button"
                            onclick="document.getElementById('addMemberModal').style.display='none'"
                            style="padding:.7rem 1.2rem;background:rgba(5,8,20,.9);color:var(--text-main);border:1px solid rgba(31,41,55,.7);border-radius:.6rem;font-weight:500;font-size:.9rem;cursor:pointer;"
                        >
                            Cancelar
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</x-app-layout>
