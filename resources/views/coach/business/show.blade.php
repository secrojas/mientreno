<x-app-layout>
    <header style="display:flex;justify-content:space-between;align-items:center;gap:1rem;margin-bottom:1.5rem;">
        <div style="display:flex;flex-direction:column;gap:.2rem;">
            <h1 style="font-family:'Space Grotesk',system-ui,sans-serif;font-size:1.6rem;">{{ $business->name }}</h1>
            <p style="font-size:.9rem;color:var(--text-muted);">Gestión de tu negocio de coaching.</p>
        </div>
        <div style="display:flex;align-items:center;gap:.5rem;">
            <a href="{{ businessRoute('coach.business.edit') }}" style="padding:.45rem .9rem;border-radius:999px;background:rgba(31,41,55,.5);border:1px solid rgba(31,41,55,.7);font-size:.8rem;display:inline-flex;align-items:center;gap:.35rem;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:14px;height:14px;">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                </svg>
                Editar
            </a>
        </div>
    </header>

    <!-- Información del negocio -->
    <x-card title="Información del Negocio" subtitle="Detalles de configuración">
        <div style="display:grid;gap:1.25rem;">
            <!-- Nombre y slug -->
            <div style="display:grid;grid-template-columns:140px 1fr;gap:1rem;align-items:start;">
                <span style="font-size:.85rem;color:var(--text-muted);">Nombre:</span>
                <span style="font-size:.9rem;font-weight:500;">{{ $business->name }}</span>
            </div>

            <div style="display:grid;grid-template-columns:140px 1fr;gap:1rem;align-items:start;">
                <span style="font-size:.85rem;color:var(--text-muted);">URL (slug):</span>
                <span style="font-size:.9rem;font-family:monospace;color:var(--accent-secondary);">{{ $business->slug }}</span>
            </div>

            @if($business->description)
            <div style="display:grid;grid-template-columns:140px 1fr;gap:1rem;align-items:start;">
                <span style="font-size:.85rem;color:var(--text-muted);">Descripción:</span>
                <span style="font-size:.9rem;">{{ $business->description }}</span>
            </div>
            @endif

            <div style="display:grid;grid-template-columns:140px 1fr;gap:1rem;align-items:start;">
                <span style="font-size:.85rem;color:var(--text-muted);">Nivel objetivo:</span>
                <span style="padding:.2rem .6rem;border-radius:.4rem;background:rgba(45,227,142,.1);border:1px solid rgba(45,227,142,.3);font-size:.8rem;display:inline-block;width:fit-content;">
                    {{ $business->level_label }}
                </span>
            </div>

            <div style="display:grid;grid-template-columns:140px 1fr;gap:1rem;align-items:start;">
                <span style="font-size:.85rem;color:var(--text-muted);">Estado:</span>
                <span style="padding:.2rem .6rem;border-radius:.4rem;background:rgba({{ $business->is_active ? '45,227,142' : '234,179,8' }},.1);border:1px solid rgba({{ $business->is_active ? '45,227,142' : '234,179,8' }},.3);font-size:.8rem;display:inline-block;width:fit-content;">
                    {{ $business->is_active ? 'Activo' : 'Inactivo' }}
                </span>
            </div>
        </div>
    </x-card>

    <!-- Estadísticas -->
    <section style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:1rem;margin-top:1.5rem;">
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
    <x-card title="Alumnos" subtitle="Runners inscritos en tu negocio" style="margin-top:1.5rem;">
        <x-slot:headerAction>
            <a href="#" style="font-size:.8rem;color:var(--accent-secondary);">Invitar alumno</a>
        </x-slot:headerAction>

        @if($business->runners->count() > 0)
            <div style="display:grid;gap:.5rem;">
                @foreach($business->runners as $runner)
                    <div style="
                        display:grid;
                        grid-template-columns:1fr 120px 100px;
                        gap:.75rem;
                        padding:.75rem;
                        border-radius:.7rem;
                        background:rgba(5,8,20,.9);
                        border:1px solid rgba(31,41,55,.7);
                        align-items:center;
                    ">
                        <div>
                            <div style="font-size:.9rem;font-weight:500;">{{ $runner->name }}</div>
                            <div style="font-size:.75rem;color:var(--text-muted);margin-top:.15rem;">{{ $runner->email }}</div>
                        </div>
                        <div style="font-size:.8rem;color:var(--text-muted);">
                            {{ $runner->workouts()->count() }} entrenos
                        </div>
                        <div style="text-align:right;">
                            <a href="#" style="font-size:.75rem;color:var(--accent-secondary);">Ver perfil</a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div style="font-size:.85rem;color:var(--text-muted);text-align:center;padding:2rem 1rem;">
                No hay alumnos inscritos todavía.
                <br>
                <a href="#" style="display:inline-flex;align-items:center;gap:.35rem;padding:.4rem .8rem;border-radius:999px;font-size:.8rem;border:1px solid var(--accent-secondary);color:var(--accent-secondary);background:rgba(45,227,142,.05);margin-top:.75rem;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:14px;height:14px;">
                        <path d="M12 5v14M5 12h14"/>
                    </svg>
                    Invitar primer alumno
                </a>
            </div>
        @endif
    </x-card>

    <!-- Horarios (placeholder) -->
    <x-card title="Horarios de Entrenamientos" subtitle="Próximamente" style="margin-top:1.5rem;">
        <div style="text-align:center;padding:1.5rem;color:var(--text-muted);font-size:.85rem;">
            La configuración de horarios estará disponible en la próxima versión.
        </div>
    </x-card>

    <style>
        @media (max-width: 1024px) {
            header {
                flex-direction: column;
                align-items: flex-start;
            }
            section {
                grid-template-columns: minmax(0, 1fr) !important;
            }
        }
    </style>
</x-app-layout>
