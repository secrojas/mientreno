@php
    $user = auth()->user();
@endphp

<x-app-layout title="Mi Perfil">
    <div class="max-w-7xl mx-auto animate-fade-in">
        <!-- Header -->
        <header class="mb-10">
            <h1 class="font-display text-responsive-3xl mb-2 bg-gradient-to-r from-text-main to-text-muted bg-clip-text text-transparent">
                Mi Perfil
            </h1>
            <p class="text-responsive-base text-text-muted">
                Gestiona tu información personal y preferencias
            </p>
        </header>

        @if(session('status') === 'profile-updated')
            <div class="px-6 py-4 bg-accent-secondary/10 border border-accent-secondary/30 rounded-card text-accent-secondary mb-6 flex items-center gap-3 animate-slide-down">
                <svg class="w-5 h-5 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
                <span class="text-responsive-sm">Perfil actualizado correctamente</span>
            </div>
        @endif

        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <div class="grid grid-cols-1 lg:grid-cols-[320px_1fr] gap-6 lg:gap-10 mb-8">
                <!-- Avatar Section -->
                <aside class="animate-fade-in-left">
                    <x-card class="bg-gradient-to-br from-bg-card/80 to-bg-main/90 border-accent-primary/20">
                        <!-- Gradient header background -->
                        <div class="absolute top-0 left-0 right-0 h-32 bg-gradient-to-br from-accent-primary/10 to-accent-secondary/5 rounded-t-card -z-10"></div>

                        <!-- Avatar -->
                        <div class="relative w-48 h-48 mx-auto mb-6 z-10">
                            <div class="relative w-full h-full rounded-full p-1 bg-gradient-to-br from-accent-primary to-accent-secondary animate-gradient-rotate">
                                <div id="avatar-preview" class="w-full h-full rounded-full bg-bg-card flex items-center justify-center overflow-hidden">
                                    @if($user->avatar)
                                        <img src="{{ $user->avatarUrl }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="font-display text-6xl font-normal text-accent-primary italic">
                                            {{ strtoupper(substr($user->name, 0, 2)) }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Upload button -->
                        <input type="file" id="avatar-input" name="avatar" accept="image/*" class="hidden">
                        <button type="button" onclick="document.getElementById('avatar-input').click()" class="w-full mb-6 px-7 py-3.5 bg-gradient-to-r from-accent-primary to-pink-500 text-white rounded-full font-semibold text-responsive-sm inline-flex items-center justify-center gap-2 hover:-translate-y-0.5 hover:shadow-lg hover:shadow-accent-primary/40 transition-all active:translate-y-0 min-h-touch">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path>
                                <circle cx="12" cy="13" r="4"></circle>
                            </svg>
                            Cambiar Foto
                        </button>

                        <!-- Info -->
                        <div class="pt-6 border-t border-white/5 space-y-3">
                            <div class="flex items-center gap-2 text-sm">
                                <span class="text-text-muted">Rol:</span>
                                <span class="text-text-main font-semibold">{{ ucfirst($user->role) }}</span>
                            </div>
                            @if($user->age)
                                <div class="flex items-center gap-2 text-sm">
                                    <span class="text-text-muted">Edad:</span>
                                    <span class="text-text-main font-semibold">{{ $user->age }} años</span>
                                </div>
                            @endif
                            @if($user->weight && $user->height)
                                <div class="flex items-center gap-2 text-sm">
                                    <span class="text-text-muted">IMC:</span>
                                    <span class="text-text-main font-semibold">{{ number_format($user->weight / (($user->height / 100) ** 2), 1) }}</span>
                                </div>
                            @endif
                        </div>
                    </x-card>
                </aside>

                <!-- Form Section -->
                <div class="animate-fade-in-right">
                    <x-card class="bg-gradient-to-br from-bg-card/60 to-bg-main/80 border-accent-secondary/15">
                        <!-- Decorative gradient -->
                        <div class="absolute -top-1/2 -right-1/5 w-96 h-96 bg-accent-secondary/5 rounded-full blur-3xl pointer-events-none -z-10"></div>

                        <div class="space-y-10">
                            <!-- Información Básica -->
                            <section>
                                <h3 class="flex items-center gap-3 text-xs font-bold uppercase tracking-widest text-accent-secondary mb-6">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="12" cy="7" r="4"></circle>
                                    </svg>
                                    Información Básica
                                    <span class="flex-1 h-px bg-gradient-to-r from-accent-secondary/30 to-transparent"></span>
                                </h3>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                    <div>
                                        <label for="name" class="form-label">Nombre Completo</label>
                                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required class="form-input">
                                    </div>
                                    <div>
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required class="form-input">
                                    </div>
                                </div>
                            </section>

                            <!-- Datos Personales -->
                            <section>
                                <h3 class="flex items-center gap-3 text-xs font-bold uppercase tracking-widest text-accent-secondary mb-6">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                        <line x1="16" y1="2" x2="16" y2="6"></line>
                                        <line x1="8" y1="2" x2="8" y2="6"></line>
                                        <line x1="3" y1="10" x2="21" y2="10"></line>
                                    </svg>
                                    Datos Personales
                                    <span class="flex-1 h-px bg-gradient-to-r from-accent-secondary/30 to-transparent"></span>
                                </h3>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                    <div>
                                        <label for="birth_date" class="form-label">Fecha de Nacimiento</label>
                                        <input type="date" id="birth_date" name="birth_date" value="{{ old('birth_date', $user->birth_date?->format('Y-m-d')) }}" class="form-input">
                                        <div class="flex items-center gap-1.5 mt-1 text-xs text-text-muted">
                                            <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <circle cx="12" cy="12" r="10"></circle>
                                                <path d="M12 16v-4"></path>
                                                <path d="M12 8h.01"></path>
                                            </svg>
                                            Usamos esto para calcular tu edad
                                        </div>
                                    </div>
                                    <div>
                                        <label for="gender" class="form-label">Género</label>
                                        <select id="gender" name="gender" class="form-select">
                                            <option value="">Selecciona...</option>
                                            <option value="male" {{ old('gender', $user->gender) === 'male' ? 'selected' : '' }}>Masculino</option>
                                            <option value="female" {{ old('gender', $user->gender) === 'female' ? 'selected' : '' }}>Femenino</option>
                                            <option value="other" {{ old('gender', $user->gender) === 'other' ? 'selected' : '' }}>Otro</option>
                                            <option value="prefer_not_to_say" {{ old('gender', $user->gender) === 'prefer_not_to_say' ? 'selected' : '' }}>Prefiero no decir</option>
                                        </select>
                                    </div>
                                </div>
                            </section>

                            <!-- Datos Físicos -->
                            <section>
                                <h3 class="flex items-center gap-3 text-xs font-bold uppercase tracking-widest text-accent-secondary mb-6">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M22 12h-4l-3 9L9 3l-3 9H2"></path>
                                    </svg>
                                    Datos Físicos
                                    <span class="flex-1 h-px bg-gradient-to-r from-accent-secondary/30 to-transparent"></span>
                                </h3>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                    <div>
                                        <label for="weight" class="form-label">Peso (kg)</label>
                                        <input type="number" id="weight" name="weight" value="{{ old('weight', $user->weight) }}" step="0.1" min="20" max="300" placeholder="70.5" class="form-input">
                                    </div>
                                    <div>
                                        <label for="height" class="form-label">Altura (cm)</label>
                                        <input type="number" id="height" name="height" value="{{ old('height', $user->height) }}" min="100" max="250" placeholder="175" class="form-input">
                                    </div>
                                </div>
                            </section>

                            <!-- Bio -->
                            <section>
                                <h3 class="flex items-center gap-3 text-xs font-bold uppercase tracking-widest text-accent-secondary mb-6">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                        <polyline points="14 2 14 8 20 8"></polyline>
                                        <line x1="16" y1="13" x2="8" y2="13"></line>
                                        <line x1="16" y1="17" x2="8" y2="17"></line>
                                        <polyline points="10 9 9 9 8 9"></polyline>
                                    </svg>
                                    Sobre Ti
                                    <span class="flex-1 h-px bg-gradient-to-r from-accent-secondary/30 to-transparent"></span>
                                </h3>
                                <div>
                                    <label for="bio" class="form-label">Bio</label>
                                    <textarea id="bio" name="bio" maxlength="150" placeholder="Cuéntanos un poco sobre ti y tus objetivos..." class="form-input resize-y min-h-[100px]">{{ old('bio', $user->bio) }}</textarea>
                                    <div class="text-xs text-text-muted text-right mt-1">
                                        <span id="bio-count">{{ strlen($user->bio ?? '') }}</span>/150 caracteres
                                    </div>
                                </div>
                            </section>

                            <!-- Actions -->
                            <div class="flex flex-col sm:flex-row justify-end gap-4 pt-8 border-t border-white/5">
                                <a href="{{ route('dashboard') }}" class="btn-ghost min-h-touch justify-center">
                                    Cancelar
                                </a>
                                <button type="submit" class="btn-primary min-h-touch justify-center">
                                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                                        <polyline points="17 21 17 13 7 13 7 21"></polyline>
                                        <polyline points="7 3 7 8 15 8"></polyline>
                                    </svg>
                                    Guardar Cambios
                                </button>
                            </div>
                        </div>
                    </x-card>
                </div>
            </div>
        </form>
    </div>

    @push('styles')
    <style>
        @keyframes fade-in {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes fade-in-left {
            from { opacity: 0; transform: translateX(-30px); }
            to { opacity: 1; transform: translateX(0); }
        }
        @keyframes fade-in-right {
            from { opacity: 0; transform: translateX(30px); }
            to { opacity: 1; transform: translateX(0); }
        }
        @keyframes slide-down {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes gradient-rotate {
            0% { filter: hue-rotate(0deg); }
            100% { filter: hue-rotate(360deg); }
        }
        .animate-fade-in { animation: fade-in 0.6s ease-out; }
        .animate-fade-in-left { animation: fade-in-left 0.8s ease-out 0.2s both; }
        .animate-fade-in-right { animation: fade-in-right 0.8s ease-out 0.3s both; }
        .animate-slide-down { animation: slide-down 0.5s ease-out; }
        .animate-gradient-rotate { animation: gradient-rotate 3s linear infinite; }
    </style>
    @endpush

    @push('scripts')
    <script>
        // Avatar preview
        document.getElementById('avatar-input').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('avatar-preview').innerHTML = `<img src="${e.target.result}" alt="Preview" class="w-full h-full object-cover">`;
                };
                reader.readAsDataURL(file);
            }
        });

        // Bio character counter
        const bioTextarea = document.getElementById('bio');
        const bioCount = document.getElementById('bio-count');
        bioTextarea?.addEventListener('input', function() {
            bioCount.textContent = this.value.length;
        });
    </script>
    @endpush
</x-app-layout>
