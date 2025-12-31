<x-guest-layout>
    <section class="max-w-xl w-full mx-auto">
        <div class="text-center mb-7">
            <div class="badge mb-3 inline-flex">
                <span>{{ isset($businessName) ? 'Unirse a ' . $businessName : 'Nuevo en MiEntreno' }}</span>
            </div>
            <h1 class="font-display text-responsive-2xl mb-2">
                Crear cuenta
            </h1>
            <p class="text-responsive-sm text-text-muted">
                @if(isset($businessName))
                    Te estás registrando en <strong class="text-text-main">{{ $businessName }}</strong>
                @else
                    Empezá a registrar tus kilómetros y seguí tus objetivos.
                @endif
            </p>
        </div>

        <form method="POST" action="{{ route('register') }}"
              class="card p-6 grid gap-4">
            @csrf

            @if(isset($invitationToken))
                <input type="hidden" name="invitation_token" value="{{ $invitationToken }}">
            @endif

            @if ($errors->any())
                <div class="px-3 py-3 bg-accent-primary bg-opacity-10 border border-accent-primary border-opacity-30
                            rounded-btn text-sm text-[#ff6b6b]">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label for="name" class="form-label">Nombre</label>
                    <input
                        id="name"
                        name="name"
                        type="text"
                        required
                        value="{{ old('name') }}"
                        class="form-input"
                    >
                </div>

                <div>
                    <label for="role" class="form-label">Tipo de usuario</label>
                    <select
                        id="role"
                        name="role"
                        required
                        class="form-select"
                    >
                        <option value="runner" {{ old('role') === 'runner' ? 'selected' : '' }}>Runner / Alumno</option>
                        <option value="coach" {{ old('role') === 'coach' ? 'selected' : '' }}>Entrenador</option>
                    </select>
                </div>
            </div>

            <div>
                <label for="email" class="form-label">Email</label>
                <input
                    id="email"
                    name="email"
                    type="email"
                    required
                    value="{{ old('email') }}"
                    class="form-input"
                >
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label for="password" class="form-label">Password</label>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        required
                        class="form-input"
                    >
                </div>
                <div>
                    <label for="password_confirmation" class="form-label">Repetir password</label>
                    <input
                        id="password_confirmation"
                        name="password_confirmation"
                        type="password"
                        required
                        class="form-input"
                    >
                </div>
            </div>

            <button type="submit" class="btn-primary w-full justify-center">
                Crear cuenta
            </button>
        </form>

        <p class="mt-4 text-responsive-xs text-text-muted text-center">
            ¿Ya tenés cuenta?
            <a href="{{ route('login') }}" class="text-accent-primary hover:text-accent-primary/80 transition-colors">
                Iniciar sesión
            </a>
        </p>
    </section>
</x-guest-layout>
