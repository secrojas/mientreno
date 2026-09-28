@php
    $isAwaitingFollowUp = $appointment->isAwaitingFollowUp();
    $isUpcoming = $appointment->status === \App\Enums\AppointmentStatus::Scheduled && ! $isAwaitingFollowUp;
    $statusLabel = $isAwaitingFollowUp ? 'Pendiente de completar' : $appointment->status->label();
    $statusClass = $isAwaitingFollowUp ? 'text-amber-400 bg-amber-400/10 border-amber-400/30' : $appointment->status->badgeClass();
    $linkedDocumentIds = $appointment->documents->pluck('id')->map(fn ($id) => (string) $id)->all();
    $linkedOrderIds = $appointment->orders->pluck('id')->map(fn ($id) => (string) $id)->all();
    $isCompleted = $appointment->status === \App\Enums\AppointmentStatus::Completed;
    $hasDetails = $isCompleted
        || $appointment->observations
        || $appointment->tasks->isNotEmpty()
        || $appointment->documents->isNotEmpty()
        || $appointment->orders->isNotEmpty()
        || $appointment->referredFrom
        || $appointment->referrals->isNotEmpty();
@endphp

<div class="card mb-3 {{ $isAwaitingFollowUp ? 'border-amber-400/30' : '' }}"
     x-data="{
        editing: false,
        expanded: false,
        confirmDelete: false,
        addingTask: false,
        uploadingOrder: false,
        status: '{{ $appointment->status->value }}',
        newTasks: [],
        completeVisit() {
            this.status = 'completed';
            this.editing = true;
            if (this.newTasks.length === 0) { this.addTaskRow(); }
        },
        addTaskRow() {
            this.newTasks.push({ type: 'medication', description: '', due_date: '' });
        }
     }">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row items-start gap-4">
        <div class="shrink-0 w-14 text-center rounded-btn border border-border-subtle bg-bg-sidebar py-1.5">
            <div class="text-[10px] uppercase tracking-wider text-text-muted">{{ $appointment->scheduled_at->locale('es')->isoFormat('MMM') }}</div>
            <div class="font-display text-xl leading-none">{{ $appointment->scheduled_at->format('d') }}</div>
            <div class="text-[10px] text-text-muted mt-0.5">{{ $appointment->scheduled_at->format('H:i') }}</div>
        </div>

        <div class="flex-1 min-w-0">
            <div class="flex flex-wrap items-center gap-2 mb-1">
                <span class="font-medium text-sm">{{ $appointment->doctor?->name ?? 'Sin médico asignado' }}</span>
                @if($appointment->doctor?->specialty)
                    <span class="text-xs text-text-muted">{{ $appointment->doctor->specialty }}</span>
                @endif
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs border {{ $statusClass }}">{{ $statusLabel }}</span>
            </div>
            <div class="flex flex-wrap gap-x-4 gap-y-1 text-xs text-text-muted">
                <span>{{ ucfirst($appointment->scheduled_at->locale('es')->isoFormat('dddd D [de] MMMM, HH:mm')) }}</span>
                @if($isUpcoming)
                    <span class="text-accent-secondary">{{ $appointment->scheduled_at->locale('es')->diffForHumans() }}</span>
                @endif
                @if($appointment->location)
                    <span>📍 {{ $appointment->location }}</span>
                @endif
            </div>
            @if($appointment->reason)
                <p class="text-sm text-text-main/90 mt-1.5">{{ $appointment->reason }}</p>
            @endif
            @if($appointment->observations)
                <p x-show="!expanded" class="text-xs text-text-muted mt-1.5 line-clamp-2">{{ $appointment->observations }}</p>
            @endif
            @php
                $openTaskCount = $appointment->tasks->whereNull('completed_at')->count();
            @endphp
            @if($openTaskCount > 0)
                <p x-show="!expanded" class="text-xs text-amber-400 mt-1">{{ $openTaskCount }} {{ $openTaskCount === 1 ? 'indicación pendiente' : 'indicaciones pendientes' }}</p>
            @endif
        </div>

        <div class="flex flex-wrap items-center gap-2 shrink-0">
            @if($isAwaitingFollowUp)
                <button @click="completeVisit()" class="btn-primary text-sm px-3 py-2">¿Cómo te fue?</button>
            @endif

            @if($isUpcoming)
                <a href="{{ route('medical.appointments.calendar', $appointment) }}" class="btn-ghost text-sm px-3 py-2" title="Agregar al calendario">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                        <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                        <line x1="12" y1="14" x2="12" y2="18"/><line x1="10" y1="16" x2="14" y2="16"/>
                    </svg>
                    <span class="hidden sm:inline">Calendario</span>
                </a>
            @endif

            @if($hasDetails)
                <button @click="expanded = !expanded" class="btn-ghost text-sm px-3 py-2" x-text="expanded ? 'Ocultar' : 'Ver detalle'">Ver detalle</button>
            @endif

            <button @click="editing = !editing" class="btn-ghost text-sm px-3 py-2">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                </svg>
                <span class="hidden sm:inline">Editar</span>
            </button>

            <div x-show="!confirmDelete">
                <button @click="confirmDelete = true" class="btn-ghost text-sm px-3 py-2 text-red-400 hover:text-red-300 hover:bg-red-400/10">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/>
                    </svg>
                </button>
            </div>
            <div x-show="confirmDelete" class="flex items-center gap-2" style="display: none;">
                <span class="text-xs text-red-400">¿Eliminar?</span>
                <form action="{{ route('medical.appointments.destroy', $appointment) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-xs px-2 py-1 rounded bg-red-500/20 text-red-400 border border-red-500/30 hover:bg-red-500/30 transition-colors">Sí</button>
                </form>
                <button @click="confirmDelete = false" class="text-xs px-2 py-1 rounded bg-border-subtle text-text-muted hover:text-text-main transition-colors">No</button>
            </div>
        </div>
    </div>

    {{-- Details --}}
    @if($hasDetails)
        <div x-show="expanded && !editing" x-cloak class="mt-4 pt-4 border-t border-white/5 space-y-4">
            @if($appointment->referredFrom)
                <p class="text-xs text-text-muted">
                    ↪ Derivado desde:
                    <span class="text-text-main">{{ $appointment->referredFrom->doctor?->name ?? 'turno' }}</span>
                    ({{ $appointment->referredFrom->scheduled_at->format('d/m/Y') }})
                </p>
            @endif

            @if($appointment->observations)
                <div>
                    <div class="text-xs font-bold uppercase tracking-widest text-text-muted mb-1.5">Observaciones</div>
                    <p class="text-sm leading-relaxed whitespace-pre-line">{{ $appointment->observations }}</p>
                </div>
            @endif

            @if($appointment->tasks->isNotEmpty())
                <div>
                    <div class="text-xs font-bold uppercase tracking-widest text-text-muted mb-1.5">Indicaciones</div>
                    <ul class="space-y-1.5">
                        @foreach($appointment->tasks as $task)
                            @include('medical.partials.appointment-task', ['task' => $task, 'showOrigin' => false])
                        @endforeach
                    </ul>
                </div>
            @endif

            @if($appointment->documents->isNotEmpty() || $appointment->orders->isNotEmpty())
                <div class="flex flex-col sm:flex-row gap-4">
                    @if($appointment->documents->isNotEmpty())
                        <div class="flex-1">
                            <div class="text-xs font-bold uppercase tracking-widest text-text-muted mb-1.5">Estudios llevados</div>
                            <div class="flex flex-wrap gap-2">
                                @foreach($appointment->documents as $document)
                                    <a href="{{ route('medical.documents.preview', $document) }}" target="_blank"
                                       class="inline-flex items-center gap-1 px-2 py-1 rounded-btn border text-xs {{ $document->type->badgeClass() }} hover:opacity-80">
                                        {{ $document->title }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    @if($appointment->orders->isNotEmpty())
                        <div class="flex-1">
                            <div class="text-xs font-bold uppercase tracking-widest text-text-muted mb-1.5">Órdenes recibidas</div>
                            <div class="flex flex-wrap gap-2">
                                @foreach($appointment->orders as $order)
                                    <a href="{{ route('medical.orders.preview', $order) }}" target="_blank"
                                       class="inline-flex items-center gap-1 px-2 py-1 rounded-btn border text-xs text-amber-400 bg-amber-400/10 border-amber-400/30 hover:opacity-80">
                                        {{ $order->title }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            @if($appointment->referrals->isNotEmpty())
                <p class="text-xs text-text-muted">
                    Derivaciones agendadas:
                    @foreach($appointment->referrals as $referral)
                        <span class="text-text-main">{{ $referral->doctor?->name ?? 'turno' }} ({{ $referral->scheduled_at->format('d/m/Y') }})</span>@if(! $loop->last), @endif
                    @endforeach
                </p>
            @endif
        </div>
    @endif

    {{-- Quick actions --}}
    @if($isCompleted)
        <div x-show="expanded && !editing" x-cloak class="mt-3 flex flex-wrap gap-3 text-xs">
            <button type="button" @click="addingTask = !addingTask; uploadingOrder = false" class="text-accent-secondary hover:underline">+ Agregar indicación</button>
            <button type="button" @click="uploadingOrder = !uploadingOrder; addingTask = false" class="text-accent-secondary hover:underline">+ Subir orden de este turno</button>
        </div>

        <div x-show="addingTask && !editing" x-cloak class="mt-3">
            <form action="{{ route('medical.appointments.tasks.store', $appointment) }}" method="POST" class="flex flex-col sm:flex-row gap-2">
                @csrf
                <select name="type" class="form-select sm:w-44">
                    @foreach(\App\Enums\AppointmentTaskType::cases() as $taskType)
                        <option value="{{ $taskType->value }}">{{ $taskType->icon() }} {{ $taskType->label() }}</option>
                    @endforeach
                </select>
                <input type="text" name="description" required maxlength="255" placeholder="Qué hay que hacer" class="form-input flex-1">
                <input type="date" name="due_date" class="form-input sm:w-40" title="Fecha límite (opcional)">
                <button type="submit" class="btn-primary text-sm justify-center">Agregar</button>
            </form>
        </div>

        <div x-show="uploadingOrder && !editing" x-cloak class="mt-3">
            <form action="{{ route('medical.orders.store') }}" method="POST" enctype="multipart/form-data" class="flex flex-col sm:flex-row gap-2">
                @csrf
                <input type="hidden" name="medical_appointment_id" value="{{ $appointment->id }}">
                <input type="hidden" name="doctor_id" value="{{ $appointment->doctor_id }}">
                <input type="hidden" name="issued_at" value="{{ $appointment->scheduled_at->format('Y-m-d') }}">
                <input type="text" name="title" required maxlength="255" placeholder="ej: Receta atorvastatina" class="form-input flex-1">
                <input type="file" name="file" required accept=".pdf,.jpg,.jpeg,.png" class="form-input sm:w-64">
                <button type="submit" class="btn-primary text-sm justify-center">Subir</button>
            </form>
        </div>
    @endif

    {{-- Edit / complete form --}}
    <div x-show="editing" x-cloak class="mt-4 pt-4 border-t border-white/5">
        <form action="{{ route('medical.appointments.update', $appointment) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Estado</label>
                    <select name="status" x-model="status" class="form-select">
                        @foreach(\App\Enums\AppointmentStatus::cases() as $appointmentStatus)
                            <option value="{{ $appointmentStatus->value }}">{{ $appointmentStatus->label() }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="form-label">Fecha y hora</label>
                    <input type="datetime-local" name="scheduled_at" value="{{ $appointment->scheduled_at->format('Y-m-d\TH:i') }}" required class="form-input">
                </div>

                <div>
                    <label class="form-label">Médico</label>
                    <select name="doctor_id" class="form-select">
                        <option value="">Sin especificar</option>
                        @foreach($doctors as $doctor)
                            <option value="{{ $doctor->id }}" {{ $appointment->doctor_id === $doctor->id ? 'selected' : '' }}>
                                {{ $doctor->name }}{{ $doctor->specialty ? ' — '.$doctor->specialty : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="form-label">Lugar</label>
                    <input type="text" name="location" value="{{ $appointment->location }}" maxlength="255" class="form-input">
                </div>

                <div class="sm:col-span-2">
                    <label class="form-label">Motivo</label>
                    <input type="text" name="reason" value="{{ $appointment->reason }}" maxlength="255" class="form-input">
                </div>

                <div class="sm:col-span-2" x-show="status === 'completed'">
                    <label class="form-label">Observaciones <span class="text-xs text-text-muted">(qué te dijo, diagnóstico, hallazgos)</span></label>
                    <textarea name="observations" rows="6" maxlength="10000" class="form-input"
                              placeholder="ej: Colesterol LDL alto. Pólipo vesicular en la ecografía, recomienda consulta con cirujano. Resto de los estudios normales.">{{ $appointment->observations }}</textarea>
                </div>

                <div class="sm:col-span-2" x-show="status === 'completed'">
                    <div class="flex items-center justify-between mb-2">
                        <label class="form-label mb-0">Nuevas indicaciones y pendientes</label>
                        <button type="button" @click="addTaskRow()" class="text-xs text-accent-secondary hover:underline">+ Agregar</button>
                    </div>
                    <template x-for="(task, index) in newTasks" :key="index">
                        <div class="flex flex-col sm:flex-row gap-2 mb-2">
                            <select :name="`new_tasks[${index}][type]`" x-model="task.type" class="form-select sm:w-44">
                                @foreach(\App\Enums\AppointmentTaskType::cases() as $taskType)
                                    <option value="{{ $taskType->value }}">{{ $taskType->icon() }} {{ $taskType->label() }}</option>
                                @endforeach
                            </select>
                            <input type="text" :name="`new_tasks[${index}][description]`" x-model="task.description" maxlength="255"
                                   :placeholder="{{ \Illuminate\Support\Js::from(collect(\App\Enums\AppointmentTaskType::cases())->mapWithKeys(fn ($taskType) => [$taskType->value => $taskType->placeholder()])) }}[task.type]"
                                   class="form-input flex-1">
                            <input type="date" :name="`new_tasks[${index}][due_date]`" x-model="task.due_date" class="form-input sm:w-40" title="Fecha límite (opcional)">
                            <button type="button" @click="newTasks.splice(index, 1)" class="btn-ghost text-sm px-3 text-red-400">✕</button>
                        </div>
                    </template>
                    <p x-show="newTasks.length === 0" class="text-xs text-text-muted">Medicación, derivaciones, estudios a realizar, controles… Cada una queda como pendiente para tildar.</p>
                </div>

                @if($documents->isNotEmpty())
                    <div x-show="status === 'completed'">
                        <label class="form-label">Estudios que llevaste</label>
                        <div class="max-h-40 overflow-y-auto space-y-1.5 rounded-btn border border-border-subtle p-3">
                            @foreach($documents as $document)
                                <label class="flex items-center gap-2 text-sm cursor-pointer">
                                    <input type="checkbox" name="document_ids[]" value="{{ $document->id }}" @checked(in_array((string) $document->id, $linkedDocumentIds, true))
                                           class="w-4 h-4 rounded border-border-subtle text-accent-secondary focus:ring-accent-secondary/50">
                                    <span class="truncate">{{ $document->title }}</span>
                                    @if($document->issued_at)
                                        <span class="text-xs text-text-muted shrink-0">{{ $document->issued_at->format('d/m/Y') }}</span>
                                    @endif
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($orders->isNotEmpty())
                    <div x-show="status === 'completed'">
                        <label class="form-label">Órdenes recibidas</label>
                        <div class="max-h-40 overflow-y-auto space-y-1.5 rounded-btn border border-border-subtle p-3">
                            @foreach($orders as $order)
                                <label class="flex items-center gap-2 text-sm cursor-pointer">
                                    <input type="checkbox" name="order_ids[]" value="{{ $order->id }}" @checked(in_array((string) $order->id, $linkedOrderIds, true))
                                           class="w-4 h-4 rounded border-border-subtle text-accent-secondary focus:ring-accent-secondary/50">
                                    <span class="truncate">{{ $order->title }}</span>
                                    @if($order->issued_at)
                                        <span class="text-xs text-text-muted shrink-0">{{ $order->issued_at->format('d/m/Y') }}</span>
                                    @endif
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <div class="flex justify-end gap-3 mt-5">
                <button type="button" @click="editing = false" class="btn-ghost text-sm">Cancelar</button>
                <button type="submit" class="btn-primary text-sm">Guardar</button>
            </div>
        </form>
    </div>
</div>
