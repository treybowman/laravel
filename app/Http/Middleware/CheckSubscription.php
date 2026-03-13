<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return $next($request);
        }

        // Check if the route is listing creation
        if ($request->routeIs('listings.create') || $request->routeIs('listings.store')) {
            if (!$user->canCreateListing()) {
                $limit = (int) setting('platform.max_free_listings', 1);
                return redirect()->route('dashboard.listings')
                    ->with('error', "Free tier is limited to {$limit} active listing(s). Upgrade to post more.");
            }
        }

        // Check if the route is saved search creation
        if ($request->routeIs('saved_searches.create') || $request->routeIs('saved_searches.store')) {
            if (!$user->canCreateSavedSearch()) {
                return redirect()->route('dashboard.listings')
                    ->with('error', 'You have reached your saved search limit. Upgrade to create more.');
            }
        }

        return $next($request);
    }
}
