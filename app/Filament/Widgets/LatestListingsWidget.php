<?php

namespace App\Filament\Widgets;

use App\Models\Listing;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestListingsWidget extends BaseWidget
{
    protected static ?string $heading = 'Latest Listings';
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(Listing::with(['user', 'event'])->orderByDesc('created_at')->limit(5))
            ->columns([
                Tables\Columns\TextColumn::make('title')->limit(40),
                Tables\Columns\TextColumn::make('user.username')->label('Seller'),
                Tables\Columns\TextColumn::make('asking_price')->money('USD'),
                Tables\Columns\BadgeColumn::make('status'),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->since(),
            ]);
    }
}
