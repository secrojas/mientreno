<x-app-layout>
    <header class="flex flex-col gap-1 mb-6">
        <h1 class="font-display text-responsive-2xl">Crear Mi Negocio</h1>
        <p class="text-responsive-sm text-text-muted">Configurá tu negocio de coaching para empezar a gestionar alumnos.</p>
    </header>

    <x-card>
        <form method="POST" action="{{ route('coach.business.store') }}" class="grid gap-6">
            @csrf

            <!-- Nombre del negocio -->
            <div>
                <label for="name" class="form-label">
                    Nombre del negocio <span class="text-accent-primary">*</span>
                </label>
                <input
                    type="text"
                    name="name"
                    id="name"
                    value="{{ old('name') }}"
                    required
                    placeholder="Ej: Running Team Palermo"
                    class="form-input"
                >
                @error('name')
                    <span class="text-xs text-accent-primary mt-1 block">{{ $message }}</span>
                @enderror
                <span class="text-xs text-text-muted mt-1 block">
                    Este nombre aparecerá en la URL y será visible para tus alumnos.
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
                >{{ old('description') }}</textarea>
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
                    <option value="">Seleccionar nivel</option>
                    <option value="beginner" {{ old('level') === 'beginner' ? 'selected' : '' }}>Principiante</option>
                    <option value="intermediate" {{ old('level') === 'intermediate' ? 'selected' : '' }}>Intermedio</option>
                    <option value="advanced" {{ old('level') === 'advanced' ? 'selected' : '' }}>Avanzado</option>
                </select>
                @error('level')
                    <span class="text-xs text-accent-primary mt-1 block">{{ $message }}</span>
                @enderror
                <span class="text-xs text-text-muted mt-1 block">
                    Nivel de experiencia al que apuntás con tus entrenamientos.
                </span>
            </div>

            <!-- Horarios (opcional por ahora) -->
            <div>
                <label class="form-label">
                    Horarios de entrenamientos
                </label>
                <div id="schedule-container" class="grid gap-3">
                    <!-- Los horarios se pueden agregar dinámicamente en una versión futura -->
                    <div class="p-4 rounded-btn bg-bg-main/50 border border-dashed border-border-subtle text-center">
                        <p class="text-sm text-text-muted">
                            La configuración de horarios estará disponible después de crear el negocio.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Botones -->
            <div class="flex gap-3 mt-4">
                <button
                    type="submit"
                    class="btn-primary min-h-touch">
                    Crear Negocio
                </button>
            </div>
        </form>
    </x-card>
</x-app-layout>
