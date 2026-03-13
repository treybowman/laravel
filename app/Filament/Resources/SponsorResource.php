<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SponsorResource\Pages;
use App\Models\Sponsor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SponsorResource extends Resource
{
    protected static ?string $model = Sponsor::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';
    protected static ?string $navigationGroup = 'Content';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->required(),
            Forms\Components\TextInput::make('logo_url')->url(),
            Forms\Components\TextInput::make('website_url')->url(),
            Forms\Components\TextInput::make('tagline'),
            Forms\Components\Select::make('sponsor_type')
                ->options(['venue' => 'Venue', 'event' => 'Event', 'sitewide' => 'Sitewide'])->required(),
            Forms\Components\Select::make('venue_id')->relationship('venue', 'name')->nullable(),
            Forms\Components\Select::make('event_id')->relationship('event', 'name')->nullable(),
            Forms\Components\DatePicker::make('active_from'),
            Forms\Components\DatePicker::make('active_until'),
            Forms\Components\Toggle::make('is_active'),
            Forms\Components\TextInput::make('display_order')->numeric()->default(0),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\BadgeColumn::make('sponsor_type'),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
                Tables\Columns\TextColumn::make('active_from')->date(),
                Tables\Columns\TextColumn::make('active_until')->date(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSponsors::route('/'),
            'create' => Pages\CreateSponsor::route('/create'),
            'edit' => Pages\EditSponsor::route('/{record}/edit'),
        ];
    }
}
