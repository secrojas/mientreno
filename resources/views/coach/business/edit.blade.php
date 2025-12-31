<x-app-layout>
    <header class="flex flex-col gap-1 mb-6">
        <h1 class="font-display text-responsive-2xl">Editar Negocio</h1>
        <p class="text-responsive-sm text-text-muted">Actualizar información de {{ $business->name }}</p>
    </header>

    <x-card>
        <form method="POST" action="{{ businessRoute('coach.business.update') }}" class="grid gap-6">
            @csrf
            @method('PUT')

            <!-- Nombre del negocio -->
            <div>
                <label for="name" class="form-label">
                    Nombre del negocio <span class="text-accent-primary">*</span>
                </label>
                <input
                    type="text"
                    name="name"
                    id="name"
                    value="{{ old('name', $business->name) }}"
                    required
                    placeholder="Ej: Running Team Palermo"
                    class="form-input"
                >
                @error('name')
                    <span class="text-xs text-accent-primary mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <!-- Slug (solo lectura) -->
            <div>
                <label for="slug" class="form-label">
                    URL (Slug)
                </label>
                <input
                    type="text"
                    id="slug"
                    value="{{ $business->slug }}"
                    disabled
                    class="form-input opacity-60 cursor-not-allowed font-mono"
                >
                <span class="text-xs text-text-muted mt-1 block">
                    El slug no puede ser modificado después de la creación.
                </span>
            </div>

            <!-- Descripción -->
            <div>
                <label for="description" class="form-label">
                    Descripción
                </label>
                <textarea
                    name="description"
                    id="description"
                    rows="3"
                    placeholder="Describe tu negocio de coaching..."
                    class="form-input resize-y"
                >{{ old('description', $business->description) }}</textarea>
                @error('description')
                    <span class="text-xs text-accent-primary mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <!-- Nivel objetivo -->
            <div>
                <label for="level" class="form-label">
                    Nivel objetivo <span class="text-accent-primary">*</span>
                </label>
                <select
                    name="level"
                    id="level"
                    required
                    class="form-select"
                >
                    <option value="beginner" {{ old('level', $business->level) === 'beginner' ? 'selected' : '' }}>Principiante</option>
                    <option value="intermediate" {{ old('level', $business->level) === 'intermediate' ? 'selected' : '' }}>Intermedio</option>
                    <option value="advanced" {{ old('level', $business->level) === 'advanced' ? 'selected' : '' }}>Avanzado</option>
                </select>
                @error('level')
                    <span class="text-xs text-accent-primary mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <!-- Estado activo/inactivo -->
            <div>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input
                        type="checkbox"
                        name="is_active"
                        value="1"
                        {{ old('is_active', $business->is_active) ? 'checked' : '' }}
                        class="w-5 h-5 cursor-pointer min-w-touch min-h-touch"
                    >
                    <span class="text-responsive-sm font-medium">Negocio activo</span>
                </label>
                <span class="text-xs text-text-muted mt-1 block ml-7">
                    Los negocios inactivos no pueden recibir nuevos alumnos.
                </span>
            </div>

            <!-- Horarios (placeholder) -->
            <div>
                <label class="form-label">
                    Horarios de entrenamientos
                </label>
                <div class="p-4 rounded-btn bg-bg-main/50 border border-dashed border-border-subtle text-center">
                    <p class="text-sm text-text-muted">
                        La configuración de horarios estará disponible en la próxima versión.
                    </p>
                </div>
            </div>

            <!-- Botones -->
            <div class="flex flex-col sm:flex-row gap-3 mt-4">
                <button
                    type="submit"
                    class="btn-primary min-h-touch w-full sm:w-auto justify-center">
                    Guardar Cambios
                </button>
                <a
                    href="{{ businessRoute('coach.business.show') }}"
                    class="btn-ghost min-h-touch w-full sm:w-auto justify-center">
                    Cancelar
                </a>
            </div>
        </form>
    </x-card>
</x-app-layout>
