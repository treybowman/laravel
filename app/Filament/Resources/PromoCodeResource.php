<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PromoCodeResource\Pages;
use App\Filament\Resources\PromoCodeResource\RelationManagers;
use App\Models\PromoCode;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PromoCodeResource extends Resource
{
    protected static ?string $model = PromoCode::class;
    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationGroup = 'Commerce';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('code')
                ->required()
                ->maxLength(50)
                ->default(fn () => strtoupper(Str::random(8)))
                ->suffixAction(
                    Forms\Components\Actions\Action::make('generate')
                        ->label('Generate')
                        ->action(fn (Forms\Set $set) => $set('code', strtoupper(Str::random(8))))
                ),
            Forms\Components\TextInput::make('credits_amount')->numeric()->required()->minValue(1),
            Forms\Components\TextInput::make('max_uses')->numeric()->nullable()->helperText('Leave blank for unlimited'),
            Forms\Components\DateTimePicker::make('expires_at')->nullable(),
            Forms\Components\Toggle::make('is_active'),
            Forms\Components\Textarea::make('notes'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')->searchable()->copyable(),
                Tables\Columns\TextColumn::make('credits_amount')->label('Credits'),
                Tables\Columns\TextColumn::make('uses_count')->label('Uses'),
                Tables\Columns\TextColumn::make('max_uses')->label('Max')->default('∞'),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
                Tables\Columns\TextColumn::make('expires_at')->dateTime(),
                Tables\Columns\TextColumn::make('redemptions_count')->counts('redemptions')->label('Redemptions'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\RedemptionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPromoCodes::route('/'),
            'create' => Pages\CreatePromoCode::route('/create'),
            'edit' => Pages\EditPromoCode::route('/{record}/edit'),
        ];
    }
}
