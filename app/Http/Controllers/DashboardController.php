<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function listings(Request $request): View
    {
        $user = $request->user();
        $tab = $request->get('tab', 'active');

        $statuses = ['active', 'pending_approval', 'sold', 'expired'];
        if (!in_array($tab, $statuses)) {
            $tab = 'active';
        }

        $listings = $user->listings()
            ->with(['event', 'venue'])
            ->where('status', $tab)
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        $counts = [];
        foreach ($statuses as $status) {
            $counts[$status] = $user->listings()->where('status', $status)->count();
        }

        return view('dashboard.listings', compact('listings', 'tab', 'counts'));
    }
}
