<x-app-layout>
    <div class="max-w-3xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('goals.index') }}" class="inline-flex items-center gap-1.5 text-sm text-text-muted hover:text-text-main transition-colors">
                ← Volver
            </a>
        </div>

        <h1 class="font-display text-responsive-2xl mb-2">
            Nuevo Objetivo
        </h1>
        <p class="text-responsive-sm text-text-muted mb-6">
            Define una nueva meta de entrenamiento.
        </p>

        <x-card>
            <form method="POST" action="{{ route('goals.store') }}">
                @csrf

                <div class="grid gap-4">
                    <!-- Título -->
                    <div>
                        <label class="form-label">Título *</label>
                        <input type="text" name="title" value="{{ old('title') }}" required
                            class="form-input"
                            placeholder="Correr 10K sub 50 minutos">
                        @error('title')
                            <span class="text-xs text-accent-primary">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Tipo -->
                        <div>
                            <label class="form-label">Tipo *</label>
                            <select name="type" id="goal-type" required class="form-select">
                                @foreach($typeOptions as $value => $label)
                                    <option value="{{ $value }}" {{ old('type') == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="form-label">Estado *</label>
                            <select name="status" required class="form-select">
                                @foreach($statusOptions as $value => $label)
                                    <option value="{{ $value }}" {{ old('status', 'active') == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Campos dinámicos según tipo de objetivo -->
                    <div id="dynamic-fields">
                        <!-- Race type -->
                        <div id="fields-race" class="goal-type-fields hidden">
                            <label class="form-label">Tiempo objetivo *</label>
                            <div class="grid grid-cols-3 gap-3">
                                <div>
                                    <label class="text-xs text-text-muted block mb-1">Horas</label>
                                    <input type="number" id="race-hours" min="0" max="10" value="0" class="form-input text-center">
                                </div>
                                <div>
                                    <label class="text-xs text-text-muted block mb-1">Minutos</label>
                                    <input type="number" id="race-minutes" min="0" max="59" value="45" class="form-input text-center">
                                </div>
                                <div>
                                    <label class="text-xs text-text-muted block mb-1">Segundos</label>
                                    <input type="number" id="race-seconds" min="0" max="59" value="0" class="form-input text-center">
                                </div>
                            </div>
                            <small class="text-xs text-text-muted block mt-1">Ej: 0h 45m 0s para una meta de 45 minutos</small>
                        </div>

                        <!-- Distance type -->
                        <div id="fields-distance" class="goal-type-fields hidden">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="form-label">Distancia (km) *</label>
                                    <input type="number" id="distance-value" min="1" step="0.1" value="50" class="form-input">
                                </div>
                                <div>
                                    <label class="form-label">Período *</label>
                                    <select id="distance-period" class="form-select">
                                        <option value="week">Por semana</option>
                                        <option value="month">Por mes</option>
                                    </select>
                                </div>
                            </div>
                            <small class="text-xs text-text-muted block mt-1">Ej: 50 km por semana</small>
                        </div>

                        <!-- Pace type -->
                        <div id="fields-pace" class="goal-type-fields hidden">
                            <label class="form-label">Pace objetivo (min/km) *</label>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="text-xs text-text-muted block mb-1">Minutos</label>
                                    <input type="number" id="pace-minutes" min="3" max="15" value="5" class="form-input text-center">
                                </div>
                                <div>
                                    <label class="text-xs text-text-muted block mb-1">Segundos</label>
                                    <input type="number" id="pace-seconds" min="0" max="59" value="0" class="form-input text-center">
                                </div>
                            </div>
                            <small class="text-xs text-text-muted block mt-1">Ej: 5m 0s = pace de 5:00/km</small>
                        </div>

                        <!-- Frequency type -->
                        <div id="fields-frequency" class="goal-type-fields hidden">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="form-label">Sesiones *</label>
                                    <input type="number" id="frequency-sessions" min="1" max="30" value="4" class="form-input">
                                </div>
                                <div>
                                    <label class="form-label">Período *</label>
                                    <select id="frequency-period" class="form-select">
                                        <option value="week">Por semana</option>
                                        <option value="month">Por mes</option>
                                    </select>
                                </div>
                            </div>
                            <small class="text-xs text-text-muted block mt-1">Ej: 4 sesiones por semana</small>
                        </div>
                    </div>

                    <!-- Hidden field que contiene el JSON -->
                    <input type="hidden" name="target_value" id="target_value_json" value="{{ old('target_value', '{}') }}">
                    @error('target_value')
                        <span class="text-xs text-accent-primary">{{ $message }}</span>
                    @enderror

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Fecha inicio -->
                        <div>
                            <label class="form-label">Fecha inicio</label>
                            <input type="date" name="start_date" value="{{ old('start_date') }}" class="form-input">
                        </div>

                        <!-- Fecha límite -->
                        <div>
                            <label class="form-label">Fecha límite</label>
                            <input type="date" name="target_date" value="{{ old('target_date') }}" class="form-input">
                        </div>
                    </div>

                    <!-- Carrera asociada -->
                    @if($upcomingRaces->count() > 0)
                        <div>
                            <label class="form-label">Carrera asociada</label>
                            <select name="race_id" class="form-select">
                                <option value="">Ninguna</option>
                                @foreach($upcomingRaces as $race)
                                    <option value="{{ $race->id }}" {{ old('race_id') == $race->id ? 'selected' : '' }}>
                                        {{ $race->name }} ({{ $race->date->format('d/m/Y') }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <!-- Descripción -->
                    <div>
                        <label class="form-label">Descripción</label>
                        <textarea name="description" rows="3" class="form-input resize-y">{{ old('description') }}</textarea>
                    </div>

                    <!-- Submit -->
                    <button type="submit" class="btn-primary w-full justify-center min-h-touch">
                        Crear Objetivo
                    </button>
                </div>
            </form>
        </x-card>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const goalTypeSelect = document.getElementById('goal-type');
            const allFieldSets = document.querySelectorAll('.goal-type-fields');
            const targetValueInput = document.getElementById('target_value_json');

            // Función para mostrar/ocultar campos según el tipo
            function updateFields() {
                const selectedType = goalTypeSelect.value;

                // Ocultar todos los campos
                allFieldSets.forEach(fieldset => {
                    fieldset.classList.add('hidden');
                });

                // Mostrar solo el conjunto de campos del tipo seleccionado
                const activeFieldset = document.getElementById(`fields-${selectedType}`);
                if (activeFieldset) {
                    activeFieldset.classList.remove('hidden');
                }

                // Actualizar el JSON
                updateJSON();
            }

            // Función para generar el JSON según el tipo de goal
            function updateJSON() {
                const type = goalTypeSelect.value;
                let targetValue = {};

                switch(type) {
                    case 'race':
                        const hours = parseInt(document.getElementById('race-hours').value) || 0;
                        const minutes = parseInt(document.getElementById('race-minutes').value) || 0;
                        const seconds = parseInt(document.getElementById('race-seconds').value) || 0;
                        targetValue = { time: hours * 3600 + minutes * 60 + seconds };
                        break;

                    case 'distance':
                        targetValue = {
                            distance: parseFloat(document.getElementById('distance-value').value) || 0,
                            period: document.getElementById('distance-period').value
                        };
                        break;

                    case 'pace':
                        const paceMin = parseInt(document.getElementById('pace-minutes').value) || 0;
                        const paceSec = parseInt(document.getElementById('pace-seconds').value) || 0;
                        targetValue = { pace: paceMin * 60 + paceSec };
                        break;

                    case 'frequency':
                        targetValue = {
                            sessions: parseInt(document.getElementById('frequency-sessions').value) || 0,
                            period: document.getElementById('frequency-period').value
                        };
                        break;
                }

                targetValueInput.value = JSON.stringify(targetValue);
            }

            // Event listeners
            goalTypeSelect.addEventListener('change', updateFields);

            // Event listeners para todos los inputs dinámicos
            document.querySelectorAll('.goal-type-fields input, .goal-type-fields select').forEach(input => {
                input.addEventListener('change', updateJSON);
                input.addEventListener('input', updateJSON);
            });

            // Inicializar
            updateFields();
        });
    </script>
</x-app-layout>
