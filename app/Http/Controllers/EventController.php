<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Listing;
use App\Models\Venue;
use Illuminate\View\View;

class EventController extends Controller
{
    public function show(Venue $venue, Event $event): View
    {
        $listings = Listing::with(['user'])
            ->where('event_id', $event->id)
            ->where('status', 'active')
            ->orderByDesc('is_featured')
            ->orderBy('asking_price')
            ->paginate(20);

        $event->load('venue');

        return view('events.show', compact('venue', 'event', 'listings'));
    }
}
