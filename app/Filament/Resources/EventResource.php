<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventResource\Pages;
use App\Models\Event;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;
    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationGroup = 'Marketplace';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('venue_id')->relationship('venue', 'name')->required(),
            Forms\Components\TextInput::make('name')->required()->maxLength(255),
            Forms\Components\TextInput::make('slug')->required()->maxLength(255),
            Forms\Components\DatePicker::make('event_date')->required(),
            Forms\Components\TimePicker::make('event_time'),
            Forms\Components\Select::make('category')
                ->options(['sports' => 'Sports', 'concert' => 'Concert', 'theatre' => 'Theatre', 'other' => 'Other'])
                ->required(),
            Forms\Components\TextInput::make('home_team'),
            Forms\Components\TextInput::make('away_team'),
            Forms\Components\TextInput::make('image_url')->url(),
            Forms\Components\TextInput::make('seatgeek_event_id'),
            Forms\Components\Toggle::make('is_active'),
            Forms\Components\TextInput::make('meta_title'),
            Forms\Components\Textarea::make('meta_description'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable()->limit(40),
                Tables\Columns\TextColumn::make('venue.name')->sortable(),
                Tables\Columns\TextColumn::make('event_date')->date()->sortable(),
                Tables\Columns\BadgeColumn::make('category'),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
                Tables\Columns\TextColumn::make('listings_count')
                    ->counts('listings')
                    ->label('Listings'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->options(['sports' => 'Sports', 'concert' => 'Concert', 'theatre' => 'Theatre', 'other' => 'Other']),
                Tables\Filters\TernaryFilter::make('is_active'),
                Tables\Filters\SelectFilter::make('venue')->relationship('venue', 'name'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('event_date');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEvents::route('/'),
            'create' => Pages\CreateEvent::route('/create'),
            'edit' => Pages\EditEvent::route('/{record}/edit'),
        ];
    }
}
