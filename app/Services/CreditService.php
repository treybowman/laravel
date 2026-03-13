<?php

namespace App\Services;

use App\Models\CreditTransaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CreditService
{
    public function addCredits(
        User $user,
        int $amount,
        string $type,
        ?string $notes = null,
        ?string $referenceType = null,
        ?int $referenceId = null
    ): void {
        DB::transaction(function () use ($user, $amount, $type, $notes, $referenceType, $referenceId) {
            CreditTransaction::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'type' => $type,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'notes' => $notes,
                'created_at' => now(),
            ]);

            $user->increment('credits_balance', $amount);
        });
    }

    public function deductCredits(
        User $user,
        int $amount,
        string $type,
        ?string $referenceType = null,
        ?int $referenceId = null
    ): bool {
        if ($user->credits_balance < $amount) {
            return false;
        }

        DB::transaction(function () use ($user, $amount, $type, $referenceType, $referenceId) {
            CreditTransaction::create([
                'user_id' => $user->id,
                'amount' => -$amount,
                'type' => $type,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'created_at' => now(),
            ]);

            $user->decrement('credits_balance', $amount);
        });

        return true;
    }

    public function refundCredits(
        User $user,
        int $amount,
        ?string $referenceType = null,
        ?int $referenceId = null
    ): void {
        DB::transaction(function () use ($user, $amount, $referenceType, $referenceId) {
            CreditTransaction::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'type' => 'refund',
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'notes' => 'Credit refund',
                'created_at' => now(),
            ]);

            $user->increment('credits_balance', $amount);
        });
    }
}
