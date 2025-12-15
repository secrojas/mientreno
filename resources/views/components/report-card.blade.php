@props([
    'title' => null,
    'subtitle' => null,
])

<div {{ $attributes->merge(['style' => 'padding:1.5rem;border-radius:.9rem;background:rgba(15,23,42,.95);border:1px solid var(--border-subtle);margin-bottom:1.5rem;']) }}>
    @if($title)
        <div style="margin-bottom:1rem;padding-bottom:.75rem;border-bottom:1px solid var(--border-subtle);">
            <h3 style="font-size:1.1rem;font-weight:600;margin:0;">{{ $title }}</h3>
            @if($subtitle)
                <p style="font-size:.85rem;color:var(--text-muted);margin:.25rem 0 0 0;">{{ $subtitle }}</p>
            @endif
        </div>
    @endif

    <div>
        {{ $slot }}
    </div>
</div>
