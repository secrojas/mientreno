@php
    $user = auth()->user();
    $documentTypes = \App\Enums\MedicalDocumentType::cases();
@endphp

<x-app-layout title="Salud Médica">
    <div x-data="medicalHub({{ \Illuminate\Support\Js::from($documents->pluck('title', 'id')) }})" class="max-w-7xl mx-auto">

        {{-- Header --}}
        <header class="mb-8">
            <h1 class="font-display text-responsive-2xl mb-2 bg-gradient-to-r from-text-main to-text-muted bg-clip-text text-transparent">
                Salud Médica
            </h1>
            <p class="text-responsive-base text-text-muted">
                Tus estudios y certificados médicos, más un resumen de entrenamiento para tu cardiólogo.
            </p>
        </header>

        {{-- Submenú --}}
        <div class="flex gap-1 mb-8 border-b border-white/5">
            <span class="px-4 py-2.5 text-sm font-medium text-accent-secondary border-b-2 border-accent-secondary">
                Documentos y Estudios
            </span>
            <a href="{{ route('medical.orders.index') }}" class="px-4 py-2.5 text-sm font-medium text-text-muted hover:text-text-main transition-colors">
                Órdenes Médicas
            </a>
        </div>

        {{-- Flash messages --}}
        @php
            $flashMessages = [
                'document-uploaded' => 'Documento subido correctamente.',
                'document-updated' => 'Documento actualizado correctamente.',
                'document-deleted' => 'Documento eliminado.',
                'doctor-created' => 'Médico agregado correctamente.',
                'doctor-updated' => 'Médico actualizado correctamente.',
                'doctor-deleted' => 'Médico eliminado.',
                'group-created' => 'Reporte de estudios creado correctamente.',
                'group-deleted' => 'Reporte de estudios eliminado.',
            ];
            $status = session('status');
            $isError = in_array($status, ['document-deleted', 'doctor-deleted', 'group-deleted'], true);
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

        {{-- Apto Médico --}}
        <section class="mb-8">
            <h2 class="flex items-center gap-3 text-xs font-bold uppercase tracking-widest text-accent-secondary mb-4">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                </svg>
                Apto Médico
                <span class="flex-1 h-px bg-gradient-to-r from-accent-secondary/30 to-transparent"></span>
            </h2>

            @if($fitnessCertificate)
                @php
                    $isExpired = $fitnessCertificate->isExpired();
                    $isExpiringSoon = $fitnessCertificate->isExpiringSoon();
                    $daysUntil = $fitnessCertificate->days_until_expiry;

                    if ($isExpired) {
                        $cardGradient = 'from-red-500/10 to-red-900/5 border-red-500/30';
                        $iconColor = 'text-red-400';
                        $badgeClass = 'text-red-400 bg-red-400/10 border border-red-400/30';
                        $badgeLabel = 'VENCIDO';
                        $expiryColor = 'text-red-400';
                    } elseif ($isExpiringSoon) {
                        $cardGradient = 'from-amber-500/10 to-amber-900/5 border-amber-500/30';
                        $iconColor = 'text-amber-400';
                        $badgeClass = 'text-amber-400 bg-amber-400/10 border border-amber-400/30';
                        $badgeLabel = 'POR VENCER';
                        $expiryColor = 'text-amber-400';
                    } else {
                        $cardGradient = 'from-emerald-500/10 to-emerald-900/5 border-emerald-500/30';
                        $iconColor = 'text-emerald-400';
                        $badgeClass = 'text-emerald-400 bg-emerald-400/10 border border-emerald-400/30';
                        $badgeLabel = 'VIGENTE';
                        $expiryColor = 'text-text-muted';
                    }
                @endphp

                <div class="card bg-gradient-to-r {{ $cardGradient }} flex flex-col sm:flex-row items-start sm:items-center gap-5">
                    <div class="{{ $iconColor }} shrink-0">
                        @if($isExpired)
                            <svg class="w-14 h-14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                <path d="M9 9l6 6M15 9l-6 6"/>
                            </svg>
                        @else
                            <svg class="w-14 h-14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                <path d="M9 12l2 2 4-4"/>
                            </svg>
                        @endif
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-3 mb-1.5">
                            <span class="text-lg font-semibold">{{ $fitnessCertificate->title }}</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider {{ $badgeClass }}">
                                {{ $badgeLabel }}
                            </span>
                        </div>

                        <div class="flex flex-wrap gap-x-6 gap-y-1 text-sm">
                            @if($fitnessCertificate->issued_at)
                                <span class="text-text-muted">
                                    Emitido: <span class="text-text-main">{{ $fitnessCertificate->issued_at->format('d/m/Y') }}</span>
                                </span>
                            @endif
                            @if($fitnessCertificate->expires_at)
                                <span class="{{ $expiryColor }}">
                                    Vence: {{ $fitnessCertificate->expires_at->format('d/m/Y') }}
                                    @if($isExpired && $daysUntil !== null)
                                        — hace {{ abs($daysUntil) }} días
                                    @elseif(!$isExpired && $daysUntil !== null)
                                        — en {{ $daysUntil }} días
                                    @endif
                                </span>
                            @endif
                        </div>

                        @if($fitnessCertificate->notes)
                            <p class="text-text-muted text-sm mt-2">{{ $fitnessCertificate->notes }}</p>
                        @endif
                    </div>

                    <div class="shrink-0 flex gap-2">
                        <button @click="openPreview('{{ route('medical.documents.preview', $fitnessCertificate) }}', '{{ addslashes($fitnessCertificate->title) }}')"
                                class="btn-ghost text-sm">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                            Ver
                        </button>
                        <a href="{{ route('medical.documents.download', $fitnessCertificate) }}"
                           class="btn-secondary text-sm">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                <polyline points="7 10 12 15 17 10"/>
                                <line x1="12" y1="15" x2="12" y2="3"/>
                            </svg>
                            Descargar
                        </a>
                    </div>
                </div>
            @else
                <div class="card border-2 border-dashed border-border-subtle text-center py-10">
                    <svg class="w-12 h-12 text-text-muted mx-auto mb-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                        <path d="M12 8v4M12 16h.01"/>
                    </svg>
                    <p class="text-text-main font-medium mb-1">Sin apto médico cargado</p>
                    <p class="text-text-muted text-sm mb-4">Subí tu certificado para tenerlo siempre disponible.</p>
                    <button @click="showUploadForm = true" class="btn-primary text-sm mx-auto">
                        Subir Apto Médico
                    </button>
                </div>
            @endif
        </section>

        {{-- Resumen para el Cardiólogo --}}
        <section class="mb-8">
            <div class="flex items-center gap-3 mb-4">
                <h2 class="flex items-center gap-3 text-xs font-bold uppercase tracking-widest text-accent-secondary">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
                    </svg>
                    Resumen de Entrenamiento
                </h2>
                <span class="flex-1 h-px bg-gradient-to-r from-accent-secondary/30 to-transparent"></span>
                <a href="{{ route('medical.report') }}" class="btn-primary text-xs shrink-0">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <line x1="12" y1="18" x2="12" y2="12"/>
                        <line x1="9" y1="15" x2="15" y2="15"/>
                    </svg>
                    Ver Reporte de Entrenamiento
                </a>
            </div>
            <p class="text-xs text-text-muted mb-4">Datos de todos tus entrenamientos completados · Para compartir con tu cardiólogo.</p>

            @php
                $totalHours = floor($totalMetrics['total_duration'] / 3600);
                $totalMinutes = floor(($totalMetrics['total_duration'] % 3600) / 60);
                $yearHours = floor($yearlyMetrics['total_duration'] / 3600);
                $yearMinutes = floor(($yearlyMetrics['total_duration'] % 3600) / 60);
            @endphp

            <div class="grid-responsive-4 gap-4">
                <x-metric-card
                    label="Kilómetros totales"
                    :value="number_format($totalMetrics['total_distance'], 1) . ' km'"
                    :subtitle="'Este año: ' . number_format($yearlyMetrics['total_distance'], 1) . ' km'"
                    accent="primary"
                />

                <x-metric-card
                    label="Tiempo total"
                    :value="($totalHours > 0 ? $totalHours . 'h ' : '') . $totalMinutes . 'm'"
                    :subtitle="'Este año: ' . ($yearHours > 0 ? $yearHours . 'h ' : '') . $yearMinutes . 'm'"
                />

                <x-metric-card
                    label="Entrenamientos"
                    :value="$totalMetrics['total_workouts']"
                    :subtitle="'Este año: ' . $yearlyMetrics['total_workouts'] . ' sesiones'"
                />

                <x-metric-card
                    label="Entrenando desde"
                    :value="$firstWorkout ? $firstWorkout->date->format('M Y') : '—'"
                    :subtitle="$firstWorkout ? $firstWorkout->date->diffForHumans(now(), true) : 'Sin entrenamientos'"
                />
            </div>
        </section>

        {{-- Documentos Médicos --}}
        <section class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                <h2 class="flex items-center gap-3 text-xs font-bold uppercase tracking-widest text-accent-secondary">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                    </svg>
                    Documentos Médicos
                    <span class="text-text-muted font-normal normal-case tracking-normal">({{ $documents->count() }})</span>
                </h2>
                <div class="flex gap-2 w-full sm:w-auto">
                    <button x-show="selectedDocs.length > 0"
                            x-cloak
                            @click="showGroupForm = true"
                            class="btn-secondary text-sm flex-1 sm:flex-initial justify-center">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/>
                            <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/>
                            <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/>
                        </svg>
                        <span>Reporte de Estudios (<span x-text="selectedDocs.length"></span>)</span>
                    </button>
                    <button @click="showUploadForm = !showUploadForm"
                            class="btn-primary text-sm flex-1 sm:flex-initial justify-center">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                            <polyline points="17 8 12 3 7 8"/>
                            <line x1="12" y1="3" x2="12" y2="15"/>
                        </svg>
                        <span x-text="showUploadForm ? 'Cancelar' : 'Subir Documento'">Subir Documento</span>
                    </button>
                </div>
            </div>
            <p class="text-xs text-text-muted mb-4">Tildá los estudios que quieras agrupar y compartir con un médico en un solo link.</p>

            {{-- Upload Form --}}
            <div x-show="showUploadForm"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-2"
                 class="mb-6"
                 style="display: none;">

                <x-card class="border-accent-secondary/20">
                    <div class="card-header mb-4">
                        <div>
                            <div class="card-title">Nuevo Documento</div>
                            <div class="card-subtitle">Solo se aceptan archivos PDF · Máximo 10MB</div>
                        </div>
                    </div>

                    <form action="{{ route('medical.documents.store') }}"
                          method="POST"
                          enctype="multipart/form-data"
                          x-data="{ selectedType: '' }">
                        @csrf

                        @if($errors->any())
                            <div class="mb-4 px-4 py-3 bg-red-500/10 border border-red-500/30 rounded-btn text-red-400 text-sm">
                                <ul class="list-disc list-inside space-y-1">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label class="form-label">Tipo de documento <span class="text-accent-primary">*</span></label>
                                <select name="type" x-model="selectedType" required class="form-select">
                                    <option value="">Seleccioná el tipo...</option>
                                    @foreach($documentTypes as $type)
                                        <option value="{{ $type->value }}" {{ old('type') === $type->value ? 'selected' : '' }}>
                                            {{ $type->label() }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="form-label">Título <span class="text-accent-primary">*</span></label>
                                <input type="text"
                                       name="title"
                                       value="{{ old('title') }}"
                                       placeholder="ej: Análisis de sangre junio 2025"
                                       required
                                       class="form-input">
                            </div>

                            <div>
                                <label class="form-label">Fecha del estudio</label>
                                <input type="date"
                                       name="issued_at"
                                       value="{{ old('issued_at') }}"
                                       class="form-input">
                            </div>

                            <div>
                                <label class="form-label">
                                    Fecha de vencimiento
                                    <span class="text-xs text-text-muted ml-1">(apto médico, etc.)</span>
                                </label>
                                <input type="date"
                                       name="expires_at"
                                       value="{{ old('expires_at') }}"
                                       class="form-input">
                            </div>

                            <div class="sm:col-span-2">
                                <label class="form-label">
                                    Médico solicitante <span class="text-xs text-text-muted">(opcional)</span>
                                </label>
                                <select name="doctor_id" class="form-select">
                                    <option value="">Sin especificar</option>
                                    @foreach($doctors as $doctor)
                                        <option value="{{ $doctor->id }}" {{ (string) old('doctor_id') === (string) $doctor->id ? 'selected' : '' }}>
                                            {{ $doctor->name }}{{ $doctor->specialty ? ' — '.$doctor->specialty : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="sm:col-span-2">
                                <label class="form-label">Notas <span class="text-xs text-text-muted">(opcional)</span></label>
                                <textarea name="notes"
                                          maxlength="1000"
                                          rows="2"
                                          placeholder="Observaciones, resultados clave, etc."
                                          class="form-input resize-none">{{ old('notes') }}</textarea>
                            </div>

                            <div class="sm:col-span-2">
                                <label class="form-label">Archivo PDF <span class="text-accent-primary">*</span></label>
                                <label class="flex items-center gap-3 px-4 py-3 rounded-btn border border-dashed border-border-subtle
                                              bg-bg-sidebar cursor-pointer hover:border-accent-secondary/50 transition-colors">
                                    <svg class="w-5 h-5 text-text-muted shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                        <polyline points="14 2 14 8 20 8"/>
                                        <path d="M12 18v-6M9 15l3-3 3 3"/>
                                    </svg>
                                    <input type="file" name="document" accept=".pdf" required
                                           x-ref="fileInput"
                                           @change="$el.parentElement.querySelector('span').textContent = $event.target.files[0]?.name || 'Seleccioná un archivo PDF'"
                                           class="sr-only">
                                    <span class="text-sm text-text-muted">Seleccioná un archivo PDF</span>
                                </label>
                            </div>

                            <div class="sm:col-span-2">
                                <label class="form-label">
                                    Link de imágenes del estudio
                                    <span class="text-xs text-text-muted">(opcional — ej: ecografías, radiografías)</span>
                                </label>
                                <input type="url"
                                       name="images_url"
                                       value="{{ old('images_url') }}"
                                       placeholder="https://imagenes.iradiologico.com.ar/..."
                                       maxlength="2048"
                                       class="form-input">
                            </div>
                        </div>

                        <div class="flex justify-end gap-3 mt-6 pt-5 border-t border-white/5">
                            <button type="button" @click="showUploadForm = false" class="btn-ghost text-sm">
                                Cancelar
                            </button>
                            <button type="submit" class="btn-primary text-sm">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                    <polyline points="17 8 12 3 7 8"/>
                                    <line x1="12" y1="3" x2="12" y2="15"/>
                                </svg>
                                Subir Documento
                            </button>
                        </div>
                    </form>
                </x-card>
            </div>

            {{-- Document List --}}
            @forelse($documents as $document)
                <div class="card mb-3" x-data="{ confirmDelete: false, editing: false }">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">

                        {{-- Select checkbox --}}
                        <div class="shrink-0 pt-1 sm:pt-0">
                            <input type="checkbox" x-model="selectedDocs" value="{{ $document->id }}"
                                   class="w-4 h-4 rounded border-border-subtle text-accent-secondary focus:ring-accent-secondary/50">
                        </div>

                        {{-- Type Icon --}}
                        <div class="shrink-0">
                            @switch($document->type)
                                @case(\App\Enums\MedicalDocumentType::BloodTest)
                                    <div class="w-10 h-10 rounded-btn flex items-center justify-center bg-red-400/10">
                                        <svg class="w-5 h-5 text-red-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M12 22a7 7 0 0 0 7-7c0-2-1-3.9-3-5.5s-3.5-4-4-6.5c-.5 2.5-2 4.9-4 6.5C6 11.1 5 13 5 15a7 7 0 0 0 7 7z"/>
                                        </svg>
                                    </div>
                                    @break
                                @case(\App\Enums\MedicalDocumentType::Ergometry)
                                    <div class="w-10 h-10 rounded-btn flex items-center justify-center bg-orange-400/10">
                                        <svg class="w-5 h-5 text-orange-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
                                        </svg>
                                    </div>
                                    @break
                                @case(\App\Enums\MedicalDocumentType::Ecg)
                                    <div class="w-10 h-10 rounded-btn flex items-center justify-center bg-blue-400/10">
                                        <svg class="w-5 h-5 text-blue-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                                        </svg>
                                    </div>
                                    @break
                                @case(\App\Enums\MedicalDocumentType::Echocardiogram)
                                    <div class="w-10 h-10 rounded-btn flex items-center justify-center bg-purple-400/10">
                                        <svg class="w-5 h-5 text-purple-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                                        </svg>
                                    </div>
                                    @break
                                @case(\App\Enums\MedicalDocumentType::ChestXray)
                                    <div class="w-10 h-10 rounded-btn flex items-center justify-center bg-cyan-400/10">
                                        <svg class="w-5 h-5 text-cyan-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M6 3v10a6 6 0 0 0 12 0V3"/>
                                            <path d="M6 3h12"/>
                                        </svg>
                                    </div>
                                    @break
                                @case(\App\Enums\MedicalDocumentType::AbdominalUltrasound)
                                    <div class="w-10 h-10 rounded-btn flex items-center justify-center bg-indigo-400/10">
                                        <svg class="w-5 h-5 text-indigo-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7-10-7-10-7z"/>
                                            <circle cx="12" cy="12" r="3"/>
                                        </svg>
                                    </div>
                                    @break
                                @case(\App\Enums\MedicalDocumentType::CtScan)
                                    <div class="w-10 h-10 rounded-btn flex items-center justify-center bg-fuchsia-400/10">
                                        <svg class="w-5 h-5 text-fuchsia-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <circle cx="12" cy="12" r="9"/>
                                            <circle cx="12" cy="12" r="3"/>
                                        </svg>
                                    </div>
                                    @break
                                @case(\App\Enums\MedicalDocumentType::Mri)
                                    <div class="w-10 h-10 rounded-btn flex items-center justify-center bg-pink-400/10">
                                        <svg class="w-5 h-5 text-pink-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <rect x="3" y="3" width="18" height="18" rx="9"/>
                                            <path d="M3 12h18"/>
                                        </svg>
                                    </div>
                                    @break
                                @case(\App\Enums\MedicalDocumentType::FitnessCertificate)
                                    <div class="w-10 h-10 rounded-btn flex items-center justify-center bg-emerald-400/10">
                                        <svg class="w-5 h-5 text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                            <path d="M9 12l2 2 4-4"/>
                                        </svg>
                                    </div>
                                    @break
                                @default
                                    <div class="w-10 h-10 rounded-btn flex items-center justify-center bg-gray-400/10">
                                        <svg class="w-5 h-5 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                            <polyline points="14 2 14 8 20 8"/>
                                        </svg>
                                    </div>
                            @endswitch
                        </div>

                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-wrap items-center gap-2 mb-1">
                                <span class="font-medium text-sm truncate">{{ $document->title }}</span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs border {{ $document->type->badgeClass() }}">
                                    {{ $document->type->label() }}
                                </span>
                                @if($document->type === \App\Enums\MedicalDocumentType::FitnessCertificate && $document->expires_at)
                                    @if($document->isExpired())
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs border text-red-400 bg-red-400/10 border-red-400/30">VENCIDO</span>
                                    @elseif($document->isExpiringSoon())
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs border text-amber-400 bg-amber-400/10 border-amber-400/30">POR VENCER</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs border text-emerald-400 bg-emerald-400/10 border-emerald-400/30">VIGENTE</span>
                                    @endif
                                @endif
                            </div>
                            <div class="flex flex-wrap gap-x-4 text-xs text-text-muted">
                                @if($document->issued_at)
                                    <span>{{ $document->issued_at->format('d/m/Y') }}</span>
                                @endif
                                @if($document->expires_at)
                                    <span>Vence: {{ $document->expires_at->format('d/m/Y') }}</span>
                                @endif
                                @if($document->doctor)
                                    <span>Dr. {{ $document->doctor->name }}</span>
                                @endif
                                @if($document->notes)
                                    <span class="truncate max-w-xs">{{ $document->notes }}</span>
                                @endif
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center gap-2 shrink-0">
                            <button @click="openPreview('{{ route('medical.documents.preview', $document) }}', '{{ addslashes($document->title) }}')"
                                    class="btn-ghost text-sm px-3 py-2">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                                Ver
                            </button>

                            @if($document->images_url)
                                <a href="{{ $document->images_url }}"
                                   target="_blank" rel="noopener noreferrer"
                                   class="btn-ghost text-sm px-3 py-2">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="3" y="3" width="18" height="18" rx="2"/>
                                        <circle cx="8.5" cy="8.5" r="1.5"/>
                                        <path d="M21 15l-5-5L5 21"/>
                                    </svg>
                                    Imágenes
                                </a>
                            @endif

                            <button @click="editing = !editing" class="btn-ghost text-sm px-3 py-2">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                </svg>
                                Editar
                            </button>

                            <a href="{{ route('medical.documents.download', $document) }}"
                               class="btn-ghost text-sm px-3 py-2">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                    <polyline points="7 10 12 15 17 10"/>
                                    <line x1="12" y1="15" x2="12" y2="3"/>
                                </svg>
                                Descargar
                            </a>

                            <div x-show="!confirmDelete">
                                <button @click="confirmDelete = true"
                                        class="btn-ghost text-sm px-3 py-2 text-red-400 hover:text-red-300 hover:bg-red-400/10">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="3 6 5 6 21 6"/>
                                        <path d="M19 6l-1 14H6L5 6"/>
                                        <path d="M10 11v6M14 11v6"/>
                                    </svg>
                                </button>
                            </div>

                            <div x-show="confirmDelete" class="flex items-center gap-2" style="display: none;">
                                <span class="text-xs text-red-400">¿Eliminar?</span>
                                <form action="{{ route('medical.documents.destroy', $document) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs px-2 py-1 rounded bg-red-500/20 text-red-400 border border-red-500/30 hover:bg-red-500/30 transition-colors">
                                        Sí
                                    </button>
                                </form>
                                <button @click="confirmDelete = false" class="text-xs px-2 py-1 rounded bg-border-subtle text-text-muted hover:text-text-main transition-colors">
                                    No
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Inline edit form --}}
                    <div x-show="editing" x-cloak class="mt-4 pt-4 border-t border-white/5">
                        <form action="{{ route('medical.documents.update', $document) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="form-label">Tipo de documento</label>
                                    <select name="type" required class="form-select">
                                        @foreach($documentTypes as $type)
                                            <option value="{{ $type->value }}" {{ $document->type === $type ? 'selected' : '' }}>
                                                {{ $type->label() }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="form-label">Título</label>
                                    <input type="text" name="title" value="{{ $document->title }}" required class="form-input">
                                </div>

                                <div>
                                    <label class="form-label">Fecha del estudio</label>
                                    <input type="date" name="issued_at" value="{{ $document->issued_at?->format('Y-m-d') }}" class="form-input">
                                </div>

                                <div>
                                    <label class="form-label">Fecha de vencimiento</label>
                                    <input type="date" name="expires_at" value="{{ $document->expires_at?->format('Y-m-d') }}" class="form-input">
                                </div>

                                <div>
                                    <label class="form-label">Médico solicitante</label>
                                    <select name="doctor_id" class="form-select">
                                        <option value="">Sin especificar</option>
                                        @foreach($doctors as $doctor)
                                            <option value="{{ $doctor->id }}" {{ $document->doctor_id === $doctor->id ? 'selected' : '' }}>
                                                {{ $doctor->name }}{{ $doctor->specialty ? ' — '.$doctor->specialty : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="form-label">Reemplazar archivo <span class="text-xs text-text-muted">(opcional)</span></label>
                                    <input type="file" name="document" accept=".pdf" class="form-input">
                                </div>

                                <div class="sm:col-span-2">
                                    <label class="form-label">
                                        Link de imágenes del estudio
                                        <span class="text-xs text-text-muted">(opcional)</span>
                                    </label>
                                    <input type="url"
                                           name="images_url"
                                           value="{{ $document->images_url }}"
                                           placeholder="https://imagenes.iradiologico.com.ar/..."
                                           maxlength="2048"
                                           class="form-input">
                                </div>

                                <div class="sm:col-span-2">
                                    <label class="form-label">Notas</label>
                                    <textarea name="notes" maxlength="1000" rows="2" class="form-input resize-none">{{ $document->notes }}</textarea>
                                </div>
                            </div>

                            <div class="flex justify-end gap-3 mt-4">
                                <button type="button" @click="editing = false" class="btn-ghost text-sm">Cancelar</button>
                                <button type="submit" class="btn-primary text-sm">Guardar Cambios</button>
                            </div>
                        </form>
                    </div>
                </div>
            @empty
                <div class="card border-dashed border-2 border-border-subtle text-center py-12">
                    <svg class="w-12 h-12 text-text-muted mx-auto mb-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <path d="M9 13h6M9 17h4"/>
                    </svg>
                    <p class="text-text-main font-medium mb-1">Sin documentos cargados</p>
                    <p class="text-text-muted text-sm">Subí tus estudios para tenerlos siempre disponibles.</p>
                </div>
            @endforelse
        </section>

        {{-- Reportes de Estudios Compartidos --}}
        @if($documentGroups->isNotEmpty())
            <section class="mb-8">
                <h2 class="flex items-center gap-3 text-xs font-bold uppercase tracking-widest text-accent-secondary mb-4">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/>
                        <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/>
                        <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/>
                    </svg>
                    Reportes de Estudios
                    <span class="text-text-muted font-normal normal-case tracking-normal">({{ $documentGroups->count() }})</span>
                    <span class="flex-1 h-px bg-gradient-to-r from-accent-secondary/30 to-transparent"></span>
                </h2>
                <p class="text-xs text-text-muted mb-4">Grupos de estudios listos para compartir por link con cualquier médico.</p>

                @foreach($documentGroups as $group)
                    <div class="card mb-3" x-data="{ confirmDeleteGroup: false }">
                        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                            <div class="flex-1 min-w-0">
                                <div class="flex flex-wrap items-center gap-2 mb-1">
                                    <span class="font-medium text-sm">{{ $group->title }}</span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs border text-accent-secondary bg-accent-secondary/10 border-accent-secondary/30">
                                        {{ $group->documents->count() }} {{ Str::plural('estudio', $group->documents->count()) }}
                                    </span>
                                </div>
                                <div class="flex flex-wrap gap-x-4 text-xs text-text-muted">
                                    @if($group->doctor)
                                        <span>Para: Dr. {{ $group->doctor->name }}</span>
                                    @endif
                                    <span>Creado: {{ $group->created_at->format('d/m/Y') }}</span>
                                    @if($group->notes)
                                        <span class="truncate max-w-xs">{{ $group->notes }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-center gap-2 shrink-0">
                                <button @click="shareGroup({{ $group->id }}, '{{ route('medical.groups.share', $group) }}')"
                                        :disabled="sharingGroup === {{ $group->id }}"
                                        class="btn-secondary text-sm px-3 py-2">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/>
                                        <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/>
                                        <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/>
                                    </svg>
                                    <span x-text="sharingGroup === {{ $group->id }} ? 'Generando...' : 'Compartir'">Compartir</span>
                                </button>

                                <a href="{{ route('medical.groups.zip', $group) }}" class="btn-ghost text-sm px-3 py-2">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                        <polyline points="7 10 12 15 17 10"/>
                                        <line x1="12" y1="15" x2="12" y2="3"/>
                                    </svg>
                                    ZIP
                                </a>

                                <div x-show="!confirmDeleteGroup">
                                    <button @click="confirmDeleteGroup = true"
                                            class="btn-ghost text-sm px-3 py-2 text-red-400 hover:text-red-300 hover:bg-red-400/10">
                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <polyline points="3 6 5 6 21 6"/>
                                            <path d="M19 6l-1 14H6L5 6"/>
                                            <path d="M10 11v6M14 11v6"/>
                                        </svg>
                                    </button>
                                </div>
                                <div x-show="confirmDeleteGroup" class="flex items-center gap-2" style="display: none;">
                                    <span class="text-xs text-red-400">¿Eliminar?</span>
                                    <form action="{{ route('medical.groups.destroy', $group) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs px-2 py-1 rounded bg-red-500/20 text-red-400 border border-red-500/30 hover:bg-red-500/30 transition-colors">Sí</button>
                                    </form>
                                    <button @click="confirmDeleteGroup = false" class="text-xs px-2 py-1 rounded bg-border-subtle text-text-muted hover:text-text-main transition-colors">No</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </section>
        @endif

        {{-- Médicos --}}
        <section class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                <h2 class="flex items-center gap-3 text-xs font-bold uppercase tracking-widest text-accent-secondary">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                    Médicos
                    <span class="text-text-muted font-normal normal-case tracking-normal">({{ $doctors->count() }})</span>
                </h2>
                <button @click="showDoctorForm = !showDoctorForm" class="btn-primary text-sm w-full sm:w-auto justify-center">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    <span x-text="showDoctorForm ? 'Cancelar' : 'Agregar Médico'">Agregar Médico</span>
                </button>
            </div>

            {{-- New doctor form --}}
            <div x-show="showDoctorForm" x-cloak class="mb-6" style="display: none;">
                <x-card class="border-accent-secondary/20">
                    <form action="{{ route('medical.doctors.store') }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="form-label">Nombre <span class="text-accent-primary">*</span></label>
                                <input type="text" name="name" placeholder="ej: Dr. Juan Pérez" required class="form-input">
                            </div>
                            <div>
                                <label class="form-label">Especialidad</label>
                                <input type="text" name="specialty" placeholder="ej: Cardiología" class="form-input">
                            </div>
                            <div>
                                <label class="form-label">Teléfono</label>
                                <input type="text" name="phone" class="form-input">
                            </div>
                            <div>
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-input">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="form-label">Consultorio / Dirección</label>
                                <input type="text" name="address" class="form-input">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="form-label">Notas</label>
                                <textarea name="notes" rows="2" class="form-input resize-none"></textarea>
                            </div>
                        </div>
                        <div class="flex justify-end gap-3 mt-4">
                            <button type="button" @click="showDoctorForm = false" class="btn-ghost text-sm">Cancelar</button>
                            <button type="submit" class="btn-primary text-sm">Guardar Médico</button>
                        </div>
                    </form>
                </x-card>
            </div>

            @forelse($doctors as $doctor)
                <div class="card mb-3" x-data="{ confirmDeleteDoctor: false, editingDoctor: false }">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                        <div class="flex-1 min-w-0">
                            <div class="font-medium text-sm">{{ $doctor->name }}</div>
                            <div class="flex flex-wrap gap-x-4 text-xs text-text-muted mt-1">
                                @if($doctor->specialty)<span>{{ $doctor->specialty }}</span>@endif
                                @if($doctor->phone)<span>{{ $doctor->phone }}</span>@endif
                                @if($doctor->email)<span>{{ $doctor->email }}</span>@endif
                                @if($doctor->address)<span>{{ $doctor->address }}</span>@endif
                            </div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <button @click="editingDoctor = !editingDoctor" class="btn-ghost text-sm px-3 py-2">Editar</button>
                            <div x-show="!confirmDeleteDoctor">
                                <button @click="confirmDeleteDoctor = true" class="btn-ghost text-sm px-3 py-2 text-red-400 hover:text-red-300 hover:bg-red-400/10">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/>
                                    </svg>
                                </button>
                            </div>
                            <div x-show="confirmDeleteDoctor" class="flex items-center gap-2" style="display: none;">
                                <span class="text-xs text-red-400">¿Eliminar?</span>
                                <form action="{{ route('medical.doctors.destroy', $doctor) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs px-2 py-1 rounded bg-red-500/20 text-red-400 border border-red-500/30 hover:bg-red-500/30 transition-colors">Sí</button>
                                </form>
                                <button @click="confirmDeleteDoctor = false" class="text-xs px-2 py-1 rounded bg-border-subtle text-text-muted hover:text-text-main transition-colors">No</button>
                            </div>
                        </div>
                    </div>

                    <div x-show="editingDoctor" x-cloak class="mt-4 pt-4 border-t border-white/5">
                        <form action="{{ route('medical.doctors.update', $doctor) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="form-label">Nombre</label>
                                    <input type="text" name="name" value="{{ $doctor->name }}" required class="form-input">
                                </div>
                                <div>
                                    <label class="form-label">Especialidad</label>
                                    <input type="text" name="specialty" value="{{ $doctor->specialty }}" class="form-input">
                                </div>
                                <div>
                                    <label class="form-label">Teléfono</label>
                                    <input type="text" name="phone" value="{{ $doctor->phone }}" class="form-input">
                                </div>
                                <div>
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" value="{{ $doctor->email }}" class="form-input">
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="form-label">Consultorio / Dirección</label>
                                    <input type="text" name="address" value="{{ $doctor->address }}" class="form-input">
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="form-label">Notas</label>
                                    <textarea name="notes" rows="2" class="form-input resize-none">{{ $doctor->notes }}</textarea>
                                </div>
                            </div>
                            <div class="flex justify-end gap-3 mt-4">
                                <button type="button" @click="editingDoctor = false" class="btn-ghost text-sm">Cancelar</button>
                                <button type="submit" class="btn-primary text-sm">Guardar Cambios</button>
                            </div>
                        </form>
                    </div>
                </div>
            @empty
                <div class="card border-dashed border-2 border-border-subtle text-center py-8">
                    <p class="text-text-muted text-sm">Todavía no cargaste ningún médico.</p>
                </div>
            @endforelse
        </section>

        {{-- Preview Modal --}}
        <div x-show="previewUrl"
             x-cloak
             class="fixed inset-0 bg-black/70 z-[9999] flex items-center justify-center p-4"
             @click.self="closePreview()"
             style="display: none;">
            <div class="bg-bg-card border border-border-subtle rounded-card w-full max-w-2xl h-[92vh] flex flex-col shadow-2xl">
                <div class="flex items-center justify-between px-5 py-3 border-b border-white/5 shrink-0">
                    <span class="font-medium text-sm truncate" x-text="previewTitle"></span>
                    <button @click="closePreview()" class="btn-ghost text-sm px-2 py-1">✕</button>
                </div>
                <iframe :src="previewUrl" class="flex-1 w-full rounded-b-card bg-white"></iframe>
            </div>
        </div>

        {{-- New Group Modal --}}
        <div x-show="showGroupForm"
             x-cloak
             class="fixed inset-0 bg-black/70 z-[9999] flex items-center justify-center p-4"
             @click.self="showGroupForm = false"
             style="display: none;">
            <div class="bg-bg-card border border-accent-secondary/30 rounded-card w-full max-w-lg shadow-2xl">
                <form action="{{ route('medical.groups.store') }}" method="POST" class="p-6">
                    @csrf
                    <h3 class="font-display text-responsive-lg mb-1 text-accent-secondary">Nuevo Reporte de Estudios</h3>
                    <p class="text-xs text-text-muted mb-4">
                        <span x-text="selectedDocs.length"></span> estudio(s) seleccionado(s):
                    </p>
                    <ul class="text-xs text-text-muted mb-4 list-disc list-inside max-h-24 overflow-y-auto">
                        <template x-for="id in selectedDocs" :key="id">
                            <li x-text="docsMap[id]"></li>
                        </template>
                    </ul>

                    <template x-for="id in selectedDocs" :key="'input-'+id">
                        <input type="hidden" name="document_ids[]" :value="id">
                    </template>

                    <div class="space-y-4">
                        <div>
                            <label class="form-label">Título <span class="text-accent-primary">*</span></label>
                            <input type="text" name="title" placeholder="ej: Estudios pre-consulta clínica" required class="form-input">
                        </div>
                        <div>
                            <label class="form-label">Médico destinatario <span class="text-xs text-text-muted">(opcional)</span></label>
                            <select name="doctor_id" class="form-select">
                                <option value="">Sin especificar</option>
                                @foreach($doctors as $doctor)
                                    <option value="{{ $doctor->id }}">{{ $doctor->name }}{{ $doctor->specialty ? ' — '.$doctor->specialty : '' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Notas <span class="text-xs text-text-muted">(opcional)</span></label>
                            <textarea name="notes" rows="2" class="form-input resize-none"></textarea>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" @click="showGroupForm = false" class="btn-ghost text-sm">Cancelar</button>
                        <button type="submit" class="btn-primary text-sm">Crear Reporte</button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <script>
        function medicalHub(docsMap) {
            return {
                showUploadForm: false,
                showDoctorForm: false,
                showGroupForm: false,
                previewUrl: null,
                previewTitle: '',
                selectedDocs: [],
                sharingGroup: null,
                docsMap: docsMap,
                openPreview(url, title) {
                    this.previewUrl = url;
                    this.previewTitle = title;
                },
                closePreview() {
                    this.previewUrl = null;
                },
                shareGroup(groupId, shareUrl) {
                    this.sharingGroup = groupId;
                    fetch(shareUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) { showShareModal(data.url, data.expires_at); }
                        else { alert('Error al generar el link.'); }
                    })
                    .catch(() => alert('Error al generar el link.'))
                    .finally(() => { this.sharingGroup = null; });
                }
            };
        }

        function showShareModal(url, expiresAt) {
            const overlay = document.createElement('div');
            overlay.className = 'fixed inset-0 bg-black/70 z-[9999] flex items-center justify-center p-4';
            const modal = document.createElement('div');
            modal.className = 'bg-bg-card border border-accent-primary rounded-card p-6 sm:p-8 max-w-lg w-full shadow-2xl shadow-accent-primary/20';
            modal.innerHTML = `
                <div class="text-center">
                    <h3 class="font-display text-responsive-xl mb-2 text-accent-primary">Compartir</h3>
                    <p class="text-text-muted mb-1 text-sm">Válido por 7 días · Expira el <strong class="text-text-main">${expiresAt}</strong></p>
                    <p class="text-text-muted mb-5 text-xs">No requiere inicio de sesión para verlo.</p>
                    <div class="bg-border-subtle/50 p-3 rounded-btn mb-5 break-all">
                        <input type="text" id="shareUrl" value="${url}" readonly
                               class="w-full bg-transparent border-none text-accent-secondary text-sm text-center outline-none select-all">
                    </div>
                    <div class="flex flex-col sm:flex-row gap-3 justify-center">
                        <button onclick="copyShareUrl()" class="btn-secondary flex-1 sm:flex-initial justify-center py-3">
                            Copiar Link
                        </button>
                        <button onclick="closeShareModal()" class="btn-ghost flex-1 sm:flex-initial justify-center py-3">
                            Cerrar
                        </button>
                    </div>
                </div>`;
            overlay.appendChild(modal);
            document.body.appendChild(overlay);
            overlay.addEventListener('click', e => { if (e.target === overlay) closeShareModal(); });
            window.shareModalOverlay = overlay;
        }

        function copyShareUrl() {
            const input = document.getElementById('shareUrl');
            input.select();
            document.execCommand('copy');
            const btn = event.target;
            const orig = btn.innerHTML;
            btn.innerHTML = '✓ Copiado!';
            btn.classList.add('!bg-green-500/20', '!border-green-500', '!text-green-400');
            setTimeout(() => {
                btn.innerHTML = orig;
                btn.classList.remove('!bg-green-500/20', '!border-green-500', '!text-green-400');
            }, 2000);
        }

        function closeShareModal() {
            if (window.shareModalOverlay) {
                document.body.removeChild(window.shareModalOverlay);
                window.shareModalOverlay = null;
            }
        }
    </script>
</x-app-layout>
