<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Models\SeatgeekSyncLog;
use App\Models\Venue;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class EventsSyncCommand extends Command
{
    protected $signature = 'events:sync';
    protected $description = 'Sync events from SeatGeek API for Atlanta.';

    public function handle(): int
    {
        if (!setting('seatgeek.sync_enabled', false)) {
            Log::info('events:sync: SeatGeek sync is disabled. Skipping.');
            $this->info('SeatGeek sync is disabled. Set seatgeek.sync_enabled to true to enable.');
            return Command::SUCCESS;
        }

        $clientId = setting('seatgeek.client_id', '');

        if (empty($clientId)) {
            Log::warning('events:sync: seatgeek.client_id is not set.');
            $this->error('SeatGeek client_id is not configured.');
            return Command::FAILURE;
        }

        $startTime = microtime(true);
        $synced = 0;
        $expired = 0;

        try {
            $page = 1;
            $perPage = 100;

            do {
                $response = Http::get('https://api.seatgeek.com/2/events', [
                    'client_id' => $clientId,
                    'venue.city' => 'Atlanta',
                    'venue.state' => 'GA',
                    'per_page' => $perPage,
                    'page' => $page,
                    'sort' => 'datetime_local.asc',
                ]);

                if (!$response->successful()) {
                    throw new \RuntimeException('SeatGeek API returned non-200: ' . $response->status());
                }

                $data = $response->json();
                $events = $data['events'] ?? [];

                foreach ($events as $sgEvent) {
                    $this->upsertEvent($sgEvent);
                    $synced++;
                }

                $total = $data['meta']['total'] ?? 0;
                $page++;
            } while (count($events) === $perPage && ($page - 1) * $perPage < $total);

            // Expire old events
            $expireDays = (int) setting('seatgeek.event_expire_days', 1);
            $expired = Event::where('event_date', '<', Carbon::now()->subDays($expireDays))
                ->where('is_active', true)
                ->update(['is_active' => false]);

            $durationMs = (int) ((microtime(true) - $startTime) * 1000);

            SeatgeekSyncLog::create([
                'run_at' => now(),
                'events_synced' => $synced,
                'events_expired' => $expired,
                'status' => 'success',
                'duration_ms' => $durationMs,
                'created_at' => now(),
            ]);

            $this->info("Synced {$synced} events, expired {$expired} events in {$durationMs}ms.");
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $durationMs = (int) ((microtime(true) - $startTime) * 1000);

            SeatgeekSyncLog::create([
                'run_at' => now(),
                'events_synced' => $synced,
                'events_expired' => $expired,
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'duration_ms' => $durationMs,
                'created_at' => now(),
            ]);

            Log::error('events:sync failed: ' . $e->getMessage());
            $this->error('Sync failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    private function upsertEvent(array $sgEvent): void
    {
        $venueName = $sgEvent['venue']['name'] ?? null;
        $venue = null;

        if ($venueName) {
            $venue = Venue::where('name', 'LIKE', '%' . $venueName . '%')->first();
        }

        if (!$venue) {
            $venue = Venue::where('slug', 'other')->first();
        }

        if (!$venue) {
            return;
        }

        $performers = $sgEvent['performers'] ?? [];
        $homeTeam = isset($performers[0]) ? ($performers[0]['name'] ?? null) : null;
        $awayTeam = isset($performers[1]) ? ($performers[1]['name'] ?? null) : null;

        $category = $this->mapCategory($sgEvent['type'] ?? '');
        $eventDate = substr($sgEvent['datetime_local'] ?? '', 0, 10);
        $eventTime = strlen($sgEvent['datetime_local'] ?? '') >= 16
            ? substr($sgEvent['datetime_local'], 11, 5)
            : null;

        $name = $sgEvent['title'] ?? 'Unknown Event';
        $slug = Str::slug($name . '-' . $eventDate);

        // Ensure unique slug
        $existingBySlug = Event::where('slug', $slug)
            ->where('seatgeek_event_id', '!=', (string) $sgEvent['id'])
            ->exists();

        if ($existingBySlug) {
            $slug = $slug . '-' . $sgEvent['id'];
        }

        Event::updateOrCreate(
            ['seatgeek_event_id' => (string) $sgEvent['id']],
            [
                'venue_id' => $venue->id,
                'name' => $name,
                'slug' => $slug,
                'event_date' => $eventDate,
                'event_time' => $eventTime,
                'category' => $category,
                'home_team' => $homeTeam,
                'away_team' => $awayTeam,
                'image_url' => $sgEvent['performers'][0]['image'] ?? null,
                'is_active' => true,
            ]
        );
    }

    private function mapCategory(string $type): string
    {
        return match (strtolower($type)) {
            'mlb', 'nba', 'nfl', 'nhl', 'mls', 'ncaa_football', 'ncaa_basketball', 'sports' => 'sports',
            'concert', 'music_festival' => 'concert',
            'theater', 'theatre', 'broadway' => 'theatre',
            default => 'other',
        };
    }
}
