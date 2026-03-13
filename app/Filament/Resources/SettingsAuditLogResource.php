<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SettingsAuditLogResource\Pages;
use App\Models\SettingsAuditLog;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SettingsAuditLogResource extends Resource
{
    protected static ?string $model = SettingsAuditLog::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Logs';
    protected static ?string $label = 'Settings Audit Log';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->is_super_admin ?? false;
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
                Tables\Columns\TextColumn::make('setting_key')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('old_value')->limit(30)->label('Old Value'),
                Tables\Columns\TextColumn::make('new_value')->limit(30)->label('New Value'),
                Tables\Columns\TextColumn::make('ip_address'),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSettingsAuditLogs::route('/'),
        ];
    }
}
