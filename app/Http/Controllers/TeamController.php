<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TeamController extends Controller
{
    public function show(string $team, Request $request): View
    {
        $teamName = str_replace('-', ' ', $team);

        $listings = Listing::with(['event', 'venue', 'user'])
            ->where('status', 'active')
            ->whereHas('event', function ($q) use ($teamName) {
                $q->where('home_team', 'LIKE', '%' . $teamName . '%')
                  ->orWhere('away_team', 'LIKE', '%' . $teamName . '%');
            })
            ->orderByDesc('is_featured')
            ->orderBy('asking_price')
            ->paginate(20);

        return view('teams.show', compact('teamName', 'listings'));
    }
}
