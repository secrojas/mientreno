<x-app-layout>
    <header style="display:flex;justify-content:space-between;align-items:center;gap:1rem;margin-bottom:1.5rem;">
        <div style="display:flex;flex-direction:column;gap:.2rem;">
            <h1 style="font-family:'Space Grotesk',system-ui,sans-serif;font-size:1.6rem;">Dashboard</h1>
            <p style="font-size:.9rem;color:var(--text-muted);">Resumen de tus entrenamientos y actividad reciente.</p>
        </div>
        <div style="display:flex;align-items:center;gap:.5rem;">
            <button class="btn-ghost" style="padding:.3rem .45rem;border-radius:999px;border:1px solid var(--border-subtle);background:rgba(15,23,42,.9);" title="Notificaciones">
                <!-- Bell icon -->
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px;">
                    <path d="M18 8a6 6 0 1 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"></path>
                    <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                </svg>
            </button>
            <button class="btn-secondary" style="border-radius:999px;padding:.45rem .9rem;font-size:.8rem;font-weight:500;border:1px solid var(--accent-secondary);cursor:pointer;display:inline-flex;align-items:center;justify-content:center;gap:.4rem;transition:all .18s ease-out;background:transparent;color:var(--text-main);">
                <!-- Plus icon -->
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px;">
                    <path d="M12 5v14"></path>
                    <path d="M5 12h14"></path>
                </svg>
                Nuevo entreno
            </button>
            <button class="btn-primary" style="border-radius:999px;padding:.45rem .9rem;font-size:.8rem;font-weight:500;border:1px solid transparent;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;gap:.4rem;transition:all .18s ease-out;background:linear-gradient(135deg,var(--accent-primary),#FF4FA3);color:#0B0C12;box-shadow:0 8px 24px rgba(255,59,92,.35);">
                <!-- Lightning icon -->
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px;">
                    <path d="M13 2L4 14h7l-1 8 9-12h-7z"></path>
                </svg>
                Generar semana
            </button>
        </div>
    </header>

    <!-- METRIC CARDS -->
    <section style="display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:1rem;margin-bottom:1.5rem;">
        <div style="padding:1rem;border-radius:.9rem;background:rgba(15,23,42,.95);border:1px solid var(--border-subtle);">
            <div style="font-size:.75rem;color:var(--text-muted);margin-bottom:.25rem;">Km esta semana</div>
            <div style="font-size:1.4rem;font-weight:600;">00.0</div>
            <div style="font-size:.78rem;color:var(--accent-secondary);margin-top:.2rem;">Objetivo: 40 km</div>
        </div>
        <div style="padding:1rem;border-radius:.9rem;background:rgba(15,23,42,.95);border:1px solid var(--border-subtle);">
            <div style="font-size:.75rem;color:var(--text-muted);margin-bottom:.25rem;">Tiempo total</div>
            <div style="font-size:1.4rem;font-weight:600;">00:00:00</div>
            <div style="font-size:.78rem;color:var(--accent-secondary);margin-top:.2rem;">5 sesiones planificadas</div>
        </div>
        <div style="padding:1rem;border-radius:.9rem;background:rgba(15,23,42,.95);border:1px solid var(--border-subtle);">
            <div style="font-size:.75rem;color:var(--text-muted);margin-bottom:.25rem;">Pace medio</div>
            <div style="font-size:1.4rem;font-weight:600;">–</div>
            <div style="font-size:.78rem;color:var(--accent-secondary);margin-top:.2rem;">Se calcula con tus últimos entrenos</div>
        </div>
        <div style="padding:1rem;border-radius:.9rem;background:rgba(15,23,42,.95);border:1px solid var(--border-subtle);">
            <div style="font-size:.75rem;color:var(--text-muted);margin-bottom:.25rem;">Próxima carrera</div>
            <div style="font-size:1.4rem;font-weight:600;">—</div>
            <div style="font-size:.78rem;color:var(--accent-secondary);margin-top:.2rem;">Agregá una carrera para seguir tu objetivo</div>
        </div>
    </section>

    <!-- CONTENT -->
    <section style="display:grid;grid-template-columns:minmax(0,2fr) minmax(0,1.2fr);gap:1.5rem;">
        <!-- Entrenamientos -->
        <div style="padding:1rem;border-radius:.9rem;background:rgba(15,23,42,.95);border:1px solid var(--border-subtle);">
            <div style="display:flex;justify-content:space-between;align-items:center;gap:.5rem;margin-bottom:.75rem;">
                <div>
                    <div style="font-size:1rem;">Entrenamientos recientes</div>
                    <div style="font-size:.8rem;color:var(--text-muted);">Cuando empieces a registrar, vas a verlos acá.</div>
                </div>
                <a href="#" style="font-size:.8rem;color:var(--accent-secondary);">Ver todos</a>
            </div>

            <div style="font-size:.85rem;color:var(--text-muted);">
                No hay entrenamientos cargados todavía.<br>
                <br>
                <span style="display:inline-flex;align-items:center;gap:.35rem;border-radius:999px;padding:.18rem .6rem;font-size:.7rem;border:1px solid rgba(148,163,184,.5);color:var(--text-muted);">
                    <span style="width:6px;height:6px;border-radius:999px;background:var(--accent-secondary);"></span>
                    Empezá creando tu primer entreno desde <strong style="margin-left:.15rem;">"Nuevo entreno"</strong>.
                </span>
            </div>
        </div>

        <!-- Panel Coach -->
        <div style="padding:1rem;border-radius:.9rem;background:rgba(15,23,42,.95);border:1px solid var(--border-subtle);">
            <div style="display:flex;justify-content:space-between;align-items:center;gap:.5rem;margin-bottom:.75rem;">
                <div>
                    <div style="font-size:1rem;">Panel coach</div>
                    <div style="font-size:.8rem;color:var(--text-muted);">Vista rápida de grupos y alumnos.</div>
                </div>
            </div>

            <ul style="list-style:none;display:grid;gap:.35rem;font-size:.82rem;color:var(--text-muted);">
                <li>· Totalizadores semanales y mensuales por alumno.</li>
                <li>· Estado de planes de entrenamiento (pendiente, en curso, completo).</li>
                <li>· Próximas carreras y objetivos marcados por cada runner.</li>
                <li>· Asistencias a entrenamientos de grupo.</li>
            </ul>
        </div>
    </section>

    <style>
        @media (max-width: 1024px) {
            header {
                flex-direction: column;
                align-items: flex-start;
            }
            header > div:last-child {
                width: 100%;
                justify-content: flex-start;
                flex-wrap: wrap;
            }
            section:first-of-type {
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            }
            section:last-of-type {
                grid-template-columns: minmax(0, 1fr) !important;
            }
        }

        @media (max-width: 600px) {
            section:first-of-type {
                grid-template-columns: minmax(0, 1fr) !important;
            }
        }

        .btn-secondary:hover {
            background: rgba(45, 227, 142, .08);
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 12px 28px rgba(255, 59, 92, .45);
        }

        .btn-ghost:hover {
            background: rgba(15, 23, 42, .85);
            color: var(--text-main);
        }
    </style>
</x-app-layout>
