<x-app-layout>
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
        <div>
            <h1 style="font-family:'Space Grotesk',system-ui,sans-serif;font-size:1.6rem;margin-bottom:.3rem;">
                Mi Suscripción
            </h1>
            <p style="font-size:.9rem;color:var(--text-muted);">
                Gestiona tu plan y límites del negocio
            </p>
        </div>
        <a href="{{ businessRoute('coach.subscriptions.plans') }}" class="btn-primary" style="display:inline-flex;align-items:center;gap:.4rem;padding:.6rem 1.2rem;background:linear-gradient(135deg,var(--accent-primary),#FF4FA3);color:#0B0C12;border-radius:.6rem;font-weight:500;font-size:.9rem;border:none;cursor:pointer;text-decoration:none;transition:all .18s ease-out;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px;">
                <path d="M12 5v14M5 12h14"/>
            </svg>
            Ver Planes
        </a>
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

    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:1.5rem;">
        {{-- Plan Actual --}}
        <div style="background:rgba(15,23,42,.95);border-radius:1rem;border:1px solid rgba(31,41,55,.7);padding:1.5rem;">
            <h2 style="font-size:1.1rem;font-weight:600;margin-bottom:1rem;display:flex;align-items:center;gap:.5rem;">
                <svg style="width:20px;height:20px;color:var(--accent-primary);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
                Plan Actual
            </h2>

            @if($currentPlan)
                <div style="margin-bottom:1rem;">
                    <div style="font-size:1.8rem;font-weight:700;margin-bottom:.3rem;text-transform:capitalize;">
                        {{ $currentPlan->name }}
                    </div>

                    @if($subscription)
                        <div style="display:inline-block;padding:.25rem .7rem;border-radius:999px;font-size:.75rem;font-weight:600;margin-bottom:.5rem;
                            {{ $subscription->isActive() ? 'background:rgba(45,227,142,.1);color:#2DE38E;' : '' }}
                            {{ $subscription->isTrial() ? 'background:rgba(96,165,250,.1);color:#60A5FA;' : '' }}
                            {{ $subscription->isCancelled() ? 'background:rgba(255,59,92,.1);color:#FF3B5C;' : '' }}
                        ">
                            {{ $subscription->isActive() ? 'Activa' : ($subscription->isTrial() ? 'Trial' : 'Cancelada') }}
                        </div>

                        @if($subscription->isValid())
                            <div style="font-size:.85rem;color:var(--text-muted);margin-top:.5rem;">
                                Vence: {{ $subscription->current_period_end->format('d/m/Y') }}
                                <span style="color:var(--accent-secondary);">({{ $subscription->daysRemaining() }} días restantes)</span>
                            </div>

                            @if($subscription->isNearExpiration())
                                <div style="margin-top:.8rem;padding:.6rem .8rem;background:rgba(255,165,0,.1);border:1px solid rgba(255,165,0,.3);border-radius:.5rem;font-size:.8rem;color:orange;">
                                    ⚠️ Tu suscripción vence pronto. Renueva para mantener el acceso.
                                </div>
                            @endif
                        @endif
                    @endif

                    @if($currentPlan->description)
                        <p style="color:var(--text-muted);font-size:.85rem;margin-top:.8rem;line-height:1.4;">
                            {{ $currentPlan->description }}
                        </p>
                    @endif
                </div>

                @if(!$currentPlan->isFree())
                    <div style="margin-top:1rem;padding-top:1rem;border-top:1px solid rgba(31,41,55,.5);">
                        <div style="font-size:.85rem;color:var(--text-muted);margin-bottom:.5rem;">Precio:</div>
                        <div style="font-size:1.3rem;font-weight:700;">
                            ${{ number_format($currentPlan->monthly_price, 0) }}<span style="font-size:.85rem;font-weight:400;color:var(--text-muted);">/mes</span>
                        </div>
                    </div>
                @endif
            @else
                <div style="text-align:center;padding:2rem 1rem;">
                    <div style="font-size:1.5rem;font-weight:700;margin-bottom:.5rem;">Plan Free</div>
                    <p style="color:var(--text-muted);font-size:.85rem;margin-bottom:1rem;">Plan gratuito por defecto</p>
                    <a href="{{ businessRoute('coach.subscriptions.plans') }}" style="display:inline-flex;align-items:center;gap:.4rem;padding:.6rem 1.2rem;background:linear-gradient(135deg,var(--accent-primary),#FF4FA3);color:#0B0C12;border-radius:.6rem;font-weight:500;font-size:.9rem;text-decoration:none;">
                        Actualizar Plan
                    </a>
                </div>
            @endif
        </div>

        {{-- Uso de Límites --}}
        <div style="background:rgba(15,23,42,.95);border-radius:1rem;border:1px solid rgba(31,41,55,.7);padding:1.5rem;">
            <h2 style="font-size:1.1rem;font-weight:600;margin-bottom:1rem;display:flex;align-items:center;gap:.5rem;">
                <svg style="width:20px;height:20px;color:var(--accent-primary);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                Uso de Recursos
            </h2>

            {{-- Estudiantes --}}
            <div style="margin-bottom:1.5rem;">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.5rem;">
                    <span style="font-size:.9rem;font-weight:500;">Estudiantes</span>
                    <span style="font-size:.85rem;color:var(--text-muted);">
                        {{ $studentsCount }} / {{ $studentLimit ? $studentLimit : '∞' }}
                    </span>
                </div>

                @if($studentLimit)
                    @php
                        $studentPercentage = $studentLimit > 0 ? min(($studentsCount / $studentLimit) * 100, 100) : 0;
                        $isNearLimit = $studentPercentage >= 80;
                    @endphp
                    <div style="width:100%;height:8px;background:rgba(31,41,55,.5);border-radius:999px;overflow:hidden;">
                        <div style="width:{{ $studentPercentage }}%;height:100%;background:{{ $isNearLimit ? 'linear-gradient(90deg, orange, #FF3B5C)' : 'linear-gradient(90deg, var(--accent-primary), var(--accent-secondary))' }};transition:width .3s ease-out;"></div>
                    </div>
                    @if($isNearLimit)
                        <div style="margin-top:.5rem;font-size:.75rem;color:orange;">
                            ⚠️ Cerca del límite
                        </div>
                    @endif
                @else
                    <div style="font-size:.8rem;color:var(--accent-secondary);">✓ Ilimitado</div>
                @endif
            </div>

            {{-- Grupos --}}
            <div style="margin-bottom:1.5rem;">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.5rem;">
                    <span style="font-size:.9rem;font-weight:500;">Grupos</span>
                    <span style="font-size:.85rem;color:var(--text-muted);">
                        {{ $groupsCount }} / {{ $groupLimit ? $groupLimit : '∞' }}
                    </span>
                </div>

                @if($groupLimit)
                    @php
                        $groupPercentage = $groupLimit > 0 ? min(($groupsCount / $groupLimit) * 100, 100) : 0;
                        $isNearLimit = $groupPercentage >= 80;
                    @endphp
                    <div style="width:100%;height:8px;background:rgba(31,41,55,.5);border-radius:999px;overflow:hidden;">
                        <div style="width:{{ $groupPercentage }}%;height:100%;background:{{ $isNearLimit ? 'linear-gradient(90deg, orange, #FF3B5C)' : 'linear-gradient(90deg, var(--accent-primary), var(--accent-secondary))' }};transition:width .3s ease-out;"></div>
                    </div>
                    @if($isNearLimit)
                        <div style="margin-top:.5rem;font-size:.75rem;color:orange;">
                            ⚠️ Cerca del límite
                        </div>
                    @endif
                @else
                    <div style="font-size:.8rem;color:var(--accent-secondary);">✓ Ilimitado</div>
                @endif
            </div>

            <div style="margin-top:1.5rem;padding-top:1.5rem;border-top:1px solid rgba(31,41,55,.5);">
                <a href="{{ businessRoute('coach.subscriptions.plans') }}" style="display:block;text-align:center;padding:.6rem;background:rgba(31,41,55,.5);color:var(--text-primary);border-radius:.5rem;font-size:.85rem;font-weight:500;text-decoration:none;transition:all .15s;">
                    Ver Planes con Más Límites →
                </a>
            </div>
        </div>
    </div>

    {{-- Cancelar Suscripción --}}
    @if($subscription && $subscription->isActive())
        <div style="margin-top:2rem;background:rgba(15,23,42,.95);border-radius:1rem;border:1px solid rgba(255,59,92,.3);padding:1.5rem;">
            <h3 style="font-size:1rem;font-weight:600;margin-bottom:.5rem;color:#ff6b6b;">Cancelar Suscripción</h3>
            <p style="font-size:.85rem;color:var(--text-muted);margin-bottom:1rem;">
                Al cancelar, mantendrás acceso a las funciones del plan hasta el final del período actual ({{ $subscription->current_period_end->format('d/m/Y') }}).
            </p>

            <form action="{{ businessRoute('coach.subscriptions.cancel') }}" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas cancelar tu suscripción?');">
                @csrf
                <div style="margin-bottom:1rem;">
                    <label for="reason" style="display:block;font-size:.85rem;font-weight:500;margin-bottom:.4rem;">Motivo (opcional):</label>
                    <textarea name="reason" id="reason" rows="2" style="width:100%;padding:.6rem;background:rgba(5,8,20,.7);border:1px solid rgba(31,41,55,.5);border-radius:.5rem;color:var(--text-primary);font-size:.85rem;font-family:inherit;resize:vertical;" placeholder="Cuéntanos por qué cancelas..."></textarea>
                </div>
                <button type="submit" style="padding:.6rem 1.2rem;background:rgba(255,59,92,.15);color:#ff6b6b;border:1px solid rgba(255,59,92,.3);border-radius:.5rem;font-weight:500;font-size:.85rem;cursor:pointer;transition:all .15s;">
                    Cancelar Suscripción
                </button>
            </form>
        </div>
    @endif
</x-app-layout>
