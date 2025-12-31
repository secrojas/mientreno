<x-app-layout>
    <div class="max-w-3xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('races.index') }}" class="inline-flex items-center gap-1.5 text-sm text-text-muted hover:text-text-main transition-colors">
                ← Volver
            </a>
        </div>

        <h1 class="font-display text-responsive-2xl mb-2">
            Nueva Carrera
        </h1>
        <p class="text-responsive-sm text-text-muted mb-6">
            Registra una carrera próxima o pasada.
        </p>

        <x-card>
            <form method="POST" action="{{ route('races.store') }}">
                @csrf

                <div class="grid gap-4">
                    <!-- Nombre -->
                    <div>
                        <label class="form-label">Nombre de la carrera *</label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                            class="form-input"
                            placeholder="Maratón de Buenos Aires">
                        @error('name')
                            <span class="text-xs text-accent-primary">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Distancia -->
                        <div>
                            <label class="form-label">Distancia (km) *</label>
                            <select name="distance" required class="form-select">
                                <option value="">Seleccionar...</option>
                                @foreach($commonDistances as $value => $label)
                                    <option value="{{ $value }}" {{ old('distance') == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                                <option value="custom">Otra distancia</option>
                            </select>
                            @error('distance')
                                <span class="text-xs text-accent-primary">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Fecha -->
                        <div>
                            <label class="form-label">Fecha *</label>
                            <input type="date" name="date" value="{{ old('date') }}" required class="form-input">
                            @error('date')
                                <span class="text-xs text-accent-primary">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Ubicación -->
                    <div>
                        <label class="form-label">Ubicación</label>
                        <input type="text" name="location" value="{{ old('location') }}"
                            class="form-input"
                            placeholder="Buenos Aires, Argentina">
                    </div>

                    <!-- Tiempo objetivo -->
                    <div>
                        <label class="form-label">Tiempo objetivo (en segundos)</label>
                        <input type="number" name="target_time" value="{{ old('target_time') }}" min="1"
                            class="form-input"
                            placeholder="Ej: 3000 (50 minutos)">
                        <small class="text-xs text-text-muted block mt-1">Ejemplo: 3600 = 1 hora, 7200 = 2 horas</small>
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="form-label">Estado *</label>
                        <select name="status" required class="form-select">
                            @foreach($statusOptions as $value => $label)
                                <option value="{{ $value }}" {{ old('status', 'upcoming') == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Notas -->
                    <div>
                        <label class="form-label">Notas</label>
                        <textarea name="notes" rows="3" class="form-input resize-y" placeholder="Notas adicionales...">{{ old('notes') }}</textarea>
                    </div>

                    <!-- Submit -->
                    <button type="submit" class="btn-primary w-full justify-center min-h-touch">
                        Crear Carrera
                    </button>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>
