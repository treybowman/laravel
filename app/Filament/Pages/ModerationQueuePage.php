<?php

namespace App\Filament\Pages;

use App\Models\AdminAction;
use App\Models\Listing;
use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class ModerationQueuePage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-queue-list';
    protected static string $view = 'filament.pages.moderation-queue-page';
    protected static ?string $navigationLabel = 'Moderation Queue';
    protected static ?string $navigationGroup = 'System';
    protected static ?int $navigationSort = 2;
    protected static ?string $slug = 'moderation-queue';

    public function getPendingListings()
    {
        return Listing::with(['user', 'event', 'venue'])
            ->where('status', 'pending_approval')
            ->orderBy('created_at')
            ->get();
    }

    public function getFlaggedUsers()
    {
        return User::where('is_banned', true)
            ->orWhereHas('disputes', fn ($q) => $q->where('status', 'open'))
            ->orderByDesc('created_at')
            ->take(20)
            ->get();
    }

    public function approveListing(int $listingId): void
    {
        $listing = Listing::findOrFail($listingId);
        $listing->update(['status' => 'active']);
        $listing->user->increment('listings_approved_count');

        $threshold = (int) setting('platform.probation_threshold', 3);
        if ($listing->user->listings_approved_count >= $threshold) {
            $listing->user->update(['probation' => false]);
        }

        AdminAction::create([
            'admin_id' => auth()->id(),
            'action_type' => 'approve_listing',
            'target_type' => 'listing',
            'target_id' => $listingId,
            'notes' => 'Approved from moderation queue',
            'ip_address' => request()->ip(),
            'created_at' => now(),
        ]);

        Notification::make()->title('Listing approved')->success()->send();
    }

    public function rejectListing(int $listingId): void
    {
        $listing = Listing::findOrFail($listingId);
        $listing->update(['status' => 'expired']);

        AdminAction::create([
            'admin_id' => auth()->id(),
            'action_type' => 'reject_listing',
            'target_type' => 'listing',
            'target_id' => $listingId,
            'notes' => 'Rejected from moderation queue',
            'ip_address' => request()->ip(),
            'created_at' => now(),
        ]);

        Notification::make()->title('Listing rejected')->warning()->send();
    }
}
