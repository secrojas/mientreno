@php
    $medicalTabs = [
        'documents' => ['label' => 'Documentos y Estudios', 'short_label' => 'Estudios', 'route' => 'medical.index'],
        'appointments' => ['label' => 'Turnos', 'short_label' => 'Turnos', 'route' => 'medical.appointments.index'],
        'orders' => ['label' => 'Órdenes Médicas', 'short_label' => 'Órdenes', 'route' => 'medical.orders.index'],
    ];
@endphp

<div class="flex gap-1 mb-8 border-b border-white/5">
    @foreach($medicalTabs as $key => $tab)
        @if($key === $active)
            <span class="px-3 sm:px-4 py-2.5 text-sm font-medium text-accent-secondary border-b-2 border-accent-secondary whitespace-nowrap">
                <span class="sm:hidden">{{ $tab['short_label'] }}</span>
                <span class="hidden sm:inline">{{ $tab['label'] }}</span>
            </span>
        @else
            <a href="{{ route($tab['route']) }}" class="px-3 sm:px-4 py-2.5 text-sm font-medium text-text-muted hover:text-text-main transition-colors whitespace-nowrap">
                <span class="sm:hidden">{{ $tab['short_label'] }}</span>
                <span class="hidden sm:inline">{{ $tab['label'] }}</span>
            </a>
        @endif
    @endforeach
</div>
