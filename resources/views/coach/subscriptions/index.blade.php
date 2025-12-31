<x-app-layout>
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="font-display text-responsive-2xl mb-1">
                Mi Suscripción
            </h1>
            <p class="text-responsive-sm text-text-muted">
                Gestiona tu plan y límites del negocio
            </p>
        </div>
        <a href="{{ businessRoute('coach.subscriptions.plans') }}" class="btn-primary min-h-touch w-full sm:w-auto justify-center">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                <path d="M12 5v14M5 12h14"/>
            </svg>
            Ver Planes
        </a>
    </div>

    @if(session('success'))
        <div class="px-4 py-3 bg-accent-secondary/10 border border-accent-secondary/30 rounded-card text-sm text-accent-secondary mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="px-4 py-3 bg-accent-primary/10 border border-accent-primary/30 rounded-card text-sm text-red-400 mb-4">
            {{ session('error') }}
        </div>
    @endif

    @if(session('info'))
        <div class="px-4 py-3 bg-blue-400/10 border border-blue-400/30 rounded-card text-sm text-blue-400 mb-4">
            {{ session('info') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Plan Actual --}}
        <x-card>
            <h2 class="text-lg font-semibold mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-accent-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
                Plan Actual
            </h2>

            @if($currentPlan)
                <div class="mb-4">
                    <div class="text-3xl font-bold mb-1.5 capitalize">
                        {{ $currentPlan->name }}
                    </div>

                    @if($subscription)
                        <div class="inline-block px-3 py-1 rounded-full text-xs font-semibold mb-2
                            {{ $subscription->isActive() ? 'bg-accent-secondary/10 text-accent-secondary' : '' }}
                            {{ $subscription->isTrial() ? 'bg-blue-400/10 text-blue-400' : '' }}
                            {{ $subscription->isCancelled() ? 'bg-accent-primary/10 text-accent-primary' : '' }}
                        ">
                            {{ $subscription->isActive() ? 'Activa' : ($subscription->isTrial() ? 'Trial' : 'Cancelada') }}
                        </div>

                        @if($subscription->isValid())
                            <div class="text-sm text-text-muted mt-2">
                                Vence: {{ $subscription->current_period_end->format('d/m/Y') }}
                                <span class="text-accent-secondary">({{ $subscription->daysRemaining() }} días restantes)</span>
                            </div>

                            @if($subscription->isNearExpiration())
                                <div class="mt-3 px-3 py-2.5 bg-orange-500/10 border border-orange-500/30 rounded-card text-sm text-orange-400">
                                    ⚠️ Tu suscripción vence pronto. Renueva para mantener el acceso.
                                </div>
                            @endif
                        @endif
                    @endif

                    @if($currentPlan->description)
                        <p class="text-text-muted text-sm mt-3 leading-relaxed">
                            {{ $currentPlan->description }}
                        </p>
                    @endif
                </div>

                @if(!$currentPlan->isFree())
                    <div class="mt-4 pt-4 border-t border-border-subtle">
                        <div class="text-sm text-text-muted mb-2">Precio:</div>
                        <div class="text-2xl font-bold">
                            ${{ number_format($currentPlan->monthly_price, 0) }}<span class="text-sm font-normal text-text-muted">/mes</span>
                        </div>
                    </div>
                @endif
            @else
                <div class="text-center py-8 px-4">
                    <div class="text-2xl font-bold mb-2">Plan Free</div>
                    <p class="text-text-muted text-sm mb-4">Plan gratuito por defecto</p>
                    <a href="{{ businessRoute('coach.subscriptions.plans') }}" class="btn-primary inline-flex min-h-touch">
                        Actualizar Plan
                    </a>
                </div>
            @endif
        </x-card>

        {{-- Uso de Límites --}}
        <x-card>
            <h2 class="text-lg font-semibold mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-accent-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                Uso de Recursos
            </h2>

            {{-- Estudiantes --}}
            <div class="mb-6">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-responsive-sm font-medium">Estudiantes</span>
                    <span class="text-sm text-text-muted">
                        {{ $studentsCount }} / {{ $studentLimit ? $studentLimit : '∞' }}
                    </span>
                </div>

                @if($studentLimit)
                    @php
                        $studentPercentage = $studentLimit > 0 ? min(($studentsCount / $studentLimit) * 100, 100) : 0;
                        $isNearLimit = $studentPercentage >= 80;
                    @endphp
                    <div class="w-full h-2 bg-border-subtle rounded-full overflow-hidden">
                        <div class="h-full {{ $isNearLimit ? 'bg-gradient-to-r from-orange-500 to-accent-primary' : 'bg-gradient-to-r from-accent-primary to-accent-secondary' }} transition-all duration-300" style="width: {{ $studentPercentage }}%"></div>
                    </div>
                    @if($isNearLimit)
                        <div class="mt-2 text-xs text-orange-400">
                            ⚠️ Cerca del límite
                        </div>
                    @endif
                @else
                    <div class="text-sm text-accent-secondary">✓ Ilimitado</div>
                @endif
            </div>

            {{-- Grupos --}}
            <div class="mb-6">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-responsive-sm font-medium">Grupos</span>
                    <span class="text-sm text-text-muted">
                        {{ $groupsCount }} / {{ $groupLimit ? $groupLimit : '∞' }}
                    </span>
                </div>

                @if($groupLimit)
                    @php
                        $groupPercentage = $groupLimit > 0 ? min(($groupsCount / $groupLimit) * 100, 100) : 0;
                        $isNearLimit = $groupPercentage >= 80;
                    @endphp
                    <div class="w-full h-2 bg-border-subtle rounded-full overflow-hidden">
                        <div class="h-full {{ $isNearLimit ? 'bg-gradient-to-r from-orange-500 to-accent-primary' : 'bg-gradient-to-r from-accent-primary to-accent-secondary' }} transition-all duration-300" style="width: {{ $groupPercentage }}%"></div>
                    </div>
                    @if($isNearLimit)
                        <div class="mt-2 text-xs text-orange-400">
                            ⚠️ Cerca del límite
                        </div>
                    @endif
                @else
                    <div class="text-sm text-accent-secondary">✓ Ilimitado</div>
                @endif
            </div>

            <div class="mt-6 pt-6 border-t border-border-subtle">
                <a href="{{ businessRoute('coach.subscriptions.plans') }}" class="block text-center px-4 py-2.5 bg-border-subtle/50 hover:bg-border-subtle text-text-main rounded-card text-sm font-medium transition-colors min-h-touch">
                    Ver Planes con Más Límites →
                </a>
            </div>
        </x-card>
    </div>

    {{-- Cancelar Suscripción --}}
    @if($subscription && $subscription->isActive())
        <x-card class="mt-8 border-accent-primary/30">
            <h3 class="text-base font-semibold mb-2 text-red-400">Cancelar Suscripción</h3>
            <p class="text-sm text-text-muted mb-4">
                Al cancelar, mantendrás acceso a las funciones del plan hasta el final del período actual ({{ $subscription->current_period_end->format('d/m/Y') }}).
            </p>

            <form action="{{ businessRoute('coach.subscriptions.cancel') }}" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas cancelar tu suscripción?');">
                @csrf
                <div class="mb-4">
                    <label for="reason" class="form-label">Motivo (opcional):</label>
                    <textarea name="reason" id="reason" rows="2" class="form-input resize-y" placeholder="Cuéntanos por qué cancelas..."></textarea>
                </div>
                <button type="submit" class="px-5 py-2.5 bg-accent-primary/15 text-red-400 border border-accent-primary/30 rounded-card font-medium text-sm hover:bg-accent-primary/25 transition-colors cursor-pointer min-h-touch">
                    Cancelar Suscripción
                </button>
            </form>
        </x-card>
    @endif
</x-app-layout>
