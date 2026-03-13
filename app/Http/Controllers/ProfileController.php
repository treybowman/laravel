<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(string $username): View
    {
        $user = User::where('username', $username)->firstOrFail();

        $listings = $user->listings()
            ->with(['event', 'venue'])
            ->where('status', 'active')
            ->orderByDesc('created_at')
            ->paginate(12);

        $feedbacks = $user->feedbacksReceived()
            ->with(['reviewer', 'listing.event'])
            ->where('is_visible', true)
            ->orderByDesc('created_at')
            ->take(20)
            ->get();

        return view('profile.show', compact('user', 'listings', 'feedbacks'));
    }
}
