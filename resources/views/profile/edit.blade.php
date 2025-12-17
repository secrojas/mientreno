@php
    $user = auth()->user();
@endphp

<x-app-layout title="Mi Perfil">
    <style>

        .profile-container {
            max-width: 1200px;
            margin: 0 auto;
            animation: fadeInUp 0.6s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .profile-header {
            margin-bottom: 2.5rem;
        }

        .profile-title {
            font-family: 'Space Grotesk', system-ui, sans-serif;
            font-size: 2.75rem;
            font-weight: 400;
            color: var(--text-main);
            margin-bottom: 0.5rem;
            letter-spacing: -0.02em;
            background: linear-gradient(135deg, #F9FAFB 0%, #9CA3AF 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .profile-subtitle {
            font-family: 'Inter', system-ui, sans-serif;
            font-size: 1rem;
            color: var(--text-muted);
            font-weight: 400;
        }

        .profile-grid {
            display: grid;
            grid-template-columns: 320px 1fr;
            gap: 2.5rem;
            margin-bottom: 2rem;
        }

        /* Avatar Section */
        .avatar-section {
            position: relative;
            animation: fadeInLeft 0.8s ease-out 0.2s both;
        }

        @keyframes fadeInLeft {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .avatar-card {
            background: linear-gradient(135deg, rgba(11, 12, 18, 0.8) 0%, rgba(5, 6, 10, 0.9) 100%);
            border: 1px solid rgba(255, 59, 92, 0.2);
            border-radius: 1.5rem;
            padding: 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .avatar-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 120px;
            background: linear-gradient(135deg, rgba(255, 59, 92, 0.1) 0%, rgba(45, 227, 142, 0.05) 100%);
            z-index: 0;
        }

        .avatar-wrapper {
            position: relative;
            width: 200px;
            height: 200px;
            margin: 0 auto 1.5rem;
            z-index: 1;
        }

        .avatar-frame {
            position: relative;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            padding: 4px;
            background: linear-gradient(135deg, #FF3B5C 0%, #2DE38E 100%);
            animation: rotateGradient 3s linear infinite;
        }

        @keyframes rotateGradient {
            0% {
                filter: hue-rotate(0deg);
            }
            100% {
                filter: hue-rotate(360deg);
            }
        }

        .avatar-image {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: var(--bg-card);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        .avatar-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .avatar-initials {
            font-family: 'Space Grotesk', system-ui, sans-serif;
            font-size: 4rem;
            font-weight: 400;
            color: #FF3B5C;
            font-style: italic;
        }

        .avatar-upload-btn {
            position: relative;
            z-index: 1;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.875rem 1.75rem;
            background: linear-gradient(135deg, #FF3B5C 0%, #e63153 100%);
            border: none;
            border-radius: 3rem;
            color: white;
            font-family: 'Inter', system-ui, sans-serif;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 16px rgba(255, 59, 92, 0.3);
        }

        .avatar-upload-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 24px rgba(255, 59, 92, 0.4);
        }

        .avatar-upload-btn:active {
            transform: translateY(0);
        }

        #avatar-input {
            display: none;
        }

        .avatar-info {
            position: relative;
            z-index: 1;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
        }

        .info-item {
            font-family: 'Inter', system-ui, sans-serif;
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .info-label {
            opacity: 0.7;
        }

        .info-value {
            color: var(--text-main);
            font-weight: 600;
        }

        /* Form Section */
        .form-section {
            animation: fadeInRight 0.8s ease-out 0.3s both;
        }

        @keyframes fadeInRight {
            from {
                opacity: 0;
                transform: translateX(30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .form-card {
            background: linear-gradient(135deg, rgba(11, 12, 18, 0.6) 0%, rgba(5, 6, 10, 0.8) 100%);
            border: 1px solid rgba(45, 227, 142, 0.15);
            border-radius: 1.5rem;
            padding: 2.5rem;
            position: relative;
            overflow: hidden;
        }

        .form-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(45, 227, 142, 0.05) 0%, transparent 70%);
            pointer-events: none;
        }

        .section-group {
            margin-bottom: 2.5rem;
            position: relative;
        }

        .section-title {
            font-family: 'Inter', system-ui, sans-serif;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #2DE38E;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .section-title::after {
            content: '';
            flex: 1;
            height: 1px;
            background: linear-gradient(to right, rgba(45, 227, 142, 0.3), transparent);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .form-row.full {
            grid-template-columns: 1fr;
        }

        .form-group {
            position: relative;
        }

        .form-label {
            font-family: 'Inter', system-ui, sans-serif;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-main);
            margin-bottom: 0.5rem;
            display: block;
        }

        .form-input,
        .form-select,
        .form-textarea {
            width: 100%;
            padding: 0.875rem 1rem;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 0.75rem;
            color: var(--text-main);
            font-family: 'Inter', system-ui, sans-serif;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .form-input:focus,
        .form-select:focus,
        .form-textarea:focus {
            outline: none;
            background: rgba(255, 255, 255, 0.05);
            border-color: #2DE38E;
            box-shadow: 0 0 0 3px rgba(45, 227, 142, 0.1);
        }

        .form-select option {
            background: #0B0C12;
            color: var(--text-main);
            padding: 0.5rem;
        }

        .form-select option:hover,
        .form-select option:checked {
            background: rgba(45, 227, 142, 0.1);
        }

        .form-input::placeholder {
            color: rgba(156, 163, 175, 0.5);
        }

        .form-textarea {
            resize: vertical;
            min-height: 100px;
        }

        .char-counter {
            font-size: 0.75rem;
            color: var(--text-muted);
            margin-top: 0.25rem;
            text-align: right;
        }

        .input-hint {
            font-size: 0.75rem;
            color: var(--text-muted);
            margin-top: 0.25rem;
            display: flex;
            align-items: center;
            gap: 0.35rem;
        }

        /* Form Actions */
        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            margin-top: 2.5rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
        }

        .btn {
            padding: 0.875rem 2rem;
            border-radius: 0.75rem;
            font-family: 'Inter', system-ui, sans-serif;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, #2DE38E 0%, #1ea568 100%);
            color: #05060A;
            box-shadow: 0 4px 16px rgba(45, 227, 142, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 24px rgba(45, 227, 142, 0.4);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-main);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.08);
        }

        /* Success Message */
        .success-message {
            padding: 1rem 1.5rem;
            background: linear-gradient(135deg, rgba(45, 227, 142, 0.15) 0%, rgba(45, 227, 142, 0.05) 100%);
            border: 1px solid rgba(45, 227, 142, 0.3);
            border-radius: 0.75rem;
            color: #2DE38E;
            font-family: 'Inter', system-ui, sans-serif;
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            animation: slideInDown 0.5s ease-out;
        }

        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 968px) {
            .profile-grid {
                grid-template-columns: 1fr;
                gap: 2rem;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .profile-title {
                font-size: 2.25rem;
            }
        }
    </style>

    <div class="profile-container">
        <!-- Header -->
        <div class="profile-header">
            <h1 class="profile-title">Mi Perfil</h1>
            <p class="profile-subtitle">Gestiona tu información personal y preferencias</p>
        </div>

        @if(session('status') === 'profile-updated')
            <div class="success-message">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
                Perfil actualizado correctamente
            </div>
        @endif

        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <div class="profile-grid">
                <!-- Avatar Section -->
                <div class="avatar-section">
                    <div class="avatar-card">
                        <div class="avatar-wrapper">
                            <div class="avatar-frame">
                                <div class="avatar-image" id="avatar-preview">
                                    @if($user->avatar)
                                        <img src="{{ $user->avatarUrl }}" alt="{{ $user->name }}">
                                    @else
                                        <div class="avatar-initials">{{ strtoupper(substr($user->name, 0, 2)) }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <input type="file" id="avatar-input" name="avatar" accept="image/*">
                        <button type="button" class="avatar-upload-btn" onclick="document.getElementById('avatar-input').click()">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path>
                                <circle cx="12" cy="13" r="4"></circle>
                            </svg>
                            Cambiar Foto
                        </button>

                        <div class="avatar-info">
                            <div class="info-item">
                                <span class="info-label">Rol:</span>
                                <span class="info-value">{{ ucfirst($user->role) }}</span>
                            </div>
                            @if($user->age)
                                <div class="info-item">
                                    <span class="info-label">Edad:</span>
                                    <span class="info-value">{{ $user->age }} años</span>
                                </div>
                            @endif
                            @if($user->weight && $user->height)
                                <div class="info-item">
                                    <span class="info-label">IMC:</span>
                                    <span class="info-value">{{ number_format($user->weight / (($user->height / 100) ** 2), 1) }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Form Section -->
                <div class="form-section">
                    <div class="form-card">
                        <!-- Información Básica -->
                        <div class="section-group">
                            <h3 class="section-title">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                                Información Básica
                            </h3>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="name" class="form-label">Nombre Completo</label>
                                    <input type="text" id="name" name="name" class="form-input"
                                           value="{{ old('name', $user->name) }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" id="email" name="email" class="form-input"
                                           value="{{ old('email', $user->email) }}" required>
                                </div>
                            </div>
                        </div>

                        <!-- Datos Personales -->
                        <div class="section-group">
                            <h3 class="section-title">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                </svg>
                                Datos Personales
                            </h3>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="birth_date" class="form-label">Fecha de Nacimiento</label>
                                    <input type="date" id="birth_date" name="birth_date" class="form-input"
                                           value="{{ old('birth_date', $user->birth_date?->format('Y-m-d')) }}">
                                    <div class="input-hint">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <path d="M12 16v-4"></path>
                                            <path d="M12 8h.01"></path>
                                        </svg>
                                        Usamos esto para calcular tu edad
                                    </div>
                                </div>
                                <div class="form-group">
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
                        </div>

                        <!-- Datos Físicos -->
                        <div class="section-group">
                            <h3 class="section-title">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M22 12h-4l-3 9L9 3l-3 9H2"></path>
                                </svg>
                                Datos Físicos
                            </h3>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="weight" class="form-label">Peso (kg)</label>
                                    <input type="number" id="weight" name="weight" class="form-input"
                                           value="{{ old('weight', $user->weight) }}"
                                           step="0.1" min="20" max="300" placeholder="70.5">
                                </div>
                                <div class="form-group">
                                    <label for="height" class="form-label">Altura (cm)</label>
                                    <input type="number" id="height" name="height" class="form-input"
                                           value="{{ old('height', $user->height) }}"
                                           min="100" max="250" placeholder="175">
                                </div>
                            </div>
                        </div>

                        <!-- Bio -->
                        <div class="section-group">
                            <h3 class="section-title">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <polyline points="14 2 14 8 20 8"></polyline>
                                    <line x1="16" y1="13" x2="8" y2="13"></line>
                                    <line x1="16" y1="17" x2="8" y2="17"></line>
                                    <polyline points="10 9 9 9 8 9"></polyline>
                                </svg>
                                Sobre Ti
                            </h3>
                            <div class="form-row full">
                                <div class="form-group">
                                    <label for="bio" class="form-label">Bio</label>
                                    <textarea id="bio" name="bio" class="form-textarea"
                                              maxlength="150" placeholder="Cuéntanos un poco sobre ti y tus objetivos...">{{ old('bio', $user->bio) }}</textarea>
                                    <div class="char-counter">
                                        <span id="bio-count">{{ strlen($user->bio ?? '') }}</span>/150 caracteres
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="form-actions">
                            <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                                    <polyline points="17 21 17 13 7 13 7 21"></polyline>
                                    <polyline points="7 3 7 8 15 8"></polyline>
                                </svg>
                                Guardar Cambios
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        // Avatar preview
        document.getElementById('avatar-input').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('avatar-preview');
                    preview.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
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
</x-app-layout>
