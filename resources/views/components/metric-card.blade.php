@props([
    'label' => '',
    'value' => '',
    'subtitle' => '',
    'accent' => 'secondary', // 'primary', 'secondary'
])

@php
    $accentClass = $accent === 'primary' ? 'text-accent-primary' : 'text-accent-secondary';
@endphp

<div {{ $attributes->merge(['class' => 'metric-card']) }}>
    <div class="metric-label">{{ $label }}</div>
    <div class="metric-value">
        {{ $value }}
    </div>
    @if($subtitle)
        <div class="metric-subtitle {{ $accentClass }}">
            {{ $subtitle }}
        </div>
    @endif
</div>
