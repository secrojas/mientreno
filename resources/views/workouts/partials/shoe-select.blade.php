@php
    $selectedShoeId = old('shoe_id', $selectedShoeId ?? null);
@endphp

<div>
    <label for="shoe_id" class="form-label">Zapatillas</label>
    @if($shoes->isEmpty())
        <div class="px-4 py-3 rounded-btn border border-dashed border-border-subtle text-sm text-text-muted">
            Todavía no cargaste zapatillas.
            <a href="{{ route('shoes.index') }}" class="text-accent-secondary hover:underline">Agregá tu primer par</a>
            y MiEntreno va a llevar la cuenta de sus kilómetros.
        </div>
    @else
        <select id="shoe_id" name="shoe_id" class="form-select">
            <option value="">Sin especificar</option>
            @foreach($shoes as $shoe)
                <option value="{{ $shoe->id }}" @selected((string) $selectedShoeId === (string) $shoe->id)>
                    {{ $shoe->name() }}{{ $shoe->nickname ? ' ('.$shoe->nickname.')' : '' }} · {{ number_format($shoe->totalKm(), 0, ',', '.') }} km{{ $shoe->isRetired() ? ' · retirada' : '' }}
                </option>
            @endforeach
        </select>
        <small class="text-xs text-text-muted block mt-1">
            Los km de este entrenamiento se suman a las zapatillas elegidas.
        </small>
    @endif
</div>
