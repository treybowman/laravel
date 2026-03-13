<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMaintenanceMode
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (setting('platform.maintenance_mode', false) && (!$user || !$user->is_admin)) {
            return response()->view('errors.maintenance', [], 503);
        }

        return $next($request);
    }
}
