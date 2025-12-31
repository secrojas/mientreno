<x-guest-layout>
    <section class="max-w-md w-full mx-auto">
        <div class="text-center mb-7">
            <div class="badge mb-3 inline-flex">
                <span>Bienvenido de vuelta</span>
            </div>
            <h1 class="font-display text-responsive-2xl mb-2">
                Iniciar sesión
            </h1>
            <p class="text-responsive-sm text-text-muted">
                Accedé a tus entrenamientos y dashboards.
            </p>
        </div>

        <form method="POST" action="{{ route('login') }}"
              class="card p-6 grid gap-4">
            @csrf

            @if ($errors->any())
                <div class="px-3 py-3 bg-accent-primary bg-opacity-10 border border-accent-primary border-opacity-30
                            rounded-btn text-sm text-[#ff6b6b]">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <div>
                <label for="email" class="form-label">Email</label>
                <input
                    id="email"
                    name="email"
                    type="email"
                    required
                    autocomplete="email"
                    value="{{ old('email') }}"
                    class="form-input"
                >
            </div>

            <div>
                <label for="password" class="form-label">Password</label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    required
                    autocomplete="current-password"
                    class="form-input"
                >
            </div>

            <div class="flex flex-col xs:flex-row justify-between items-start xs:items-center gap-2 text-sm text-text-muted">
                <label class="flex items-center gap-2 cursor-pointer min-h-touch">
                    <input type="checkbox" name="remember" class="accent-accent-primary">
                    <span>Recordarme</span>
                </label>
                <a href="#" class="text-accent-secondary hover:text-accent-secondary/80 transition-colors min-h-touch flex items-center">
                    ¿Olvidaste tu contraseña?
                </a>
            </div>

            <button type="submit" class="btn-primary w-full justify-center">
                Entrar
            </button>
        </form>

        <p class="mt-4 text-responsive-xs text-text-muted text-center">
            ¿No tenés cuenta?
            <a href="{{ route('register') }}" class="text-accent-primary hover:text-accent-primary/80 transition-colors">
                Crear cuenta
            </a>
        </p>
    </section>
</x-guest-layout>
