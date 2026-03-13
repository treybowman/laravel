<?php

namespace App\Jobs;

use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class EventExpireJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $expireDays = (int) setting('seatgeek.event_expire_days', 1);

        $count = Event::where('event_date', '<', Carbon::now()->subDays($expireDays))
            ->where('is_active', true)
            ->update(['is_active' => false]);

        Log::info("EventExpireJob: Expired {$count} events.");
    }
}
