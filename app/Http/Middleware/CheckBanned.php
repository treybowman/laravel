<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\Response;

class CheckBanned
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->is_banned) {
            $permanent = $user->ban_expires_at === null;
            $active = $permanent || $user->ban_expires_at > Carbon::now();

            if ($active) {
                return redirect('/banned')->with('ban_message', $user->banned_reason ?? 'Your account has been suspended.');
            }
        }

        return $next($request);
    }
}
