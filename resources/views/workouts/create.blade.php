<x-app-layout>
    <div class="max-w-3xl">
        <div class="mb-6">
            <div class="flex items-center gap-2 mb-2">
                <a href="{{ route('workouts.index') }}" class="text-text-muted hover:text-text-main inline-flex items-center gap-1.5 text-sm transition-colors">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                        <path d="M19 12H5M12 19l-7-7 7-7"/>
                    </svg>
                    Volver
                </a>
            </div>
            <h1 class="font-display text-responsive-2xl mb-1">
                Nuevo Entrenamiento
            </h1>
            <p class="text-responsive-sm text-text-muted">
                Registrá los datos de tu sesión de running.
            </p>
        </div>

        <form method="POST" action="{{ route('workouts.store') }}" class="card p-responsive grid gap-5">
            @csrf

            @if ($errors->any())
                <div class="px-4 py-3 bg-accent-primary/10 border border-accent-primary/30 rounded-btn text-sm text-red-400">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <!-- Fecha, Tipo y Estado -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="date" class="form-label">Fecha *</label>
                    <input id="date" name="date" type="date" required
                           value="{{ old('date', now()->format('Y-m-d')) }}"
                           class="form-input">
                </div>

                <div>
                    <label for="type" class="form-label">Tipo de Entrenamiento *</label>
                    <select id="type" name="type" required class="form-select">
                        <option value="">Seleccionar...</option>
                        @foreach($types as $key => $label)
                            <option value="{{ $key }}" {{ old('type') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="status" class="form-label">Estado *</label>
                    <select id="status" name="status" required class="form-select">
                        <option value="completed" {{ old('status', 'completed') === 'completed' ? 'selected' : '' }}>Completado</option>
                        <option value="planned" {{ old('status') === 'planned' ? 'selected' : '' }}>Planificado</option>
                    </select>
                    <small class="text-xs text-text-muted block mt-1">
                        Planificado = para cargar después
                    </small>
                </div>
            </div>

            <!-- Distancia y Duración -->
            <div class="grid grid-cols-1 lg:grid-cols-[1fr_2fr] gap-4">
                <div>
                    <label for="distance" class="form-label">Distancia (km) *</label>
                    <input id="distance" name="distance" type="number"
                           step="0.01" min="0" max="999" required
                           value="{{ old('distance') }}" placeholder="10.5"
                           class="form-input">
                    @error('distance')
                        <span class="text-xs text-accent-primary">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="form-label">Duración *</label>
                    <div class="grid grid-cols-3 gap-2">
                        <div>
                            <input id="hours" type="number" min="0" max="23"
                                   value="{{ old('hours', 0) }}" placeholder="HH"
                                   class="form-input text-center">
                            <div class="text-xs text-text-muted text-center mt-1">horas</div>
                        </div>
                        <div>
                            <input id="minutes" type="number" min="0" max="59"
                                   value="{{ old('minutes', 0) }}" placeholder="MM"
                                   class="form-input text-center">
                            <div class="text-xs text-text-muted text-center mt-1">minutos</div>
                        </div>
                        <div>
                            <input id="seconds" type="number" min="0" max="59"
                                   value="{{ old('seconds', 0) }}" placeholder="SS"
                                   class="form-input text-center">
                            <div class="text-xs text-text-muted text-center mt-1">segundos</div>
                        </div>
                    </div>
                    <input type="hidden" id="duration" name="duration" value="{{ old('duration', 0) }}">
                </div>
            </div>

            <!-- FC y Desnivel -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="avg_heart_rate" class="form-label">FC Promedio (bpm)</label>
                    <input id="avg_heart_rate" name="avg_heart_rate" type="number"
                           min="40" max="250" value="{{ old('avg_heart_rate') }}"
                           placeholder="Opcional" class="form-input">
                </div>

                <div>
                    <label for="elevation_gain" class="form-label">Desnivel + (m)</label>
                    <input id="elevation_gain" name="elevation_gain" type="number"
                           min="0" value="{{ old('elevation_gain') }}"
                           placeholder="Opcional" class="form-input">
                </div>
            </div>

            <!-- Dificultad -->
            <div>
                <label for="difficulty" class="form-label mb-2">Dificultad Percibida *</label>
                <div class="flex gap-2">
                    @for($i = 1; $i <= 5; $i++)
                        <label class="flex-1 cursor-pointer min-h-touch">
                            <input type="radio" name="difficulty" value="{{ $i }}" required
                                   {{ old('difficulty', 3) == $i ? 'checked' : '' }}
                                   class="difficulty-radio hidden">
                            <div class="difficulty-option px-3 py-2.5 rounded-btn border border-border-subtle bg-bg-sidebar
                                        text-center text-sm transition-all hover:bg-border-subtle"
                                 data-value="{{ $i }}">
                                {{ $i }}
                            </div>
                        </label>
                    @endfor
                </div>
                <div class="text-xs text-text-muted mt-2 flex justify-between">
                    <span>1 = Muy fácil</span>
                    <span>5 = Muy difícil</span>
                </div>
            </div>

            <!-- Notas -->
            <div>
                <label for="notes" class="form-label">Notas</label>
                <textarea id="notes" name="notes" rows="4"
                          placeholder="Sensaciones, clima, recorrido..."
                          class="form-input resize-y">{{ old('notes') }}</textarea>
            </div>

            <!-- Carrera asociada -->
            @if($upcomingRaces->count() > 0)
                <div>
                    <label for="race_id" class="form-label">¿Es para una carrera específica?</label>
                    <select id="race_id" name="race_id" class="form-select">
                        <option value="">Ninguna (entrenamiento general)</option>
                        @foreach($upcomingRaces as $race)
                            <option value="{{ $race->id }}" {{ old('race_id') == $race->id ? 'selected' : '' }}>
                                {{ $race->name }} - {{ $race->distance }}km ({{ $race->date->format('d/m/Y') }})
                            </option>
                        @endforeach
                    </select>
                    <small class="text-xs text-text-muted block mt-1">
                        Vinculá este entreno a una carrera próxima para mejor seguimiento.
                    </small>
                </div>
            @endif

            <!-- Botones -->
            <div class="flex flex-col sm:flex-row gap-3 mt-2">
                <button type="submit" class="btn-primary flex-1 justify-center py-3">
                    Guardar Entrenamiento
                </button>
                <a href="{{ route('workouts.index') }}" class="btn-ghost py-3 justify-center">
                    Cancelar
                </a>
            </div>
        </form>
    </div>

    <script>
        // Calcular duración total en segundos
        function updateDuration() {
            const hours = parseInt(document.getElementById('hours').value) || 0;
            const minutes = parseInt(document.getElementById('minutes').value) || 0;
            const seconds = parseInt(document.getElementById('seconds').value) || 0;
            const total = (hours * 3600) + (minutes * 60) + seconds;
            document.getElementById('duration').value = total;
        }

        document.getElementById('hours').addEventListener('input', updateDuration);
        document.getElementById('minutes').addEventListener('input', updateDuration);
        document.getElementById('seconds').addEventListener('input', updateDuration);

        // UI para dificultad
        document.querySelectorAll('.difficulty-option').forEach(option => {
            const input = option.parentElement.querySelector('input');

            if (input.checked) {
                option.classList.add('!border-accent-primary', '!bg-accent-primary/10');
            }

            option.addEventListener('click', () => {
                document.querySelectorAll('.difficulty-option').forEach(opt => {
                    opt.classList.remove('!border-accent-primary', '!bg-accent-primary/10');
                });
                option.classList.add('!border-accent-primary', '!bg-accent-primary/10');
            });
        });

        // Manejar campos opcionales según el estado
        const statusSelect = document.getElementById('status');
        const difficultyRadios = document.querySelectorAll('input[name="difficulty"]');

        function updateRequiredFields() {
            const isPlanned = statusSelect.value === 'planned';

            // Hacer campos opcionales si es planificado
            difficultyRadios.forEach(radio => {
                radio.required = !isPlanned;
            });

            // Mostrar/ocultar indicador de opcional
            const difficultyLabel = difficultyRadios[0].closest('div').previousElementSibling;
            if (isPlanned) {
                if (!difficultyLabel.querySelector('.optional-indicator')) {
                    difficultyLabel.innerHTML = difficultyLabel.innerHTML.replace(' *', '');
                    difficultyLabel.innerHTML += ' <span class="optional-indicator text-text-muted font-normal">(opcional)</span>';
                }
            } else {
                const indicator = difficultyLabel.querySelector('.optional-indicator');
                if (indicator) {
                    indicator.remove();
                    if (!difficultyLabel.textContent.includes('*')) {
                        difficultyLabel.innerHTML = difficultyLabel.innerHTML.replace('Dificultad Percibida', 'Dificultad Percibida *');
                    }
                }
            }
        }

        statusSelect.addEventListener('change', updateRequiredFields);
        updateRequiredFields();

        // Inicializar duración
        updateDuration();
    </script>
</x-app-layout>
