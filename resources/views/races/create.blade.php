<x-app-layout>
    <div style="max-width:700px;margin:0 auto;">
        <div style="margin-bottom:1.5rem;">
            <a href="{{ route('races.index') }}" style="display:inline-flex;align-items:center;gap:.4rem;font-size:.85rem;color:var(--text-muted);">
                ← Volver
            </a>
        </div>

        <h1 style="font-family:'Space Grotesk',system-ui,sans-serif;font-size:1.6rem;margin-bottom:.5rem;">
            Nueva Carrera
        </h1>
        <p style="font-size:.9rem;color:var(--text-muted);margin-bottom:1.5rem;">
            Registra una carrera próxima o pasada.
        </p>

        <x-card>
            <form method="POST" action="{{ route('races.store') }}">
                @csrf

                <div style="display:grid;gap:1rem;">
                    <!-- Nombre -->
                    <div>
                        <label style="display:block;font-size:.85rem;margin-bottom:.4rem;">Nombre de la carrera *</label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                            style="width:100%;padding:.6rem;background:rgba(5,8,20,.9);border:1px solid var(--border-subtle);border-radius:.6rem;color:var(--text-main);"
                            placeholder="Maratón de Buenos Aires">
                        @error('name')
                            <span style="color:#ff6b6b;font-size:.8rem;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                        <!-- Distancia -->
                        <div>
                            <label style="display:block;font-size:.85rem;margin-bottom:.4rem;">Distancia (km) *</label>
                            <select name="distance" required style="width:100%;padding:.6rem;background:rgba(5,8,20,.9);border:1px solid var(--border-subtle);border-radius:.6rem;color:var(--text-main);">
                                <option value="">Seleccionar...</option>
                                @foreach($commonDistances as $value => $label)
                                    <option value="{{ $value }}" {{ old('distance') == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                                <option value="custom">Otra distancia</option>
                            </select>
                            @error('distance')
                                <span style="color:#ff6b6b;font-size:.8rem;">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Fecha -->
                        <div>
                            <label style="display:block;font-size:.85rem;margin-bottom:.4rem;">Fecha *</label>
                            <input type="date" name="date" value="{{ old('date') }}" required
                                style="width:100%;padding:.6rem;background:rgba(5,8,20,.9);border:1px solid var(--border-subtle);border-radius:.6rem;color:var(--text-main);">
                            @error('date')
                                <span style="color:#ff6b6b;font-size:.8rem;">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Ubicación -->
                    <div>
                        <label style="display:block;font-size:.85rem;margin-bottom:.4rem;">Ubicación</label>
                        <input type="text" name="location" value="{{ old('location') }}"
                            style="width:100%;padding:.6rem;background:rgba(5,8,20,.9);border:1px solid var(--border-subtle);border-radius:.6rem;color:var(--text-main);"
                            placeholder="Buenos Aires, Argentina">
                    </div>

                    <!-- Tiempo objetivo -->
                    <div>
                        <label style="display:block;font-size:.85rem;margin-bottom:.4rem;">Tiempo objetivo (en segundos)</label>
                        <input type="number" name="target_time" value="{{ old('target_time') }}" min="1"
                            style="width:100%;padding:.6rem;background:rgba(5,8,20,.9);border:1px solid var(--border-subtle);border-radius:.6rem;color:var(--text-main);"
                            placeholder="Ej: 3000 (50 minutos)">
                        <small style="font-size:.75rem;color:var(--text-muted);">Ejemplo: 3600 = 1 hora, 7200 = 2 horas</small>
                    </div>

                    <!-- Status -->
                    <div>
                        <label style="display:block;font-size:.85rem;margin-bottom:.4rem;">Estado *</label>
                        <select name="status" required style="width:100%;padding:.6rem;background:rgba(5,8,20,.9);border:1px solid var(--border-subtle);border-radius:.6rem;color:var(--text-main);">
                            @foreach($statusOptions as $value => $label)
                                <option value="{{ $value }}" {{ old('status', 'upcoming') == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Notas -->
                    <div>
                        <label style="display:block;font-size:.85rem;margin-bottom:.4rem;">Notas</label>
                        <textarea name="notes" rows="3"
                            style="width:100%;padding:.6rem;background:rgba(5,8,20,.9);border:1px solid var(--border-subtle);border-radius:.6rem;color:var(--text-main);resize:vertical;"
                            placeholder="Notas adicionales...">{{ old('notes') }}</textarea>
                    </div>

                    <!-- Submit -->
                    <button type="submit" class="btn-primary" style="width:100%;justify-content:center;">
                        Crear Carrera
                    </button>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>
