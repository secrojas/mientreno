<x-app-layout>
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
        <div>
            <h1 style="font-family:'Space Grotesk',system-ui,sans-serif;font-size:1.6rem;margin-bottom:.3rem;">
                Mis Carreras
            </h1>
            <p style="font-size:.9rem;color:var(--text-muted);">
                Gestiona tus carreras próximas y pasadas.
            </p>
        </div>
        <a href="{{ route('races.create') }}" class="btn-primary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px;">
                <path d="M12 5v14M5 12h14"/>
            </svg>
            Nueva Carrera
        </a>
    </div>

    @if (session('success'))
        <div style="padding:.75rem 1rem;background:rgba(45,227,142,.1);border:1px solid rgba(45,227,142,.3);border-radius:.6rem;font-size:.85rem;color:var(--accent-secondary);margin-bottom:1rem;">
            {{ session('success') }}
        </div>
    @endif

    <!-- Próximas carreras -->
    @if($upcomingRaces->count() > 0)
        <x-card title="Próximas Carreras" subtitle="{{ $upcomingRaces->count() }} carrera(s)" style="margin-bottom:1.5rem;">
            <div style="display:grid;gap:.75rem;">
                @foreach($upcomingRaces as $race)
                    <div style="padding:1rem;border-radius:.7rem;background:rgba(5,8,20,.9);border:1px solid rgba(31,41,55,.7);">
                        <div style="display:grid;grid-template-columns:1fr auto auto;gap:1rem;align-items:center;">
                            <div>
                                <div style="font-size:1.1rem;font-weight:600;margin-bottom:.3rem;">{{ $race->name }}</div>
                                <div style="font-size:.85rem;color:var(--text-muted);">
                                    <span style="font-family:'Space Grotesk',monospace;">{{ $race->distance_label }}</span>
                                    @if($race->location)
                                        · {{ $race->location }}
                                    @endif
                                    · {{ $race->date->format('d/m/Y') }}
                                    @if($race->days_until >= 0)
                                        · <span style="color:var(--accent-secondary);">en {{ $race->days_until }} días</span>
                                    @endif
                                </div>
                                @if($race->target_time)
                                    <div style="font-size:.8rem;color:var(--text-muted);margin-top:.3rem;">
                                        Objetivo: <span style="font-family:'Space Grotesk',monospace;">{{ $race->formatted_target_time }}</span>
                                    </div>
                                @endif
                            </div>
                            <a href="{{ route('races.edit', $race) }}" style="padding:.4rem .7rem;border-radius:.5rem;background:rgba(15,23,42,.9);border:1px solid #1F2937;color:var(--text-muted);font-size:.85rem;">
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
            <div style="display:grid;gap:.5rem;">
                @foreach($pastRaces as $race)
                    <div style="display:grid;grid-template-columns:100px 1fr 150px 120px;gap:1rem;padding:.75rem;border-bottom:1px solid rgba(15,23,42,.9);align-items:center;">
                        <div style="font-family:'Space Grotesk',monospace;color:var(--text-muted);font-size:.85rem;">
                            {{ $race->date->format('d/m/Y') }}
                        </div>
                        <div>
                            <div style="font-weight:500;">{{ $race->name }}</div>
                            <div style="font-size:.8rem;color:var(--text-muted);">
                                {{ $race->distance_label }}
                                @if($race->location)
                                    · {{ $race->location }}
                                @endif
                            </div>
                        </div>
                        <div style="font-family:'Space Grotesk',monospace;font-size:.85rem;">
                            @if($race->actual_time)
                                <span style="color:var(--accent-secondary);">{{ $race->formatted_actual_time }}</span>
                            @elseif($race->target_time)
                                <span style="color:var(--text-muted);">{{ $race->formatted_target_time }}</span>
                            @else
                                –
                            @endif
                        </div>
                        <div style="display:flex;gap:.5rem;justify-content:flex-end;">
                            <a href="{{ route('races.edit', $race) }}" style="padding:.35rem .6rem;border-radius:.5rem;background:rgba(15,23,42,.9);border:1px solid #1F2937;color:var(--text-muted);font-size:.8rem;">
                                Editar
                            </a>
                            <form method="POST" action="{{ route('races.destroy', $race) }}" style="display:inline;" onsubmit="return confirm('¿Eliminar esta carrera?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="padding:.35rem .6rem;border-radius:.5rem;background:rgba(255,59,92,.1);border:1px solid rgba(255,59,92,.3);color:#ff6b6b;cursor:pointer;font-size:.8rem;">
                                    Eliminar
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <div style="margin-top:1rem;">
                {{ $pastRaces->links() }}
            </div>
        @else
            <div style="text-align:center;padding:2rem;color:var(--text-muted);">
                No hay carreras pasadas registradas.
            </div>
        @endif
    </x-card>
</x-app-layout>
