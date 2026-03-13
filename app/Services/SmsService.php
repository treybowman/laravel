<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    public function send(string $to, string $message): bool
    {
        if (!setting('sms.active', false)) {
            Log::info('SmsService: SMS is disabled. Skipping message.', ['to' => $to]);
            return false;
        }

        $sid = setting('sms.twilio_sid', '');
        $authToken = setting('sms.twilio_auth_token', '');
        $from = setting('sms.twilio_from_number', '');

        if (empty($sid) || empty($authToken) || empty($from)) {
            Log::warning('SmsService: Twilio credentials are not configured.');
            return false;
        }

        try {
            $response = Http::withBasicAuth($sid, $authToken)
                ->asForm()
                ->post("https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json", [
                    'To' => $to,
                    'From' => $from,
                    'Body' => $message,
                ]);

            if ($response->successful()) {
                return true;
            }

            Log::error('SmsService: Twilio API error.', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return false;
        } catch (\Exception $e) {
            Log::error('SmsService: Exception while sending SMS.', ['error' => $e->getMessage()]);
            return false;
        }
    }

    public function sendToUser(User $user, string $message): bool
    {
        if (!$user->sms_alerts_enabled || !$user->phone_verified || empty($user->phone_number)) {
            return false;
        }

        return $this->send($user->phone_number, $message);
    }
}
