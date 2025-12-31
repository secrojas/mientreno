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
    $trendColor = 'text-text-muted'; // stable
    $trendIcon = '➡️';

    if ($trend === 'up') {
        $trendColor = $invertTrend ? 'text-red-500' : 'text-accent-secondary';
        $trendIcon = '↗️';
    } elseif ($trend === 'down') {
        $trendColor = $invertTrend ? 'text-accent-secondary' : 'text-red-500';
        $trendIcon = '↘️';
    }
@endphp

<div {{ $attributes->merge(['class' => 'p-4 rounded-btn bg-bg-card/50 border border-border-subtle']) }}>
    <div class="text-sm text-text-muted mb-2">{{ $label }}</div>

    <div class="flex flex-col xs:flex-row xs:items-baseline gap-2 xs:gap-3 mb-2">
        <div class="text-2xl font-semibold text-text-main">
            {{ $current }}{{ $unit }}
        </div>

        @if($previous > 0)
            <div class="text-sm {{ $trendColor }} flex items-center gap-1">
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
        <div class="text-xs text-text-muted">
            Anterior: {{ $previous }}{{ $unit }}
        </div>
    @endif
</div>
