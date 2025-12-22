<x-app-layout>
    <div style="max-width:720px;">
        <div style="margin-bottom:1.5rem;">
            <div style="display:flex;align-items:center;gap:.5rem;margin-bottom:.5rem;">
                <a href="{{ businessRoute('coach.groups.show', ['group' => $group]) }}" style="color:var(--text-muted);display:inline-flex;align-items:center;gap:.3rem;font-size:.85rem;text-decoration:none;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px;">
                        <path d="M19 12H5M12 19l-7-7 7-7"/>
                    </svg>
                    Volver al Grupo
                </a>
            </div>
            <h1 style="font-family:'Space Grotesk',system-ui,sans-serif;font-size:1.6rem;margin-bottom:.3rem;">
                Editar: {{ $group->name }}
            </h1>
            <p style="font-size:.9rem;color:var(--text-muted);">
                Actualiza los detalles del grupo
            </p>
        </div>

        <form method="POST" action="{{ businessRoute('coach.groups.update', ['group' => $group]) }}" style="background:rgba(15,23,42,.9);border-radius:1rem;padding:1.5rem;border:1px solid var(--border-subtle);display:grid;gap:1.25rem;">
            @csrf
            @method('PUT')

            @if($errors->any())
                <div style="padding:.75rem;background:rgba(255,59,92,.1);border:1px solid rgba(255,59,92,.3);border-radius:.6rem;font-size:.85rem;color:#ff6b6b;">
                    @foreach($errors->all() as $error)
                        <div style="margin-bottom:.25rem;">{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <!-- Nombre del grupo -->
            <div>
                <label for="name" style="display:block;font-size:.8rem;margin-bottom:.25rem;font-weight:500;">
                    Nombre del Grupo <span style="color:var(--accent-primary);">*</span>
                </label>
                <input
                    type="text"
                    name="name"
                    id="name"
                    value="{{ old('name', $group->name) }}"
                    required
                    style="width:100%;padding:.6rem .75rem;border-radius:.6rem;border:1px solid #1F2937;background:#050814;color:var(--text-main);font-size:.9rem;"
                >
            </div>

            <!-- Descripción -->
            <div>
                <label for="description" style="display:block;font-size:.8rem;margin-bottom:.25rem;font-weight:500;">
                    Descripción
                </label>
                <textarea
                    name="description"
                    id="description"
                    rows="4"
                    style="width:100%;padding:.6rem .75rem;border-radius:.6rem;border:1px solid #1F2937;background:#050814;color:var(--text-main);font-size:.9rem;resize:none;"
                >{{ old('description', $group->description) }}</textarea>
                <small style="font-size:.75rem;color:var(--text-muted);display:block;margin-top:.35rem;">
                    Máximo 1000 caracteres
                </small>
            </div>

            <!-- Nivel y Max Miembros -->
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem;">
                <div>
                    <label for="level" style="display:block;font-size:.8rem;margin-bottom:.25rem;font-weight:500;">
                        Nivel del Grupo <span style="color:var(--accent-primary);">*</span>
                    </label>
                    <select
                        name="level"
                        id="level"
                        required
                        style="width:100%;padding:.6rem .75rem;border-radius:.6rem;border:1px solid #1F2937;background:#050814;color:var(--text-main);font-size:.9rem;"
                    >
                        <option value="beginner" {{ old('level', $group->level) === 'beginner' ? 'selected' : '' }}>Principiante</option>
                        <option value="intermediate" {{ old('level', $group->level) === 'intermediate' ? 'selected' : '' }}>Intermedio</option>
                        <option value="advanced" {{ old('level', $group->level) === 'advanced' ? 'selected' : '' }}>Avanzado</option>
                    </select>
                </div>

                <div>
                    <label for="max_members" style="display:block;font-size:.8rem;margin-bottom:.25rem;font-weight:500;">
                        Número Máximo de Miembros
                    </label>
                    <input
                        type="number"
                        name="max_members"
                        id="max_members"
                        value="{{ old('max_members', $group->max_members) }}"
                        min="{{ $group->members->count() }}"
                        max="200"
                        placeholder="Ilimitado"
                        style="width:100%;padding:.6rem .75rem;border-radius:.6rem;border:1px solid #1F2937;background:#050814;color:var(--text-main);font-size:.9rem;"
                    >
                    <small style="font-size:.75rem;color:var(--text-muted);display:block;margin-top:.35rem;">
                        Hay {{ $group->members->count() }} miembro(s) actualmente
                    </small>
                </div>
            </div>

            <!-- Estado activo -->
            <div>
                <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer;">
                    <input
                        type="checkbox"
                        name="is_active"
                        value="1"
                        {{ old('is_active', $group->is_active) ? 'checked' : '' }}
                        style="width:18px;height:18px;cursor:pointer;"
                    >
                    <span style="font-size:.85rem;font-weight:500;">Grupo activo</span>
                </label>
                <small style="font-size:.75rem;color:var(--text-muted);display:block;margin-top:.35rem;margin-left:1.8rem;">
                    Los grupos inactivos no aparecerán en los listados principales
                </small>
            </div>

            <!-- Botones -->
            <div style="display:flex;gap:.75rem;margin-top:.5rem;">
                <button
                    type="submit"
                    style="flex:1;padding:.7rem 1.2rem;background:linear-gradient(135deg,var(--accent-primary),#FF4FA3);color:#0B0C12;border:none;border-radius:.6rem;font-weight:600;font-size:.9rem;cursor:pointer;transition:all .18s ease-out;"
                >
                    Actualizar Grupo
                </button>
                <a
                    href="{{ businessRoute('coach.groups.show', ['group' => $group]) }}"
                    style="padding:.7rem 1.2rem;background:rgba(5,8,20,.9);color:var(--text-main);border:1px solid rgba(31,41,55,.7);border-radius:.6rem;font-weight:500;font-size:.9rem;text-decoration:none;display:inline-flex;align-items:center;justify-content:center;transition:all .15s ease-out;"
                >
                    Cancelar
                </a>
            </div>
        </form>

        <!-- Zona de Peligro -->
        <div style="background:rgba(15,23,42,.9);border-radius:1rem;padding:1.5rem;border:1px solid rgba(255,59,92,.2);margin-top:1.5rem;">
            <h3 style="font-size:1.1rem;font-weight:600;margin-bottom:.5rem;color:#ff6b6b;">Zona de Peligro</h3>
            <p style="font-size:.85rem;color:var(--text-muted);margin-bottom:1rem;">Desactivar el grupo lo ocultará pero conservará todos los datos</p>
            <form method="POST" action="{{ businessRoute('coach.groups.destroy', ['group' => $group]) }}" onsubmit="return confirm('¿Estás seguro de desactivar este grupo? Los miembros no se eliminarán pero el grupo quedará inactivo.')">
                @csrf
                @method('DELETE')
                <button
                    type="submit"
                    style="padding:.6rem 1rem;background:rgba(255,59,92,.1);color:#ff6b6b;border:1px solid rgba(255,59,92,.3);border-radius:.6rem;font-weight:500;font-size:.85rem;cursor:pointer;transition:all .15s ease-out;"
                >
                    Desactivar Grupo
                </button>
            </form>
        </div>
    </div>
</x-app-layout>
