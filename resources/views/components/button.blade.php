@props([
    'variant' => 'primary', // 'primary', 'secondary', 'ghost', 'danger'
    'size' => 'md', // 'sm', 'md', 'lg'
    'icon' => null,
])

@php
    $variantClasses = [
        'primary' => 'btn-primary',
        'secondary' => 'btn-secondary',
        'ghost' => 'btn-ghost',
        'danger' => 'btn px-3 py-2 rounded-btn text-sm bg-red-500/15 text-red-500 border border-red-600 hover:bg-red-500/25',
    ];

    $sizeClasses = [
        'sm' => 'px-3 py-1.5 text-xs',
        'md' => 'px-4 py-2.5 text-sm',
        'lg' => 'px-5 py-3 text-base',
    ];

    $iconSizes = [
        'sm' => 'w-3.5 h-3.5',
        'md' => 'w-4 h-4',
        'lg' => 'w-[18px] h-[18px]',
    ];

    $baseClass = $variantClasses[$variant] ?? $variantClasses['primary'];

    // Si no es un variant predefinido (primary, secondary, ghost), aplicar tamaño
    if (!in_array($variant, ['primary', 'secondary', 'ghost'])) {
        $baseClass .= ' ' . ($sizeClasses[$size] ?? $sizeClasses['md']);
    }
@endphp

<button {{ $attributes->merge(['type' => 'button', 'class' => $baseClass]) }}>
    @if($icon)
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="{{ $iconSizes[$size] ?? $iconSizes['md'] }}">
            {!! $icon !!}
        </svg>
    @endif
    {{ $slot }}
</button>
