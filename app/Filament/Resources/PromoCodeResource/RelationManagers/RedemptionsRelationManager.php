<?php

namespace App\Filament\Resources\PromoCodeResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class RedemptionsRelationManager extends RelationManager
{
    protected static string $relationship = 'redemptions';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')->label('User'),
                Tables\Columns\TextColumn::make('user.username')->label('Username'),
                Tables\Columns\TextColumn::make('credits_amount')->label('Credits'),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->label('Redeemed At'),
            ]);
    }
}
