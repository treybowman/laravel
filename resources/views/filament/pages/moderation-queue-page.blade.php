<x-filament-panels::page>
    <div class="space-y-8">
        <!-- Pending Listings -->
        <x-filament::section>
            <x-slot name="heading">Pending Listings ({{ $this->getPendingListings()->count() }})</x-slot>

            @php $pending = $this->getPendingListings(); @endphp

            @if($pending->isEmpty())
            <p class="text-gray-500 text-sm">No listings pending approval.</p>
            @else
            <div class="space-y-3">
                @foreach($pending as $listing)
                <div class="flex items-center justify-between border border-gray-700 rounded-lg p-4">
                    <div>
                        <p class="font-semibold text-sm">{{ $listing->title }}</p>
                        <p class="text-xs text-gray-400">{{ $listing->user->username }} · {{ $listing->event?->name }} · ${{ number_format($listing->asking_price, 2) }} · {{ $listing->quantity }} tickets</p>
                        <p class="text-xs text-gray-500 mt-0.5">Transfer: {{ str_replace('_', ' ', $listing->transfer_method) }} · Listed {{ $listing->created_at->diffForHumans() }}</p>
                    </div>
                    <div class="flex gap-2">
                        <x-filament::button wire:click="approveListing({{ $listing->id }})" color="success" size="sm">Approve</x-filament::button>
                        <x-filament::button wire:click="rejectListing({{ $listing->id }})" color="danger" size="sm">Reject</x-filament::button>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </x-filament::section>

        <!-- Flagged Users -->
        <x-filament::section>
            <x-slot name="heading">Flagged Users</x-slot>

            @php $flagged = $this->getFlaggedUsers(); @endphp

            @if($flagged->isEmpty())
            <p class="text-gray-500 text-sm">No flagged users.</p>
            @else
            <div class="space-y-2">
                @foreach($flagged as $user)
                <div class="flex items-center justify-between border border-gray-700 rounded-lg p-3">
                    <div>
                        <p class="font-semibold text-sm">{{ $user->name }} (@{{ $user->username }})</p>
                        <p class="text-xs text-gray-400">
                            @if($user->is_banned) <span class="text-red-400">Banned</span> @else <span class="text-orange-400">Flagged</span> @endif
                            @if($user->banned_reason) · {{ $user->banned_reason }} @endif
                        </p>
                    </div>
                    <a href="{{ route('filament.admin.resources.users.edit', $user) }}" class="text-xs text-blue-400 hover:underline">Edit User →</a>
                </div>
                @endforeach
            </div>
            @endif
        </x-filament::section>
    </div>
</x-filament-panels::page>
