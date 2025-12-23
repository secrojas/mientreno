<x-app-layout>
    <div style="margin-bottom:1.5rem;">
        <div style="display:flex;align-items:center;gap:.5rem;margin-bottom:.5rem;">
            <a href="{{ businessRoute('coach.subscriptions.index') }}" style="color:var(--text-muted);text-decoration:none;font-size:.9rem;transition:color .15s;">
                ← Volver a Mi Suscripción
            </a>
        </div>
        <h1 style="font-family:'Space Grotesk',system-ui,sans-serif;font-size:1.6rem;margin-bottom:.3rem;">
            Planes de Suscripción
        </h1>
        <p style="font-size:.9rem;color:var(--text-muted);">
            Elige el plan que mejor se adapte a tu negocio
        </p>
    </div>

    @if(session('success'))
        <div style="padding:.75rem 1rem;background:rgba(45,227,142,.1);border:1px solid rgba(45,227,142,.3);border-radius:.6rem;font-size:.85rem;color:var(--accent-secondary);margin-bottom:1rem;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="padding:.75rem 1rem;background:rgba(255,59,92,.1);border:1px solid rgba(255,59,92,.3);border-radius:.6rem;font-size:.85rem;color:#ff6b6b;margin-bottom:1rem;">
            {{ session('error') }}
        </div>
    @endif

    @if(session('info'))
        <div style="padding:.75rem 1rem;background:rgba(96,165,250,.1);border:1px solid rgba(96,165,250,.3);border-radius:.6rem;font-size:.85rem;color:#60A5FA;margin-bottom:1rem;">
            {{ session('info') }}
        </div>
    @endif

    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:1.5rem;margin-top:2rem;">
        @foreach($plans as $plan)
            @php
                $isCurrentPlan = $currentPlanId && $currentPlanId === $plan->id;
                $isFree = $plan->isFree();
            @endphp

            <div style="background:rgba(15,23,42,.95);border-radius:1rem;border:2px solid {{ $isCurrentPlan ? 'var(--accent-primary)' : 'rgba(31,41,55,.7)' }};padding:1.5rem;position:relative;display:flex;flex-direction:column;transition:all .2s ease-out;">

                @if($isCurrentPlan)
                    <div style="position:absolute;top:-12px;left:50%;transform:translateX(-50%);padding:.3rem .8rem;background:linear-gradient(135deg,var(--accent-primary),#FF4FA3);color:#0B0C12;border-radius:999px;font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.02em;">
                        Plan Actual
                    </div>
                @endif

                <div style="text-align:center;margin-bottom:1.5rem;">
                    <h3 style="font-size:1.3rem;font-weight:700;margin-bottom:.5rem;text-transform:capitalize;">
                        {{ $plan->name }}
                    </h3>

                    @if($isFree)
                        <div style="font-size:2.5rem;font-weight:800;margin-bottom:.5rem;">
                            Gratis
                        </div>
                    @else
                        <div style="font-size:2.5rem;font-weight:800;margin-bottom:.5rem;">
                            ${{ number_format($plan->monthly_price, 0) }}
                            <span style="font-size:1rem;font-weight:400;color:var(--text-muted);">/ mes</span>
                        </div>

                        @if($plan->annual_price > 0)
                            <div style="font-size:.85rem;color:var(--text-muted);">
                                o ${{ number_format($plan->annual_price, 0) }}/año
                                @if($plan->getAnnualDiscount() > 0)
                                    <span style="color:var(--accent-secondary);">({{ $plan->getAnnualDiscount() }}% OFF)</span>
                                @endif
                            </div>
                        @endif
                    @endif

                    @if($plan->description)
                        <p style="color:var(--text-muted);font-size:.85rem;margin-top:.8rem;line-height:1.4;">
                            {{ $plan->description }}
                        </p>
                    @endif
                </div>

                <div style="flex:1;margin-bottom:1.5rem;">
                    <div style="font-size:.85rem;font-weight:600;color:var(--text-muted);margin-bottom:.8rem;text-transform:uppercase;letter-spacing:.05em;">
                        Características:
                    </div>

                    <div style="display:flex;flex-direction:column;gap:.6rem;">
                        <div style="display:flex;align-items:center;gap:.5rem;font-size:.9rem;">
                            <svg style="width:18px;height:18px;color:var(--accent-secondary);flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>
                                @if($plan->hasStudentLimit())
                                    Hasta {{ $plan->getStudentLimit() }} estudiantes
                                @else
                                    Estudiantes ilimitados
                                @endif
                            </span>
                        </div>

                        <div style="display:flex;align-items:center;gap:.5rem;font-size:.9rem;">
                            <svg style="width:18px;height:18px;color:var(--accent-secondary);flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>
                                @if($plan->hasGroupLimit())
                                    Hasta {{ $plan->getGroupLimit() }} grupos
                                @else
                                    Grupos ilimitados
                                @endif
                            </span>
                        </div>

                        <div style="display:flex;align-items:center;gap:.5rem;font-size:.9rem;">
                            <svg style="width:18px;height:18px;color:var(--accent-secondary);flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>
                                @if($plan->hasStorageLimit())
                                    {{ $plan->getStorageLimitGb() }} GB almacenamiento
                                @else
                                    Almacenamiento ilimitado
                                @endif
                            </span>
                        </div>
                    </div>
                </div>

                @if($isCurrentPlan)
                    <button disabled style="width:100%;padding:.7rem 1.2rem;background:rgba(31,41,55,.8);color:var(--text-muted);border-radius:.6rem;font-weight:600;font-size:.9rem;border:none;cursor:not-allowed;">
                        Plan Actual
                    </button>
                @else
                    <form action="{{ businessRoute('coach.subscriptions.subscribe') }}" method="POST">
                        @csrf
                        <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                        <button type="submit" style="width:100%;padding:.7rem 1.2rem;background:linear-gradient(135deg,var(--accent-primary),#FF4FA3);color:#0B0C12;border-radius:.6rem;font-weight:600;font-size:.9rem;border:none;cursor:pointer;transition:all .18s ease-out;">
                            {{ $currentPlanId ? 'Cambiar a este Plan' : 'Activar Plan' }}
                        </button>
                    </form>
                @endif
            </div>
        @endforeach
    </div>

    @if($plans->isEmpty())
        <div style="background:rgba(15,23,42,.95);border-radius:1rem;border:1px solid var(--border-subtle);padding:3rem 2rem;text-align:center;">
            <p style="color:var(--text-muted);">No hay planes disponibles en este momento.</p>
        </div>
    @endif
</x-app-layout>
