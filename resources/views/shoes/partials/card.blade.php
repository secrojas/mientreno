@php
    /** @var \App\Models\Shoe $shoe */
    $condition = $shoe->condition();
    $totalKm = $shoe->totalKm();
    $remainingKm = $shoe->remainingKm();
    $ringCircumference = 2 * M_PI * 42;
    $ringOffset = $ringCircumference * (1 - min($shoe->usedRatio(), 1));
    $costPerKm = $shoe->costPerKm();
    $shoeRotation = $rotation->get($shoe->id, collect());
@endphp

<div class="card flex flex-col" x-data="{ editing: false, confirmDelete: false }" @close-shoe-form="editing = false">
    <div x-show="!editing" class="flex flex-col flex-1">
        {{-- Header --}}
        <div class="flex items-start justify-between gap-3 mb-3">
            <div class="min-w-0">
                <div class="flex items-center gap-2">
                    <h3 class="font-display text-lg leading-tight truncate">{{ $shoe->model }}</h3>
                    @if($shoe->is_default)
                        <span class="text-amber-400" title="Predeterminada al cargar entrenamientos">★</span>
                    @endif
                </div>
                <p class="text-xs text-text-muted truncate">
                    {{ $shoe->brand }}{{ $shoe->nickname ? ' · "'.$shoe->nickname.'"' : '' }}{{ $shoe->usage ? ' · '.$shoe->usage->label() : '' }}
                </p>
            </div>
            <span class="shrink-0 inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs border {{ $condition->badgeClass() }}">
                <span class="w-1.5 h-1.5 rounded-full bg-current"></span>{{ $condition->label() }}
            </span>
        </div>

        {{-- Visual + ring --}}
        <div class="flex items-center gap-4 mb-4">
            <div class="flex-1 min-w-0 aspect-[2/1] rounded-card bg-bg-sidebar border border-border-subtle flex items-center justify-center overflow-hidden {{ $shoe->photo_path ? '' : 'p-3' }}">
                @if($shoe->photo_path)
                    <img src="{{ route('shoes.photo', $shoe) }}" alt="{{ $shoe->name() }}" class="w-full h-full object-cover" loading="lazy">
                @else
                    <x-shoe-illustration :color="$shoe->color" class="w-full h-auto" />
                @endif
            </div>
            <div class="relative w-24 h-24 shrink-0">
                <svg viewBox="0 0 100 100" class="w-24 h-24 -rotate-90">
                    <circle cx="50" cy="50" r="42" fill="none" stroke="rgba(255,255,255,.06)" stroke-width="8"/>
                    <circle cx="50" cy="50" r="42" fill="none" stroke="{{ $condition->ringColor() }}" stroke-width="8" stroke-linecap="round"
                            stroke-dasharray="{{ round($ringCircumference, 2) }}" stroke-dashoffset="{{ round($ringOffset, 2) }}"/>
                </svg>
                <div class="absolute inset-0 flex flex-col items-center justify-center">
                    <span class="font-display text-xl leading-none">{{ number_format($totalKm, 0, ',', '.') }}</span>
                    <span class="text-[10px] text-text-muted">de {{ number_format($shoe->max_km, 0, ',', '.') }} km</span>
                </div>
            </div>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-2 gap-2 text-xs mb-3">
            <div class="rounded-btn bg-white/[.03] px-3 py-2">
                <div class="text-text-muted">{{ $remainingKm >= 0 ? 'Le quedan' : 'Pasada por' }}</div>
                <div class="font-medium text-sm {{ $remainingKm < 0 ? 'text-accent-primary' : '' }}">{{ number_format(abs($remainingKm), 0, ',', '.') }} km</div>
            </div>
            <div class="rounded-btn bg-white/[.03] px-3 py-2">
                <div class="text-text-muted">Entrenamientos</div>
                <div class="font-medium text-sm">{{ $shoe->completed_sessions ?? 0 }}</div>
            </div>
            <div class="rounded-btn bg-white/[.03] px-3 py-2">
                <div class="text-text-muted">Costo por km</div>
                <div class="font-medium text-sm">{{ $costPerKm !== null ? '$ '.number_format($costPerKm, $costPerKm < 100 ? 2 : 0, ',', '.') : '—' }}</div>
            </div>
            <div class="rounded-btn bg-white/[.03] px-3 py-2">
                <div class="text-text-muted">Último uso</div>
                <div class="font-medium text-sm">{{ $shoe->last_used_at ? \Illuminate\Support\Carbon::parse($shoe->last_used_at)->format('d/m/Y') : '—' }}</div>
            </div>
        </div>

        {{-- Rotation --}}
        @if($shoeRotation->isNotEmpty())
            <div class="mb-3">
                <div class="flex h-1.5 rounded-full overflow-hidden bg-white/5 mb-1.5">
                    @foreach($shoeRotation as $index => $typeUsage)
                        <span style="width: {{ $typeUsage['percentage'] }}%; background: {{ ['#2DE38E', '#60A5FA', '#F59E0B', '#FF4FA3', '#A78BFA', '#14B8A6', '#FF3B5C'][$index % 7] }};"></span>
                    @endforeach
                </div>
                <div class="flex flex-wrap gap-x-3 gap-y-0.5 text-[11px] text-text-muted">
                    @foreach($shoeRotation->take(3) as $typeUsage)
                        <span>{{ $typeUsage['label'] }} {{ $typeUsage['percentage'] }}%</span>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Actions --}}
        <div class="mt-auto pt-3 border-t border-white/5 flex items-center gap-0.5">
            <button type="button" @click="editing = true" class="btn-ghost text-xs px-2 py-1.5">Editar</button>

            @if(! $shoe->isRetired() && ! $shoe->is_default)
                <form action="{{ route('shoes.default', $shoe) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn-ghost text-xs px-2 py-1.5 whitespace-nowrap" title="Preseleccionarla al cargar entrenamientos">★ Por defecto</button>
                </form>
            @endif

            <form action="{{ route('shoes.retire', $shoe) }}" method="POST">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn-ghost text-xs px-2 py-1.5 {{ $condition === \App\Enums\ShoeCondition::Worn ? 'text-accent-primary' : '' }}">
                    {{ $shoe->isRetired() ? 'Reactivar' : 'Retirar' }}
                </button>
            </form>

            <div class="ml-auto">
                <div x-show="!confirmDelete">
                    <button type="button" @click="confirmDelete = true" class="btn-ghost text-xs px-2.5 py-1.5 text-red-400 hover:text-red-300 hover:bg-red-400/10" title="Eliminar">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/>
                        </svg>
                    </button>
                </div>
                <div x-show="confirmDelete" class="flex items-center gap-2" style="display: none;">
                    <span class="text-xs text-red-400">¿Eliminar?</span>
                    <form action="{{ route('shoes.destroy', $shoe) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-xs px-2 py-1 rounded bg-red-500/20 text-red-400 border border-red-500/30 hover:bg-red-500/30 transition-colors">Sí</button>
                    </form>
                    <button type="button" @click="confirmDelete = false" class="text-xs px-2 py-1 rounded bg-border-subtle text-text-muted hover:text-text-main transition-colors">No</button>
                </div>
            </div>
        </div>
    </div>

    <div x-show="editing" x-cloak>
        @include('shoes.partials.form', ['shoe' => $shoe])
    </div>
</div>
