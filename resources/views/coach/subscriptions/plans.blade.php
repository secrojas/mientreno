<x-app-layout>
    <div class="mb-6">
        <div class="flex items-center gap-2 mb-2">
            <a href="{{ businessRoute('coach.subscriptions.index') }}" class="text-text-muted hover:text-text-main transition-colors text-responsive-sm">
                ← Volver a Mi Suscripción
            </a>
        </div>
        <h1 class="font-display text-responsive-2xl mb-1">
            Planes de Suscripción
        </h1>
        <p class="text-responsive-sm text-text-muted">
            Elige el plan que mejor se adapte a tu negocio
        </p>
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

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mt-8">
        @foreach($plans as $plan)
            @php
                $isCurrentPlan = $currentPlanId && $currentPlanId === $plan->id;
                $isFree = $plan->isFree();
            @endphp

            <div class="bg-bg-card rounded-card border-2 {{ $isCurrentPlan ? 'border-accent-primary' : 'border-border-subtle' }} p-6 relative flex flex-col transition-all hover:shadow-lg">

                @if($isCurrentPlan)
                    <div class="absolute -top-3 left-1/2 -translate-x-1/2 px-3 py-1.5 bg-gradient-to-br from-accent-primary to-pink-500 text-bg-main rounded-full text-xs font-bold uppercase tracking-wide">
                        Plan Actual
                    </div>
                @endif

                <div class="text-center mb-6">
                    <h3 class="text-xl font-bold mb-2 capitalize">
                        {{ $plan->name }}
                    </h3>

                    @if($isFree)
                        <div class="text-4xl font-extrabold mb-2">
                            Gratis
                        </div>
                    @else
                        <div class="text-4xl font-extrabold mb-2">
                            ${{ number_format($plan->monthly_price, 0) }}
                            <span class="text-base font-normal text-text-muted">/ mes</span>
                        </div>

                        @if($plan->annual_price > 0)
                            <div class="text-sm text-text-muted">
                                o ${{ number_format($plan->annual_price, 0) }}/año
                                @if($plan->getAnnualDiscount() > 0)
                                    <span class="text-accent-secondary">({{ $plan->getAnnualDiscount() }}% OFF)</span>
                                @endif
                            </div>
                        @endif
                    @endif

                    @if($plan->description)
                        <p class="text-text-muted text-sm mt-3 leading-relaxed">
                            {{ $plan->description }}
                        </p>
                    @endif
                </div>

                <div class="flex-1 mb-6">
                    <div class="text-sm font-semibold text-text-muted mb-3 uppercase tracking-wider">
                        Características:
                    </div>

                    <div class="flex flex-col gap-2.5">
                        <div class="flex items-center gap-2 text-responsive-sm">
                            <svg class="w-5 h-5 text-accent-secondary flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

                        <div class="flex items-center gap-2 text-responsive-sm">
                            <svg class="w-5 h-5 text-accent-secondary flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

                        <div class="flex items-center gap-2 text-responsive-sm">
                            <svg class="w-5 h-5 text-accent-secondary flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                    <button disabled class="w-full px-5 py-3 bg-border-subtle text-text-muted rounded-card font-semibold text-responsive-sm border-none cursor-not-allowed min-h-touch">
                        Plan Actual
                    </button>
                @else
                    <form action="{{ businessRoute('coach.subscriptions.subscribe') }}" method="POST">
                        @csrf
                        <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                        <button type="submit" class="btn-primary w-full min-h-touch justify-center">
                            {{ $currentPlanId ? 'Cambiar a este Plan' : 'Activar Plan' }}
                        </button>
                    </form>
                @endif
            </div>
        @endforeach
    </div>

    @if($plans->isEmpty())
        <div class="bg-bg-card rounded-card border border-border-subtle py-12 px-8 text-center">
            <p class="text-text-muted">No hay planes disponibles en este momento.</p>
        </div>
    @endif
</x-app-layout>
