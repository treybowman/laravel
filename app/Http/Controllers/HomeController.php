<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Event;
use App\Models\Listing;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $featuredListings = Listing::with(['event', 'venue', 'user'])
            ->where('status', 'active')
            ->where('is_featured', true)
            ->where('featured_until', '>', now())
            ->orderByDesc('featured_until')
            ->take(6)
            ->get();

        $upcomingEvents = Event::with('venue')
            ->where('is_active', true)
            ->where('event_date', '>=', now()->toDateString())
            ->orderBy('event_date')
            ->take(8)
            ->get();

        $recentListings = Listing::with(['event', 'venue', 'user'])
            ->where('status', 'active')
            ->orderByDesc('created_at')
            ->take(8)
            ->get();

        $announcements = Announcement::where('is_pinned', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->orderByDesc('published_at')
            ->take(3)
            ->get();

        return view('home.index', compact(
            'featuredListings',
            'upcomingEvents',
            'recentListings',
            'announcements'
        ));
    }
}
