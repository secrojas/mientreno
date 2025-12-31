<x-app-layout>
    <div class="max-w-3xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('races.index') }}" class="inline-flex items-center gap-1.5 text-sm text-text-muted hover:text-text-main transition-colors">
                ← Volver
            </a>
        </div>

        <h1 class="font-display text-responsive-2xl mb-2">
            Editar Carrera
        </h1>
        <p class="text-responsive-sm text-text-muted mb-6">
            {{ $race->name }} · {{ $race->date->format('d/m/Y') }}
        </p>

        <x-card>
            <form method="POST" action="{{ route('races.update', $race) }}">
                @csrf
                @method('PUT')

                <div class="grid gap-4">
                    <!-- Nombre -->
                    <div>
                        <label class="form-label">Nombre de la carrera *</label>
                        <input type="text" name="name" value="{{ old('name', $race->name) }}" required class="form-input">
                        @error('name')
                            <span class="text-xs text-accent-primary">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Distancia -->
                        <div>
                            <label class="form-label">Distancia (km) *</label>
                            <input type="number" name="distance" value="{{ old('distance', $race->distance) }}" required step="0.01" min="0.1" class="form-input">
                            @error('distance')
                                <span class="text-xs text-accent-primary">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Fecha -->
                        <div>
                            <label class="form-label">Fecha *</label>
                            <input type="date" name="date" value="{{ old('date', $race->date->format('Y-m-d')) }}" required class="form-input">
                            @error('date')
                                <span class="text-xs text-accent-primary">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Ubicación -->
                    <div>
                        <label class="form-label">Ubicación</label>
                        <input type="text" name="location" value="{{ old('location', $race->location) }}" class="form-input">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Tiempo objetivo -->
                        <div>
                            <label class="form-label">Tiempo objetivo (segundos)</label>
                            <input type="number" name="target_time" value="{{ old('target_time', $race->target_time) }}" min="1" class="form-input">
                        </div>

                        <!-- Tiempo real -->
                        <div>
                            <label class="form-label">Tiempo real (segundos)</label>
                            <input type="number" name="actual_time" value="{{ old('actual_time', $race->actual_time) }}" min="1" class="form-input">
                        </div>
                    </div>

                    <!-- Posición -->
                    <div>
                        <label class="form-label">Posición general</label>
                        <input type="number" name="position" value="{{ old('position', $race->position) }}" min="1" class="form-input">
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="form-label">Estado *</label>
                        <select name="status" required class="form-select">
                            @foreach($statusOptions as $value => $label)
                                <option value="{{ $value }}" {{ old('status', $race->status) == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Notas -->
                    <div>
                        <label class="form-label">Notas</label>
                        <textarea name="notes" rows="3" class="form-input resize-y">{{ old('notes', $race->notes) }}</textarea>
                    </div>

                    <!-- Submit -->
                    <button type="submit" class="btn-primary w-full justify-center min-h-touch">
                        Actualizar Carrera
                    </button>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>
