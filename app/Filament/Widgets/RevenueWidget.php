<?php

namespace App\Filament\Widgets;

use App\Models\CreditTransaction;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RevenueWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $creditPurchasesThisMonth = CreditTransaction::where('type', 'purchase')
            ->where('created_at', '>=', now()->startOfMonth())
            ->sum('amount');

        $memberCount = User::where('subscription_tier', 'member')->count();
        $proCount = User::where('subscription_tier', 'pro')->count();

        return [
            Stat::make('Credits Purchased (This Month)', $creditPurchasesThisMonth)
                ->description('From credit package sales')
                ->icon('heroicon-o-currency-dollar'),

            Stat::make('Member Subscribers', $memberCount)
                ->description('Active member tier subscribers')
                ->icon('heroicon-o-star'),

            Stat::make('Pro Subscribers', $proCount)
                ->description('Active pro tier subscribers')
                ->icon('heroicon-o-bolt'),
        ];
    }
}
