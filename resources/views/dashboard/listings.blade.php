<x-app-layout>
    <x-slot name="title">My Listings — {{ setting('platform.site_name', 'ATL Ticket Exchange') }}</x-slot>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="font-['Bebas_Neue'] text-4xl text-white">My Listings</h1>
            <a href="{{ route('listings.create') }}" class="inline-flex items-center px-4 py-2 bg-[#E81F27] text-white text-sm font-semibold rounded-lg hover:bg-red-700 transition-colors">
                + New Listing
            </a>
        </div>

        <!-- Tabs -->
        <div class="flex gap-1 bg-[#0a1520] border border-gray-800 rounded-xl p-1 mb-6">
            @foreach(['active' => 'Active', 'pending_approval' => 'Pending', 'sold' => 'Sold', 'expired' => 'Expired'] as $status => $label)
            <a href="{{ route('dashboard.listings', ['tab' => $status]) }}"
               class="flex-1 text-center py-2 px-4 rounded-lg text-sm font-medium transition-colors {{ $tab === $status ? 'bg-[#E81F27] text-white' : 'text-gray-400 hover:text-white' }}">
                {{ $label }}
                <span class="ml-1 text-xs {{ $tab === $status ? 'text-white/70' : 'text-gray-600' }}">({{ $counts[$status] }})</span>
            </a>
            @endforeach
        </div>

        @if($listings->isEmpty())
        <div class="text-center py-16 bg-[#0a1520] border border-gray-800 rounded-xl">
            <p class="text-gray-500 text-lg mb-2">No {{ $tab === 'pending_approval' ? 'pending' : $tab }} listings</p>
            @if($tab === 'active')
            <a href="{{ route('listings.create') }}" class="text-[#E81F27] text-sm hover:underline">Create your first listing →</a>
            @endif
        </div>
        @else
        <div class="space-y-3">
            @foreach($listings as $listing)
            <div class="bg-[#0a1520] border border-gray-800 rounded-xl p-5 flex flex-col sm:flex-row sm:items-center gap-4">
                <div class="flex-1 min-w-0">
                    <div class="flex items-start gap-2 mb-1">
                        @if($listing->is_featured)
                        <span class="bg-[#E81F27]/20 text-[#E81F27] text-xs font-bold px-2 py-0.5 rounded uppercase shrink-0">Featured</span>
                        @endif
                        <h3 class="text-white font-semibold text-sm truncate">{{ $listing->title }}</h3>
                    </div>
                    <p class="text-gray-400 text-xs">
                        {{ $listing->event->name ?? '' }} · {{ $listing->event->event_date?->format('M d, Y') ?? '' }}
                    </p>
                    <p class="text-gray-500 text-xs mt-1">
                        {{ $listing->quantity }} {{ Str::plural('ticket', $listing->quantity) }} · ${{ number_format($listing->asking_price, 2) }} each
                        @if($listing->section) · Sec {{ $listing->section }} @endif
                    </p>
                    @if($listing->status === 'active')
                    <p class="text-gray-600 text-xs mt-1">Expires {{ $listing->expires_at?->diffForHumans() }} · {{ $listing->views_count }} views</p>
                    @elseif($listing->status === 'pending_approval')
                    <p class="text-orange-400 text-xs mt-1">⏳ Awaiting admin approval</p>
                    @elseif($listing->rejection_reason)
                    <p class="text-red-400 text-xs mt-1">Rejected: {{ $listing->rejection_reason }}</p>
                    @endif
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    <span class="font-['Bebas_Neue'] text-xl text-[#E81F27]">${{ number_format($listing->asking_price, 2) }}</span>

                    @if(in_array($listing->status, ['active', 'pending_approval']))
                    <a href="{{ route('listings.edit', $listing) }}" class="px-3 py-1.5 border border-gray-700 text-gray-400 text-xs rounded-lg hover:text-white hover:border-gray-500 transition-colors">Edit</a>

                    @if($listing->status === 'active')
                    <!-- Mark as Sold -->
                    <div x-data="{ open: false }">
                        <button @click="open = true" class="px-3 py-1.5 bg-green-800 text-green-200 text-xs rounded-lg hover:bg-green-700 transition-colors">Mark Sold</button>
                        <div x-show="open" x-transition class="fixed inset-0 bg-black/70 z-50 flex items-center justify-center p-4">
                            <div class="bg-[#0a1520] border border-gray-700 rounded-2xl p-6 w-full max-w-sm" @click.stop>
                                <h3 class="text-white font-semibold text-lg mb-4">Mark as Sold</h3>
                                <form method="POST" action="{{ route('listings.sold', $listing) }}">
                                    @csrf
                                    <div class="mb-4">
                                        <label class="block text-gray-400 text-sm mb-2">Buyer's username (optional)</label>
                                        <input type="text" name="buyer_username" placeholder="e.g. john_hawks_fan"
                                            class="w-full bg-[#0D1B2A] border border-gray-700 text-gray-300 rounded-lg px-3 py-2 text-sm focus:border-[#E81F27] focus:outline-none">
                                        <p class="text-gray-600 text-xs mt-1">Leave blank to skip feedback request.</p>
                                    </div>
                                    <div class="flex gap-2">
                                        <button type="submit" class="flex-1 bg-green-700 text-white text-sm font-semibold py-2 rounded-lg hover:bg-green-600 transition-colors">Confirm Sold</button>
                                        <button type="button" @click="open = false" class="flex-1 border border-gray-700 text-gray-400 text-sm py-2 rounded-lg hover:text-white transition-colors">Cancel</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endif
                    @endif

                    @if(in_array($listing->status, ['sold', 'expired']))
                    <form method="POST" action="{{ route('listings.relist', $listing) }}">
                        @csrf
                        <button type="submit" class="px-3 py-1.5 bg-blue-800 text-blue-200 text-xs rounded-lg hover:bg-blue-700 transition-colors">Relist</button>
                    </form>
                    @endif

                    <form method="POST" action="{{ route('listings.destroy', $listing) }}" onsubmit="return confirm('Delete this listing?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="px-3 py-1.5 border border-red-900 text-red-400 text-xs rounded-lg hover:bg-red-950 transition-colors">Delete</button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $listings->links() }}
        </div>
        @endif
    </div>
</x-app-layout>
