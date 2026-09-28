@php
    $isOverdue = $task->isOverdue();
@endphp

<li class="flex items-start gap-3 text-sm group">
    <form action="{{ route('medical.appointments.tasks.toggle', $task) }}" method="POST" class="shrink-0 pt-0.5">
        @csrf
        @method('PATCH')
        <button type="submit"
                class="w-4 h-4 rounded border flex items-center justify-center transition-colors {{ $task->isCompleted() ? 'bg-accent-secondary border-accent-secondary text-bg-main' : 'border-text-muted/60 hover:border-accent-secondary' }}"
                title="{{ $task->isCompleted() ? 'Marcar como pendiente' : 'Marcar como hecho' }}">
            @if($task->isCompleted())
                <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
            @endif
        </button>
    </form>

    <div class="flex-1 min-w-0">
        <div class="{{ $task->isCompleted() ? 'line-through text-text-muted' : '' }}">
            <span title="{{ $task->type->label() }}">{{ $task->type->icon() }}</span>
            {{ $task->description }}
        </div>
        <div class="flex flex-wrap gap-x-3 text-xs text-text-muted">
            <span>{{ $task->type->label() }}</span>
            @if($task->due_date)
                <span class="{{ $isOverdue ? 'text-red-400' : '' }}">{{ $isOverdue ? 'Vencida el' : 'Hasta el' }} {{ $task->due_date->format('d/m/Y') }}</span>
            @endif
            @if($showOrigin)
                <span>De: {{ $task->appointment->doctor?->name ?? 'turno' }} · {{ $task->appointment->scheduled_at->format('d/m/Y') }}</span>
            @endif
            @if($task->followUpAppointment)
                <span class="text-accent-secondary">Turno agendado: {{ $task->followUpAppointment->scheduled_at->format('d/m/Y H:i') }}</span>
            @endif
        </div>
    </div>

    <div class="flex items-center gap-2 shrink-0">
        @if($task->type === \App\Enums\AppointmentTaskType::Referral && ! $task->followUpAppointment && ! $task->isCompleted())
            <button type="button"
                    @click="$dispatch('open-appointment-form', {{ \Illuminate\Support\Js::from([
                        'referredFromId' => $task->medical_appointment_id,
                        'referredFromLabel' => ($task->appointment->doctor?->name ?? 'Turno').' · '.$task->appointment->scheduled_at->format('d/m/Y'),
                        'taskId' => $task->id,
                        'reason' => $task->description,
                    ]) }})"
                    class="text-xs px-2 py-1 rounded-btn border border-accent-secondary/40 text-accent-secondary hover:bg-accent-secondary/10 transition-colors whitespace-nowrap">
                Agendar turno
            </button>
        @endif
        <form action="{{ route('medical.appointments.tasks.destroy', $task) }}" method="POST"
              onsubmit="return confirm('¿Eliminar esta indicación?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="text-text-muted hover:text-red-400 text-xs px-1 sm:opacity-0 sm:group-hover:opacity-100 transition-opacity" title="Eliminar">✕</button>
        </form>
    </div>
</li>
