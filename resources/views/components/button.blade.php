@props([
    'variant' => 'primary', // 'primary', 'secondary', 'ghost', 'danger'
    'size' => 'md', // 'sm', 'md', 'lg'
    'icon' => null,
])

@php
    $baseStyle = 'display:inline-flex;align-items:center;gap:.35rem;font-weight:500;transition:all .15s ease-out;cursor:pointer;border:none;';

    // Variantes
    $variantStyles = [
        'primary' => 'background:var(--accent-primary);color:white;border:1px solid var(--accent-primary);',
        'secondary' => 'background:transparent;color:var(--accent-secondary);border:1px solid var(--accent-secondary);',
        'ghost' => 'background:rgba(249,250,251,.05);color:var(--text-main);border:1px solid var(--border-subtle);',
        'danger' => 'background:rgba(239,68,68,.15);color:#EF4444;border:1px solid #DC2626;',
    ];

    // Tamaños
    $sizeStyles = [
        'sm' => 'padding:.35rem .7rem;font-size:.8rem;border-radius:.6rem;',
        'md' => 'padding:.45rem .9rem;font-size:.85rem;border-radius:.7rem;',
        'lg' => 'padding:.6rem 1.2rem;font-size:.9rem;border-radius:.8rem;',
    ];

    $style = $baseStyle . ($variantStyles[$variant] ?? $variantStyles['primary']) . ($sizeStyles[$size] ?? $sizeStyles['md']);
@endphp

<button {{ $attributes->merge(['type' => 'button', 'style' => $style]) }}>
    @if($icon)
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:{{ $size === 'sm' ? '14px' : ($size === 'lg' ? '18px' : '16px') }};height:{{ $size === 'sm' ? '14px' : ($size === 'lg' ? '18px' : '16px') }};">
            {!! $icon !!}
        </svg>
    @endif
    {{ $slot }}
</button>
