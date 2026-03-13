<?php

namespace App\Jobs;

use App\Models\FeaturedQueue;
use App\Models\Listing;
use App\Services\CreditService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ListingExpireJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(CreditService $creditService): void
    {
        $now = Carbon::now();

        // Expire listings past their expires_at
        $expiredCount = Listing::where('expires_at', '<', $now)
            ->where('status', 'active')
            ->update(['status' => 'expired']);

        Log::info("ListingExpireJob: Expired {$expiredCount} listings.");

        // Clear featured flag where featured_until is past
        $unfeaturedListings = Listing::where('featured_until', '<', $now)
            ->where('is_featured', true)
            ->get();

        foreach ($unfeaturedListings as $listing) {
            $listing->update([
                'is_featured' => false,
                'featured_until' => null,
            ]);

            // Activate next pending featured_queue item for this event
            $nextQueued = FeaturedQueue::where('event_id', $listing->event_id)
                ->where('status', 'pending')
                ->orderBy('created_at', 'asc')
                ->first();

            if ($nextQueued) {
                $durationHours = $nextQueued->duration_hours;
                $nextQueued->update([
                    'status' => 'active',
                    'activates_at' => $now,
                    'expires_at' => $now->copy()->addHours($durationHours),
                ]);

                $nextQueued->listing->update([
                    'is_featured' => true,
                    'featured_until' => $now->copy()->addHours($durationHours),
                ]);
            }
        }

        // Expire active featured_queue items that have passed their expires_at
        $expiredQueued = FeaturedQueue::where('status', 'active')
            ->where('expires_at', '<', $now)
            ->get();

        foreach ($expiredQueued as $queueItem) {
            $queueItem->update(['status' => 'expired']);

            $listing = $queueItem->listing;

            if ($listing && in_array($listing->status, ['sold', 'expired'])) {
                // Refund credits if listing sold or expired before activation was used
                $creditService->refundCredits(
                    $queueItem->user,
                    $queueItem->credits_spent,
                    FeaturedQueue::class,
                    $queueItem->id
                );
            }
        }

        // Cancel pending featured_queue items for sold/expired listings and refund credits
        $cancelledQueued = FeaturedQueue::where('status', 'pending')
            ->whereHas('listing', function ($q) {
                $q->whereIn('status', ['sold', 'expired']);
            })
            ->get();

        foreach ($cancelledQueued as $queueItem) {
            $queueItem->update(['status' => 'cancelled']);

            $creditService->refundCredits(
                $queueItem->user,
                $queueItem->credits_spent,
                FeaturedQueue::class,
                $queueItem->id
            );
        }
    }
}
