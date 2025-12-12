<x-app-layout>
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
        <div>
            <h1 style="font-family:'Space Grotesk',system-ui,sans-serif;font-size:1.6rem;margin-bottom:.3rem;">
                Mis Objetivos
            </h1>
            <p style="font-size:.9rem;color:var(--text-muted);">
                Gestiona tus metas de entrenamiento.
            </p>
        </div>
        <a href="{{ route('goals.create') }}" class="btn-primary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px;">
                <path d="M12 5v14M5 12h14"/>
            </svg>
            Nuevo Objetivo
        </a>
    </div>

    @if (session('success'))
        <div style="padding:.75rem 1rem;background:rgba(45,227,142,.1);border:1px solid rgba(45,227,142,.3);border-radius:.6rem;font-size:.85rem;color:var(--accent-secondary);margin-bottom:1rem;">
            {{ session('success') }}
        </div>
    @endif

    <x-card>
        @if($goals->count() > 0)
            <div style="display:grid;gap:.75rem;">
                @foreach($goals as $goal)
                    <div style="padding:1rem;border-radius:.7rem;background:rgba(5,8,20,.9);border:1px solid {{ $goal->status === 'active' ? 'rgba(45,227,142,.3)' : 'rgba(31,41,55,.7)' }};">
                        <div style="display:grid;grid-template-columns:1fr auto auto;gap:1rem;align-items:center;">
                            <div>
                                <div style="display:flex;align-items:center;gap:.5rem;margin-bottom:.3rem;">
                                    <span style="padding:.15rem .5rem;border-radius:.4rem;background:rgba(45,227,142,.1);border:1px solid rgba(45,227,142,.3);font-size:.7rem;text-transform:uppercase;letter-spacing:.05em;">
                                        {{ $goal->type_label }}
                                    </span>
                                    <span style="padding:.15rem .5rem;border-radius:.4rem;background:rgba(15,23,42,.9);border:1px solid rgba(31,41,55,.7);font-size:.7rem;">
                                        {{ $goal->status_label }}
                                    </span>
                                </div>
                                <div style="font-size:1rem;font-weight:600;margin-bottom:.3rem;">{{ $goal->title }}</div>
                                <div style="font-size:.85rem;color:var(--text-muted);">
                                    {{ $goal->getTargetDescription() }}
                                    @if($goal->target_date)
                                        · Fecha límite: {{ $goal->target_date->format('d/m/Y') }}
                                        @if($goal->days_until !== null && $goal->days_until >= 0)
                                            <span style="color:var(--accent-secondary);">({{ $goal->days_until }} días)</span>
                                        @elseif($goal->isOverdue())
                                            <span style="color:#ff6b6b;">(vencido)</span>
                                        @endif
                                    @endif
                                </div>
                                @if($goal->progress_percentage > 0)
                                    <div style="margin-top:.5rem;">
                                        <div style="width:100%;height:4px;background:rgba(15,23,42,.9);border-radius:999px;overflow:hidden;">
                                            <div style="width:{{ $goal->progress_percentage }}%;height:100%;background:var(--accent-secondary);"></div>
                                        </div>
                                        <div style="font-size:.75rem;color:var(--text-muted);margin-top:.3rem;">
                                            {{ $goal->progress_percentage }}% completado
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div style="display:flex;gap:.5rem;">
                                <a href="{{ route('goals.edit', $goal) }}" style="padding:.35rem .6rem;border-radius:.5rem;background:rgba(15,23,42,.9);border:1px solid #1F2937;color:var(--text-muted);font-size:.8rem;">
                                    Editar
                                </a>
                                <form method="POST" action="{{ route('goals.destroy', $goal) }}" style="display:inline;" onsubmit="return confirm('¿Eliminar este objetivo?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="padding:.35rem .6rem;border-radius:.5rem;background:rgba(255,59,92,.1);border:1px solid rgba(255,59,92,.3);color:#ff6b6b;cursor:pointer;font-size:.8rem;">
                                        Eliminar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div style="margin-top:1rem;">
                {{ $goals->links() }}
            </div>
        @else
            <div style="text-align:center;padding:3rem;color:var(--text-muted);">
                <div style="font-size:1.1rem;margin-bottom:.5rem;">No hay objetivos registrados</div>
                <p style="margin-bottom:1.5rem;">Empezá a definir tus metas de entrenamiento.</p>
                <a href="{{ route('goals.create') }}" class="btn-primary">
                    Crear Primer Objetivo
                </a>
            </div>
        @endif
    </x-card>
</x-app-layout>
