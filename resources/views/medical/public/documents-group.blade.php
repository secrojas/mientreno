@php
    $reportTitle = $group->title;
    $reportSubtitle = 'Estudios médicos de '.$group->user->name;
@endphp

<x-public-layout
    :title="$reportTitle"
    :subtitle="$reportSubtitle"
>
    {{-- Aviso compartido --}}
    <div class="public-notice">
        <strong>Reporte de Estudios</strong> de {{ $group->user->name }}<br>
        <span style="font-size:0.85rem;">
            @if($group->doctor)
                Para: Dr. {{ $group->doctor->name }}{{ $group->doctor->specialty ? ' — '.$group->doctor->specialty : '' }} &bull;
            @endif
            Expira: {{ $share->expires_at->format('d/m/Y H:i') }} &bull;
            Vistas: {{ $share->view_count }}
        </span>
    </div>

    @if($group->user->health_insurance_provider || $group->user->health_insurance_plan || $group->user->health_insurance_member_number)
        <div style="border:1px solid var(--border-subtle);border-radius:0.5rem;padding:0.85rem 1.25rem;margin-bottom:1.5rem;font-size:0.85rem;color:#94A3B8;">
            <strong style="color:var(--text-main);">Obra Social:</strong>
            {{ $group->user->health_insurance_provider ?: '—' }}
            @if($group->user->health_insurance_plan) &bull; Plan {{ $group->user->health_insurance_plan }} @endif
            @if($group->user->health_insurance_member_number) &bull; N° Credencial: {{ $group->user->health_insurance_member_number }} @endif
        </div>
    @endif

    @if($group->notes)
        <div style="margin-bottom:1.5rem;font-size:0.9rem;color:#64748B;">
            {{ $group->notes }}
        </div>
    @endif

    <div style="margin-bottom:1.5rem;">
        <a href="{{ route('medical.groups.shared-zip', $share->token) }}"
           style="display:inline-flex;align-items:center;gap:0.5rem;background:var(--accent-secondary);color:#05060A;font-weight:700;padding:0.65rem 1.25rem;border-radius:0.5rem;font-size:0.85rem;">
            ⬇ Descargar todo (ZIP)
        </a>
    </div>

    <h3 style="font-size:0.75rem;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:#94A3B8;margin-bottom:0.75rem;">
        Estudios ({{ $group->documents->count() }})
    </h3>

    @foreach($group->documents as $document)
        <div style="border:1px solid var(--border-subtle);border-radius:0.5rem;padding:1rem 1.25rem;margin-bottom:0.75rem;display:flex;align-items:center;justify-content:space-between;gap:1rem;flex-wrap:wrap;">
            <div style="min-width:0;">
                <div style="font-weight:600;margin-bottom:0.25rem;">{{ $document->title }}</div>
                <div style="font-size:0.8rem;color:#94A3B8;">
                    {{ $document->type->label() }}
                    @if($document->issued_at)
                        &bull; {{ $document->issued_at->format('d/m/Y') }}
                    @endif
                </div>
            </div>
            <div style="display:flex;gap:0.5rem;flex-shrink:0;">
                <a href="{{ route('medical.groups.shared-document-preview', [$share->token, $document]) }}"
                   target="_blank"
                   style="display:inline-flex;align-items:center;gap:0.4rem;border:1px solid var(--border-subtle);color:var(--text-main);padding:0.5rem 1rem;border-radius:0.5rem;font-size:0.8rem;font-weight:600;">
                    Ver
                </a>
                @if($document->images_url)
                    <a href="{{ $document->images_url }}"
                       target="_blank" rel="noopener noreferrer"
                       style="display:inline-flex;align-items:center;gap:0.4rem;border:1px solid var(--border-subtle);color:var(--text-main);padding:0.5rem 1rem;border-radius:0.5rem;font-size:0.8rem;font-weight:600;">
                        Imágenes
                    </a>
                @endif
            </div>
        </div>
    @endforeach
</x-public-layout>
