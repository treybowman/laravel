<?php

namespace App\Filament\Resources\EventResource\Pages;

use App\Filament\Resources\EventResource;
use App\Jobs\EventExpireJob;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListEvents extends ListRecords
{
    protected static string $resource = EventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('sync')
                ->label('Sync from SeatGeek')
                ->icon('heroicon-o-arrow-path')
                ->action(function () {
                    dispatch(new EventExpireJob());
                    \Artisan::call('events:sync');
                    Notification::make()->title('Sync triggered')->success()->send();
                }),
            Actions\CreateAction::make(),
        ];
    }
}
