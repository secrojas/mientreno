@php
    $hasFilters = request()->filled('doctor') || request()->filled('specialty');
    $isCreateFormReopened = old('_form') === 'create-appointment';
@endphp

<x-app-layout title="Turnos Médicos">
    <div x-data class="max-w-7xl mx-auto">

        {{-- Header --}}
        <header class="mb-8">
            <h1 class="font-display text-responsive-2xl mb-2 bg-gradient-to-r from-text-main to-text-muted bg-clip-text text-transparent">
                Salud Médica
            </h1>
            <p class="text-responsive-base text-text-muted">
                Tus turnos médicos: qué te dijo cada médico y qué quedó pendiente después de cada consulta.
            </p>
        </header>

        @include('medical.partials.tabs', ['active' => 'appointments'])

        {{-- Flash messages --}}
        @php
            $flashMessages = [
                'appointment-created' => 'Turno agendado correctamente.',
                'appointment-updated' => 'Turno actualizado correctamente.',
                'appointment-deleted' => 'Turno eliminado.',
                'task-created' => 'Indicación agregada.',
                'task-deleted' => 'Indicación eliminada.',
                'order-uploaded' => 'Orden subida y vinculada al turno.',
            ];
            $status = session('status');
            $isError = in_array($status, ['appointment-deleted', 'task-deleted'], true);
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

        <div class="flex justify-end mb-6">
            <button type="button" @click="$dispatch('open-appointment-form', {})" class="btn-primary text-sm w-full sm:w-auto justify-center">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Nuevo Turno
            </button>
        </div>

        {{-- ¿Cómo te fue? --}}
        @if($awaitingFollowUp->isNotEmpty())
            <section class="mb-8">
                <h2 class="flex items-center gap-3 text-xs font-bold uppercase tracking-widest text-amber-400 mb-2">
                    ¿Cómo te fue?
                    <span class="text-text-muted font-normal normal-case tracking-normal">({{ $awaitingFollowUp->count() }})</span>
                    <span class="flex-1 h-px bg-gradient-to-r from-amber-400/30 to-transparent"></span>
                </h2>
                <p class="text-xs text-text-muted mb-4">Estos turnos ya pasaron. Registrá qué te dijeron, o marcalos como cancelados si no fuiste.</p>
                @foreach($awaitingFollowUp as $appointment)
                    @include('medical.partials.appointment-card', ['appointment' => $appointment])
                @endforeach
            </section>
        @endif

        {{-- Pendientes --}}
        <section class="mb-8">
            <h2 class="flex items-center gap-3 text-xs font-bold uppercase tracking-widest text-accent-secondary mb-2">
                Pendientes
                <span class="text-text-muted font-normal normal-case tracking-normal">({{ $openTasks->count() }})</span>
                <span class="flex-1 h-px bg-gradient-to-r from-accent-secondary/30 to-transparent"></span>
            </h2>
            @if($openTasks->isEmpty())
                <p class="text-sm text-text-muted">No tenés indicaciones pendientes. 🎉</p>
            @else
                <p class="text-xs text-text-muted mb-4">Indicaciones de todos tus turnos que todavía no marcaste como hechas.</p>
                <div class="card">
                    <ul class="space-y-3">
                        @foreach($openTasks as $task)
                            @include('medical.partials.appointment-task', ['task' => $task, 'showOrigin' => true])
                        @endforeach
                    </ul>
                </div>
            @endif
        </section>

        {{-- Próximos turnos --}}
        <section class="mb-8">
            <h2 class="flex items-center gap-3 text-xs font-bold uppercase tracking-widest text-accent-secondary mb-4">
                Próximos Turnos
                <span class="text-text-muted font-normal normal-case tracking-normal">({{ $upcomingAppointments->count() }})</span>
                <span class="flex-1 h-px bg-gradient-to-r from-accent-secondary/30 to-transparent"></span>
            </h2>
            @forelse($upcomingAppointments as $appointment)
                @include('medical.partials.appointment-card', ['appointment' => $appointment])
            @empty
                <div class="card border-dashed border-2 border-border-subtle text-center py-10">
                    <p class="text-text-main font-medium mb-1">Sin turnos próximos</p>
                    <p class="text-text-muted text-sm">Cuando saques un turno, cargalo acá y agregalo a tu calendario.</p>
                </div>
            @endforelse
        </section>

        {{-- Historial --}}
        <section class="mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 mb-4">
                <h2 class="flex items-center gap-3 text-xs font-bold uppercase tracking-widest text-accent-secondary">
                    Historial
                    <span class="text-text-muted font-normal normal-case tracking-normal">({{ $history->flatten()->count() }})</span>
                </h2>
                @if($doctors->isNotEmpty())
                    <form method="GET" action="{{ route('medical.appointments.index') }}" class="flex flex-col sm:flex-row gap-2">
                        <select name="doctor" onchange="this.form.submit()" class="form-select sm:w-56 text-sm">
                            <option value="">Todos los médicos</option>
                            @foreach($doctors as $doctor)
                                <option value="{{ $doctor->id }}" @selected((string) request('doctor') === (string) $doctor->id)>{{ $doctor->name }}</option>
                            @endforeach
                        </select>
                        @if($specialties->isNotEmpty())
                            <select name="specialty" onchange="this.form.submit()" class="form-select sm:w-60 text-sm">
                                <option value="">Todas las especialidades</option>
                                @foreach($specialties as $specialty)
                                    <option value="{{ $specialty }}" @selected(request('specialty') === $specialty)>{{ $specialty }}</option>
                                @endforeach
                            </select>
                        @endif
                        @if($hasFilters)
                            <a href="{{ route('medical.appointments.index') }}" class="btn-ghost text-sm justify-center">Limpiar</a>
                        @endif
                    </form>
                @endif
            </div>

            @forelse($history as $month => $appointments)
                <div class="mb-6">
                    <div class="text-xs font-medium text-text-muted mb-2 pl-1">{{ $month }}</div>
                    @foreach($appointments as $appointment)
                        @include('medical.partials.appointment-card', ['appointment' => $appointment])
                    @endforeach
                </div>
            @empty
                <div class="card border-dashed border-2 border-border-subtle text-center py-10">
                    <p class="text-text-main font-medium mb-1">{{ $hasFilters ? 'No hay turnos con esos filtros' : 'Todavía no hay turnos en el historial' }}</p>
                    <p class="text-text-muted text-sm">Los turnos realizados, cancelados o a los que no fuiste quedan acá.</p>
                </div>
            @endforelse
        </section>

        {{-- New appointment modal --}}
        <div x-data="appointmentForm({{ $isCreateFormReopened ? 'true' : 'false' }})"
             @open-appointment-form.window="open($event.detail)"
             x-show="isOpen"
             x-cloak
             class="fixed inset-0 bg-black/70 z-[9999] flex items-center justify-center p-4"
             @click.self="isOpen = false"
             @keydown.escape.window="isOpen = false"
             style="display: none;">
            <div class="bg-bg-card border border-accent-secondary/30 rounded-card w-full max-w-lg shadow-2xl max-h-[92vh] overflow-y-auto">
                <form action="{{ route('medical.appointments.store') }}" method="POST" class="p-6">
                    @csrf
                    <input type="hidden" name="_form" value="create-appointment">
                    <input type="hidden" name="referred_from_id" :value="referredFromId" value="{{ old('referred_from_id') }}">
                    <input type="hidden" name="referral_task_id" :value="taskId" value="{{ old('referral_task_id') }}">

                    <h3 class="font-display text-responsive-lg mb-1 text-accent-secondary">Nuevo Turno</h3>
                    <p x-show="referredFromLabel" class="text-xs text-text-muted mb-4">
                        ↪ Derivación desde <span class="text-text-main" x-text="referredFromLabel"></span>
                    </p>
                    <p x-show="!referredFromLabel" class="text-xs text-text-muted mb-4">Cargá el turno ahora; después del turno registrás cómo te fue.</p>

                    <div class="space-y-4">
                        <div>
                            <label class="form-label">Médico</label>
                            <select x-model="doctorChoice" class="form-select">
                                <option value="">Sin especificar</option>
                                @foreach($doctors as $doctor)
                                    <option value="{{ $doctor->id }}">{{ $doctor->name }}{{ $doctor->specialty ? ' — '.$doctor->specialty : '' }}</option>
                                @endforeach
                                <option value="new">+ Nuevo médico…</option>
                            </select>
                            <input type="hidden" name="doctor_id" :value="doctorChoice === 'new' ? '' : doctorChoice">
                        </div>

                        <div x-show="doctorChoice === 'new'" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="form-label">Nombre <span class="text-accent-primary">*</span></label>
                                <input type="text" name="new_doctor_name" value="{{ old('new_doctor_name') }}" :required="doctorChoice === 'new'" :disabled="doctorChoice !== 'new'" maxlength="255" placeholder="ej: Dra. Pérez" class="form-input">
                            </div>
                            <div>
                                <label class="form-label">Especialidad</label>
                                <input type="text" name="new_doctor_specialty" value="{{ old('new_doctor_specialty') }}" :disabled="doctorChoice !== 'new'" maxlength="255" placeholder="ej: Cirugía general" class="form-input">
                            </div>
                        </div>

                        <div>
                            <label class="form-label">Fecha y hora <span class="text-accent-primary">*</span></label>
                            <input type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at') }}" required class="form-input">
                        </div>

                        <div>
                            <label class="form-label">Lugar <span class="text-xs text-text-muted">(opcional)</span></label>
                            <input type="text" name="location" value="{{ old('location') }}" maxlength="255" placeholder="Si lo dejás vacío se usa el consultorio del médico" class="form-input">
                        </div>

                        <div>
                            <label class="form-label">Motivo <span class="text-xs text-text-muted">(opcional)</span></label>
                            <input type="text" name="reason" x-model="reason" maxlength="255" placeholder="ej: Mostrar batería de estudios" class="form-input">
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" @click="isOpen = false" class="btn-ghost text-sm">Cancelar</button>
                        <button type="submit" class="btn-primary text-sm">Agendar Turno</button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <script>
        function appointmentForm(startOpen) {
            return {
                isOpen: startOpen,
                doctorChoice: {{ \Illuminate\Support\Js::from((string) (old('doctor_id') ?: (filled(old('new_doctor_name')) ? 'new' : ''))) }},
                reason: {{ \Illuminate\Support\Js::from(old('reason', '')) }},
                referredFromId: {{ \Illuminate\Support\Js::from(old('referred_from_id', '')) }},
                referredFromLabel: '',
                taskId: {{ \Illuminate\Support\Js::from(old('referral_task_id', '')) }},
                open(detail) {
                    this.referredFromId = detail.referredFromId ?? '';
                    this.referredFromLabel = detail.referredFromLabel ?? '';
                    this.taskId = detail.taskId ?? '';
                    this.reason = detail.reason ?? '';
                    this.isOpen = true;
                }
            };
        }
    </script>
</x-app-layout>
