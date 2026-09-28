@props([
    'color' => '#2DE38E',
])

<svg viewBox="0 0 240 120" fill="none" xmlns="http://www.w3.org/2000/svg" role="img" aria-hidden="true" {{ $attributes }}>
    {{-- Sombra --}}
    <ellipse cx="124" cy="112" rx="104" ry="5" fill="#000" opacity=".35"/>

    {{-- Suela --}}
    <path d="M16 90c0 12 10 18 30 18h160c16 0 26-6 26-16l-4-6H20z" fill="#E5E7EB"/>
    <path d="M18 100c4 5 14 8 28 8h160c14 0 22-4 25-10" stroke="#9CA3AF" stroke-width="3" stroke-linecap="round"/>
    <path d="M20 86h204" stroke="#CBD5E1" stroke-width="2"/>

    {{-- Capellada --}}
    <path d="M22 86c-4-18 4-32 24-34l42-4c12-15 28-25 46-26h14c6 14 22 26 46 34 22 7 32 16 32 30z" fill="{{ $color }}"/>
    {{-- Sombra de volumen --}}
    <path d="M22 86c-4-18 4-32 24-34l42-4c-6 14-2 26 16 38z" fill="#000" opacity=".18"/>
    {{-- Franja lateral --}}
    <path d="M60 80c30-4 70-18 104-38" stroke="#fff" stroke-width="7" stroke-linecap="round" opacity=".55"/>
    {{-- Talonera --}}
    <path d="M24 66c2-8 10-13 22-14" stroke="#000" stroke-width="5" stroke-linecap="round" opacity=".25"/>
    {{-- Cordones --}}
    <g stroke="#fff" stroke-width="3" stroke-linecap="round" opacity=".85">
        <path d="M122 30l10 8"/>
        <path d="M132 26l10 9"/>
        <path d="M112 36l10 8"/>
    </g>
    {{-- Puntera --}}
    <path d="M196 60c14 5 24 12 30 22" stroke="#fff" stroke-width="3" stroke-linecap="round" opacity=".35"/>
</svg>
