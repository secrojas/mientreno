<x-app-layout>
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
        <div>
            <h1 style="font-family:'Space Grotesk',system-ui,sans-serif;font-size:1.6rem;margin-bottom:.3rem;">
                Grupos de Entrenamiento
            </h1>
            <p style="font-size:.9rem;color:var(--text-muted);">
                Gestiona tus grupos y asigna alumnos
            </p>
        </div>
        <a href="{{ businessRoute('coach.groups.create') }}" class="btn-primary" style="display:inline-flex;align-items:center;gap:.4rem;padding:.6rem 1.2rem;background:linear-gradient(135deg,var(--accent-primary),#FF4FA3);color:#0B0C12;border-radius:.6rem;font-weight:500;font-size:.9rem;border:none;cursor:pointer;transition:all .18s ease-out;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px;">
                <path d="M12 5v14M5 12h14"/>
            </svg>
            Crear Grupo
        </a>
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

    @if($groups->isEmpty())
        <div style="background:rgba(15,23,42,.95);border-radius:1rem;border:1px solid var(--border-subtle);padding:3rem 2rem;text-align:center;">
            <div style="width:80px;height:80px;background:rgba(5,8,20,.9);border-radius:50%;margin:0 auto 1.5rem;display:flex;align-items:center;justify-content:center;">
                <svg style="width:40px;height:40px;color:var(--text-muted);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
            <h3 style="font-size:1.2rem;font-weight:600;margin-bottom:.5rem;">No tienes grupos creados</h3>
            <p style="color:var(--text-muted);margin-bottom:1.5rem;font-size:.9rem;">Crea tu primer grupo de entrenamiento para organizar a tus alumnos</p>
            <a href="{{ businessRoute('coach.groups.create') }}" style="display:inline-flex;align-items:center;gap:.4rem;padding:.6rem 1.2rem;background:linear-gradient(135deg,var(--accent-primary),#FF4FA3);color:#0B0C12;border-radius:.6rem;font-weight:500;font-size:.9rem;text-decoration:none;">
                Crear Primer Grupo
            </a>
        </div>
    @else
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(340px,1fr));gap:1rem;">
            @foreach($groups as $group)
                <div style="background:rgba(15,23,42,.95);border-radius:1rem;border:1px solid {{ $group->is_active ? 'rgba(31,41,55,.7)' : 'rgba(31,41,55,.3)' }};padding:1.25rem;transition:all .2s ease-out;{{ $group->is_active ? '' : 'opacity:.6;' }}">
                    <div style="display:flex;justify-content:space-between;align-items:start;margin-bottom:1rem;">
                        <div style="flex:1;">
                            <h3 style="font-size:1.1rem;font-weight:600;margin-bottom:.4rem;">{{ $group->name }}</h3>
                            <div style="display:flex;align-items:center;gap:.5rem;">
                                <span style="display:inline-block;padding:.2rem .6rem;border-radius:999px;font-size:.75rem;font-weight:600;
                                    {{ $group->level === 'beginner' ? 'background:rgba(45,227,142,.1);color:#2DE38E;' : '' }}
                                    {{ $group->level === 'intermediate' ? 'background:rgba(96,165,250,.1);color:#60A5FA;' : '' }}
                                    {{ $group->level === 'advanced' ? 'background:rgba(255,59,92,.1);color:#FF3B5C;' : '' }}
                                ">
                                    {{ $group->level_label }}
                                </span>
                                @if(!$group->is_active)
                                    <span style="padding:.2rem .6rem;font-size:.75rem;background:rgba(31,41,55,.8);color:var(--text-muted);border-radius:999px;">Inactivo</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if($group->description)
                        <p style="color:var(--text-muted);font-size:.85rem;margin-bottom:1rem;line-height:1.4;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">{{ $group->description }}</p>
                    @endif

                    <div style="display:flex;align-items:center;gap:1rem;margin-bottom:1rem;font-size:.85rem;color:var(--text-muted);">
                        <div style="display:flex;align-items:center;gap:.4rem;">
                            <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                            <span>{{ $group->members_count }} miembro{{ $group->members_count !== 1 ? 's' : '' }}</span>
                        </div>
                        @if($group->max_members)
                            <div style="display:flex;align-items:center;gap:.4rem;">
                                <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                                <span>Máx: {{ $group->max_members }}</span>
                            </div>
                        @endif
                    </div>

                    <div style="display:flex;gap:.5rem;">
                        <a href="{{ businessRoute('coach.groups.show', ['group' => $group]) }}" style="flex:1;padding:.5rem 1rem;background:rgba(5,8,20,.9);color:var(--text-main);text-align:center;border-radius:.6rem;font-weight:500;font-size:.85rem;text-decoration:none;border:1px solid rgba(31,41,55,.7);transition:all .15s ease-out;">
                            Ver Detalle
                        </a>
                        <a href="{{ businessRoute('coach.groups.edit', ['group' => $group]) }}" style="padding:.5rem .9rem;background:rgba(96,165,250,.1);color:#60A5FA;border-radius:.6rem;font-weight:500;font-size:.85rem;text-decoration:none;border:1px solid rgba(96,165,250,.2);transition:all .15s ease-out;">
                            Editar
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-app-layout>
