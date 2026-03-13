<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FeedbackResource\Pages;
use App\Models\Feedback;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FeedbackResource extends Resource
{
    protected static ?string $model = Feedback::class;
    protected static ?string $navigationIcon = 'heroicon-o-star';
    protected static ?string $navigationGroup = 'Community';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('reviewer_id')->relationship('reviewer', 'name')->searchable()->required(),
            Forms\Components\Select::make('reviewee_id')->relationship('reviewee', 'name')->searchable()->required(),
            Forms\Components\Select::make('listing_id')->relationship('listing', 'title')->searchable(),
            Forms\Components\Select::make('transaction_type')->options(['buy' => 'Buy', 'sell' => 'Sell', 'trade' => 'Trade'])->required(),
            Forms\Components\Select::make('rating')->options(['positive' => 'Positive', 'neutral' => 'Neutral', 'negative' => 'Negative'])->required(),
            Forms\Components\Textarea::make('comment'),
            Forms\Components\Toggle::make('is_visible'),
            Forms\Components\Textarea::make('admin_note'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reviewer.username')->label('Reviewer')->searchable(),
                Tables\Columns\TextColumn::make('reviewee.username')->label('Reviewee')->searchable(),
                Tables\Columns\BadgeColumn::make('rating')
                    ->colors(['success' => 'positive', 'warning' => 'neutral', 'danger' => 'negative']),
                Tables\Columns\TextColumn::make('transaction_type'),
                Tables\Columns\TextColumn::make('comment')->limit(50),
                Tables\Columns\IconColumn::make('is_visible')->boolean(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('rating')
                    ->options(['positive' => 'Positive', 'neutral' => 'Neutral', 'negative' => 'Negative']),
                Tables\Filters\TernaryFilter::make('is_visible'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('toggle_visible')
                    ->label(fn (Feedback $record) => $record->is_visible ? 'Hide' : 'Show')
                    ->action(fn (Feedback $record) => $record->update(['is_visible' => !$record->is_visible])),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFeedbacks::route('/'),
            'edit' => Pages\EditFeedback::route('/{record}/edit'),
        ];
    }
}
