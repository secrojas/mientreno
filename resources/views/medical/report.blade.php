<x-app-layout title="Reporte Médico">
    <div x-data="medicalReport()" class="max-w-7xl mx-auto">

        {{-- Header --}}
        <header class="mb-8 flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <a href="{{ route('medical.index') }}" class="text-text-muted hover:text-text-main transition-colors text-sm">
                        Salud Médica
                    </a>
                    <span class="text-text-muted text-sm">/</span>
                    <span class="text-text-muted text-sm">Reporte</span>
                </div>
                <h1 class="font-display text-responsive-2xl mb-1 bg-gradient-to-r from-text-main to-text-muted bg-clip-text text-transparent">
                    Reporte para el Cardiólogo
                </h1>
                <p class="text-responsive-sm text-text-muted">
                    Historial completo de entrenamiento ·
                    @if($report['first_workout'])
                        Desde {{ $report['first_workout']->date->locale('es')->isoFormat('MMMM YYYY') }}
                    @endif
                    · Generado el {{ now()->locale('es')->isoFormat('D MMMM YYYY') }}
                </p>
            </div>

            <div class="flex flex-wrap gap-2 shrink-0">
                <button @click="shareMedicalReport()"
                        :disabled="sharing"
                        class="btn-secondary text-sm">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/>
                        <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/>
                        <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/>
                    </svg>
                    <span x-text="sharing ? 'Generando...' : 'Compartir'">Compartir</span>
                </button>
                <a href="{{ route('medical.report.pdf') }}" class="btn-primary text-sm">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="7 10 12 15 17 10"/>
                        <line x1="12" y1="15" x2="12" y2="3"/>
                    </svg>
                    Exportar PDF
                </a>
            </div>
        </header>

        {{-- ─── RESUMEN GLOBAL ─── --}}
        @php
            $global = $report['global_summary'];
            $first = $report['first_workout'];
            $totalHours = floor($global['total_duration'] / 3600);
            $totalMins  = floor(($global['total_duration'] % 3600) / 60);
        @endphp

        <section class="mb-8">
            <h2 class="flex items-center gap-3 text-xs font-bold uppercase tracking-widest text-accent-primary mb-4">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
                </svg>
                Totales Históricos
                <span class="flex-1 h-px bg-gradient-to-r from-accent-primary/30 to-transparent"></span>
            </h2>

            <div class="grid-responsive-4 gap-4">
                <x-metric-card
                    label="Kilómetros totales"
                    :value="number_format($global['total_distance'], 1) . ' km'"
                    accent="primary"
                />
                <x-metric-card
                    label="Tiempo total"
                    :value="($totalHours > 0 ? $totalHours . 'h ' : '') . $totalMins . 'm'"
                />
                <x-metric-card
                    label="Sesiones completadas"
                    :value="$global['total_sessions']"
                    :subtitle="'Pace prom: ' . $global['formatted_pace'] . '/km'"
                />
                <x-metric-card
                    label="Entrenando desde"
                    :value="$first ? $first->date->locale('es')->isoFormat('MMM YYYY') : '—'"
                    :subtitle="$first ? $first->date->diffForHumans(now(), true) : ''"
                />
            </div>

            @if(!empty($report['hr_stats']))
                @php $hr = $report['hr_stats']; @endphp
                <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="card flex items-center gap-4 py-3">
                        <div class="w-10 h-10 rounded-btn bg-red-500/10 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-red-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-xl font-bold text-red-400">{{ $hr['overall_avg'] }} <span class="text-sm font-normal text-text-muted">bpm</span></div>
                            <div class="text-xs text-text-muted uppercase tracking-wider">FC Promedio</div>
                        </div>
                    </div>
                    <div class="card flex items-center gap-4 py-3">
                        <div class="w-10 h-10 rounded-btn bg-orange-500/10 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-orange-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/>
                                <polyline points="17 6 23 6 23 12"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-xl font-bold text-orange-400">{{ $hr['max'] }} <span class="text-sm font-normal text-text-muted">bpm</span></div>
                            <div class="text-xs text-text-muted uppercase tracking-wider">FC Máxima</div>
                        </div>
                    </div>
                    <div class="card flex items-center gap-4 py-3">
                        <div class="w-10 h-10 rounded-btn bg-emerald-500/10 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="23 18 13.5 8.5 8.5 13.5 1 6"/>
                                <polyline points="17 18 23 18 23 12"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-xl font-bold text-emerald-400">{{ $hr['min'] }} <span class="text-sm font-normal text-text-muted">bpm</span></div>
                            <div class="text-xs text-text-muted uppercase tracking-wider">FC Mínima</div>
                        </div>
                    </div>
                </div>
            @endif
        </section>

        {{-- ─── PROGRESIÓN MES A MES ─── --}}
        <section class="mb-8">
            <h2 class="flex items-center gap-3 text-xs font-bold uppercase tracking-widest text-accent-secondary mb-4">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                    <line x1="16" y1="2" x2="16" y2="6"/>
                    <line x1="8" y1="2" x2="8" y2="6"/>
                    <line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
                Progresión Mes a Mes
                <span class="flex-1 h-px bg-gradient-to-r from-accent-secondary/30 to-transparent"></span>
            </h2>

            @forelse($report['monthly_breakdown'] as $year => $months)
                <div class="mb-5">
                    <h3 class="text-sm font-semibold text-text-main mb-2">{{ $year }}</h3>
                    <div class="card p-0 overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="bg-bg-sidebar border-b border-white/5">
                                        <th class="text-left px-4 py-2.5 text-xs font-bold uppercase tracking-wider text-text-muted">Mes</th>
                                        <th class="text-right px-4 py-2.5 text-xs font-bold uppercase tracking-wider text-text-muted">Sesiones</th>
                                        <th class="text-right px-4 py-2.5 text-xs font-bold uppercase tracking-wider text-accent-primary">Km</th>
                                        <th class="text-right px-4 py-2.5 text-xs font-bold uppercase tracking-wider text-text-muted">Tiempo</th>
                                        <th class="text-right px-4 py-2.5 text-xs font-bold uppercase tracking-wider text-text-muted">Pace prom.</th>
                                        <th class="text-right px-4 py-2.5 text-xs font-bold uppercase tracking-wider text-red-400">FC prom.</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-white/5">
                                    @php $yearTotal = ['sessions' => 0, 'distance' => 0, 'duration' => 0]; @endphp
                                    @foreach($months as $m)
                                        @php
                                            $yearTotal['sessions'] += $m['total_sessions'];
                                            $yearTotal['distance'] += $m['total_distance'];
                                            $yearTotal['duration'] += $m['total_duration'];
                                        @endphp
                                        <tr class="hover:bg-white/3 transition-colors">
                                            <td class="px-4 py-2.5 font-medium text-text-main">{{ $m['month_name'] }}</td>
                                            <td class="px-4 py-2.5 text-right text-text-muted">{{ $m['total_sessions'] }}</td>
                                            <td class="px-4 py-2.5 text-right font-semibold text-accent-primary">{{ number_format($m['total_distance'], 1) }}</td>
                                            <td class="px-4 py-2.5 text-right text-text-muted">{{ $m['formatted_duration'] }}</td>
                                            <td class="px-4 py-2.5 text-right text-text-muted">{{ $m['formatted_pace'] }}/km</td>
                                            <td class="px-4 py-2.5 text-right {{ $m['avg_heart_rate_month'] ? 'text-red-400' : 'text-text-muted' }}">
                                                {{ $m['avg_heart_rate_month'] ? $m['avg_heart_rate_month'] . ' bpm' : '—' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="bg-accent-primary/5 border-t border-accent-primary/20">
                                        <td class="px-4 py-2.5 text-xs font-bold uppercase tracking-wider text-accent-primary">Total {{ $year }}</td>
                                        <td class="px-4 py-2.5 text-right text-sm font-bold text-text-main">{{ $yearTotal['sessions'] }}</td>
                                        <td class="px-4 py-2.5 text-right text-sm font-bold text-accent-primary">{{ number_format($yearTotal['distance'], 1) }}</td>
                                        <td class="px-4 py-2.5 text-right text-sm font-bold text-text-main">
                                            @php $yh = floor($yearTotal['duration'] / 3600); $ym = floor(($yearTotal['duration'] % 3600) / 60); @endphp
                                            {{ $yh > 0 ? $yh . 'h ' : '' }}{{ $ym }}m
                                        </td>
                                        <td class="px-4 py-2.5" colspan="2"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            @empty
                <div class="card text-center py-10 text-text-muted text-sm">
                    Sin entrenamientos registrados.
                </div>
            @endforelse
        </section>

        {{-- ─── TOP 10 RECORRIDOS ─── --}}
        <section class="mb-8">
            <h2 class="flex items-center gap-3 text-xs font-bold uppercase tracking-widest text-amber-400 mb-4">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                </svg>
                Top 10 Recorridos por Distancia
                <span class="flex-1 h-px bg-gradient-to-r from-amber-400/30 to-transparent"></span>
            </h2>

            <div class="card p-0 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-bg-sidebar border-b border-white/5">
                                <th class="text-center px-3 py-2.5 text-xs font-bold uppercase tracking-wider text-text-muted w-10">#</th>
                                <th class="text-left px-4 py-2.5 text-xs font-bold uppercase tracking-wider text-text-muted">Fecha</th>
                                <th class="text-left px-4 py-2.5 text-xs font-bold uppercase tracking-wider text-text-muted">Tipo</th>
                                <th class="text-right px-4 py-2.5 text-xs font-bold uppercase tracking-wider text-amber-400">Distancia</th>
                                <th class="text-right px-4 py-2.5 text-xs font-bold uppercase tracking-wider text-text-muted">Tiempo</th>
                                <th class="text-right px-4 py-2.5 text-xs font-bold uppercase tracking-wider text-text-muted">Pace</th>
                                <th class="text-right px-4 py-2.5 text-xs font-bold uppercase tracking-wider text-red-400">FC</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @forelse($report['top_workouts'] as $i => $workout)
                                <tr class="hover:bg-white/3 transition-colors">
                                    <td class="px-3 py-2.5 text-center">
                                        @if($i === 0)
                                            <span class="text-amber-400 font-bold text-base">🥇</span>
                                        @elseif($i === 1)
                                            <span class="text-slate-400 font-bold text-base">🥈</span>
                                        @elseif($i === 2)
                                            <span class="text-orange-600 font-bold text-base">🥉</span>
                                        @else
                                            <span class="text-text-muted text-xs font-bold">{{ $i + 1 }}</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2.5 text-text-main">
                                        {{ $workout->date->locale('es')->isoFormat('D MMM YYYY') }}
                                    </td>
                                    <td class="px-4 py-2.5">
                                        <span class="text-xs text-text-muted">{{ $workout->type_label }}</span>
                                    </td>
                                    <td class="px-4 py-2.5 text-right font-bold text-amber-400">
                                        {{ number_format($workout->distance, 2) }} km
                                    </td>
                                    <td class="px-4 py-2.5 text-right text-text-muted">
                                        {{ $workout->formatted_duration }}
                                    </td>
                                    <td class="px-4 py-2.5 text-right text-text-muted">
                                        {{ $workout->formatted_pace }}/km
                                    </td>
                                    <td class="px-4 py-2.5 text-right {{ $workout->avg_heart_rate ? 'text-red-400' : 'text-text-muted' }}">
                                        {{ $workout->avg_heart_rate ? $workout->avg_heart_rate . ' bpm' : '—' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-8 text-text-muted text-sm">Sin entrenamientos registrados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        {{-- ─── DISTRIBUCIÓN POR TIPO ─── --}}
        @if(!empty($report['distribution']))
            <section class="mb-8">
                <h2 class="flex items-center gap-3 text-xs font-bold uppercase tracking-widest text-accent-secondary mb-4">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21.21 15.89A10 10 0 1 1 8 2.83"/>
                        <path d="M22 12A10 10 0 0 0 12 2v10z"/>
                    </svg>
                    Distribución por Tipo de Entrenamiento
                    <span class="flex-1 h-px bg-gradient-to-r from-accent-secondary/30 to-transparent"></span>
                </h2>

                @php
                    $typeColors = [
                        'easy_run'     => ['label' => 'Fondo Suave',       'color' => 'text-blue-400',   'bg' => 'bg-blue-400/10'],
                        'intervals'    => ['label' => 'Series/Intervalos', 'color' => 'text-red-400',    'bg' => 'bg-red-400/10'],
                        'tempo'        => ['label' => 'Ritmo Sostenido',   'color' => 'text-amber-400',  'bg' => 'bg-amber-400/10'],
                        'long_run'     => ['label' => 'Fondo Largo',       'color' => 'text-violet-400', 'bg' => 'bg-violet-400/10'],
                        'recovery'     => ['label' => 'Recuperación',      'color' => 'text-emerald-400','bg' => 'bg-emerald-400/10'],
                        'race'         => ['label' => 'Carrera',           'color' => 'text-pink-400',   'bg' => 'bg-pink-400/10'],
                        'training_run' => ['label' => 'Entrenamiento',     'color' => 'text-cyan-400',   'bg' => 'bg-cyan-400/10'],
                    ];
                @endphp

                <div class="card grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($report['distribution'] as $type => $data)
                        @php $tc = $typeColors[$type] ?? ['label' => $type, 'color' => 'text-text-muted', 'bg' => 'bg-white/5']; @endphp
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-btn {{ $tc['bg'] }} flex items-center justify-center shrink-0">
                                <span class="text-lg font-bold {{ $tc['color'] }}">{{ $data['percentage'] }}%</span>
                            </div>
                            <div class="min-w-0">
                                <div class="text-sm font-medium text-text-main truncate">{{ $tc['label'] }}</div>
                                <div class="text-xs text-text-muted">{{ $data['count'] }} sesiones · {{ number_format($data['distance'], 1) }} km</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- ─── APTO MÉDICO ─── --}}
        @if($fitnessCertificate)
            <section class="mb-8">
                <h2 class="flex items-center gap-3 text-xs font-bold uppercase tracking-widest text-accent-secondary mb-4">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    </svg>
                    Apto Médico
                    <span class="flex-1 h-px bg-gradient-to-r from-accent-secondary/30 to-transparent"></span>
                </h2>
                @php
                    $certExpired = $fitnessCertificate->isExpired();
                    $certSoon    = $fitnessCertificate->isExpiringSoon();
                    $certColor   = $certExpired ? 'text-red-400 bg-red-400/10 border-red-400/30' : ($certSoon ? 'text-amber-400 bg-amber-400/10 border-amber-400/30' : 'text-emerald-400 bg-emerald-400/10 border-emerald-400/30');
                    $certLabel   = $certExpired ? 'VENCIDO' : ($certSoon ? 'POR VENCER' : 'VIGENTE');
                @endphp
                <div class="card flex flex-wrap items-center gap-4">
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-2 mb-1">
                            <span class="font-medium">{{ $fitnessCertificate->title }}</span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold uppercase border {{ $certColor }}">{{ $certLabel }}</span>
                        </div>
                        <div class="flex flex-wrap gap-x-5 text-xs text-text-muted">
                            @if($fitnessCertificate->issued_at)
                                <span>Emitido: {{ $fitnessCertificate->issued_at->format('d/m/Y') }}</span>
                            @endif
                            @if($fitnessCertificate->expires_at)
                                <span>Vence: {{ $fitnessCertificate->expires_at->format('d/m/Y') }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </section>
        @endif

    </div>

    <script>
        function medicalReport() {
            return {
                sharing: false,
                shareMedicalReport() {
                    this.sharing = true;
                    fetch('{{ route('medical.report.share') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) { showShareModal(data.url, data.expires_at); }
                        else { alert('Error al generar el link.'); }
                    })
                    .catch(() => alert('Error al generar el link.'))
                    .finally(() => { this.sharing = false; });
                }
            };
        }

        function showShareModal(url, expiresAt) {
            const overlay = document.createElement('div');
            overlay.className = 'fixed inset-0 bg-black/70 z-[9999] flex items-center justify-center p-4';
            const modal = document.createElement('div');
            modal.className = 'bg-bg-card border border-accent-primary rounded-card p-6 sm:p-8 max-w-lg w-full shadow-2xl shadow-accent-primary/20';
            modal.innerHTML = `
                <div class="text-center">
                    <h3 class="font-display text-responsive-xl mb-2 text-accent-primary">Compartir con el Cardiólogo</h3>
                    <p class="text-text-muted mb-1 text-sm">Válido por 7 días · Expira el <strong class="text-text-main">${expiresAt}</strong></p>
                    <p class="text-text-muted mb-5 text-xs">No requiere inicio de sesión para verlo.</p>
                    <div class="bg-border-subtle/50 p-3 rounded-btn mb-5 break-all">
                        <input type="text" id="shareUrl" value="${url}" readonly
                               class="w-full bg-transparent border-none text-accent-secondary text-sm text-center outline-none select-all">
                    </div>
                    <div class="flex flex-col sm:flex-row gap-3 justify-center">
                        <button onclick="copyShareUrl()" class="btn-secondary flex-1 sm:flex-initial justify-center py-3">
                            Copiar Link
                        </button>
                        <button onclick="closeShareModal()" class="btn-ghost flex-1 sm:flex-initial justify-center py-3">
                            Cerrar
                        </button>
                    </div>
                </div>`;
            overlay.appendChild(modal);
            document.body.appendChild(overlay);
            overlay.addEventListener('click', e => { if (e.target === overlay) closeShareModal(); });
            window.shareModalOverlay = overlay;
        }

        function copyShareUrl() {
            const input = document.getElementById('shareUrl');
            input.select();
            document.execCommand('copy');
            const btn = event.target;
            const orig = btn.innerHTML;
            btn.innerHTML = '✓ Copiado!';
            btn.classList.add('!bg-green-500/20', '!border-green-500', '!text-green-400');
            setTimeout(() => {
                btn.innerHTML = orig;
                btn.classList.remove('!bg-green-500/20', '!border-green-500', '!text-green-400');
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
