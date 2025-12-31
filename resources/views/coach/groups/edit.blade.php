<x-app-layout>
    <div class="max-w-3xl">
        <div class="mb-6">
            <div class="flex items-center gap-2 mb-2">
                <a href="{{ businessRoute('coach.groups.show', ['group' => $group]) }}" class="text-text-muted inline-flex items-center gap-1.5 text-sm hover:text-text-main transition-colors">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                        <path d="M19 12H5M12 19l-7-7 7-7"/>
                    </svg>
                    Volver al Grupo
                </a>
            </div>
            <h1 class="font-display text-responsive-2xl mb-1">
                Editar: {{ $group->name }}
            </h1>
            <p class="text-responsive-sm text-text-muted">
                Actualiza los detalles del grupo
            </p>
        </div>

        <x-card>
            <form method="POST" action="{{ businessRoute('coach.groups.update', ['group' => $group]) }}" class="grid gap-5">
                @csrf
                @method('PUT')

                @if($errors->any())
                    <div class="px-3 py-2.5 bg-accent-primary/10 border border-accent-primary/30 rounded-card text-sm text-red-400">
                        @foreach($errors->all() as $error)
                            <div class="mb-1 last:mb-0">{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <!-- Nombre del grupo -->
                <div>
                    <label for="name" class="form-label">
                        Nombre del Grupo <span class="text-accent-primary">*</span>
                    </label>
                    <input
                        type="text"
                        name="name"
                        id="name"
                        value="{{ old('name', $group->name) }}"
                        required
                        class="form-input"
                    >
                </div>

                <!-- Descripción -->
                <div>
                    <label for="description" class="form-label">
                        Descripción
                    </label>
                    <textarea
                        name="description"
                        id="description"
                        rows="4"
                        class="form-input resize-none"
                    >{{ old('description', $group->description) }}</textarea>
                    <small class="text-xs text-text-muted block mt-1.5">
                        Máximo 1000 caracteres
                    </small>
                </div>

                <!-- Nivel y Max Miembros -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label for="level" class="form-label">
                            Nivel del Grupo <span class="text-accent-primary">*</span>
                        </label>
                        <select
                            name="level"
                            id="level"
                            required
                            class="form-select"
                        >
                            <option value="beginner" {{ old('level', $group->level) === 'beginner' ? 'selected' : '' }}>Principiante</option>
                            <option value="intermediate" {{ old('level', $group->level) === 'intermediate' ? 'selected' : '' }}>Intermedio</option>
                            <option value="advanced" {{ old('level', $group->level) === 'advanced' ? 'selected' : '' }}>Avanzado</option>
                        </select>
                    </div>

                    <div>
                        <label for="max_members" class="form-label">
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
                            class="form-input"
                        >
                        <small class="text-xs text-text-muted block mt-1.5">
                            Hay {{ $group->members->count() }} miembro(s) actualmente
                        </small>
                    </div>
                </div>

                <!-- Estado activo -->
                <div>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input
                            type="checkbox"
                            name="is_active"
                            value="1"
                            {{ old('is_active', $group->is_active) ? 'checked' : '' }}
                            class="w-5 h-5 cursor-pointer min-w-touch min-h-touch"
                        >
                        <span class="text-responsive-sm font-medium">Grupo activo</span>
                    </label>
                    <small class="text-xs text-text-muted block mt-1.5 ml-7">
                        Los grupos inactivos no aparecerán en los listados principales
                    </small>
                </div>

                <!-- Botones -->
                <div class="flex flex-col sm:flex-row gap-3 mt-2">
                    <button
                        type="submit"
                        class="btn-primary flex-1 sm:flex-none min-h-touch justify-center"
                    >
                        Actualizar Grupo
                    </button>
                    <a
                        href="{{ businessRoute('coach.groups.show', ['group' => $group]) }}"
                        class="btn-ghost min-h-touch justify-center"
                    >
                        Cancelar
                    </a>
                </div>
            </form>
        </x-card>

        <!-- Zona de Peligro -->
        <x-card class="mt-6 border-accent-primary/20">
            <h3 class="text-lg font-semibold mb-2 text-red-400">Zona de Peligro</h3>
            <p class="text-sm text-text-muted mb-4">Desactivar el grupo lo ocultará pero conservará todos los datos</p>
            <form method="POST" action="{{ businessRoute('coach.groups.destroy', ['group' => $group]) }}" onsubmit="return confirm('¿Estás seguro de desactivar este grupo? Los miembros no se eliminarán pero el grupo quedará inactivo.')">
                @csrf
                @method('DELETE')
                <button
                    type="submit"
                    class="px-4 py-2.5 bg-accent-primary/10 text-red-400 border border-accent-primary/30 rounded-card font-medium text-sm hover:bg-accent-primary/20 transition-colors min-h-touch"
                >
                    Desactivar Grupo
                </button>
            </form>
        </x-card>
    </div>
</x-app-layout>
