@props([
    'title' => 'Filtros',
    'icon' => null,
    'defaultOpen' => false,
])

<div x-data="{ open: {{ $defaultOpen ? 'true' : 'false' }} }" class="border border-border-subtle rounded-card overflow-hidden bg-bg-card">
    <!-- Accordion Header / Trigger -->
    <button @click="open = !open"
            type="button"
            class="w-full flex items-center justify-between gap-3 p-4 min-h-touch
                   text-left transition-all duration-200
                   hover:bg-border-subtle/30 active:bg-border-subtle/50"
            :class="open ? 'bg-border-subtle/20' : 'bg-transparent'">

        <div class="flex items-center gap-3 flex-1 min-w-0">
            @if($icon)
            <div class="flex-shrink-0 w-5 h-5 text-accent-secondary">
                {!! $icon !!}
            </div>
            @else
            <!-- Default filter icon -->
            <svg class="flex-shrink-0 w-5 h-5 text-accent-secondary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
            </svg>
            @endif

            <span class="font-semibold text-text-main text-base truncate"
                  style="font-family: 'Syne', 'Space Grotesk', sans-serif; letter-spacing: -0.01em;">
                {{ $title }}
            </span>
        </div>

        <!-- Chevron indicator -->
        <svg class="flex-shrink-0 w-5 h-5 text-text-muted transition-transform duration-300"
             :class="open ? 'rotate-180' : 'rotate-0'"
             viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="6 9 12 15 18 9"></polyline>
        </svg>
    </button>

    <!-- Accordion Content -->
    <div x-show="open"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="border-t border-border-subtle/50"
         style="display: none;">

        <div class="p-4 space-y-3 bg-gradient-to-b from-border-subtle/10 to-transparent">
            {{ $slot }}
        </div>
    </div>
</div>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Syne:wght@600;700&display=swap');
</style>
