@props([
    'label' => '',
    'current' => 0,
    'previous' => 0,
    'diff' => [],
    'unit' => '',
    'invertTrend' => false, // Para métricas donde menor es mejor (ej: pace)
])

@php
    $trend = $diff['trend'] ?? 'stable';
    $percentage = $diff['percentage'] ?? 0;
    $diffValue = $diff['diff'] ?? 0;

    // Determinar color según tendencia
    $trendColor = 'var(--text-muted)'; // stable
    $trendIcon = '➡️';

    if ($trend === 'up') {
        $trendColor = $invertTrend ? 'var(--danger)' : 'var(--success)';
        $trendIcon = '↗️';
    } elseif ($trend === 'down') {
        $trendColor = $invertTrend ? 'var(--success)' : 'var(--danger)';
        $trendIcon = '↘️';
    }
@endphp

<div {{ $attributes->merge(['style' => 'padding:1rem;border-radius:.6rem;background:rgba(30,41,59,.5);border:1px solid var(--border-subtle);']) }}>
    <div style="font-size:.85rem;color:var(--text-muted);margin-bottom:.5rem;">{{ $label }}</div>

    <div style="display:flex;align-items:baseline;gap:.75rem;margin-bottom:.5rem;">
        <div style="font-size:1.5rem;font-weight:600;color:var(--text-primary);">
            {{ $current }}{{ $unit }}
        </div>

        @if($previous > 0)
            <div style="font-size:.9rem;color:{{ $trendColor }};display:flex;align-items:center;gap:.25rem;">
                <span>{{ $trendIcon }}</span>
                <span>
                    @if($diffValue > 0)+@endif{{ $diffValue }}{{ $unit }}
                    @if($percentage != 0)
                        ({{ $percentage > 0 ? '+' : '' }}{{ $percentage }}%)
                    @endif
                </span>
            </div>
        @endif
    </div>

    @if($previous > 0)
        <div style="font-size:.75rem;color:var(--text-muted);">
            Anterior: {{ $previous }}{{ $unit }}
        </div>
    @endif
</div>
