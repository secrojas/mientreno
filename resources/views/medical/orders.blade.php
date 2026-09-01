<x-app-layout title="Órdenes Médicas">
    <div x-data="ordersHub()" class="max-w-7xl mx-auto">

        {{-- Header --}}
        <header class="mb-8">
            <h1 class="font-display text-responsive-2xl mb-2 bg-gradient-to-r from-text-main to-text-muted bg-clip-text text-transparent">
                Salud Médica
            </h1>
            <p class="text-responsive-base text-text-muted">
                Historial de órdenes y certificados que te fue emitiendo cada médico.
            </p>
        </header>

        {{-- Submenú --}}
        <div class="flex gap-1 mb-8 border-b border-white/5">
            <a href="{{ route('medical.index') }}" class="px-4 py-2.5 text-sm font-medium text-text-muted hover:text-text-main transition-colors">
                Documentos y Estudios
            </a>
            <span class="px-4 py-2.5 text-sm font-medium text-accent-secondary border-b-2 border-accent-secondary">
                Órdenes Médicas
            </span>
        </div>

        {{-- Flash messages --}}
        @php
            $flashMessages = [
                'order-uploaded' => 'Orden subida correctamente.',
                'order-updated' => 'Orden actualizada correctamente.',
                'order-deleted' => 'Orden eliminada.',
            ];
            $status = session('status');
            $isError = $status === 'order-deleted';
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

        {{-- Órdenes --}}
        <section class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                <h2 class="flex items-center gap-3 text-xs font-bold uppercase tracking-widest text-accent-secondary">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <path d="M9 13h6M9 17h4"/>
                    </svg>
                    Órdenes Médicas
                    <span class="text-text-muted font-normal normal-case tracking-normal">({{ $orders->count() }})</span>
                </h2>
                <button @click="showUploadForm = !showUploadForm" class="btn-primary text-sm w-full sm:w-auto justify-center">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/>
                        <circle cx="12" cy="13" r="4"/>
                    </svg>
                    <span x-text="showUploadForm ? 'Cancelar' : 'Subir Orden'">Subir Orden</span>
                </button>
            </div>
            <p class="text-xs text-text-muted mb-4">Sacale una foto a la orden en papel (o subí el PDF) para tener el historial de lo que te fueron pidiendo.</p>

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
                            <div class="card-title">Nueva Orden</div>
                            <div class="card-subtitle">Foto (JPG/PNG) o PDF · Máximo 10MB</div>
                        </div>
                    </div>

                    <form action="{{ route('medical.orders.store') }}" method="POST" enctype="multipart/form-data">
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
                            <div class="sm:col-span-2">
                                <label class="form-label">Título <span class="text-accent-primary">*</span></label>
                                <input type="text" name="title" value="{{ old('title') }}" placeholder="ej: Orden análisis de sangre" required class="form-input">
                            </div>

                            <div>
                                <label class="form-label">Fecha de emisión</label>
                                <input type="date" name="issued_at" value="{{ old('issued_at') }}" class="form-input">
                            </div>

                            <div>
                                <label class="form-label">Médico emisor <span class="text-xs text-text-muted">(opcional)</span></label>
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
                                <textarea name="notes" maxlength="1000" rows="2" placeholder="Qué estudios pide, observaciones, etc." class="form-input resize-none">{{ old('notes') }}</textarea>
                            </div>

                            <div class="sm:col-span-2">
                                <label class="form-label">Foto o PDF <span class="text-accent-primary">*</span></label>
                                <label class="flex items-center gap-3 px-4 py-3 rounded-btn border border-dashed border-border-subtle
                                              bg-bg-sidebar cursor-pointer hover:border-accent-secondary/50 transition-colors">
                                    <svg class="w-5 h-5 text-text-muted shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/>
                                        <circle cx="12" cy="13" r="4"/>
                                    </svg>
                                    <input type="file" name="file" accept=".pdf,.jpg,.jpeg,.png" required
                                           @change="$el.parentElement.querySelector('span').textContent = $event.target.files[0]?.name || 'Seleccioná una foto o PDF'"
                                           class="sr-only">
                                    <span class="text-sm text-text-muted">Seleccioná una foto o PDF</span>
                                </label>
                            </div>
                        </div>

                        <div class="flex justify-end gap-3 mt-6 pt-5 border-t border-white/5">
                            <button type="button" @click="showUploadForm = false" class="btn-ghost text-sm">Cancelar</button>
                            <button type="submit" class="btn-primary text-sm">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                    <polyline points="17 8 12 3 7 8"/>
                                    <line x1="12" y1="3" x2="12" y2="15"/>
                                </svg>
                                Subir Orden
                            </button>
                        </div>
                    </form>
                </x-card>
            </div>

            {{-- Orders List --}}
            @forelse($orders as $order)
                <div class="card mb-3" x-data="{ confirmDelete: false, editing: false }">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                        <div class="shrink-0">
                            <div class="w-10 h-10 rounded-btn flex items-center justify-center bg-amber-400/10">
                                <svg class="w-5 h-5 text-amber-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                    <polyline points="14 2 14 8 20 8"/>
                                    <path d="M9 13h6M9 17h4"/>
                                </svg>
                            </div>
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="flex flex-wrap items-center gap-2 mb-1">
                                <span class="font-medium text-sm truncate">{{ $order->title }}</span>
                            </div>
                            <div class="flex flex-wrap gap-x-4 text-xs text-text-muted">
                                @if($order->issued_at)
                                    <span>{{ $order->issued_at->format('d/m/Y') }}</span>
                                @endif
                                @if($order->doctor)
                                    <span>Dr. {{ $order->doctor->name }}</span>
                                @endif
                                @if($order->notes)
                                    <span class="truncate max-w-xs">{{ $order->notes }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center gap-2 shrink-0">
                            <button @click="openPreview('{{ route('medical.orders.preview', $order) }}', '{{ addslashes($order->title) }}')"
                                    class="btn-ghost text-sm px-3 py-2">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                                Ver
                            </button>

                            <button @click="editing = !editing" class="btn-ghost text-sm px-3 py-2">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                </svg>
                                Editar
                            </button>

                            <a href="{{ route('medical.orders.download', $order) }}" class="btn-ghost text-sm px-3 py-2">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                    <polyline points="7 10 12 15 17 10"/>
                                    <line x1="12" y1="15" x2="12" y2="3"/>
                                </svg>
                                Descargar
                            </a>

                            <div x-show="!confirmDelete">
                                <button @click="confirmDelete = true" class="btn-ghost text-sm px-3 py-2 text-red-400 hover:text-red-300 hover:bg-red-400/10">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/>
                                    </svg>
                                </button>
                            </div>
                            <div x-show="confirmDelete" class="flex items-center gap-2" style="display: none;">
                                <span class="text-xs text-red-400">¿Eliminar?</span>
                                <form action="{{ route('medical.orders.destroy', $order) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs px-2 py-1 rounded bg-red-500/20 text-red-400 border border-red-500/30 hover:bg-red-500/30 transition-colors">Sí</button>
                                </form>
                                <button @click="confirmDelete = false" class="text-xs px-2 py-1 rounded bg-border-subtle text-text-muted hover:text-text-main transition-colors">No</button>
                            </div>
                        </div>
                    </div>

                    {{-- Inline edit form --}}
                    <div x-show="editing" x-cloak class="mt-4 pt-4 border-t border-white/5">
                        <form action="{{ route('medical.orders.update', $order) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="sm:col-span-2">
                                    <label class="form-label">Título</label>
                                    <input type="text" name="title" value="{{ $order->title }}" required class="form-input">
                                </div>

                                <div>
                                    <label class="form-label">Fecha de emisión</label>
                                    <input type="date" name="issued_at" value="{{ $order->issued_at?->format('Y-m-d') }}" class="form-input">
                                </div>

                                <div>
                                    <label class="form-label">Médico emisor</label>
                                    <select name="doctor_id" class="form-select">
                                        <option value="">Sin especificar</option>
                                        @foreach($doctors as $doctor)
                                            <option value="{{ $doctor->id }}" {{ $order->doctor_id === $doctor->id ? 'selected' : '' }}>
                                                {{ $doctor->name }}{{ $doctor->specialty ? ' — '.$doctor->specialty : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="sm:col-span-2">
                                    <label class="form-label">Reemplazar archivo <span class="text-xs text-text-muted">(opcional)</span></label>
                                    <input type="file" name="file" accept=".pdf,.jpg,.jpeg,.png" class="form-input">
                                </div>

                                <div class="sm:col-span-2">
                                    <label class="form-label">Notas</label>
                                    <textarea name="notes" maxlength="1000" rows="2" class="form-input resize-none">{{ $order->notes }}</textarea>
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
                        <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/>
                        <circle cx="12" cy="13" r="4"/>
                    </svg>
                    <p class="text-text-main font-medium mb-1">Sin órdenes cargadas</p>
                    <p class="text-text-muted text-sm">Sacale una foto a la próxima orden que te den y subila acá.</p>
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

    </div>

    <script>
        function ordersHub() {
            return {
                showUploadForm: false,
                previewUrl: null,
                previewTitle: '',
                openPreview(url, title) {
                    this.previewUrl = url;
                    this.previewTitle = title;
                },
                closePreview() {
                    this.previewUrl = null;
                }
            };
        }
    </script>
</x-app-layout>
