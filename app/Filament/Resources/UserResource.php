<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\AdminAction;
use App\Models\User;
use App\Services\CreditService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Community';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Profile')
                    ->schema([
                        Forms\Components\TextInput::make('name')->required(),
                        Forms\Components\TextInput::make('email')->email()->required(),
                        Forms\Components\TextInput::make('username')->unique(ignoreRecord: true),
                        Forms\Components\Select::make('subscription_tier')
                            ->options(['free' => 'Free', 'member' => 'Member', 'pro' => 'Pro']),
                        Forms\Components\Select::make('trust_level')
                            ->options(['none' => 'None', 'trusted' => 'Trusted', 'power_seller' => 'Power Seller']),
                        Forms\Components\TextInput::make('credits_balance')->numeric(),
                        Forms\Components\TextInput::make('bst_score_override')->numeric()->nullable(),
                    ])->columns(2),

                Forms\Components\Section::make('Status')
                    ->schema([
                        Forms\Components\Toggle::make('is_admin')->label('Admin'),
                        Forms\Components\Toggle::make('is_super_admin')->label('Super Admin'),
                        Forms\Components\Toggle::make('is_verified_sth')->label('Verified STH'),
                        Forms\Components\Toggle::make('probation')->label('On Probation'),
                        Forms\Components\Toggle::make('is_banned')->label('Banned'),
                        Forms\Components\DateTimePicker::make('ban_expires_at')->nullable(),
                        Forms\Components\Textarea::make('banned_reason')->nullable(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('username')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\BadgeColumn::make('subscription_tier')
                    ->colors([
                        'secondary' => 'free',
                        'primary' => 'member',
                        'warning' => 'pro',
                    ]),
                Tables\Columns\BadgeColumn::make('trust_level')
                    ->colors([
                        'secondary' => 'none',
                        'success' => 'trusted',
                        'warning' => 'power_seller',
                    ]),
                Tables\Columns\IconColumn::make('is_admin')->boolean()->label('Admin'),
                Tables\Columns\IconColumn::make('is_banned')->boolean()->label('Banned'),
                Tables\Columns\IconColumn::make('probation')->boolean(),
                Tables\Columns\TextColumn::make('credits_balance')->sortable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('subscription_tier')
                    ->options(['free' => 'Free', 'member' => 'Member', 'pro' => 'Pro']),
                Tables\Filters\TernaryFilter::make('probation'),
                Tables\Filters\TernaryFilter::make('is_banned')->label('Banned'),
                Tables\Filters\TernaryFilter::make('is_admin')->label('Admin'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('ban')
                    ->label('Ban')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->form([
                        Forms\Components\Textarea::make('reason')->label('Ban Reason')->required(),
                        Forms\Components\DateTimePicker::make('expires_at')->label('Expires At (leave blank for permanent)'),
                    ])
                    ->action(function (User $record, array $data) {
                        $record->update([
                            'is_banned' => true,
                            'banned_reason' => $data['reason'],
                            'ban_expires_at' => $data['expires_at'] ?? null,
                        ]);
                        AdminAction::create([
                            'admin_id' => auth()->id(),
                            'action_type' => 'ban_user',
                            'target_type' => 'user',
                            'target_id' => $record->id,
                            'notes' => $data['reason'],
                            'ip_address' => request()->ip(),
                            'created_at' => now(),
                        ]);
                        Notification::make()->title('User banned')->success()->send();
                    })
                    ->visible(fn (User $record) => !$record->is_banned),

                Tables\Actions\Action::make('unban')
                    ->label('Unban')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(function (User $record) {
                        $record->update(['is_banned' => false, 'banned_reason' => null, 'ban_expires_at' => null]);
                        AdminAction::create([
                            'admin_id' => auth()->id(),
                            'action_type' => 'unban_user',
                            'target_type' => 'user',
                            'target_id' => $record->id,
                            'notes' => null,
                            'ip_address' => request()->ip(),
                            'created_at' => now(),
                        ]);
                        Notification::make()->title('User unbanned')->success()->send();
                    })
                    ->visible(fn (User $record) => $record->is_banned),

                Tables\Actions\Action::make('grant_credits')
                    ->label('Grant Credits')
                    ->icon('heroicon-o-currency-dollar')
                    ->form([
                        Forms\Components\TextInput::make('amount')->numeric()->required()->minValue(1),
                        Forms\Components\TextInput::make('notes')->label('Notes'),
                    ])
                    ->action(function (User $record, array $data) {
                        app(CreditService::class)->addCredits(
                            $record,
                            (int) $data['amount'],
                            'admin_grant',
                            $data['notes'] ?? 'Admin grant'
                        );
                        AdminAction::create([
                            'admin_id' => auth()->id(),
                            'action_type' => 'grant_credits',
                            'target_type' => 'user',
                            'target_id' => $record->id,
                            'notes' => "Granted {$data['amount']} credits. " . ($data['notes'] ?? ''),
                            'ip_address' => request()->ip(),
                            'created_at' => now(),
                        ]);
                        Notification::make()->title("Granted {$data['amount']} credits")->success()->send();
                    }),

                Tables\Actions\Action::make('reset_probation')
                    ->label('Clear Probation')
                    ->icon('heroicon-o-shield-check')
                    ->action(function (User $record) {
                        $record->update(['probation' => false]);
                        AdminAction::create([
                            'admin_id' => auth()->id(),
                            'action_type' => 'clear_probation',
                            'target_type' => 'user',
                            'target_id' => $record->id,
                            'notes' => null,
                            'ip_address' => request()->ip(),
                            'created_at' => now(),
                        ]);
                        Notification::make()->title('Probation cleared')->success()->send();
                    })
                    ->visible(fn (User $record) => $record->probation),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
