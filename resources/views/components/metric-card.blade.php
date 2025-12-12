@props([
    'label' => '',
    'value' => '',
    'subtitle' => '',
    'accent' => 'secondary', // 'primary', 'secondary'
])

@php
    $accentColor = $accent === 'primary' ? 'var(--accent-primary)' : 'var(--accent-secondary)';
@endphp

<div {{ $attributes->merge(['style' => 'padding:1rem;border-radius:.9rem;background:rgba(15,23,42,.95);border:1px solid var(--border-subtle);']) }}>
    <div style="font-size:.75rem;color:var(--text-muted);margin-bottom:.25rem;">{{ $label }}</div>
    <div style="font-size:1.4rem;font-weight:600;font-family:'Space Grotesk',monospace;">
        {{ $value }}
    </div>
    @if($subtitle)
        <div style="font-size:.78rem;color:{{ $accentColor }};margin-top:.2rem;">
            {{ $subtitle }}
        </div>
    @endif
</div>
