<?php

namespace App\Jobs;

use App\Models\Listing;
use App\Models\SocialPush;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SocialPushJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private int $listingId)
    {
    }

    public function handle(): void
    {
        if (!setting('features.social_push_enabled', false)) {
            Log::info('SocialPushJob: Social push is disabled. Skipping listing ' . $this->listingId);
            return;
        }

        $listing = Listing::with(['event', 'venue'])->find($this->listingId);

        if (!$listing) {
            return;
        }

        $siteUrl = setting('platform.site_url', 'https://atlticket.exchange');
        $listingUrl = $siteUrl . '/listings/' . $listing->id;

        $message = sprintf(
            '🎟 %s | %s @ %s | $%s each | %d available — %s #ATLTickets',
            $listing->title,
            $listing->event->name ?? '',
            $listing->venue->name ?? '',
            number_format($listing->asking_price, 2),
            $listing->quantity,
            $listingUrl
        );

        $this->pushToFacebook($listing, $message);
        $this->pushToTwitter($listing, $message);
    }

    private function pushToFacebook(Listing $listing, string $message): void
    {
        $pageId = setting('social.facebook_page_id', '');
        $accessToken = setting('social.facebook_page_access_token', '');

        if (empty($pageId) || empty($accessToken)) {
            Log::warning('SocialPushJob: Facebook credentials not configured. Skipping Facebook push.');
            return;
        }

        $push = SocialPush::create([
            'listing_id' => $listing->id,
            'platform' => 'facebook',
            'message' => $message,
            'status' => 'pending',
            'created_at' => now(),
        ]);

        try {
            $response = Http::post("https://graph.facebook.com/{$pageId}/feed", [
                'message' => $message,
                'access_token' => $accessToken,
            ]);

            if ($response->successful()) {
                $push->update(['status' => 'sent', 'pushed_at' => now()]);
            } else {
                $push->update([
                    'status' => 'failed',
                    'error_message' => $response->body(),
                ]);
            }
        } catch (\Exception $e) {
            $push->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
            Log::error('SocialPushJob: Facebook push failed.', ['error' => $e->getMessage()]);
        }
    }

    private function pushToTwitter(Listing $listing, string $message): void
    {
        $apiKey = setting('social.twitter_api_key', '');
        $apiSecret = setting('social.twitter_api_secret', '');
        $accessToken = setting('social.twitter_access_token', '');
        $accessTokenSecret = setting('social.twitter_access_token_secret', '');

        if (empty($apiKey) || empty($apiSecret) || empty($accessToken) || empty($accessTokenSecret)) {
            Log::warning('SocialPushJob: Twitter credentials not configured. Skipping Twitter push.');
            return;
        }

        $push = SocialPush::create([
            'listing_id' => $listing->id,
            'platform' => 'twitter',
            'message' => $message,
            'status' => 'pending',
            'created_at' => now(),
        ]);

        try {
            $url = 'https://api.twitter.com/2/tweets';
            $oauth = $this->buildOAuthHeader('POST', $url, $apiKey, $apiSecret, $accessToken, $accessTokenSecret);

            $response = Http::withHeaders(['Authorization' => $oauth])
                ->post($url, ['text' => substr($message, 0, 280)]);

            if ($response->successful()) {
                $push->update(['status' => 'sent', 'pushed_at' => now()]);
            } else {
                $push->update([
                    'status' => 'failed',
                    'error_message' => $response->body(),
                ]);
            }
        } catch (\Exception $e) {
            $push->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
            Log::error('SocialPushJob: Twitter push failed.', ['error' => $e->getMessage()]);
        }
    }

    private function buildOAuthHeader(
        string $method,
        string $url,
        string $apiKey,
        string $apiSecret,
        string $accessToken,
        string $accessTokenSecret
    ): string {
        $nonce = bin2hex(random_bytes(16));
        $timestamp = time();

        $oauthParams = [
            'oauth_consumer_key' => $apiKey,
            'oauth_nonce' => $nonce,
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_timestamp' => $timestamp,
            'oauth_token' => $accessToken,
            'oauth_version' => '1.0',
        ];

        ksort($oauthParams);
        $paramString = http_build_query($oauthParams);
        $baseString = strtoupper($method) . '&' . rawurlencode($url) . '&' . rawurlencode($paramString);
        $signingKey = rawurlencode($apiSecret) . '&' . rawurlencode($accessTokenSecret);
        $signature = base64_encode(hash_hmac('sha1', $baseString, $signingKey, true));

        $oauthParams['oauth_signature'] = $signature;

        $headerParts = array_map(
            fn($k, $v) => rawurlencode($k) . '="' . rawurlencode($v) . '"',
            array_keys($oauthParams),
            array_values($oauthParams)
        );

        return 'OAuth ' . implode(', ', $headerParts);
    }
}
