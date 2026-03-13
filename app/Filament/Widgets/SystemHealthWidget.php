<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SystemHealthWidget extends BaseWidget
{
    protected static ?string $heading = 'System Health';

    protected function getStats(): array
    {
        $mailDriver = setting('mail.driver', 'Not set');
        $smsActive = setting('sms.active', false);
        $syncEnabled = setting('seatgeek.sync_enabled', false);
        $maintenanceMode = setting('platform.maintenance_mode', false);

        return [
            Stat::make('Mail Driver', ucfirst($mailDriver))
                ->color($mailDriver !== 'Not set' ? 'success' : 'danger')
                ->icon('heroicon-o-envelope'),

            Stat::make('SMS', $smsActive ? 'Enabled' : 'Disabled')
                ->color($smsActive ? 'success' : 'secondary')
                ->icon('heroicon-o-device-phone-mobile'),

            Stat::make('SeatGeek Sync', $syncEnabled ? 'Enabled' : 'Disabled')
                ->color($syncEnabled ? 'success' : 'secondary')
                ->icon('heroicon-o-arrow-path'),

            Stat::make('Maintenance Mode', $maintenanceMode ? 'ON' : 'OFF')
                ->color($maintenanceMode ? 'danger' : 'success')
                ->icon('heroicon-o-wrench-screwdriver'),
        ];
    }
}
