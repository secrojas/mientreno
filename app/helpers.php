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

        // Si el usuario tiene business, usar ruta con prefijo
        if ($user && $user->business_id && $user->business) {
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
