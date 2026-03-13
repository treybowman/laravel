<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Listing;
use App\Models\Venue;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function index(Request $request): View
    {
        $query = trim($request->get('q', ''));
        $results = collect();

        if (strlen($query) >= 2) {
            $listings = Listing::with(['event', 'venue', 'user'])
                ->where('status', 'active')
                ->where(function ($q) use ($query) {
                    $q->where('title', 'LIKE', "%{$query}%")
                      ->orWhere('notes', 'LIKE', "%{$query}%")
                      ->orWhere('section', 'LIKE', "%{$query}%");
                })
                ->orderByDesc('is_featured')
                ->take(20)
                ->get()
                ->map(fn($l) => ['type' => 'listing', 'item' => $l]);

            $events = Event::with('venue')
                ->where('is_active', true)
                ->where(function ($q) use ($query) {
                    $q->where('name', 'LIKE', "%{$query}%")
                      ->orWhere('home_team', 'LIKE', "%{$query}%")
                      ->orWhere('away_team', 'LIKE', "%{$query}%");
                })
                ->take(10)
                ->get()
                ->map(fn($e) => ['type' => 'event', 'item' => $e]);

            $venues = Venue::where('is_active', true)
                ->where('name', 'LIKE', "%{$query}%")
                ->take(5)
                ->get()
                ->map(fn($v) => ['type' => 'venue', 'item' => $v]);

            $results = $listings->concat($events)->concat($venues);
        }

        return view('search.index', compact('query', 'results'));
    }
}
