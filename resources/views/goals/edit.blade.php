<x-app-layout>
    <div style="max-width:700px;margin:0 auto;">
        <div style="margin-bottom:1.5rem;">
            <a href="{{ route('goals.index') }}" style="display:inline-flex;align-items:center;gap:.4rem;font-size:.85rem;color:var(--text-muted);">
                ← Volver
            </a>
        </div>

        <h1 style="font-family:'Space Grotesk',system-ui,sans-serif;font-size:1.6rem;margin-bottom:.5rem;">
            Editar Objetivo
        </h1>
        <p style="font-size:.9rem;color:var(--text-muted);margin-bottom:1.5rem;">
            {{ $goal->title }}
        </p>

        <x-card>
            <form method="POST" action="{{ route('goals.update', $goal) }}">
                @csrf
                @method('PUT')

                <div style="display:grid;gap:1rem;">
                    <!-- Título -->
                    <div>
                        <label style="display:block;font-size:.85rem;margin-bottom:.4rem;">Título *</label>
                        <input type="text" name="title" value="{{ old('title', $goal->title) }}" required
                            style="width:100%;padding:.6rem;background:rgba(5,8,20,.9);border:1px solid var(--border-subtle);border-radius:.6rem;color:var(--text-main);">
                        @error('title')
                            <span style="color:#ff6b6b;font-size:.8rem;">{{ $message }}</span>
                        @enderror
                    </div>

                    @php
                        // Extraer valores del target_value JSON para pre-llenar los campos
                        $targetValue = $goal->target_value;

                        // Race values
                        $raceTime = $targetValue['time'] ?? 0;
                        $raceHours = floor($raceTime / 3600);
                        $raceMinutes = floor(($raceTime % 3600) / 60);
                        $raceSeconds = $raceTime % 60;

                        // Distance values
                        $distanceValue = $targetValue['distance'] ?? 50;
                        $distancePeriod = $targetValue['period'] ?? 'week';

                        // Pace values
                        $paceValue = $targetValue['pace'] ?? 300;
                        $paceMinutes = floor($paceValue / 60);
                        $paceSeconds = $paceValue % 60;

                        // Frequency values
                        $frequencySessions = $targetValue['sessions'] ?? 4;
                        $frequencyPeriod = $targetValue['period'] ?? 'week';
                    @endphp

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                        <!-- Tipo -->
                        <div>
                            <label style="display:block;font-size:.85rem;margin-bottom:.4rem;">Tipo *</label>
                            <select name="type" id="goal-type" required style="width:100%;padding:.6rem;background:rgba(5,8,20,.9);border:1px solid var(--border-subtle);border-radius:.6rem;color:var(--text-main);">
                                @foreach($typeOptions as $value => $label)
                                    <option value="{{ $value }}" {{ old('type', $goal->type) == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Status -->
                        <div>
                            <label style="display:block;font-size:.85rem;margin-bottom:.4rem;">Estado *</label>
                            <select name="status" required style="width:100%;padding:.6rem;background:rgba(5,8,20,.9);border:1px solid var(--border-subtle);border-radius:.6rem;color:var(--text-main);">
                                @foreach($statusOptions as $value => $label)
                                    <option value="{{ $value }}" {{ old('status', $goal->status) == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Campos dinámicos según tipo de objetivo -->
                    <div id="dynamic-fields">
                        <!-- Race type -->
                        <div id="fields-race" class="goal-type-fields" style="display:none;">
                            <label style="display:block;font-size:.85rem;margin-bottom:.4rem;">Tiempo objetivo *</label>
                            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:.75rem;">
                                <div>
                                    <label style="display:block;font-size:.75rem;color:var(--text-muted);margin-bottom:.25rem;">Horas</label>
                                    <input type="number" id="race-hours" min="0" max="10" value="{{ $raceHours }}"
                                        style="width:100%;padding:.6rem;background:rgba(5,8,20,.9);border:1px solid var(--border-subtle);border-radius:.6rem;color:var(--text-main);">
                                </div>
                                <div>
                                    <label style="display:block;font-size:.75rem;color:var(--text-muted);margin-bottom:.25rem;">Minutos</label>
                                    <input type="number" id="race-minutes" min="0" max="59" value="{{ $raceMinutes }}"
                                        style="width:100%;padding:.6rem;background:rgba(5,8,20,.9);border:1px solid var(--border-subtle);border-radius:.6rem;color:var(--text-main);">
                                </div>
                                <div>
                                    <label style="display:block;font-size:.75rem;color:var(--text-muted);margin-bottom:.25rem;">Segundos</label>
                                    <input type="number" id="race-seconds" min="0" max="59" value="{{ $raceSeconds }}"
                                        style="width:100%;padding:.6rem;background:rgba(5,8,20,.9);border:1px solid var(--border-subtle);border-radius:.6rem;color:var(--text-main);">
                                </div>
                            </div>
                            <small style="font-size:.75rem;color:var(--text-muted);">Ej: 0h 45m 0s para una meta de 45 minutos</small>
                        </div>

                        <!-- Distance type -->
                        <div id="fields-distance" class="goal-type-fields" style="display:none;">
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem;">
                                <div>
                                    <label style="display:block;font-size:.85rem;margin-bottom:.4rem;">Distancia (km) *</label>
                                    <input type="number" id="distance-value" min="1" step="0.1" value="{{ $distanceValue }}"
                                        style="width:100%;padding:.6rem;background:rgba(5,8,20,.9);border:1px solid var(--border-subtle);border-radius:.6rem;color:var(--text-main);">
                                </div>
                                <div>
                                    <label style="display:block;font-size:.85rem;margin-bottom:.4rem;">Período *</label>
                                    <select id="distance-period" style="width:100%;padding:.6rem;background:rgba(5,8,20,.9);border:1px solid var(--border-subtle);border-radius:.6rem;color:var(--text-main);">
                                        <option value="week" {{ $distancePeriod == 'week' ? 'selected' : '' }}>Por semana</option>
                                        <option value="month" {{ $distancePeriod == 'month' ? 'selected' : '' }}>Por mes</option>
                                    </select>
                                </div>
                            </div>
                            <small style="font-size:.75rem;color:var(--text-muted);">Ej: 50 km por semana</small>
                        </div>

                        <!-- Pace type -->
                        <div id="fields-pace" class="goal-type-fields" style="display:none;">
                            <label style="display:block;font-size:.85rem;margin-bottom:.4rem;">Pace objetivo (min/km) *</label>
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem;">
                                <div>
                                    <label style="display:block;font-size:.75rem;color:var(--text-muted);margin-bottom:.25rem;">Minutos</label>
                                    <input type="number" id="pace-minutes" min="3" max="15" value="{{ $paceMinutes }}"
                                        style="width:100%;padding:.6rem;background:rgba(5,8,20,.9);border:1px solid var(--border-subtle);border-radius:.6rem;color:var(--text-main);">
                                </div>
                                <div>
                                    <label style="display:block;font-size:.75rem;color:var(--text-muted);margin-bottom:.25rem;">Segundos</label>
                                    <input type="number" id="pace-seconds" min="0" max="59" value="{{ $paceSeconds }}"
                                        style="width:100%;padding:.6rem;background:rgba(5,8,20,.9);border:1px solid var(--border-subtle);border-radius:.6rem;color:var(--text-main);">
                                </div>
                            </div>
                            <small style="font-size:.75rem;color:var(--text-muted);">Ej: 5m 0s = pace de 5:00/km</small>
                        </div>

                        <!-- Frequency type -->
                        <div id="fields-frequency" class="goal-type-fields" style="display:none;">
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem;">
                                <div>
                                    <label style="display:block;font-size:.85rem;margin-bottom:.4rem;">Sesiones *</label>
                                    <input type="number" id="frequency-sessions" min="1" max="30" value="{{ $frequencySessions }}"
                                        style="width:100%;padding:.6rem;background:rgba(5,8,20,.9);border:1px solid var(--border-subtle);border-radius:.6rem;color:var(--text-main);">
                                </div>
                                <div>
                                    <label style="display:block;font-size:.85rem;margin-bottom:.4rem;">Período *</label>
                                    <select id="frequency-period" style="width:100%;padding:.6rem;background:rgba(5,8,20,.9);border:1px solid var(--border-subtle);border-radius:.6rem;color:var(--text-main);">
                                        <option value="week" {{ $frequencyPeriod == 'week' ? 'selected' : '' }}>Por semana</option>
                                        <option value="month" {{ $frequencyPeriod == 'month' ? 'selected' : '' }}>Por mes</option>
                                    </select>
                                </div>
                            </div>
                            <small style="font-size:.75rem;color:var(--text-muted);">Ej: 4 sesiones por semana</small>
                        </div>
                    </div>

                    <!-- Hidden field que contiene el JSON -->
                    <input type="hidden" name="target_value" id="target_value_json" value="{{ old('target_value', json_encode($goal->target_value)) }}">
                    @error('target_value')
                        <span style="color:#ff6b6b;font-size:.8rem;">{{ $message }}</span>
                    @enderror

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                        <!-- Fecha inicio -->
                        <div>
                            <label style="display:block;font-size:.85rem;margin-bottom:.4rem;">Fecha inicio</label>
                            <input type="date" name="start_date" value="{{ old('start_date', $goal->start_date?->format('Y-m-d')) }}"
                                style="width:100%;padding:.6rem;background:rgba(5,8,20,.9);border:1px solid var(--border-subtle);border-radius:.6rem;color:var(--text-main);">
                        </div>

                        <!-- Fecha límite -->
                        <div>
                            <label style="display:block;font-size:.85rem;margin-bottom:.4rem;">Fecha límite</label>
                            <input type="date" name="target_date" value="{{ old('target_date', $goal->target_date?->format('Y-m-d')) }}"
                                style="width:100%;padding:.6rem;background:rgba(5,8,20,.9);border:1px solid var(--border-subtle);border-radius:.6rem;color:var(--text-main);">
                        </div>
                    </div>

                    <!-- Carrera asociada -->
                    @if($upcomingRaces->count() > 0)
                        <div>
                            <label style="display:block;font-size:.85rem;margin-bottom:.4rem;">Carrera asociada</label>
                            <select name="race_id" style="width:100%;padding:.6rem;background:rgba(5,8,20,.9);border:1px solid var(--border-subtle);border-radius:.6rem;color:var(--text-main);">
                                <option value="">Ninguna</option>
                                @foreach($upcomingRaces as $race)
                                    <option value="{{ $race->id }}" {{ old('race_id', $goal->race_id) == $race->id ? 'selected' : '' }}>
                                        {{ $race->name }} ({{ $race->date->format('d/m/Y') }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <!-- Descripción -->
                    <div>
                        <label style="display:block;font-size:.85rem;margin-bottom:.4rem;">Descripción</label>
                        <textarea name="description" rows="3"
                            style="width:100%;padding:.6rem;background:rgba(5,8,20,.9);border:1px solid var(--border-subtle);border-radius:.6rem;color:var(--text-main);resize:vertical;">{{ old('description', $goal->description) }}</textarea>
                    </div>

                    <!-- Submit -->
                    <button type="submit" class="btn-primary" style="width:100%;justify-content:center;">
                        Actualizar Objetivo
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
                    fieldset.style.display = 'none';
                });

                // Mostrar solo el conjunto de campos del tipo seleccionado
                const activeFieldset = document.getElementById(`fields-${selectedType}`);
                if (activeFieldset) {
                    activeFieldset.style.display = 'block';
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
