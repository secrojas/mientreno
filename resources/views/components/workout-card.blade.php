@props([
    'workout' => null,
    'date' => null,
    'type' => 'Entrenamiento',
    'distance' => null,
    'duration' => null,
    'pace' => null,
    'notes' => null,
    'editUrl' => null,
    'deleteUrl' => null,
])

@php
    // Determine accent color based on workout type
    $isRace = $workout ? str_contains(strtolower($workout->type ?? ''), 'carrera') : str_contains(strtolower($type), 'carrera');
    $accentColor = $isRace ? 'accent-primary' : 'accent-secondary';

    // Extract data from workout object if provided
    if ($workout) {
        $date = $date ?? $workout->date->format('d/m/Y');
        $type = $type ?? $workout->type;
        $distance = $distance ?? $workout->distance;
        $duration = $duration ?? $workout->duration;
        $pace = $pace ?? $workout->pace;
        $notes = $notes ?? $workout->notes;
        $editUrl = $editUrl ?? route('workouts.edit', $workout);
        $deleteUrl = $deleteUrl ?? route('workouts.destroy', $workout);
    }
@endphp

<div class="relative group">
    <!-- Accent border - left side -->
    <div class="absolute left-0 top-0 bottom-0 w-1 bg-{{ $accentColor }} rounded-l-card"></div>

    <!-- Main card -->
    <div class="relative bg-bg-card border border-border-subtle rounded-card overflow-hidden
                transition-all duration-300 hover:border-{{ $accentColor }}/30 hover:shadow-lg hover:shadow-{{ $accentColor }}/10">

        <!-- Diagonal motion lines background -->
        <div class="absolute inset-0 opacity-[0.03] pointer-events-none">
            <div class="absolute top-0 right-0 w-full h-full"
                 style="background: linear-gradient(135deg, transparent 48%, currentColor 48%, currentColor 52%, transparent 52%),
                        linear-gradient(135deg, transparent 73%, currentColor 73%, currentColor 77%, transparent 77%);">
            </div>
        </div>

        <!-- Content -->
        <div class="relative p-4 space-y-3">
            <!-- Header: Date & Type -->
            <div class="flex items-start justify-between gap-3">
                <div class="flex-1 min-w-0">
                    <div class="text-xs text-text-muted uppercase tracking-wider font-medium">
                        {{ $date }}
                    </div>
                    <h3 class="text-base font-semibold text-text-main mt-0.5 truncate"
                        style="font-family: 'Syne', 'Space Grotesk', sans-serif; letter-spacing: -0.01em;">
                        {{ $type }}
                    </h3>
                </div>

                <!-- Action buttons -->
                @if($editUrl || $deleteUrl)
                <div class="flex items-center gap-1 -mr-1">
                    @if($editUrl)
                    <a href="{{ $editUrl }}"
                       class="p-2.5 rounded-lg text-text-muted hover:text-text-main hover:bg-border-subtle/50
                              transition-all duration-200 active:scale-95 min-w-touch min-h-touch flex items-center justify-center">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                        </svg>
                    </a>
                    @endif

                    @if($deleteUrl)
                    <form method="POST" action="{{ $deleteUrl }}" class="inline"
                          onsubmit="return confirm('¿Estás seguro de eliminar este entrenamiento?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="p-2.5 rounded-lg text-text-muted hover:text-accent-primary hover:bg-accent-primary/10
                                       transition-all duration-200 active:scale-95 min-w-touch min-h-touch flex items-center justify-center">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="3 6 5 6 21 6"></polyline>
                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                            </svg>
                        </button>
                    </form>
                    @endif
                </div>
                @endif
            </div>

            <!-- Metrics Grid -->
            <div class="grid grid-cols-3 gap-3 pt-1">
                <!-- Distance -->
                @if($distance)
                <div class="space-y-1">
                    <div class="flex items-center gap-1.5 text-text-muted">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                        </svg>
                        <span class="text-[10px] uppercase tracking-wider font-medium">Distancia</span>
                    </div>
                    <div class="text-lg font-bold text-{{ $accentColor }}" style="font-family: 'DM Sans', sans-serif; letter-spacing: -0.02em;">
                        {{ $distance }} <span class="text-xs font-normal text-text-muted">km</span>
                    </div>
                </div>
                @endif

                <!-- Duration -->
                @if($duration)
                <div class="space-y-1">
                    <div class="flex items-center gap-1.5 text-text-muted">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                        <span class="text-[10px] uppercase tracking-wider font-medium">Tiempo</span>
                    </div>
                    <div class="text-lg font-bold text-text-main" style="font-family: 'DM Sans', sans-serif; letter-spacing: -0.02em;">
                        {{ $duration }} <span class="text-xs font-normal text-text-muted">min</span>
                    </div>
                </div>
                @endif

                <!-- Pace -->
                @if($pace)
                <div class="space-y-1">
                    <div class="flex items-center gap-1.5 text-text-muted">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
                            <polyline points="17 6 23 6 23 12"></polyline>
                        </svg>
                        <span class="text-[10px] uppercase tracking-wider font-medium">Ritmo</span>
                    </div>
                    <div class="text-lg font-bold text-text-main" style="font-family: 'DM Sans', sans-serif; letter-spacing: -0.02em;">
                        {{ $pace }} <span class="text-xs font-normal text-text-muted">min/km</span>
                    </div>
                </div>
                @endif
            </div>

            <!-- Notes -->
            @if($notes)
            <div class="pt-2 border-t border-border-subtle/50">
                <p class="text-sm text-text-muted leading-relaxed line-clamp-2">
                    {{ $notes }}
                </p>
            </div>
            @endif
        </div>

        <!-- Hover gradient overlay -->
        <div class="absolute inset-0 bg-gradient-to-br from-{{ $accentColor }}/0 to-{{ $accentColor }}/5
                    opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none rounded-card">
        </div>
    </div>
</div>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Syne:wght@600;700&family=DM+Sans:wght@400;500;700&display=swap');
</style>
