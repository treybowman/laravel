<?php

namespace App\Jobs;

use App\Models\Listing;
use App\Models\SavedSearch;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Mail\Message;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CheckSavedSearchAlerts implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private int $listingId)
    {
    }

    public function handle(): void
    {
        $listing = Listing::with(['event', 'venue', 'user'])->find($this->listingId);

        if (!$listing || $listing->status !== 'active') {
            return;
        }

        $cooldownHours = (int) setting('platform.saved_search_alert_cooldown_hours', 24);
        $cooldownThreshold = Carbon::now()->subHours($cooldownHours);

        $matches = SavedSearch::with('user')
            ->where('is_active', true)
            ->where(function ($q) use ($listing) {
                $q->whereNull('venue_id')->orWhere('venue_id', $listing->venue_id);
            })
            ->where(function ($q) use ($listing) {
                $q->whereNull('event_id')->orWhere('event_id', $listing->event_id);
            })
            ->where(function ($q) use ($listing) {
                $q->whereNull('team')
                    ->orWhere('team', $listing->event->home_team ?? '')
                    ->orWhere('team', $listing->event->away_team ?? '');
            })
            ->where(function ($q) use ($listing) {
                $q->whereNull('max_price')->orWhere('max_price', '>=', $listing->asking_price);
            })
            ->where(function ($q) use ($listing) {
                $q->whereNull('min_quantity')->orWhere('min_quantity', '<=', $listing->quantity);
            })
            ->where(function ($q) use ($listing) {
                $q->whereNull('transfer_method')->orWhere('transfer_method', $listing->transfer_method);
            })
            ->where(function ($q) use ($cooldownThreshold) {
                $q->whereNull('last_alerted_at')->orWhere('last_alerted_at', '<', $cooldownThreshold);
            })
            ->get();

        foreach ($matches as $savedSearch) {
            // Don't alert the listing owner
            if ($savedSearch->user_id === $listing->user_id) {
                continue;
            }

            try {
                Mail::send([], [], function (Message $message) use ($savedSearch, $listing) {
                    $user = $savedSearch->user;
                    $siteUrl = setting('platform.site_url', 'https://atlticket.exchange');
                    $listingUrl = $siteUrl . '/listings/' . $listing->id;

                    $message->to($user->email, $user->name)
                        ->subject('New listing matching your saved search: ' . $listing->title)
                        ->html(
                            '<p>Hi ' . e($user->name) . ',</p>' .
                            '<p>A new listing has been posted that matches your saved search: <strong>' . e($savedSearch->label ?? 'Saved Search') . '</strong></p>' .
                            '<p><strong>' . e($listing->title) . '</strong></p>' .
                            '<p>Asking Price: $' . number_format($listing->asking_price, 2) . '</p>' .
                            '<p>Quantity: ' . $listing->quantity . '</p>' .
                            '<p><a href="' . $listingUrl . '">View Listing</a></p>'
                        );
                });

                $savedSearch->update(['last_alerted_at' => now()]);
            } catch (\Exception $e) {
                Log::error('CheckSavedSearchAlerts: Failed to send alert email.', [
                    'saved_search_id' => $savedSearch->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}
