<x-guest-layout>
    <section style="max-width:520px;width:100%;margin:0 auto;">
        <div style="text-align:center;margin-bottom:1.75rem;">
            <div class="badge" style="margin-bottom:.75rem;display:inline-flex;">
                <span>{{ isset($businessName) ? 'Unirse a ' . $businessName : 'Nuevo en MiEntreno' }}</span>
            </div>
            <h1 style="font-family:'Space Grotesk',system-ui,sans-serif;font-size:1.7rem;margin-bottom:.4rem;">
                Crear cuenta
            </h1>
            <p style="font-size:.9rem;color:var(--text-muted);">
                @if(isset($businessName))
                    Te estás registrando en <strong>{{ $businessName }}</strong>
                @else
                    Empezá a registrar tus kilómetros y seguí tus objetivos.
                @endif
            </p>
        </div>

        <form method="POST" action="{{ route('register') }}" style="
            background:rgba(15,23,42,.9);
            border-radius:1rem;
            padding:1.5rem;
            border:1px solid var(--border-subtle);
            display:grid;
            gap:1rem;
        ">
            @csrf

            @if(isset($invitationToken))
                <input type="hidden" name="invitation_token" value="{{ $invitationToken }}">
            @endif

            @if ($errors->any())
                <div style="padding:.75rem;background:rgba(255,59,92,.1);border:1px solid rgba(255,59,92,.3);border-radius:.6rem;font-size:.85rem;color:#ff6b6b;">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:.75rem;">
                <div>
                    <label for="name" style="display:block;font-size:.8rem;margin-bottom:.25rem;">Nombre</label>
                    <input
                        id="name"
                        name="name"
                        type="text"
                        required
                        value="{{ old('name') }}"
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
                    <label for="role" style="display:block;font-size:.8rem;margin-bottom:.25rem;">Tipo de usuario</label>
                    <select
                        id="role"
                        name="role"
                        required
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
                        <option value="runner" {{ old('role') === 'runner' ? 'selected' : '' }}>Runner / Alumno</option>
                        <option value="coach" {{ old('role') === 'coach' ? 'selected' : '' }}>Entrenador</option>
                    </select>
                </div>
            </div>

            <div>
                <label for="email" style="display:block;font-size:.8rem;margin-bottom:.25rem;">Email</label>
                <input
                    id="email"
                    name="email"
                    type="email"
                    required
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

            <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:.75rem;">
                <div>
                    <label for="password" style="display:block;font-size:.8rem;margin-bottom:.25rem;">Password</label>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        required
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
                    <label for="password_confirmation" style="display:block;font-size:.8rem;margin-bottom:.25rem;">Repetir password</label>
                    <input
                        id="password_confirmation"
                        name="password_confirmation"
                        type="password"
                        required
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
            </div>

            <button type="submit" class="btn-primary" style="width:100%;justify-content:center;">
                Crear cuenta
            </button>
        </form>

        <p style="margin-top:1rem;font-size:.85rem;color:var(--text-muted);text-align:center;">
            ¿Ya tenés cuenta?
            <a href="{{ route('login') }}" style="color:var(--accent-primary);">Iniciar sesión</a>
        </p>
    </section>
</x-guest-layout>
