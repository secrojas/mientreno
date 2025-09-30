<?php

namespace App\Http\Middleware;

use App\Models\Business;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SetCurrentBusiness
{
    public function handle(Request $request, Closure $next)
    {
        $business = $request->route('business'); // {business}
        if (!$business instanceof Business) {
            throw new NotFoundHttpException('Activity not found');
        }
        app()->instance('currentBusiness', $business);
        view()->share('currentBusiness', $business);
        return $next($request);
    }
}
