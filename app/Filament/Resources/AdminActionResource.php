<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdminActionResource\Pages;
use App\Models\AdminAction;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AdminActionResource extends Resource
{
    protected static ?string $model = AdminAction::class;
    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationGroup = 'Logs';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('admin.name')->label('Admin')->searchable(),
                Tables\Columns\TextColumn::make('action_type')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('target_type'),
                Tables\Columns\TextColumn::make('target_id'),
                Tables\Columns\TextColumn::make('notes')->limit(50),
                Tables\Columns\TextColumn::make('ip_address'),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('admin')
                    ->relationship('admin', 'name'),
                Tables\Filters\SelectFilter::make('action_type')
                    ->options(fn () => AdminAction::distinct()->pluck('action_type', 'action_type')->toArray()),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAdminActions::route('/'),
        ];
    }
}
