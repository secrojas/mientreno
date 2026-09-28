@php
    $catalogBrands = $catalog->pluck('brand')->unique()->values();
    $isCreateFormReopened = old('_form') === 'new-shoe';
@endphp

<x-app-layout title="Zapatillas">
    <div class="max-w-7xl mx-auto" x-data="{ showCreateForm: {{ $isCreateFormReopened ? 'true' : 'false' }}, showRetired: false }">

        {{-- Header --}}
        <header class="mb-8 flex flex-col sm:flex-row sm:items-end justify-between gap-4">
            <div>
                <h1 class="font-display text-responsive-2xl mb-2 bg-gradient-to-r from-text-main to-text-muted bg-clip-text text-transparent">
                    Zapatillas
                </h1>
                <p class="text-responsive-base text-text-muted">
                    Cada par suma sus kilómetros solo. Sabé cuándo rotar y cuándo cambiar.
                </p>
            </div>
            <button type="button" @click="showCreateForm = !showCreateForm" class="btn-primary text-sm w-full sm:w-auto justify-center">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                <span x-text="showCreateForm ? 'Cancelar' : 'Agregar Zapatillas'">Agregar Zapatillas</span>
            </button>
        </header>

        {{-- Flash messages --}}
        @php
            $flashMessages = [
                'shoe-created' => 'Zapatillas agregadas.',
                'shoe-updated' => 'Zapatillas actualizadas.',
                'shoe-default' => 'Listo: se van a preseleccionar por defecto al cargar entrenamientos.',
                'shoe-retired' => 'Zapatillas retiradas. Quedan en tu historial.',
                'shoe-reactivated' => 'Zapatillas reactivadas.',
                'shoe-deleted' => 'Zapatillas eliminadas.',
            ];
            $status = session('status');
            $isError = $status === 'shoe-deleted';
        @endphp
        @if($status && isset($flashMessages[$status]))
            <div class="px-5 py-4 {{ $isError ? 'bg-red-500/10 border-red-500/30 text-red-400' : 'bg-accent-secondary/10 border-accent-secondary/30 text-accent-secondary' }} border rounded-card mb-6 flex items-center gap-3">
                <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                    <polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
                <span class="text-sm">{{ $flashMessages[$status] }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 px-4 py-3 bg-red-500/10 border border-red-500/30 rounded-card text-red-400 text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Create form --}}
        <div x-show="showCreateForm" x-cloak x-transition @close-shoe-form="showCreateForm = false" class="mb-8">
            <x-card class="border-accent-secondary/20">
                <div class="card-header mb-4">
                    <div>
                        <div class="card-title">Nuevas Zapatillas</div>
                        <div class="card-subtitle">Elegí un modelo del catálogo o escribí el tuyo</div>
                    </div>
                </div>
                @include('shoes.partials.form', ['shoe' => null])
            </x-card>
        </div>

        {{-- Alerts --}}
        @foreach($shoesNeedingAttention as $shoe)
            @php $isWorn = $shoe->condition() === \App\Enums\ShoeCondition::Worn; @endphp
            <div class="mb-3 px-5 py-4 rounded-card border flex items-start sm:items-center gap-3 {{ $isWorn ? 'bg-accent-primary/10 border-accent-primary/30' : 'bg-amber-400/10 border-amber-400/30' }}">
                <svg class="w-5 h-5 shrink-0 {{ $isWorn ? 'text-accent-primary' : 'text-amber-400' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                </svg>
                <p class="text-sm">
                    @if($isWorn)
                        <strong>Tus {{ $shoe->name() }} superaron su vida útil</strong>
                        <span class="text-text-muted">({{ number_format($shoe->totalKm(), 0, ',', '.') }} de {{ number_format($shoe->max_km, 0, ',', '.') }} km). Una zapatilla gastada pierde amortiguación: es momento de retirarlas.</span>
                    @else
                        <strong>Tus {{ $shoe->name() }} se acercan al límite</strong>
                        <span class="text-text-muted">— les quedan {{ number_format($shoe->remainingKm(), 0, ',', '.') }} km. Buen momento para ir pensando en el recambio.</span>
                    @endif
                </p>
            </div>
        @endforeach

        {{-- Active shoes --}}
        <section class="mt-6 mb-10">
            <h2 class="flex items-center gap-3 text-xs font-bold uppercase tracking-widest text-accent-secondary mb-4">
                En uso
                <span class="text-text-muted font-normal normal-case tracking-normal">({{ $activeShoes->count() }})</span>
                <span class="flex-1 h-px bg-gradient-to-r from-accent-secondary/30 to-transparent"></span>
            </h2>

            @if($activeShoes->isEmpty())
                <div class="card border-dashed border-2 border-border-subtle text-center py-12">
                    <div class="w-40 mx-auto mb-4 text-accent-secondary">
                        <x-shoe-illustration color="currentColor" class="w-full h-auto" />
                    </div>
                    <p class="text-text-main font-medium mb-1">Cargá tu primer par</p>
                    <p class="text-text-muted text-sm mb-5">Después elegilo al registrar cada entrenamiento y MiEntreno lleva la cuenta de sus km.</p>
                    <button type="button" @click="showCreateForm = true; window.scrollTo({ top: 0, behavior: 'smooth' })" class="btn-primary text-sm mx-auto">Agregar Zapatillas</button>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                    @foreach($activeShoes as $shoe)
                        @include('shoes.partials.card', ['shoe' => $shoe])
                    @endforeach
                </div>
            @endif
        </section>

        {{-- Retired shoes --}}
        @if($retiredShoes->isNotEmpty())
            <section class="mb-8">
                <button type="button" @click="showRetired = !showRetired" class="w-full flex items-center gap-3 text-xs font-bold uppercase tracking-widest text-text-muted mb-4">
                    Retiradas
                    <span class="font-normal normal-case tracking-normal">({{ $retiredShoes->count() }} · {{ number_format($retiredShoes->sum(fn ($shoe) => $shoe->totalKm()), 0, ',', '.') }} km en total)</span>
                    <span class="flex-1 h-px bg-white/5"></span>
                    <span x-text="showRetired ? 'Ocultar' : 'Ver'" class="normal-case tracking-normal font-normal text-accent-secondary">Ver</span>
                </button>
                <div x-show="showRetired" x-cloak class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 opacity-80">
                    @foreach($retiredShoes as $shoe)
                        @include('shoes.partials.card', ['shoe' => $shoe])
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Catalog datalists --}}
        <datalist id="shoe-brands">
            @foreach($catalogBrands as $brand)
                <option value="{{ $brand }}"></option>
            @endforeach
        </datalist>
        @foreach($catalog->groupBy('brand') as $brand => $models)
            <datalist id="shoe-models-{{ \Illuminate\Support\Str::slug($brand) }}">
                @foreach($models as $catalogModel)
                    <option value="{{ $catalogModel['model'] }}"></option>
                @endforeach
            </datalist>
        @endforeach
        <datalist id="shoe-models-">
            @foreach($catalog as $catalogModel)
                <option value="{{ $catalogModel['model'] }}">{{ $catalogModel['brand'] }}</option>
            @endforeach
        </datalist>
    </div>

    <script>
        function shoeForm(initial, catalog) {
            const defaultMaxKmByUsage = {{ \Illuminate\Support\Js::from(collect(\App\Enums\ShoeUsage::cases())->mapWithKeys(fn ($shoeUsage) => [$shoeUsage->value => $shoeUsage->defaultMaxKm()])) }};
            const normalize = (value) => (value || '').trim().toLowerCase();

            return {
                brand: initial.brand || '',
                model: initial.model || '',
                usage: initial.usage || '',
                maxKm: initial.max_km || 700,
                color: initial.color || '#2DE38E',
                catalogMatch: false,
                brandKey() {
                    return normalize(this.brand).normalize('NFD').replace(/[̀-ͯ]/g, '').replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
                },
                applyCatalog() {
                    let match = catalog.find((item) => normalize(item.brand) === normalize(this.brand) && normalize(item.model) === normalize(this.model));

                    if (! match && ! this.brand) {
                        match = catalog.find((item) => normalize(item.model) === normalize(this.model));
                        if (match) { this.brand = match.brand; }
                    }

                    this.catalogMatch = Boolean(match);
                    if (match) {
                        this.brand = match.brand;
                        this.model = match.model;
                        this.usage = match.usage;
                        this.maxKm = match.max_km;
                    }
                },
                init() {
                    this.$watch('usage', (usage) => {
                        if (! this.catalogMatch && usage && defaultMaxKmByUsage[usage]) {
                            this.maxKm = defaultMaxKmByUsage[usage];
                        }
                    });
                }
            };
        }
    </script>
</x-app-layout>
