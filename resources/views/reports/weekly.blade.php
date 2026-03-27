@php
    $period     = $report['period'];
    $summary    = $report['summary'];
    $distribution = $report['distribution'];
    $comparison = $report['comparison'];
    $workouts   = $report['workouts'];
    $insights   = $report['insights'];

    $typeColors = [
        'easy_run'     => ['label' => 'Fondo Suave',         'hex' => '#60A5FA'],
        'intervals'    => ['label' => 'Series / Intervalos', 'hex' => '#FF3B5C'],
        'tempo'        => ['label' => 'Ritmo Sostenido',     'hex' => '#F59E0B'],
        'long_run'     => ['label' => 'Fondo Largo',         'hex' => '#8B5CF6'],
        'recovery'     => ['label' => 'Recuperación',        'hex' => '#2DE38E'],
        'race'         => ['label' => 'Carrera',             'hex' => '#FF4FA3'],
        'training_run' => ['label' => 'Entrenamiento',       'hex' => '#06B6D4'],
    ];

    // 7-day activity strip
    $weekDays  = collect();
    $dayPtr    = $period['start_date']->copy();
    while ($dayPtr->lte($period['end_date'])) {
        $dayWorkouts = $workouts->filter(fn ($w) => $w->date->isSameDay($dayPtr));
        $weekDays->push([
            'date'      => $dayPtr->copy(),
            'workouts'  => $dayWorkouts,
            'total_km'  => (float) $dayWorkouts->sum('distance'),
            'is_today'  => $dayPtr->isToday(),
            'is_future' => $dayPtr->isFuture(),
        ]);
        $dayPtr->addDay();
    }
    $maxDayKm = max($weekDays->max('total_km'), 0.01);

    $hasTrend = $comparison['distance']['previous'] > 0;
@endphp

