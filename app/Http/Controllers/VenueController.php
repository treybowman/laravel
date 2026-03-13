<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Venue;
use Illuminate\View\View;

class VenueController extends Controller
{
    public function index(): View
    {
        $venues = Venue::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('venues.index', compact('venues'));
    }

    public function show(Venue $venue): View
    {
        $events = Event::where('venue_id', $venue->id)
            ->where('is_active', true)
            ->where('event_date', '>=', now()->toDateString())
            ->orderBy('event_date')
            ->paginate(12);

        return view('venues.show', compact('venue', 'events'));
    }
}
