<?php

namespace App\Filament\Widgets;

use App\Models\Listing;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('New Users (7 days)', User::where('created_at', '>=', now()->subDays(7))->count())
                ->description('Registered in last 7 days')
                ->icon('heroicon-o-users'),

            Stat::make('Active Listings', Listing::where('status', 'active')->count())
                ->description('Currently live listings')
                ->icon('heroicon-o-ticket'),

            Stat::make('Pending Approval', Listing::where('status', 'pending_approval')->count())
                ->description('Awaiting review')
                ->color('warning')
                ->icon('heroicon-o-clock'),

            Stat::make('Total Members', User::count())
                ->description('All registered users')
                ->icon('heroicon-o-user-group'),
        ];
    }
}
