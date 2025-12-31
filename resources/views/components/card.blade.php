@props([
    'title' => null,
    'subtitle' => null,
    'headerAction' => null,
])

<div {{ $attributes->merge(['class' => 'card']) }}>
    @if($title || $headerAction)
        <div class="card-header">
            <div>
                @if($title)
                    <div class="card-title">{{ $title }}</div>
                @endif
                @if($subtitle)
                    <div class="card-subtitle">{{ $subtitle }}</div>
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
