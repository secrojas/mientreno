@php
    $period = $report['period'];
    $summary = $report['summary'];
    $distribution = $report['distribution'];
    $comparison = $report['comparison'];
    $workouts = $report['workouts'];
    $insights = $report['insights'];
@endphp

<x-app-layout title="Reporte Mensual">
    <div style="max-width:1200px;margin:0 auto;padding:2rem 1.5rem;">

        {{-- Header con navegación --}}
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:2rem;flex-wrap:wrap;gap:1rem;">
            <div style="display:flex;align-items:center;gap:1rem;">
                <a href="{{ route('reports.monthly.period', [$period['prev_year'], $period['prev_month']]) }}"
                   style="padding:.5rem 1rem;border-radius:.5rem;background:rgba(30,41,59,.7);border:1px solid var(--border-subtle);color:var(--text-main);font-size:.9rem;transition:all .2s;display:inline-block;"
                   onmouseover="this.style.background='rgba(30,41,59,1)'"
                   onmouseout="this.style.background='rgba(30,41,59,.7)'">
                    ← Anterior
                </a>

                <div style="text-align:center;">
                    <h1 style="font-family:'Space Grotesk',sans-serif;font-size:1.75rem;font-weight:700;margin:0;">
                        {{ $period['label'] }}
                    </h1>
                    <p style="color:var(--text-muted);font-size:.9rem;margin:.25rem 0 0;">
                        {{ $period['start_date']->format('d/m') }} - {{ $period['end_date']->format('d/m/Y') }}
                    </p>
                </div>

                @if(!$period['is_current_month'])
                    <a href="{{ route('reports.monthly.period', [$period['next_year'], $period['next_month']]) }}"
                       style="padding:.5rem 1rem;border-radius:.5rem;background:rgba(30,41,59,.7);border:1px solid var(--border-subtle);color:var(--text-main);font-size:.9rem;transition:all .2s;display:inline-block;"
                       onmouseover="this.style.background='rgba(30,41,59,1)'"
                       onmouseout="this.style.background='rgba(30,41,59,.7)'">
                        Siguiente →
                    </a>
                @endif
            </div>

            <div style="display:flex;gap:.75rem;">
                <a href="{{ route('reports.weekly') }}"
                   style="padding:.5rem 1rem;border-radius:.5rem;background:rgba(30,41,59,.7);border:1px solid var(--border-subtle);color:var(--text-main);font-size:.9rem;transition:all .2s;display:inline-block;"
                   onmouseover="this.style.background='rgba(30,41,59,1)'"
                   onmouseout="this.style.background='rgba(30,41,59,.7)'">
                    📊 Ver Semana
                </a>
                <button onclick="shareMonthlyReport({{ $period['year'] }}, {{ $period['month'] }})"
                   style="padding:.5rem 1rem;border-radius:.5rem;background:linear-gradient(135deg, #FF3B5C, #d92d47);border:1px solid #FF3B5C;color:#fff;font-size:.9rem;font-weight:600;transition:all .2s;cursor:pointer;"
                   onmouseover="this.style.background='linear-gradient(135deg, #d92d47, #FF3B5C)'"
                   onmouseout="this.style.background='linear-gradient(135deg, #FF3B5C, #d92d47)'">
                    🔗 Compartir
                </button>
                <a href="{{ route('reports.monthly.pdf', [$period['year'], $period['month']]) }}"
                   target="_blank"
                   style="padding:.5rem 1rem;border-radius:.5rem;background:linear-gradient(135deg, #2DE38E, #1ea568);border:1px solid #2DE38E;color:#05060A;font-size:.9rem;font-weight:600;transition:all .2s;display:inline-block;"
                   onmouseover="this.style.background='linear-gradient(135deg, #1ea568, #2DE38E)'"
                   onmouseout="this.style.background='linear-gradient(135deg, #2DE38E, #1ea568)'">
                    📥 Exportar PDF
                </a>
            </div>
        </div>

        {{-- Resumen General - Métricas principales --}}
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:1rem;margin-bottom:2rem;">
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
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:1rem;margin-bottom:2rem;">
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
            <x-report-card title="Comparativa con Mes Anterior">
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:1rem;">
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
            </x-report-card>
        @endif

        {{-- Distribución por Tipo --}}
        @if(!empty($distribution))
            <x-report-card title="Distribución por Tipo de Entrenamiento">
                <div style="display:grid;gap:1rem;">
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
                        <div style="display:flex;align-items:center;gap:1rem;">
                            <div style="flex:1;min-width:120px;">
                                <div style="font-size:.9rem;font-weight:500;margin-bottom:.25rem;">{{ $label }}</div>
                                <div style="font-size:.8rem;color:var(--text-muted);">
                                    {{ $data['count'] }} {{ $data['count'] === 1 ? 'sesión' : 'sesiones' }}
                                    • {{ number_format($data['distance'], 2) }} km
                                </div>
                            </div>
                            <div style="flex:2;display:flex;align-items:center;gap:.5rem;">
                                <div style="flex:1;height:8px;background:rgba(30,41,59,.5);border-radius:4px;overflow:hidden;">
                                    <div style="height:100%;background:linear-gradient(90deg,#2DE38E,#60A5FA);width:{{ $data['percentage'] }}%;transition:width .3s;"></div>
                                </div>
                                <div style="min-width:50px;text-align:right;font-size:.85rem;font-weight:600;color:var(--accent-secondary);">
                                    {{ $data['percentage'] }}%
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-report-card>
        @endif

        {{-- Insights --}}
        @if(!empty($insights))
            <x-report-card title="Insights del Mes">
                <div style="display:grid;gap:.75rem;">
                    @foreach($insights as $insight)
                        <div style="display:flex;align-items:center;gap:.75rem;padding:.75rem;border-radius:.5rem;background:rgba(30,41,59,.3);">
                            <span style="font-size:1.5rem;">{{ $insight['icon'] }}</span>
                            <span style="font-size:.95rem;">{{ $insight['message'] }}</span>
                        </div>
                    @endforeach
                </div>
            </x-report-card>
        @endif

        {{-- Detalle de Entrenamientos --}}
        <x-report-card title="Detalle de Entrenamientos" subtitle="{{ $workouts->count() }} {{ $workouts->count() === 1 ? 'sesión registrada' : 'sesiones registradas' }}">
            <x-workout-table :workouts="$workouts" :showActions="true" />
        </x-report-card>

        {{-- Botón volver al dashboard --}}
        <div style="text-align:center;margin-top:2rem;">
            <a href="{{ route('dashboard') }}"
               style="display:inline-block;padding:.75rem 1.5rem;border-radius:.5rem;background:rgba(30,41,59,.7);border:1px solid var(--border-subtle);color:var(--text-main);font-size:.9rem;transition:all .2s;"
               onmouseover="this.style.background='rgba(30,41,59,1)'"
               onmouseout="this.style.background='rgba(30,41,59,.7)'">
                ← Volver al Dashboard
            </a>
        </div>

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
                    // Crear modal para mostrar el link
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
            overlay.style.cssText = 'position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.7);z-index:9999;display:flex;align-items:center;justify-content:center;';

            // Crear modal
            const modal = document.createElement('div');
            modal.style.cssText = 'background:#0B0C12;border:1px solid #FF3B5C;border-radius:1rem;padding:2rem;max-width:500px;width:90%;box-shadow:0 20px 60px rgba(255,59,92,0.3);';

            modal.innerHTML = `
                <div style="text-align:center;">
                    <h3 style="font-family:'Space Grotesk',sans-serif;font-size:1.5rem;margin-bottom:1rem;color:#FF3B5C;">🔗 Link Compartible Generado</h3>
                    <p style="color:#9CA3AF;margin-bottom:1.5rem;font-size:0.9rem;">
                        Este link expira el <strong>${expiresAt}</strong>
                    </p>
                    <div style="background:rgba(30,41,59,.5);padding:1rem;border-radius:0.5rem;margin-bottom:1.5rem;word-break:break-all;">
                        <input type="text" id="shareUrl" value="${url}" readonly
                               style="width:100%;background:transparent;border:none;color:#2DE38E;font-size:0.85rem;text-align:center;outline:none;">
                    </div>
                    <div style="display:flex;gap:0.75rem;justify-content:center;">
                        <button onclick="copyShareUrl()"
                                style="padding:0.75rem 1.5rem;border-radius:0.5rem;background:linear-gradient(135deg, #2DE38E, #1ea568);border:none;color:#05060A;font-weight:600;cursor:pointer;">
                            📋 Copiar Link
                        </button>
                        <button onclick="closeShareModal()"
                                style="padding:0.75rem 1.5rem;border-radius:0.5rem;background:rgba(30,41,59,.7);border:1px solid var(--border-subtle);color:#F9FAFB;cursor:pointer;">
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
            button.style.background = 'linear-gradient(135deg, #10b981, #059669)';

            setTimeout(() => {
                button.innerHTML = originalText;
                button.style.background = 'linear-gradient(135deg, #2DE38E, #1ea568)';
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
