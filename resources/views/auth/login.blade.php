<x-guest-layout>
    <section style="max-width:420px;width:100%;margin:0 auto;">
        <div style="text-align:center;margin-bottom:1.75rem;">
            <div class="badge" style="margin-bottom:.75rem;display:inline-flex;">
                <span>Bienvenido de vuelta</span>
            </div>
            <h1 style="font-family:'Space Grotesk',system-ui,sans-serif;font-size:1.7rem;margin-bottom:.4rem;">
                Iniciar sesión
            </h1>
            <p style="font-size:.9rem;color:var(--text-muted);">
                Accedé a tus entrenamientos y dashboards.
            </p>
        </div>

        <form method="POST" action="{{ route('login') }}" style="
            background:rgba(15,23,42,.9);
            border-radius:1rem;
            padding:1.5rem;
            border:1px solid var(--border-subtle);
            display:grid;
            gap:1rem;
        ">
            @csrf

            @if ($errors->any())
                <div style="padding:.75rem;background:rgba(255,59,92,.1);border:1px solid rgba(255,59,92,.3);border-radius:.6rem;font-size:.85rem;color:#ff6b6b;">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <div>
                <label for="email" style="display:block;font-size:.8rem;margin-bottom:.25rem;">Email</label>
                <input
                    id="email"
                    name="email"
                    type="email"
                    required
                    autocomplete="email"
                    value="{{ old('email') }}"
                    style="
                        width:100%;
                        padding:.6rem .75rem;
                        border-radius:.6rem;
                        border:1px solid #1F2937;
                        background:#050814;
                        color:var(--text-main);
                        font-size:.9rem;
                    "
                >
            </div>

            <div>
                <label for="password" style="display:block;font-size:.8rem;margin-bottom:.25rem;">Password</label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    required
                    autocomplete="current-password"
                    style="
                        width:100%;
                        padding:.6rem .75rem;
                        border-radius:.6rem;
                        border:1px solid #1F2937;
                        background:#050814;
                        color:var(--text-main);
                        font-size:.9rem;
                    "
                >
            </div>

            <div style="display:flex;justify-content:space-between;align-items:center;font-size:.8rem;color:var(--text-muted);">
                <label style="display:flex;align-items:center;gap:.35rem;cursor:pointer;">
                    <input type="checkbox" name="remember" style="accent-color:var(--accent-primary);">
                    <span>Recordarme</span>
                </label>
                <a href="#" style="color:var(--accent-secondary);">¿Olvidaste tu contraseña?</a>
            </div>

            <button type="submit" class="btn-primary" style="width:100%;justify-content:center;">
                Entrar
            </button>
        </form>

        <p style="margin-top:1rem;font-size:.85rem;color:var(--text-muted);text-align:center;">
            ¿No tenés cuenta?
            <a href="{{ route('register') }}" style="color:var(--accent-primary);">Crear cuenta</a>
        </p>
    </section>
</x-guest-layout>
