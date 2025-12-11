<x-app-layout>
    <div style="max-width:720px;">
        <div style="margin-bottom:1.5rem;">
            <div style="display:flex;align-items:center;gap:.5rem;margin-bottom:.5rem;">
                <a href="{{ route('workouts.index') }}" style="color:var(--text-muted);display:inline-flex;align-items:center;gap:.3rem;font-size:.85rem;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px;">
                        <path d="M19 12H5M12 19l-7-7 7-7"/>
                    </svg>
                    Volver
                </a>
            </div>
            <h1 style="font-family:'Space Grotesk',system-ui,sans-serif;font-size:1.6rem;margin-bottom:.3rem;">
                Editar Entrenamiento
            </h1>
            <p style="font-size:.9rem;color:var(--text-muted);">
                {{ $workout->date->format('d/m/Y') }} · {{ $workout->type_label }}
            </p>
        </div>

        <form method="POST" action="{{ route('workouts.update', $workout) }}" style="
            background:rgba(15,23,42,.9);
            border-radius:1rem;
            padding:1.5rem;
            border:1px solid var(--border-subtle);
            display:grid;
            gap:1.25rem;
        ">
            @csrf
            @method('PUT')

            @if ($errors->any())
                <div style="padding:.75rem;background:rgba(255,59,92,.1);border:1px solid rgba(255,59,92,.3);border-radius:.6rem;font-size:.85rem;color:#ff6b6b;">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <!-- Fecha y Tipo -->
            <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:.75rem;">
                <div>
                    <label for="date" style="display:block;font-size:.8rem;margin-bottom:.25rem;font-weight:500;">Fecha *</label>
                    <input
                        id="date"
                        name="date"
                        type="date"
                        required
                        value="{{ old('date', $workout->date->format('Y-m-d')) }}"
                        style="width:100%;padding:.6rem .75rem;border-radius:.6rem;border:1px solid #1F2937;background:#050814;color:var(--text-main);font-size:.9rem;"
                    >
                </div>

                <div>
                    <label for="type" style="display:block;font-size:.8rem;margin-bottom:.25rem;font-weight:500;">Tipo de Entrenamiento *</label>
                    <select
                        id="type"
                        name="type"
                        required
                        style="width:100%;padding:.6rem .75rem;border-radius:.6rem;border:1px solid #1F2937;background:#050814;color:var(--text-main);font-size:.9rem;"
                    >
                        @foreach($types as $key => $label)
                            <option value="{{ $key }}" {{ old('type', $workout->type) === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Distancia y Duración -->
            <div style="display:grid;grid-template-columns:1fr 2fr;gap:.75rem;">
                <div>
                    <label for="distance" style="display:block;font-size:.8rem;margin-bottom:.25rem;font-weight:500;">Distancia (km) *</label>
                    <input
                        id="distance"
                        name="distance"
                        type="number"
                        step="0.01"
                        min="0.1"
                        max="999"
                        required
                        value="{{ old('distance', $workout->distance) }}"
                        placeholder="10.5"
                        style="width:100%;padding:.6rem .75rem;border-radius:.6rem;border:1px solid #1F2937;background:#050814;color:var(--text-main);font-size:.9rem;"
                    >
                </div>

                <div>
                    <label style="display:block;font-size:.8rem;margin-bottom:.25rem;font-weight:500;">Duración *</label>
                    <div style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:.5rem;">
                        <div>
                            <input
                                id="hours"
                                type="number"
                                min="0"
                                max="23"
                                value="{{ old('hours', floor($workout->duration / 3600)) }}"
                                placeholder="HH"
                                style="width:100%;padding:.6rem .5rem;border-radius:.6rem;border:1px solid #1F2937;background:#050814;color:var(--text-main);font-size:.9rem;text-align:center;"
                            >
                            <div style="font-size:.7rem;color:var(--text-muted);text-align:center;margin-top:.2rem;">horas</div>
                        </div>
                        <div>
                            <input
                                id="minutes"
                                type="number"
                                min="0"
                                max="59"
                                value="{{ old('minutes', floor(($workout->duration % 3600) / 60)) }}"
                                placeholder="MM"
                                style="width:100%;padding:.6rem .5rem;border-radius:.6rem;border:1px solid #1F2937;background:#050814;color:var(--text-main);font-size:.9rem;text-align:center;"
                            >
                            <div style="font-size:.7rem;color:var(--text-muted);text-align:center;margin-top:.2rem;">minutos</div>
                        </div>
                        <div>
                            <input
                                id="seconds"
                                type="number"
                                min="0"
                                max="59"
                                value="{{ old('seconds', $workout->duration % 60) }}"
                                placeholder="SS"
                                style="width:100%;padding:.6rem .5rem;border-radius:.6rem;border:1px solid #1F2937;background:#050814;color:var(--text-main);font-size:.9rem;text-align:center;"
                            >
                            <div style="font-size:.7rem;color:var(--text-muted);text-align:center;margin-top:.2rem;">segundos</div>
                        </div>
                    </div>
                    <input type="hidden" id="duration" name="duration" value="{{ old('duration', $workout->duration) }}">
                </div>
            </div>

            <!-- FC y Desnivel -->
            <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:.75rem;">
                <div>
                    <label for="avg_heart_rate" style="display:block;font-size:.8rem;margin-bottom:.25rem;font-weight:500;">FC Promedio (bpm)</label>
                    <input
                        id="avg_heart_rate"
                        name="avg_heart_rate"
                        type="number"
                        min="40"
                        max="250"
                        value="{{ old('avg_heart_rate', $workout->avg_heart_rate) }}"
                        placeholder="Opcional"
                        style="width:100%;padding:.6rem .75rem;border-radius:.6rem;border:1px solid #1F2937;background:#050814;color:var(--text-main);font-size:.9rem;"
                    >
                </div>

                <div>
                    <label for="elevation_gain" style="display:block;font-size:.8rem;margin-bottom:.25rem;font-weight:500;">Desnivel + (m)</label>
                    <input
                        id="elevation_gain"
                        name="elevation_gain"
                        type="number"
                        min="0"
                        value="{{ old('elevation_gain', $workout->elevation_gain) }}"
                        placeholder="Opcional"
                        style="width:100%;padding:.6rem .75rem;border-radius:.6rem;border:1px solid #1F2937;background:#050814;color:var(--text-main);font-size:.9rem;"
                    >
                </div>
            </div>

            <!-- Dificultad -->
            <div>
                <label for="difficulty" style="display:block;font-size:.8rem;margin-bottom:.5rem;font-weight:500;">Dificultad Percibida *</label>
                <div style="display:flex;gap:.5rem;align-items:center;">
                    @for($i = 1; $i <= 5; $i++)
                        <label style="flex:1;cursor:pointer;">
                            <input
                                type="radio"
                                name="difficulty"
                                value="{{ $i }}"
                                required
                                {{ old('difficulty', $workout->difficulty) == $i ? 'checked' : '' }}
                                style="display:none;"
                                class="difficulty-radio"
                            >
                            <div class="difficulty-option" data-value="{{ $i }}" style="
                                padding:.6rem;
                                border-radius:.6rem;
                                border:1px solid #1F2937;
                                background:#050814;
                                text-align:center;
                                font-size:.85rem;
                                transition:all .15s ease-out;
                            ">
                                {{ $i }}
                            </div>
                        </label>
                    @endfor
                </div>
                <div style="font-size:.75rem;color:var(--text-muted);margin-top:.35rem;display:flex;justify-content:space-between;">
                    <span>1 = Muy fácil</span>
                    <span>5 = Muy difícil</span>
                </div>
            </div>

            <!-- Notas -->
            <div>
                <label for="notes" style="display:block;font-size:.8rem;margin-bottom:.25rem;font-weight:500;">Notas</label>
                <textarea
                    id="notes"
                    name="notes"
                    rows="4"
                    placeholder="Sensaciones, clima, recorrido..."
                    style="width:100%;padding:.6rem .75rem;border-radius:.6rem;border:1px solid #1F2937;background:#050814;color:var(--text-main);font-size:.9rem;font-family:inherit;resize:vertical;"
                >{{ old('notes', $workout->notes) }}</textarea>
            </div>

            <!-- Botones -->
            <div style="display:flex;gap:.75rem;margin-top:.5rem;">
                <button type="submit" class="btn-primary" style="flex:1;justify-content:center;padding:.7rem;">
                    Actualizar Entrenamiento
                </button>
                <a href="{{ route('workouts.index') }}" class="btn-ghost" style="padding:.7rem 1.2rem;">
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
                option.style.borderColor = 'var(--accent-primary)';
                option.style.background = 'rgba(255,59,92,.1)';
            }

            option.addEventListener('click', () => {
                document.querySelectorAll('.difficulty-option').forEach(opt => {
                    opt.style.borderColor = '#1F2937';
                    opt.style.background = '#050814';
                });
                option.style.borderColor = 'var(--accent-primary)';
                option.style.background = 'rgba(255,59,92,.1)';
            });
        });

        // Inicializar duración
        updateDuration();
    </script>
</x-app-layout>
