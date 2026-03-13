<x-app-layout>
    <x-slot name="title">{{ $listing->title }} — {{ setting('platform.site_name', 'ATL Ticket Exchange') }}</x-slot>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Breadcrumb -->
        <nav class="text-xs text-gray-500 mb-6 flex items-center gap-2">
            <a href="{{ route('listings.index') }}" class="hover:text-gray-300">Listings</a>
            <span>/</span>
            <a href="{{ route('venues.show', $listing->venue->slug) }}" class="hover:text-gray-300">{{ $listing->venue->name }}</a>
            <span>/</span>
            <span class="text-gray-400">{{ $listing->title }}</span>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2">
                @if($listing->is_featured)
                <div class="inline-flex items-center gap-1 bg-[#E81F27]/10 border border-[#E81F27]/30 text-[#E81F27] text-xs font-bold px-3 py-1 rounded-full mb-4 uppercase tracking-wider">
                    ⭐ Featured Listing
                </div>
                @endif

                <h1 class="font-['Bebas_Neue'] text-4xl text-white mb-2">{{ $listing->title }}</h1>
                <p class="text-gray-400 mb-6">
                    <a href="{{ route('events.show', ['venue' => $listing->venue->slug, 'event' => $listing->event->slug]) }}" class="hover:text-white transition-colors">
                        {{ $listing->event->name }}
                    </a>
                    <span class="mx-2">·</span>
                    {{ $listing->event->event_date?->format('l, F j, Y') }}
                    @if($listing->event->event_time)
                    at {{ \Carbon\Carbon::parse($listing->event->event_time)->format('g:i A') }}
                    @endif
                    <span class="mx-2">·</span>
                    <a href="{{ route('venues.show', $listing->venue->slug) }}" class="hover:text-white transition-colors">{{ $listing->venue->name }}</a>
                </p>

                <!-- Details Grid -->
                <div class="bg-[#0a1520] border border-gray-800 rounded-xl p-6 mb-6">
                    <h2 class="text-white font-semibold mb-4">Ticket Details</h2>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                        <div>
                            <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Quantity</p>
                            <p class="text-white font-semibold">{{ $listing->quantity }} {{ Str::plural('ticket', $listing->quantity) }}</p>
                        </div>
                        @if($listing->section)
                        <div>
                            <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Section</p>
                            <p class="text-white font-semibold">{{ $listing->section }}</p>
                        </div>
                        @endif
                        @if($listing->row)
                        <div>
                            <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Row</p>
                            <p class="text-white font-semibold">{{ $listing->row }}</p>
                        </div>
                        @endif
                        @if($listing->seat_numbers)
                        <div>
                            <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Seats</p>
                            <p class="text-white font-semibold">{{ $listing->seat_numbers }}</p>
                        </div>
                        @endif
                        <div>
                            <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Transfer</p>
                            <p class="text-white font-semibold">{{ str_replace('_', ' ', ucfirst($listing->transfer_method)) }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Payment</p>
                            <p class="text-white font-semibold">{{ implode(', ', array_map('ucfirst', $listing->payment_methods ?? [])) }}</p>
                        </div>
                    </div>

                    @if($listing->willing_to_trade)
                    <div class="mt-4 p-3 bg-blue-900/20 border border-blue-700/30 rounded-lg">
                        <p class="text-blue-400 text-sm font-semibold">💱 Open to Trade</p>
                        @if($listing->trade_notes)
                        <p class="text-gray-400 text-sm mt-1">{{ $listing->trade_notes }}</p>
                        @endif
                    </div>
                    @endif

                    @if($listing->notes)
                    <div class="mt-4">
                        <p class="text-gray-500 text-xs uppercase tracking-wider mb-2">Seller Notes</p>
                        <p class="text-gray-300 text-sm">{{ $listing->notes }}</p>
                    </div>
                    @endif
                </div>

                @if($listing->affiliate_url)
                <div class="bg-[#0a1520] border border-gray-700 rounded-xl p-4 mb-6 flex items-center justify-between">
                    <div>
                        <p class="text-gray-400 text-xs uppercase tracking-wider mb-1">Official Tickets</p>
                        <p class="text-white text-sm">Can't make a deal? Get tickets from an official reseller.</p>
                    </div>
                    <a href="{{ $listing->affiliate_url }}" target="_blank" rel="noopener" class="inline-flex items-center px-4 py-2 border border-gray-600 text-gray-300 text-sm rounded-lg hover:text-white hover:border-gray-400 transition-colors whitespace-nowrap">
                        {{ $listing->affiliate_label ?? 'View Tickets' }} →
                    </a>
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <!-- Price + CTA Card -->
                <div class="bg-[#0a1520] border border-gray-800 rounded-xl p-6 mb-4 sticky top-20">
                    <div class="text-center mb-6">
                        <p class="text-gray-400 text-sm mb-1">Asking Price</p>
                        <p class="font-['Bebas_Neue'] text-5xl text-[#E81F27]">${{ number_format($listing->asking_price, 2) }}</p>
                        <p class="text-gray-500 text-xs mt-1">per ticket · {{ $listing->quantity }} available</p>
                    </div>

                    @auth
                        @if(auth()->id() === $listing->user_id)
                        <div class="text-center text-gray-500 text-sm mb-4">This is your listing.</div>
                        <a href="{{ route('listings.edit', $listing) }}" class="block text-center w-full py-3 border border-gray-700 text-gray-300 rounded-xl hover:text-white hover:border-gray-500 transition-colors text-sm font-semibold">
                            Edit Listing
                        </a>
                        @elseif($existingConversation)
                        <a href="{{ route('messages.show', $existingConversation) }}" class="block text-center w-full py-3 bg-gray-700 text-white rounded-xl hover:bg-gray-600 transition-colors font-semibold">
                            Continue Conversation
                        </a>
                        @else
                        <form method="POST" action="{{ route('listings.contact', $listing) }}">
                            @csrf
                            <button type="submit" class="w-full py-3 bg-[#E81F27] text-white font-semibold rounded-xl hover:bg-red-700 transition-colors text-lg">
                                I'm Interested 🎟
                            </button>
                        </form>
                        @endif
                    @else
                    <a href="{{ route('login') }}" class="block text-center w-full py-3 bg-[#E81F27] text-white font-semibold rounded-xl hover:bg-red-700 transition-colors text-lg">
                        Sign In to Contact Seller
                    </a>
                    @endauth

                    <p class="text-gray-600 text-xs text-center mt-3">ATL Ticket Exchange never processes payments. Transactions happen directly.</p>
                </div>

                <!-- Seller Info -->
                <div class="bg-[#0a1520] border border-gray-800 rounded-xl p-5">
                    <h3 class="text-gray-400 text-xs uppercase tracking-wider mb-4">Seller</h3>
                    <a href="{{ route('profile.show', $listing->user->username) }}" class="flex items-center gap-3 group">
                        <div class="w-12 h-12 rounded-full bg-[#E81F27]/20 flex items-center justify-center text-[#E81F27] font-bold text-lg">
                            {{ strtoupper(substr($listing->user->name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="text-white font-semibold group-hover:text-[#E81F27] transition-colors">{{ $listing->user->name }}</p>
                            <p class="text-gray-500 text-xs">@{{ $listing->user->username }}</p>
                        </div>
                    </a>
                    <div class="grid grid-cols-2 gap-3 mt-4">
                        <div class="bg-[#0D1B2A] rounded-lg p-3 text-center">
                            <p class="font-['Bebas_Neue'] text-2xl text-[#E81F27]">{{ $listing->user->bstScore() }}%</p>
                            <p class="text-gray-500 text-xs">BST Score</p>
                        </div>
                        <div class="bg-[#0D1B2A] rounded-lg p-3 text-center">
                            <p class="font-['Bebas_Neue'] text-2xl text-white">{{ $listing->user->bstTransactionCount() }}</p>
                            <p class="text-gray-500 text-xs">Transactions</p>
                        </div>
                    </div>
                    @if($listing->user->is_verified_sth)
                    <div class="mt-3 flex items-center gap-2 text-xs text-yellow-400">
                        <span>⭐</span>
                        <span>Verified Season Ticket Holder</span>
                    </div>
                    @endif
                    @if($listing->user->probation)
                    <div class="mt-3 flex items-center gap-2 text-xs text-orange-400">
                        <span>⚠️</span>
                        <span>New member — first few transactions</span>
                    </div>
                    @endif
                    <div class="mt-3 text-xs text-gray-600">
                        {{ $listing->views_count }} views · Listed {{ $listing->created_at?->diffForHumans() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
