@php
    $period = $report['period'];
    $summary = $report['summary'];
    $distribution = $report['distribution'];
    $comparison = $report['comparison'];
    $workouts = $report['workouts'];
    $insights = $report['insights'];
@endphp

<x-app-layout title="Reporte Mensual">
    {{-- Header con navegación --}}
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-8">
        <!-- Navegación período -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 w-full lg:w-auto">
            <a href="{{ route('reports.monthly.period', [$period['prev_year'], $period['prev_month']]) }}"
               class="btn-ghost min-h-touch w-full sm:w-auto justify-center">
                ← Anterior
            </a>

            <div class="text-center flex-1 sm:flex-initial">
                <h1 class="font-display text-responsive-2xl">
                    {{ $period['label'] }}
                </h1>
                <p class="text-text-muted text-sm mt-1">
                    {{ $period['start_date']->format('d/m') }} - {{ $period['end_date']->format('d/m/Y') }}
                </p>
            </div>

            @if(!$period['is_current_month'])
                <a href="{{ route('reports.monthly.period', [$period['next_year'], $period['next_month']]) }}"
                   class="btn-ghost min-h-touch w-full sm:w-auto justify-center">
                    Siguiente →
                </a>
            @endif
        </div>

        <!-- Botones de acción -->
        <div class="flex flex-col sm:flex-row gap-2 w-full lg:w-auto">
            <a href="{{ route('reports.weekly') }}" class="btn-secondary justify-center min-h-touch">
                📊 Ver Semana
            </a>
            <button onclick="shareMonthlyReport({{ $period['year'] }}, {{ $period['month'] }})"
                    class="btn px-4 py-2.5 rounded-btn text-sm bg-gradient-to-br from-accent-primary to-accent-pink
                           text-bg-card shadow-lg hover:shadow-xl transition-all duration-200 justify-center min-h-touch">
                🔗 Compartir
            </button>
            <a href="{{ route('reports.monthly.pdf', [$period['year'], $period['month']]) }}"
               target="_blank"
               class="btn px-4 py-2.5 rounded-btn text-sm bg-gradient-to-br from-accent-secondary to-[#1ea568]
                      text-bg-card shadow-lg hover:shadow-xl transition-all duration-200 justify-center min-h-touch">
                📥 Exportar PDF
            </a>
        </div>
    </div>

    {{-- Resumen General - Métricas principales --}}
    <div class="grid-responsive-4 gap-4 mb-6">
        <x-metric-card
            label="Kilómetros"
            :value="number_format($summary['total_distance'], 2)"
            subtitle="km totales"
            accent="#2DE38E"
        />
        <x-metric-card
            label="Tiempo"
            :value="$summary['formatted_duration']"
            subtitle="en movimiento"
            accent="#60A5FA"
        />
        <x-metric-card
            label="Sesiones"
            :value="$summary['total_sessions']"
            subtitle="entrenamientos"
            accent="#F59E0B"
        />
        <x-metric-card
            label="Pace Promedio"
            :value="$summary['formatted_pace']"
            subtitle="min/km"
            accent="#FF3B5C"
        />
    </div>

    {{-- Métricas adicionales (solo en vista mensual) --}}
    @if($summary['avg_heart_rate'] || $summary['elevation_gain'])
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            @if($summary['avg_heart_rate'])
                <x-metric-card
                    label="FC Promedio"
                    :value="round($summary['avg_heart_rate'])"
                    subtitle="bpm"
                    accent="#EF4444"
                />
            @endif
            @if($summary['elevation_gain'])
                <x-metric-card
                    label="Desnivel"
                    :value="number_format($summary['elevation_gain'])"
                    subtitle="metros D+"
                    accent="#8B5CF6"
                />
            @endif
        </div>
    @endif

    {{-- Comparativa con mes anterior --}}
    @if($comparison['distance']['previous'] > 0)
        <x-card title="Comparativa con Mes Anterior" class="mb-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <x-metric-comparison
                    label="Distancia"
                    :current="$summary['total_distance']"
                    :previous="$comparison['distance']['previous']"
                    :diff="$comparison['distance']"
                    unit=" km"
                />
                <x-metric-comparison
                    label="Sesiones"
                    :current="$summary['total_sessions']"
                    :previous="$comparison['sessions']['previous']"
                    :diff="$comparison['sessions']"
                />
                <x-metric-comparison
                    label="Tiempo"
                    :current="$summary['formatted_duration']"
                    :previous="$comparison['duration']['formatted_diff']"
                    :diff="$comparison['duration']"
                />
                <x-metric-comparison
                    label="Pace Promedio"
                    :current="$summary['formatted_pace']"
                    :previous="$comparison['pace']['formatted_previous'] ?? '–'"
                    :diff="$comparison['pace']"
                    unit="/km"
                    :invertTrend="true"
                />
            </div>
        </x-card>
    @endif

    {{-- Distribución por Tipo --}}
    @if(!empty($distribution))
        <x-card title="Distribución por Tipo de Entrenamiento" class="mb-6">
            <div class="grid gap-4">
                @foreach($distribution as $type => $data)
                    @php
                        $typeLabels = [
                            'easy_run' => 'Fondo Suave',
                            'intervals' => 'Intervalos',
                            'tempo' => 'Tempo',
                            'long_run' => 'Tirada Larga',
                            'recovery' => 'Recuperación',
                            'race' => 'Carrera',
                            'training_run' => 'Entrenamiento General',
                        ];
                        $label = $typeLabels[$type] ?? $type;
                    @endphp
                    <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-medium mb-1">{{ $label }}</div>
                            <div class="text-xs text-text-muted">
                                {{ $data['count'] }} {{ $data['count'] === 1 ? 'sesión' : 'sesiones' }}
                                • {{ number_format($data['distance'], 2) }} km
                            </div>
                        </div>
                        <div class="flex-[2] flex items-center gap-2">
                            <div class="flex-1 h-2 bg-border-subtle rounded-full overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-accent-secondary to-blue-500 transition-all duration-300"
                                     style="width: {{ $data['percentage'] }}%"></div>
                            </div>
                            <div class="min-w-[50px] text-right text-sm font-semibold text-accent-secondary">
                                {{ $data['percentage'] }}%
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-card>
    @endif

    {{-- Insights --}}
    @if(!empty($insights))
        <x-card title="Insights del Mes" class="mb-6">
            <div class="grid gap-3">
                @foreach($insights as $insight)
                    <div class="flex items-center gap-3 p-3 rounded-btn bg-border-subtle/30">
                        <span class="text-2xl flex-shrink-0">{{ $insight['icon'] }}</span>
                        <span class="text-sm">{{ $insight['message'] }}</span>
                    </div>
                @endforeach
            </div>
        </x-card>
    @endif

    {{-- Detalle de Entrenamientos --}}
    <x-card title="Detalle de Entrenamientos"
            :subtitle="$workouts->count() . ' ' . ($workouts->count() === 1 ? 'sesión registrada' : 'sesiones registradas')"
            class="mb-6">
        <x-workout-table :workouts="$workouts" :showActions="true" />
    </x-card>

    {{-- Botón volver --}}
    <div class="text-center mt-8">
        <a href="{{ route('dashboard') }}" class="btn-ghost inline-flex">
            ← Volver al Dashboard
        </a>
    </div>

    {{-- Script para compartir reporte --}}
    <script>
        function shareMonthlyReport(year, month) {
            const button = event.target;
            const originalText = button.innerHTML;
            button.disabled = true;
            button.innerHTML = '⏳ Generando...';

            fetch(`/reports/monthly/${year}/${month}/share`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showShareModal(data.url, data.expires_at);
                } else {
                    alert('Error al generar el link compartible');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al generar el link compartible');
            })
            .finally(() => {
                button.disabled = false;
                button.innerHTML = originalText;
            });
        }

        function showShareModal(url, expiresAt) {
            // Crear overlay
            const overlay = document.createElement('div');
            overlay.className = 'fixed inset-0 bg-black/70 z-[9999] flex items-center justify-center p-4';

            // Crear modal
            const modal = document.createElement('div');
            modal.className = 'bg-bg-card border border-accent-primary rounded-card p-6 sm:p-8 max-w-lg w-full shadow-2xl shadow-accent-primary/20';

            modal.innerHTML = `
                <div class="text-center">
                    <h3 class="font-display text-responsive-xl mb-4 text-accent-primary">🔗 Link Compartible Generado</h3>
                    <p class="text-text-muted mb-6 text-sm">
                        Este link expira el <strong class="text-text-main">${expiresAt}</strong>
                    </p>
                    <div class="bg-border-subtle/50 p-4 rounded-btn mb-6 break-all">
                        <input type="text" id="shareUrl" value="${url}" readonly
                               class="w-full bg-transparent border-none text-accent-secondary text-sm text-center outline-none select-all">
                    </div>
                    <div class="flex flex-col sm:flex-row gap-3 justify-center">
                        <button onclick="copyShareUrl()"
                                class="btn-secondary flex-1 sm:flex-initial justify-center py-3">
                            📋 Copiar Link
                        </button>
                        <button onclick="closeShareModal()"
                                class="btn-ghost flex-1 sm:flex-initial justify-center py-3">
                            Cerrar
                        </button>
                    </div>
                </div>
            `;

            overlay.appendChild(modal);
            document.body.appendChild(overlay);

            // Cerrar con click en overlay
            overlay.addEventListener('click', (e) => {
                if (e.target === overlay) {
                    closeShareModal();
                }
            });

            window.shareModalOverlay = overlay;
        }

        function copyShareUrl() {
            const input = document.getElementById('shareUrl');
            input.select();
            document.execCommand('copy');

            // Feedback visual
            const button = event.target;
            const originalText = button.innerHTML;
            button.innerHTML = '✓ Copiado!';
            button.classList.add('!bg-green-500/20', '!border-green-500', '!text-green-400');

            setTimeout(() => {
                button.innerHTML = originalText;
                button.classList.remove('!bg-green-500/20', '!border-green-500', '!text-green-400');
            }, 2000);
        }

        function closeShareModal() {
            if (window.shareModalOverlay) {
                document.body.removeChild(window.shareModalOverlay);
                window.shareModalOverlay = null;
            }
        }
    </script>
</x-app-layout>
