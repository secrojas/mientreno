@php
    /** @var \App\Models\Shoe|null $shoe */
    $shoe = $shoe ?? null;
    $isEditing = $shoe !== null;
    $formId = $isEditing ? 'shoe-'.$shoe->id : 'new-shoe';
    $useOldInput = old('_form') === $formId;
    $initial = [
        'brand' => $useOldInput ? old('brand') : ($shoe->brand ?? ''),
        'model' => $useOldInput ? old('model') : ($shoe->model ?? ''),
        'usage' => $useOldInput ? old('usage') : ($shoe?->usage?->value ?? ''),
        'max_km' => $useOldInput ? old('max_km') : ($shoe->max_km ?? 700),
        'color' => $useOldInput ? old('color') : ($shoe->color ?? $colors[0]),
    ];
@endphp

<form action="{{ $isEditing ? route('shoes.update', $shoe) : route('shoes.store') }}" method="POST" enctype="multipart/form-data"
      x-data="shoeForm({{ \Illuminate\Support\Js::from($initial) }}, {{ \Illuminate\Support\Js::from($catalog) }})">
    @csrf
    @if($isEditing)
        @method('PUT')
    @endif
    <input type="hidden" name="_form" value="{{ $formId }}">

    <div class="flex flex-col sm:flex-row gap-5">
        {{-- Preview --}}
        <div class="sm:w-48 shrink-0 flex flex-col items-center gap-3">
            <div class="w-full aspect-[2/1] rounded-card bg-bg-sidebar border border-border-subtle flex items-center justify-center p-3" :style="{ color: color }">
                <x-shoe-illustration color="currentColor" class="w-full h-auto" />
            </div>
            <div class="flex flex-wrap justify-center gap-1.5">
                @foreach($colors as $swatch)
                    <button type="button" @click="color = '{{ $swatch }}'"
                            class="w-6 h-6 rounded-full border-2 transition-transform hover:scale-110"
                            :class="color.toUpperCase() === '{{ strtoupper($swatch) }}' ? 'border-text-main' : 'border-white/15'"
                            style="background: {{ $swatch }};" title="{{ $swatch }}"></button>
                @endforeach
                <label class="w-6 h-6 rounded-full border-2 border-dashed border-text-muted/60 flex items-center justify-center cursor-pointer text-[10px] text-text-muted" title="Otro color">
                    <input type="color" x-model="color" class="sr-only">+
                </label>
            </div>
            <input type="hidden" name="color" :value="color">
        </div>

        {{-- Fields --}}
        <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="form-label">Marca <span class="text-accent-primary">*</span></label>
                <input type="text" name="brand" x-model="brand" @change="applyCatalog()" list="shoe-brands" required maxlength="60" placeholder="ej: Nike" class="form-input" autocomplete="off">
            </div>
            <div>
                <label class="form-label">Modelo <span class="text-accent-primary">*</span></label>
                <input type="text" name="model" x-model="model" @change="applyCatalog()" :list="'shoe-models-' + brandKey()" required maxlength="100" placeholder="ej: Pegasus 41" class="form-input" autocomplete="off">
                <p x-show="catalogMatch" x-cloak class="text-xs text-accent-secondary mt-1">✓ Modelo del catálogo: completamos uso y vida útil.</p>
            </div>

            <div>
                <label class="form-label">Apodo <span class="text-xs text-text-muted">(opcional)</span></label>
                <input type="text" name="nickname" value="{{ $useOldInput ? old('nickname') : ($shoe->nickname ?? '') }}" maxlength="60" placeholder="ej: Las rojas" class="form-input">
            </div>
            <div>
                <label class="form-label">Uso principal</label>
                <select name="usage" x-model="usage" class="form-select">
                    <option value="">Sin especificar</option>
                    @foreach(\App\Enums\ShoeUsage::cases() as $shoeUsage)
                        <option value="{{ $shoeUsage->value }}">{{ $shoeUsage->label() }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="form-label">Vida útil (km) <span class="text-accent-primary">*</span></label>
                <input type="number" name="max_km" x-model="maxKm" required min="100" max="3000" step="10" class="form-input">
            </div>
            <div>
                <label class="form-label">Km que ya tenía <span class="text-xs text-text-muted">(si no es nueva)</span></label>
                <input type="number" name="initial_km" value="{{ $useOldInput ? old('initial_km') : ($shoe?->initial_km ? (float) $shoe->initial_km : '') }}" min="0" max="5000" step="0.1" placeholder="0" class="form-input">
            </div>

            <div>
                <label class="form-label">Fecha de compra</label>
                <input type="date" name="purchased_at" value="{{ $useOldInput ? old('purchased_at') : $shoe?->purchased_at?->format('Y-m-d') }}" max="{{ now()->format('Y-m-d') }}" class="form-input">
            </div>
            <div>
                <label class="form-label">Precio pagado <span class="text-xs text-text-muted">(para el costo por km)</span></label>
                <input type="number" name="price" value="{{ $useOldInput ? old('price') : ($shoe?->price ? (float) $shoe->price : '') }}" min="0" step="1" placeholder="$" class="form-input">
            </div>

            <div class="sm:col-span-2">
                <label class="form-label">Foto <span class="text-xs text-text-muted">(opcional, reemplaza la ilustración)</span></label>
                <input type="file" name="photo" accept=".jpg,.jpeg,.png,.webp" class="form-input">
                @if($isEditing && $shoe->photo_path)
                    <label class="flex items-center gap-2 text-xs text-text-muted mt-2 cursor-pointer">
                        <input type="checkbox" name="remove_photo" value="1" class="w-4 h-4 rounded border-border-subtle text-accent-secondary focus:ring-accent-secondary/50">
                        Quitar la foto actual
                    </label>
                @endif
            </div>

            <div class="sm:col-span-2">
                <label class="form-label">Notas</label>
                <textarea name="notes" rows="2" maxlength="1000" placeholder="Talle, dónde la compraste, sensaciones…" class="form-input resize-none">{{ $useOldInput ? old('notes') : ($shoe->notes ?? '') }}</textarea>
            </div>

            @if(! $isEditing || ! $shoe->isRetired())
                <label class="sm:col-span-2 flex items-center gap-2 text-sm cursor-pointer">
                    <input type="checkbox" name="is_default" value="1" @checked($useOldInput ? old('is_default') : ($shoe->is_default ?? false))
                           class="w-4 h-4 rounded border-border-subtle text-accent-secondary focus:ring-accent-secondary/50">
                    Usarla por defecto al cargar entrenamientos
                </label>
            @endif
        </div>
    </div>

    <div class="flex justify-end gap-3 mt-6 pt-5 border-t border-white/5">
        <button type="button" @click="$dispatch('close-shoe-form')" class="btn-ghost text-sm">Cancelar</button>
        <button type="submit" class="btn-primary text-sm">{{ $isEditing ? 'Guardar Cambios' : 'Agregar Zapatillas' }}</button>
    </div>
</form>
