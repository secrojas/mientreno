<?php

if (!function_exists('businessRoute')) {
    /**
     * Generate a route URL with business context.
     *
     * @param string $name
     * @param array $parameters
     * @param bool $absolute
     * @return string
     */
    function businessRoute(string $name, array $parameters = [], bool $absolute = true): string
    {
        $user = auth()->user();

        // Si el usuario tiene business, usar ruta multi-tenant (business.*)
        if ($user && $user->business_id && $user->business) {
            // Si la ruta ya tiene el prefijo 'business.', no agregarlo de nuevo
            if (!str_starts_with($name, 'business.')) {
                $name = 'business.' . $name;
            }

            $parameters = array_merge(['business' => $user->business->slug], $parameters);
        }

        return route($name, $parameters, $absolute);
    }
}

if (!function_exists('currentBusiness')) {
    /**
     * Get the current business from context.
     *
     * @return \App\Models\Business|null
     */
    function currentBusiness()
    {
        if (auth()->check() && auth()->user()->business_id) {
            return auth()->user()->business;
        }

        return null;
    }
}

if (!function_exists('isCoach')) {
    /**
     * Check if current user is a coach or admin.
     *
     * @return bool
     */
    function isCoach(): bool
    {
        if (!auth()->check()) {
            return false;
        }

        return in_array(auth()->user()->role, ['coach', 'admin']);
    }
}

if (!function_exists('subscriptionLimitMessage')) {
    /**
     * Generate a subscription limit message.
     *
     * @param string $resource 'students' or 'groups'
     * @param \App\Models\Business $business
     * @return string
     */
    function subscriptionLimitMessage(string $resource, \App\Models\Business $business): string
    {
        $currentPlan = $business->getCurrentPlan();
        $planName = $currentPlan ? $currentPlan->name : 'free';

        if ($resource === 'students') {
            $limit = $currentPlan ? $currentPlan->getStudentLimit() : 5;
            $resourceLabel = 'estudiantes';
        } else {
            $limit = $currentPlan ? $currentPlan->getGroupLimit() : 2;
            $resourceLabel = 'grupos';
        }

        return "Has alcanzado el límite de {$resourceLabel} de tu plan {$planName} ({$limit} {$resourceLabel}). " .
               "Actualiza tu plan para poder agregar más {$resourceLabel}.";
    }
}