<x-app-layout title="Reporte Semanal">

    {{-- ═══════════════════════════════ --}}
    {{-- HEADER: Navegación + Acciones   --}}
    {{-- ═══════════════════════════════ --}}
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-8">
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 w-full lg:w-auto">
            <a href="{{ route('reports.weekly.period', [$period['prev_year'], $period['prev_week']]) }}"
               class="btn-ghost min-h-touch w-full sm:w-auto justify-center">
                ← Anterior
            </a>
            <div class="text-center flex-1 sm:flex-initial">
                <h1 class="font-display text-responsive-2xl">{{ $period['label'] }}</h1>
                <p class="text-text-muted text-sm mt-1">
                    {{ $period['start_date']->locale('es')->isoFormat('D MMM') }}
                    –
                    {{ $period['end_date']->locale('es')->isoFormat('D MMM YYYY') }}
                </p>
            </div>
            @if(!$period['is_current_week'])
                <a href="{{ route('reports.weekly.period', [$period['next_year'], $period['next_week']]) }}"
                   class="btn-ghost min-h-touch w-full sm:w-auto justify-center">
                    Siguiente →
                </a>
            @endif
        </div>

        <div class="flex flex-col sm:flex-row gap-2 w-full lg:w-auto">
            <a href="{{ route('reports.monthly') }}" class="btn-secondary justify-center min-h-touch">
                📅 Ver Mes
            </a>
            <button onclick="shareWeeklyReport({{ $period['year'] }}, {{ $period['week'] }})"
                    class="btn px-4 py-2.5 rounded-btn text-sm bg-gradient-to-br from-accent-primary to-accent-pink
                           text-bg-card shadow-lg hover:shadow-xl transition-all duration-200 justify-center min-h-touch">
                🔗 Compartir
            </button>
            <a href="{{ route('reports.weekly.pdf', [$period['year'], $period['week']]) }}"
               target="_blank"
               class="btn px-4 py-2.5 rounded-btn text-sm bg-gradient-to-br from-accent-secondary to-[#1ea568]
                      text-bg-card shadow-lg hover:shadow-xl transition-all duration-200 justify-center min-h-touch">
                📥 PDF
            </a>
        </div>
    </div>

    {{-- ═══════════════════════════════ --}}
    {{-- HERO: KM grande + stats         --}}
    {{-- ═══════════════════════════════ --}}
    <div class="relative bg-bg-card border border-border-subtle rounded-card p-6 sm:p-8 mb-4 overflow-hidden">
        {{-- Decorative gradient --}}
        <div class="absolute inset-0 bg-gradient-to-br from-accent-secondary/8 via-transparent to-transparent pointer-events-none"></div>
        <div class="absolute -top-16 -right-16 w-48 h-48 rounded-full opacity-5"
             style="background: radial-gradient(circle, #2DE38E 0%, transparent 70%)"></div>

        <div class="relative flex flex-col lg:flex-row items-start lg:items-center gap-6 lg:gap-10">
            {{-- KM hero --}}
            <div class="flex-shrink-0">
                <div class="text-7xl sm:text-8xl font-display font-bold leading-none"
                     style="color: #2DE38E; text-shadow: 0 0 40px rgba(45,227,142,0.3)">
                    {{ number_format($summary['total_distance'], 1) }}
                </div>
                <div class="text-text-muted text-xs uppercase tracking-widest mt-2 font-medium">
                    kilómetros esta semana
                </div>
                @if($hasTrend)
                    @php $distTrend = $comparison['distance']; @endphp
                    <div class="mt-3 inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold"
                         style="
                            @if($distTrend['trend'] === 'up')   background: rgba(45,227,142,0.15); color: #2DE38E;
                            @elseif($distTrend['trend'] === 'down') background: rgba(255,59,92,0.15); color: #FF3B5C;
                            @else background: rgba(156,163,175,0.15); color: #9CA3AF; @endif
                         ">
                        @if($distTrend['trend'] === 'up') ↑
                        @elseif($distTrend['trend'] === 'down') ↓
                        @else → @endif
                        {{ abs($distTrend['percentage']) }}% vs semana anterior
                    </div>
                @endif
            </div>

            {{-- Divider --}}
            <div class="hidden lg:block w-px self-stretch bg-border-subtle"></div>

            {{-- Supporting stats --}}
            <div class="flex-1 grid grid-cols-3 gap-4 sm:gap-6">
                <div>
                    <div class="text-2xl sm:text-3xl font-display font-semibold text-text-main">
                        {{ $summary['formatted_duration'] }}
                    </div>
                    <div class="text-xs text-text-muted mt-1 uppercase tracking-wide">Tiempo</div>
                </div>
                <div class="border-l border-border-subtle pl-4 sm:pl-6">
                    <div class="text-2xl sm:text-3xl font-display font-semibold text-text-main">
                        {{ $summary['total_sessions'] }}
                    </div>
                    <div class="text-xs text-text-muted mt-1 uppercase tracking-wide">Sesiones</div>
                </div>
                <div class="border-l border-border-subtle pl-4 sm:pl-6">
                    <div class="text-2xl sm:text-3xl font-display font-semibold" style="color: #FF3B5C">
                        {{ $summary['formatted_pace'] }}
                    </div>
                    <div class="text-xs text-text-muted mt-1 uppercase tracking-wide">Pace prom.</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════ --}}
    {{-- 7-DAY ACTIVITY STRIP             --}}
    {{-- ═══════════════════════════════ --}}
    <div class="bg-bg-card border border-border-subtle rounded-card p-5 sm:p-6 mb-4">
        <p class="text-xs font-semibold text-text-muted uppercase tracking-widest mb-5">
            Actividad de la semana
        </p>
        <div class="flex gap-2 sm:gap-3 items-end" style="height: 120px">
            @foreach($weekDays as $day)
                @php
                    $primaryWorkout = $day['workouts']->sortByDesc('distance')->first();
                    $barHex         = $primaryWorkout ? ($typeColors[$primaryWorkout->type]['hex'] ?? '#2DE38E') : null;
                    $barHeightPct   = $day['total_km'] > 0
                        ? max(($day['total_km'] / $maxDayKm) * 75, 6)
                        : 0;
                    $dayShort = mb_strtolower(mb_substr($day['date']->locale('es')->isoFormat('ddd'), 0, 3));
                    $dayShort = mb_strtoupper(mb_substr($dayShort, 0, 1)) . mb_substr($dayShort, 1);
                @endphp
                <div class="flex-1 flex flex-col items-center justify-end gap-0"
                     style="height: 100%; position: relative">
                    {{-- km label --}}
                    <div class="text-xs font-medium mb-1 {{ $day['total_km'] > 0 ? 'text-text-main' : 'text-transparent' }}"
                         style="font-feature-settings: 'tnum'">
                        {{ $day['total_km'] > 0 ? number_format($day['total_km'], 1) : '·' }}
                    </div>

                    {{-- Bar --}}
                    <div class="w-full relative" style="flex: 1; display: flex; align-items: flex-end;">
                        @if($day['total_km'] > 0 && $barHex)
                            <div class="w-full rounded-t-lg transition-all duration-300"
                                 style="height: {{ $barHeightPct }}%;
                                        background-color: {{ $barHex }};
                                        opacity: {{ $day['is_future'] ? '0.35' : '1' }};
                                        box-shadow: 0 -4px 12px {{ $barHex }}40;">
                            </div>
                        @else
                            <div class="w-full rounded-full" style="height: 2px; background-color: #111827; margin-bottom: 0;"></div>
                        @endif
                    </div>

                    {{-- Day label --}}
                    <div class="text-xs mt-1.5 uppercase font-medium {{ $day['is_today'] ? 'text-accent-primary' : 'text-text-muted' }}"
                         style="{{ $day['is_today'] ? 'font-weight: 700' : '' }}">
                        {{ $dayShort }}
                    </div>
                    @if($day['is_today'])
                        <div class="w-1 h-1 rounded-full mt-0.5" style="background-color: #FF3B5C"></div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- ═══════════════════════════════ --}}
    {{-- COMPARATIVA                      --}}
    {{-- ═══════════════════════════════ --}}
    @if($hasTrend)
        <x-card title="Comparativa con semana anterior" class="mb-4">
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
                    label="Pace promedio"
                    :current="$summary['formatted_pace']"
                    :previous="$comparison['pace']['formatted_previous'] ?? '–'"
                    :diff="$comparison['pace']"
                    unit="/km"
                    :invertTrend="true"
                />
            </div>
        </x-card>
    @endif

    {{-- ═══════════════════════════════════════════ --}}
    {{-- DISTRIBUCIÓN + INSIGHTS (2 columnas en lg) --}}
    {{-- ═══════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">

        {{-- Distribución por tipo --}}
        @if(!empty($distribution))
            <x-card title="Distribución por tipo">
                <div class="flex flex-col gap-4">
                    @foreach($distribution as $type => $data)
                        @php
                            $color = $typeColors[$type] ?? ['label' => $type, 'hex' => '#9CA3AF'];
                        @endphp
                        <div class="flex items-center gap-3">
                            {{-- Dot --}}
                            <div class="flex-shrink-0 w-2.5 h-2.5 rounded-full"
                                 style="background-color: {{ $color['hex'] }}"></div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between mb-1.5">
                                    <span class="text-sm font-medium text-text-main">{{ $color['label'] }}</span>
                                    <span class="text-sm font-bold ml-2 flex-shrink-0"
                                          style="color: {{ $color['hex'] }}">{{ $data['percentage'] }}%</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="flex-1 h-1.5 bg-border-subtle rounded-full overflow-hidden">
                                        <div class="h-full rounded-full transition-all duration-500"
                                             style="width: {{ $data['percentage'] }}%; background-color: {{ $color['hex'] }}"></div>
                                    </div>
                                    <span class="text-xs text-text-muted flex-shrink-0">
                                        {{ $data['count'] }} {{ $data['count'] === 1 ? 'ses.' : 'ses.' }}
                                        · {{ number_format($data['distance'], 1) }} km
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-card>
        @endif

        {{-- Insights --}}
        @if(!empty($insights))
            <x-card title="Insights de la semana">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach($insights as $insight)
                        <div class="flex items-start gap-3 p-4 rounded-card border border-border-subtle"
                             style="background: rgba(17,24,39,0.5)">
                            <div class="flex-shrink-0 w-10 h-10 rounded-btn flex items-center justify-center text-xl"
                                 style="background: rgba(45,227,142,0.1)">
                                {{ $insight['icon'] }}
                            </div>
                            <p class="text-sm text-text-main leading-relaxed pt-0.5">{{ $insight['message'] }}</p>
                        </div>
                    @endforeach
                </div>
            </x-card>
        @endif
    </div>

    {{-- ═══════════════════════════════ --}}
    {{-- DETALLE DE ENTRENAMIENTOS        --}}
    {{-- ═══════════════════════════════ --}}
    <x-card title="Detalle de entrenamientos"
            :subtitle="$workouts->count() . ' ' . ($workouts->count() === 1 ? 'sesión registrada' : 'sesiones registradas')"
            class="mb-6">
        <x-workout-table :workouts="$workouts" :showActions="true" />
    </x-card>

    <div class="text-center mt-8">
        <a href="{{ route('dashboard') }}" class="btn-ghost inline-flex">← Volver al Dashboard</a>
    </div>

    {{-- ═══════════════════════════════ --}}
    {{-- SCRIPTS                          --}}
    {{-- ═══════════════════════════════ --}}
    <script>
        function shareWeeklyReport(year, week) {
            const button = event.target;
            const originalText = button.innerHTML;
            button.disabled = true;
            button.innerHTML = '⏳ Generando...';

            fetch(`/reports/weekly/${year}/${week}/share`, {
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
            .catch(() => alert('Error al generar el link compartible'))
            .finally(() => {
                button.disabled = false;
                button.innerHTML = originalText;
            });
        }

        function showShareModal(url, expiresAt) {
            const overlay = document.createElement('div');
            overlay.className = 'fixed inset-0 bg-black/70 z-[9999] flex items-center justify-center p-4';

            const modal = document.createElement('div');
            modal.className = 'bg-bg-card border border-accent-primary rounded-card p-6 sm:p-8 max-w-lg w-full shadow-2xl shadow-accent-primary/20';

            modal.innerHTML = `
                <div class="text-center">
                    <h3 class="font-display text-responsive-xl mb-4 text-accent-primary">🔗 Link Compartible</h3>
                    <p class="text-text-muted mb-6 text-sm">
                        Expira el <strong class="text-text-main">${expiresAt}</strong>
                    </p>
                    <div class="bg-border-subtle/50 p-4 rounded-btn mb-6 break-all">
                        <input type="text" id="shareUrl" value="${url}" readonly
                               class="w-full bg-transparent border-none text-accent-secondary text-sm text-center outline-none select-all">
                    </div>
                    <div class="flex flex-col sm:flex-row gap-3 justify-center">
                        <button onclick="copyShareUrl()" class="btn-secondary flex-1 sm:flex-initial justify-center py-3">
                            📋 Copiar Link
                        </button>
                        <button onclick="closeShareModal()" class="btn-ghost flex-1 sm:flex-initial justify-center py-3">
                            Cerrar
                        </button>
                    </div>
                </div>
            `;

            overlay.appendChild(modal);
            document.body.appendChild(overlay);
            overlay.addEventListener('click', (e) => { if (e.target === overlay) { closeShareModal(); } });
            window.shareModalOverlay = overlay;
        }

        function copyShareUrl() {
            const input = document.getElementById('shareUrl');
            input.select();
            document.execCommand('copy');
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
