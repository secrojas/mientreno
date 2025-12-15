<x-app-layout>
    <div>
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
            <div>
                <h1 style="font-family:'Space Grotesk',system-ui,sans-serif;font-size:1.6rem;margin-bottom:.3rem;">
                    Mis Entrenamientos
                </h1>
                <p style="font-size:.9rem;color:var(--text-muted);">
                    Historial completo de tus sesiones de running.
                </p>
            </div>
            <a href="{{ route('workouts.create') }}" class="btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px;">
                    <path d="M12 5v14M5 12h14"/>
                </svg>
                Nuevo Entreno
            </a>
        </div>

        @if (session('success'))
            <div style="padding:.75rem 1rem;background:rgba(45,227,142,.1);border:1px solid rgba(45,227,142,.3);border-radius:.6rem;font-size:.85rem;color:var(--accent-secondary);margin-bottom:1rem;">
                {{ session('success') }}
            </div>
        @endif

        <!-- Filtros y búsqueda -->
        <div style="background:rgba(15,23,42,.9);border-radius:1rem;border:1px solid var(--border-subtle);padding:1rem;margin-bottom:1rem;">
            <form method="GET" action="{{ route('workouts.index') }}">
                <div style="display:grid;grid-template-columns:repeat(5,1fr) auto;gap:.75rem;align-items:end;">
                    <!-- Búsqueda por notas -->
                    <div>
                        <label style="display:block;font-size:.8rem;color:var(--text-muted);margin-bottom:.3rem;">Buscar en notas</label>
                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Buscar..."
                            style="width:100%;padding:.5rem .75rem;background:rgba(5,8,20,.9);border:1px solid var(--border-subtle);border-radius:.6rem;color:var(--text-main);font-size:.85rem;"
                        >
                    </div>

                    <!-- Filtro por tipo -->
                    <div>
                        <label style="display:block;font-size:.8rem;color:var(--text-muted);margin-bottom:.3rem;">Tipo</label>
                        <select
                            name="type"
                            style="width:100%;padding:.5rem .75rem;background:rgba(5,8,20,.9);border:1px solid var(--border-subtle);border-radius:.6rem;color:var(--text-main);font-size:.85rem;"
                        >
                            <option value="">Todos los tipos</option>
                            @foreach($types as $key => $label)
                                <option value="{{ $key }}" {{ request('type') === $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filtro por estado -->
                    <div>
                        <label style="display:block;font-size:.8rem;color:var(--text-muted);margin-bottom:.3rem;">Estado</label>
                        <select
                            name="status"
                            style="width:100%;padding:.5rem .75rem;background:rgba(5,8,20,.9);border:1px solid var(--border-subtle);border-radius:.6rem;color:var(--text-main);font-size:.85rem;"
                        >
                            <option value="">Todos</option>
                            <option value="planned" {{ request('status') === 'planned' ? 'selected' : '' }}>Planificado</option>
                            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completado</option>
                            <option value="skipped" {{ request('status') === 'skipped' ? 'selected' : '' }}>Saltado</option>
                        </select>
                    </div>

                    <!-- Fecha desde -->
                    <div>
                        <label style="display:block;font-size:.8rem;color:var(--text-muted);margin-bottom:.3rem;">Desde</label>
                        <input
                            type="date"
                            name="date_from"
                            value="{{ request('date_from') }}"
                            style="width:100%;padding:.5rem .75rem;background:rgba(5,8,20,.9);border:1px solid var(--border-subtle);border-radius:.6rem;color:var(--text-main);font-size:.85rem;"
                        >
                    </div>

                    <!-- Fecha hasta -->
                    <div>
                        <label style="display:block;font-size:.8rem;color:var(--text-muted);margin-bottom:.3rem;">Hasta</label>
                        <input
                            type="date"
                            name="date_to"
                            value="{{ request('date_to') }}"
                            style="width:100%;padding:.5rem .75rem;background:rgba(5,8,20,.9);border:1px solid var(--border-subtle);border-radius:.6rem;color:var(--text-main);font-size:.85rem;"
                        >
                    </div>

                    <!-- Botones -->
                    <div style="display:flex;gap:.5rem;">
                        <button
                            type="submit"
                            style="padding:.5rem 1rem;background:var(--accent-secondary);color:#000;border:none;border-radius:.6rem;font-size:.85rem;font-weight:500;cursor:pointer;"
                        >
                            Filtrar
                        </button>
                        @if(request()->anyFilled(['search', 'type', 'status', 'date_from', 'date_to']))
                            <a
                                href="{{ route('workouts.index') }}"
                                style="padding:.5rem 1rem;background:rgba(5,8,20,.9);color:var(--text-muted);border:1px solid var(--border-subtle);border-radius:.6rem;font-size:.85rem;display:inline-flex;align-items:center;justify-content:center;"
                            >
                                Limpiar
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        @if($workouts->count() > 0)
            <div style="background:rgba(15,23,42,.9);border-radius:1rem;border:1px solid var(--border-subtle);overflow:hidden;">
                <!-- Header -->
                <div style="display:grid;grid-template-columns:110px 1fr 100px 100px 100px 80px 200px;gap:1rem;padding:.75rem 1rem;border-bottom:1px solid var(--border-subtle);font-size:.75rem;text-transform:uppercase;letter-spacing:.08em;color:var(--text-muted);">
                    <div>Fecha</div>
                    <div>Tipo</div>
                    <div>Distancia</div>
                    <div>Duración</div>
                    <div>Pace</div>
                    <div>Dif.</div>
                    <div style="text-align:right;">Acciones</div>
                </div>

                <!-- Rows -->
                @foreach($workouts as $workout)
                    <div style="display:grid;grid-template-columns:110px 1fr 100px 100px 100px 80px 200px;gap:1rem;padding:1rem;border-bottom:1px solid rgba(15,23,42,.9);font-size:.9rem;align-items:center;">
                        <div style="font-family:'Space Grotesk',monospace;color:var(--text-muted);">
                            {{ $workout->date->format('d/m/Y') }}
                        </div>
                        <div>
                            <div style="display:flex;align-items:center;gap:.5rem;margin-bottom:.2rem;">
                                <span style="font-weight:500;">{{ $workout->type_label }}</span>
                                @if($workout->isPlanned())
                                    <span style="display:inline-flex;align-items:center;gap:.25rem;padding:.15rem .45rem;border-radius:.4rem;background:rgba(59,130,246,.1);border:1px solid rgba(59,130,246,.3);font-size:.7rem;color:#60a5fa;">
                                        Planificado
                                    </span>
                                @elseif($workout->isSkipped())
                                    <span style="display:inline-flex;align-items:center;gap:.25rem;padding:.15rem .45rem;border-radius:.4rem;background:rgba(234,179,8,.1);border:1px solid rgba(234,179,8,.3);font-size:.7rem;color:#facc15;">
                                        Saltado
                                    </span>
                                @endif
                            </div>
                            @if($workout->notes)
                                <div style="font-size:.8rem;color:var(--text-muted);margin-top:.15rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:300px;">
                                    {{ Str::limit($workout->notes, 50) }}
                                </div>
                            @elseif($workout->skip_reason)
                                <div style="font-size:.8rem;color:var(--text-muted);margin-top:.15rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:300px;">
                                    Razón: {{ Str::limit($workout->skip_reason, 50) }}
                                </div>
                            @endif
                        </div>
                        <div>
                            <span style="font-family:'Space Grotesk',monospace;font-weight:600;">{{ $workout->distance }}</span>
                            <span style="font-size:.8rem;color:var(--text-muted);"> km</span>
                        </div>
                        <div style="font-family:'Space Grotesk',monospace;">
                            {{ $workout->formatted_duration }}
                        </div>
                        <div style="font-family:'Space Grotesk',monospace;color:var(--accent-secondary);">
                            {{ $workout->formatted_pace }}
                        </div>
                        <div>
                            @if($workout->isCompleted())
                                <div style="display:inline-flex;align-items:center;gap:.25rem;padding:.15rem .5rem;border-radius:.4rem;background:rgba(45,227,142,.1);border:1px solid rgba(45,227,142,.3);font-size:.75rem;">
                                    {{ $workout->difficulty }}/5
                                </div>
                            @else
                                <span style="color:var(--text-muted);font-size:.75rem;">–</span>
                            @endif
                        </div>
                        <div style="display:flex;gap:.4rem;justify-content:flex-end;">
                            @if($workout->isPlanned())
                                <!-- Botón Completar -->
                                <a href="{{ route('workouts.mark-completed', $workout) }}" style="padding:.35rem .6rem;border-radius:.5rem;background:rgba(45,227,142,.1);border:1px solid rgba(45,227,142,.3);color:#2de38e;display:inline-flex;align-items:center;gap:.3rem;font-size:.75rem;">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:12px;height:12px;">
                                        <path d="M20 6L9 17l-5-5"/>
                                    </svg>
                                    Completar
                                </a>
                                <!-- Botón Saltar -->
                                <form method="POST" action="{{ route('workouts.mark-skipped', $workout) }}" style="display:inline;" onsubmit="return confirm('¿Marcar este entrenamiento como saltado?');">
                                    @csrf
                                    <button type="submit" style="padding:.35rem .6rem;border-radius:.5rem;background:rgba(234,179,8,.1);border:1px solid rgba(234,179,8,.3);color:#facc15;cursor:pointer;font-size:.75rem;display:inline-flex;align-items:center;gap:.3rem;">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:12px;height:12px;">
                                            <path d="M18 6L6 18M6 6l12 12"/>
                                        </svg>
                                        Saltar
                                    </button>
                                </form>
                            @else
                                <!-- Botón Editar -->
                                <a href="{{ route('workouts.edit', $workout) }}" style="padding:.35rem .6rem;border-radius:.5rem;background:rgba(15,23,42,.9);border:1px solid #1F2937;color:var(--text-muted);display:inline-flex;align-items:center;gap:.3rem;font-size:.75rem;">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:12px;height:12px;">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                    </svg>
                                    Editar
                                </a>
                                <!-- Botón Eliminar -->
                                <form method="POST" action="{{ route('workouts.destroy', $workout) }}" style="display:inline;" onsubmit="return confirm('¿Eliminar este entrenamiento?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="padding:.35rem .6rem;border-radius:.5rem;background:rgba(255,59,92,.1);border:1px solid rgba(255,59,92,.3);color:#ff6b6b;cursor:pointer;font-size:.75rem;">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:12px;height:12px;">
                                            <path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                        </svg>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Paginación -->
            <div style="margin-top:1.5rem;">
                {{ $workouts->appends(request()->query())->links('vendor.pagination.custom') }}
            </div>
        @else
            <div style="background:rgba(15,23,42,.9);border-radius:1rem;padding:3rem 2rem;border:1px solid var(--border-subtle);text-align:center;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="width:48px;height:48px;color:var(--text-muted);margin:0 auto 1rem;">
                    <path d="M4 19L9 10L13 15L20 5"/>
                    <path d="M20 10V5H15"/>
                </svg>
                <h3 style="font-size:1.1rem;margin-bottom:.5rem;">No hay entrenamientos registrados</h3>
                <p style="color:var(--text-muted);font-size:.9rem;margin-bottom:1.5rem;">
                    Empezá a registrar tus sesiones de running para ver tu progreso.
                </p>
                <a href="{{ route('workouts.create') }}" class="btn-primary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px;">
                        <path d="M12 5v14M5 12h14"/>
                    </svg>
                    Crear Primer Entrenamiento
                </a>
            </div>
        @endif
    </div>

    <style>
        @media (max-width: 1100px) {
            [style*="grid-template-columns:110px 1fr 100px 100px 100px 80px 200px"] {
                grid-template-columns: 1fr !important;
            }
            [style*="grid-template-columns:110px 1fr 100px 100px 100px 80px 200px"] > div:nth-child(7) {
                border-top: 1px solid rgba(15,23,42,.9);
                padding-top: 0.75rem;
                margin-top: 0.5rem;
            }
        }
    </style>
</x-app-layout>
