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
                Marcar como Completado
            </h1>
            <p class="text-responsive-sm text-text-muted">
                {{ $workout->date->format('d/m/Y') }} · {{ $workout->type_label }} · Planificado: {{ $workout->distance }}km
            </p>
        </div>

        <form method="POST" action="{{ route('workouts.mark-completed', $workout) }}" class="card p-responsive grid gap-5">
            @csrf

            @if ($errors->any())
                <div class="px-4 py-3 bg-accent-primary/10 border border-accent-primary/30 rounded-btn text-sm text-red-400">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <!-- Info del entrenamiento planificado -->
            <div class="px-4 py-3 bg-blue-500/10 border border-blue-500/30 rounded-btn text-sm">
                <div class="mb-1.5 font-medium">Entrenamiento Planificado:</div>
                <div class="text-text-muted">
                    <strong>{{ $workout->date->format('d/m/Y') }}</strong> - {{ $workout->type_label }} - {{ $workout->distance }}km
                </div>
            </div>

            <!-- Distancia Real y Duración -->
            <div class="grid grid-cols-1 lg:grid-cols-[1fr_2fr] gap-4">
                <div>
                    <label for="distance" class="form-label">Distancia Real (km) *</label>
                    <input id="distance" name="distance" type="number"
                           step="0.01" min="0" max="999" required
                           value="{{ old('distance', $workout->distance) }}" placeholder="10.5"
                           class="form-input">
                    @error('distance')
                        <span class="text-xs text-accent-primary">{{ $message }}</span>
                    @enderror
                    <small id="distance-diff" class="text-xs block mt-1"></small>
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
                          placeholder="¿Cómo te sentiste? ¿Hubo algo distinto al plan?"
                          class="form-input resize-y">{{ old('notes') }}</textarea>
            </div>

            <!-- Botones -->
            <div class="flex flex-col sm:flex-row gap-3 mt-2">
                <button type="submit" class="btn-primary flex-1 justify-center py-3">
                    Marcar como Completado
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

        // Mostrar diferencia con distancia planificada
        const plannedDistance = {{ $workout->distance }};
        const distanceInput = document.getElementById('distance');
        const distanceDiff = document.getElementById('distance-diff');

        function updateDistanceDiff() {
            const actualDistance = parseFloat(distanceInput.value) || 0;
            const diff = actualDistance - plannedDistance;

            if (diff === 0 || actualDistance === 0) {
                distanceDiff.textContent = '';
                distanceDiff.classList.remove('text-green-500', 'text-yellow-500');
                distanceDiff.classList.add('text-text-muted');
            } else if (diff > 0) {
                distanceDiff.textContent = `+${diff.toFixed(2)}km más que lo planificado`;
                distanceDiff.classList.remove('text-text-muted', 'text-yellow-500');
                distanceDiff.classList.add('text-green-500');
            } else {
                distanceDiff.textContent = `${diff.toFixed(2)}km menos que lo planificado`;
                distanceDiff.classList.remove('text-text-muted', 'text-green-500');
                distanceDiff.classList.add('text-yellow-500');
            }
        }

        distanceInput.addEventListener('input', updateDistanceDiff);
        updateDistanceDiff();

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

        // Inicializar duración
        updateDuration();
    </script>
</x-app-layout>
