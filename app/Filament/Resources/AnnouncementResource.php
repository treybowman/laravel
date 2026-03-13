<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AnnouncementResource\Pages;
use App\Models\Announcement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AnnouncementResource extends Resource
{
    protected static ?string $model = Announcement::class;
    protected static ?string $navigationIcon = 'heroicon-o-megaphone';
    protected static ?string $navigationGroup = 'Content';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('title')->required(),
            Forms\Components\RichEditor::make('body')->required()->columnSpanFull(),
            Forms\Components\DateTimePicker::make('published_at'),
            Forms\Components\Toggle::make('is_pinned'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable(),
                Tables\Columns\TextColumn::make('published_at')->dateTime()->sortable(),
                Tables\Columns\IconColumn::make('is_pinned')->boolean(),
                Tables\Columns\TextColumn::make('blast_sent_at')->dateTime()->label('Blast Sent'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('send_blast')
                    ->label('Send Email Blast')
                    ->icon('heroicon-o-envelope')
                    ->requiresConfirmation()
                    ->action(function (Announcement $record) {
                        // Queue the email blast to all users
                        $userCount = \App\Models\User::whereNotNull('email_verified_at')->count();
                        $record->update([
                            'blast_sent_at' => now(),
                            'blast_recipient_count' => $userCount,
                        ]);
                        Notification::make()->title("Email blast queued for {$userCount} users")->success()->send();
                    })
                    ->visible(fn (Announcement $record) => $record->blast_sent_at === null && $record->published_at !== null),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAnnouncements::route('/'),
            'create' => Pages\CreateAnnouncement::route('/create'),
            'edit' => Pages\EditAnnouncement::route('/{record}/edit'),
        ];
    }
}
