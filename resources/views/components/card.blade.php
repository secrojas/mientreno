@props([
    'title' => null,
    'subtitle' => null,
    'headerAction' => null,
])

<div {{ $attributes->merge(['style' => 'padding:1rem;border-radius:.9rem;background:rgba(15,23,42,.95);border:1px solid var(--border-subtle);']) }}>
    @if($title || $headerAction)
        <div style="display:flex;justify-content:space-between;align-items:center;gap:.5rem;margin-bottom:.75rem;">
            <div>
                @if($title)
                    <div style="font-size:1rem;">{{ $title }}</div>
                @endif
                @if($subtitle)
                    <div style="font-size:.8rem;color:var(--text-muted);">{{ $subtitle }}</div>
                @endif
            </div>
            @if($headerAction)
                <div>{{ $headerAction }}</div>
            @endif
        </div>
    @endif

    <div>
        {{ $slot }}
    </div>
</div>
