<x-app-layout>
    <header style="display:flex;justify-content:space-between;align-items:center;gap:1rem;margin-bottom:1.5rem;">
        <div style="display:flex;flex-direction:column;gap:.2rem;">
            <h1 style="font-family:'Space Grotesk',system-ui,sans-serif;font-size:1.6rem;">Editar Negocio</h1>
            <p style="font-size:.9rem;color:var(--text-muted);">Actualizar información de {{ $business->name }}</p>
        </div>
    </header>

    <x-card>
        <form method="POST" action="{{ route('coach.business.update', $business) }}" style="display:grid;gap:1.5rem;">
            @csrf
            @method('PUT')

            <!-- Nombre del negocio -->
            <div>
                <label for="name" style="display:block;font-size:.85rem;font-weight:500;margin-bottom:.4rem;">
                    Nombre del negocio <span style="color:var(--accent-primary);">*</span>
                </label>
                <input
                    type="text"
                    name="name"
                    id="name"
                    value="{{ old('name', $business->name) }}"
                    required
                    placeholder="Ej: Running Team Palermo"
                    style="width:100%;padding:.65rem .85rem;border-radius:.6rem;background:rgba(5,8,20,.9);border:1px solid rgba(31,41,55,.7);color:var(--text-main);font-size:.9rem;"
                    onfocus="this.style.borderColor='var(--accent-secondary)'"
                    onblur="this.style.borderColor='rgba(31,41,55,.7)'"
                >
                @error('name')
                    <span style="font-size:.75rem;color:var(--accent-primary);margin-top:.25rem;display:block;">{{ $message }}</span>
                @enderror
            </div>

            <!-- Slug (solo lectura) -->
            <div>
                <label for="slug" style="display:block;font-size:.85rem;font-weight:500;margin-bottom:.4rem;">
                    URL (Slug)
                </label>
                <input
                    type="text"
                    id="slug"
                    value="{{ $business->slug }}"
                    disabled
                    style="width:100%;padding:.65rem .85rem;border-radius:.6rem;background:rgba(5,8,20,.5);border:1px solid rgba(31,41,55,.7);color:var(--text-muted);font-size:.9rem;font-family:monospace;"
                >
                <span style="font-size:.75rem;color:var(--text-muted);margin-top:.25rem;display:block;">
                    El slug no puede ser modificado después de la creación.
                </span>
            </div>

            <!-- Descripción -->
            <div>
                <label for="description" style="display:block;font-size:.85rem;font-weight:500;margin-bottom:.4rem;">
                    Descripción
                </label>
                <textarea
                    name="description"
                    id="description"
                    rows="3"
                    placeholder="Describe tu negocio de coaching..."
                    style="width:100%;padding:.65rem .85rem;border-radius:.6rem;background:rgba(5,8,20,.9);border:1px solid rgba(31,41,55,.7);color:var(--text-main);font-size:.9rem;resize:vertical;"
                    onfocus="this.style.borderColor='var(--accent-secondary)'"
                    onblur="this.style.borderColor='rgba(31,41,55,.7)'"
                >{{ old('description', $business->description) }}</textarea>
                @error('description')
                    <span style="font-size:.75rem;color:var(--accent-primary);margin-top:.25rem;display:block;">{{ $message }}</span>
                @enderror
            </div>

            <!-- Nivel objetivo -->
            <div>
                <label for="level" style="display:block;font-size:.85rem;font-weight:500;margin-bottom:.4rem;">
                    Nivel objetivo <span style="color:var(--accent-primary);">*</span>
                </label>
                <select
                    name="level"
                    id="level"
                    required
                    style="width:100%;padding:.65rem .85rem;border-radius:.6rem;background:rgba(5,8,20,.9);border:1px solid rgba(31,41,55,.7);color:var(--text-main);font-size:.9rem;"
                    onfocus="this.style.borderColor='var(--accent-secondary)'"
                    onblur="this.style.borderColor='rgba(31,41,55,.7)'"
                >
                    <option value="beginner" {{ old('level', $business->level) === 'beginner' ? 'selected' : '' }}>Principiante</option>
                    <option value="intermediate" {{ old('level', $business->level) === 'intermediate' ? 'selected' : '' }}>Intermedio</option>
                    <option value="advanced" {{ old('level', $business->level) === 'advanced' ? 'selected' : '' }}>Avanzado</option>
                </select>
                @error('level')
                    <span style="font-size:.75rem;color:var(--accent-primary);margin-top:.25rem;display:block;">{{ $message }}</span>
                @enderror
            </div>

            <!-- Estado activo/inactivo -->
            <div>
                <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer;">
                    <input
                        type="checkbox"
                        name="is_active"
                        value="1"
                        {{ old('is_active', $business->is_active) ? 'checked' : '' }}
                        style="width:18px;height:18px;cursor:pointer;"
                    >
                    <span style="font-size:.85rem;font-weight:500;">Negocio activo</span>
                </label>
                <span style="font-size:.75rem;color:var(--text-muted);margin-top:.25rem;display:block;margin-left:1.6rem;">
                    Los negocios inactivos no pueden recibir nuevos alumnos.
                </span>
            </div>

            <!-- Horarios (placeholder) -->
            <div>
                <label style="display:block;font-size:.85rem;font-weight:500;margin-bottom:.4rem;">
                    Horarios de entrenamientos
                </label>
                <div style="padding:1rem;border-radius:.6rem;background:rgba(5,8,20,.5);border:1px dashed rgba(31,41,55,.7);text-align:center;">
                    <p style="font-size:.85rem;color:var(--text-muted);">
                        La configuración de horarios estará disponible en la próxima versión.
                    </p>
                </div>
            </div>

            <!-- Botones -->
            <div style="display:flex;gap:.75rem;margin-top:1rem;">
                <button
                    type="submit"
                    style="padding:.65rem 1.5rem;border-radius:.6rem;background:var(--accent-secondary);color:#050814;font-size:.9rem;font-weight:500;border:none;cursor:pointer;"
                    onmouseover="this.style.transform='translateY(-1px)';this.style.boxShadow='0 4px 12px rgba(45,227,142,.3)'"
                    onmouseout="this.style.transform='';this.style.boxShadow=''"
                >
                    Guardar Cambios
                </button>
                <a
                    href="{{ route('coach.business.show', $business) }}"
                    style="padding:.65rem 1.5rem;border-radius:.6rem;background:rgba(31,41,55,.5);color:var(--text-main);font-size:.9rem;border:1px solid rgba(31,41,55,.7);display:inline-flex;align-items:center;justify-content:center;"
                >
                    Cancelar
                </a>
            </div>
        </form>
    </x-card>

    <style>
        select option {
            background: #0B0C12;
            color: var(--text-main);
        }
    </style>
</x-app-layout>
