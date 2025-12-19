<x-app-layout>
    <div style="max-width:720px;">
        <div style="margin-bottom:1.5rem;">
            <div style="display:flex;align-items:center;gap:.5rem;margin-bottom:.5rem;">
                <a href="{{ route('coach.groups.index') }}" style="color:var(--text-muted);display:inline-flex;align-items:center;gap:.3rem;font-size:.85rem;text-decoration:none;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px;">
                        <path d="M19 12H5M12 19l-7-7 7-7"/>
                    </svg>
                    Volver a Grupos
                </a>
            </div>
            <h1 style="font-family:'Space Grotesk',system-ui,sans-serif;font-size:1.6rem;margin-bottom:.3rem;">
                Crear Grupo de Entrenamiento
            </h1>
            <p style="font-size:.9rem;color:var(--text-muted);">
                Define los detalles del nuevo grupo
            </p>
        </div>

        <form method="POST" action="{{ route('coach.groups.store') }}" style="background:rgba(15,23,42,.9);border-radius:1rem;padding:1.5rem;border:1px solid var(--border-subtle);display:grid;gap:1.25rem;">
            @csrf

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
                    value="{{ old('name') }}"
                    required
                    placeholder="Ej: Grupo Principiantes Lunes/Miércoles"
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
                    placeholder="Describe los objetivos y características del grupo"
                    style="width:100%;padding:.6rem .75rem;border-radius:.6rem;border:1px solid #1F2937;background:#050814;color:var(--text-main);font-size:.9rem;resize:none;"
                >{{ old('description') }}</textarea>
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
                        <option value="">Selecciona un nivel...</option>
                        <option value="beginner" {{ old('level') === 'beginner' ? 'selected' : '' }}>Principiante</option>
                        <option value="intermediate" {{ old('level') === 'intermediate' ? 'selected' : '' }}>Intermedio</option>
                        <option value="advanced" {{ old('level') === 'advanced' ? 'selected' : '' }}>Avanzado</option>
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
                        value="{{ old('max_members') }}"
                        min="1"
                        max="200"
                        placeholder="Ilimitado"
                        style="width:100%;padding:.6rem .75rem;border-radius:.6rem;border:1px solid #1F2937;background:#050814;color:var(--text-main);font-size:.9rem;"
                    >
                    <small style="font-size:.75rem;color:var(--text-muted);display:block;margin-top:.35rem;">
                        Déjalo vacío para ilimitado
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
                        {{ old('is_active', true) ? 'checked' : '' }}
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
                    Crear Grupo
                </button>
                <a
                    href="{{ route('coach.groups.index') }}"
                    style="padding:.7rem 1.2rem;background:rgba(5,8,20,.9);color:var(--text-main);border:1px solid rgba(31,41,55,.7);border-radius:.6rem;font-weight:500;font-size:.9rem;text-decoration:none;display:inline-flex;align-items:center;justify-content:center;transition:all .15s ease-out;"
                >
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</x-app-layout>
