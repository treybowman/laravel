<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GiveawayResource\Pages;
use App\Models\Giveaway;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class GiveawayResource extends Resource
{
    protected static ?string $model = Giveaway::class;
    protected static ?string $navigationIcon = 'heroicon-o-gift';
    protected static ?string $navigationGroup = 'Content';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('title')->required(),
            Forms\Components\RichEditor::make('body')->required(),
            Forms\Components\TextInput::make('image_url')->url(),
            Forms\Components\DateTimePicker::make('ends_at')->required(),
            Forms\Components\Toggle::make('is_active'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable(),
                Tables\Columns\TextColumn::make('ends_at')->dateTime()->sortable(),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
                Tables\Columns\TextColumn::make('entries_count')->counts('entries')->label('Entries'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGiveaways::route('/'),
            'create' => Pages\CreateGiveaway::route('/create'),
            'edit' => Pages\EditGiveaway::route('/{record}/edit'),
        ];
    }
}
