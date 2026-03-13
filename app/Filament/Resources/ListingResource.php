<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ListingResource\Pages;
use App\Models\AdminAction;
use App\Models\Listing;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ListingResource extends Resource
{
    protected static ?string $model = Listing::class;
    protected static ?string $navigationIcon = 'heroicon-o-ticket';
    protected static ?string $navigationGroup = 'Marketplace';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')->relationship('user', 'name')->searchable()->required(),
                Forms\Components\Select::make('event_id')->relationship('event', 'name')->searchable()->required(),
                Forms\Components\Select::make('venue_id')->relationship('venue', 'name')->required(),
                Forms\Components\TextInput::make('title')->required(),
                Forms\Components\TextInput::make('asking_price')->numeric()->prefix('$')->required(),
                Forms\Components\TextInput::make('quantity')->numeric()->required(),
                Forms\Components\Select::make('status')
                    ->options(['active' => 'Active', 'sold' => 'Sold', 'expired' => 'Expired', 'pending_approval' => 'Pending Approval']),
                Forms\Components\Toggle::make('is_featured'),
                Forms\Components\DateTimePicker::make('featured_until'),
                Forms\Components\DateTimePicker::make('expires_at'),
                Forms\Components\Textarea::make('rejection_reason'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable()->limit(40),
                Tables\Columns\TextColumn::make('user.username')->searchable()->label('Seller'),
                Tables\Columns\TextColumn::make('event.name')->searchable()->limit(30),
                Tables\Columns\TextColumn::make('asking_price')->money('USD')->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'active',
                        'warning' => 'pending_approval',
                        'secondary' => 'sold',
                        'danger' => 'expired',
                    ]),
                Tables\Columns\IconColumn::make('is_featured')->boolean()->label('Featured'),
                Tables\Columns\TextColumn::make('views_count')->sortable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(['active' => 'Active', 'sold' => 'Sold', 'expired' => 'Expired', 'pending_approval' => 'Pending Approval']),
                Tables\Filters\TernaryFilter::make('is_featured')->label('Featured'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->action(function (Listing $record) {
                        $record->update(['status' => 'active']);
                        $record->user->increment('listings_approved_count');

                        // Check probation threshold
                        $threshold = (int) setting('platform.probation_threshold', 3);
                        if ($record->user->listings_approved_count >= $threshold) {
                            $record->user->update(['probation' => false]);
                        }

                        AdminAction::create([
                            'admin_id' => auth()->id(),
                            'action_type' => 'approve_listing',
                            'target_type' => 'listing',
                            'target_id' => $record->id,
                            'notes' => null,
                            'ip_address' => request()->ip(),
                            'created_at' => now(),
                        ]);
                        Notification::make()->title('Listing approved')->success()->send();
                    })
                    ->visible(fn (Listing $record) => $record->status === 'pending_approval'),

                Tables\Actions\Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->form([
                        Forms\Components\Textarea::make('reason')->label('Rejection Reason')->required(),
                    ])
                    ->action(function (Listing $record, array $data) {
                        $record->update(['status' => 'expired', 'rejection_reason' => $data['reason']]);
                        AdminAction::create([
                            'admin_id' => auth()->id(),
                            'action_type' => 'reject_listing',
                            'target_type' => 'listing',
                            'target_id' => $record->id,
                            'notes' => $data['reason'],
                            'ip_address' => request()->ip(),
                            'created_at' => now(),
                        ]);
                        Notification::make()->title('Listing rejected')->danger()->send();
                    }),

                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('bulk_approve')
                        ->label('Approve Selected')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                if ($record->status === 'pending_approval') {
                                    $record->update(['status' => 'active']);
                                    $record->user->increment('listings_approved_count');
                                    $threshold = (int) setting('platform.probation_threshold', 3);
                                    if ($record->user->listings_approved_count >= $threshold) {
                                        $record->user->update(['probation' => false]);
                                    }
                                }
                            }
                            Notification::make()->title('Listings approved')->success()->send();
                        }),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListListings::route('/'),
            'create' => Pages\CreateListing::route('/create'),
            'edit' => Pages\EditListing::route('/{record}/edit'),
        ];
    }
}
