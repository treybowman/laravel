<x-app-layout>
    <x-slot name="title">Browse Tickets — {{ setting('platform.site_name', 'ATL Ticket Exchange') }}</x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="font-['Bebas_Neue'] text-4xl text-white">Browse Tickets</h1>
            @auth
            <a href="{{ route('listings.create') }}" class="inline-flex items-center px-4 py-2 bg-[#E81F27] text-white text-sm font-semibold rounded-lg hover:bg-red-700 transition-colors">
                + List Tickets
            </a>
            @endauth
        </div>

        <div class="flex flex-col lg:flex-row gap-6">
            <!-- Filters Sidebar -->
            <aside class="lg:w-64 shrink-0" x-data="{ open: false }">
                <div class="bg-[#0a1520] border border-gray-800 rounded-xl p-5">
                    <div class="flex items-center justify-between mb-4 lg:mb-0">
                        <h3 class="text-white font-semibold text-sm uppercase tracking-wider">Filters</h3>
                        <button @click="open = !open" class="lg:hidden text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                    </div>

                    <form method="GET" action="{{ route('listings.index') }}" class="lg:block" x-show="open || window.innerWidth >= 1024">
                        <div class="space-y-5 mt-4">
                            <!-- Venue Filter -->
                            <div>
                                <label class="block text-gray-400 text-xs uppercase tracking-wider mb-2">Venue</label>
                                <select name="venue_id" class="w-full bg-[#0D1B2A] border border-gray-700 text-gray-300 text-sm rounded-lg px-3 py-2 focus:border-[#E81F27] focus:outline-none">
                                    <option value="">All Venues</option>
                                    @foreach($venues as $venue)
                                    <option value="{{ $venue->id }}" {{ request('venue_id') == $venue->id ? 'selected' : '' }}>{{ $venue->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Price Range -->
                            <div>
                                <label class="block text-gray-400 text-xs uppercase tracking-wider mb-2">Price Range</label>
                                <div class="flex gap-2">
                                    <input type="number" name="min_price" placeholder="Min" value="{{ request('min_price') }}" min="0" class="w-1/2 bg-[#0D1B2A] border border-gray-700 text-gray-300 text-sm rounded-lg px-3 py-2 focus:border-[#E81F27] focus:outline-none">
                                    <input type="number" name="max_price" placeholder="Max" value="{{ request('max_price') }}" min="0" class="w-1/2 bg-[#0D1B2A] border border-gray-700 text-gray-300 text-sm rounded-lg px-3 py-2 focus:border-[#E81F27] focus:outline-none">
                                </div>
                            </div>

                            <!-- Quantity -->
                            <div>
                                <label class="block text-gray-400 text-xs uppercase tracking-wider mb-2">Min. Tickets</label>
                                <select name="quantity" class="w-full bg-[#0D1B2A] border border-gray-700 text-gray-300 text-sm rounded-lg px-3 py-2 focus:border-[#E81F27] focus:outline-none">
                                    <option value="">Any</option>
                                    @foreach([1,2,3,4] as $q)
                                    <option value="{{ $q }}" {{ request('quantity') == $q ? 'selected' : '' }}>{{ $q }}+</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Transfer Method -->
                            <div>
                                <label class="block text-gray-400 text-xs uppercase tracking-wider mb-2">Transfer Method</label>
                                <select name="transfer_method" class="w-full bg-[#0D1B2A] border border-gray-700 text-gray-300 text-sm rounded-lg px-3 py-2 focus:border-[#E81F27] focus:outline-none">
                                    <option value="">Any</option>
                                    <option value="pdf_download" {{ request('transfer_method') == 'pdf_download' ? 'selected' : '' }}>PDF Download</option>
                                    <option value="email_forward" {{ request('transfer_method') == 'email_forward' ? 'selected' : '' }}>Email Forward</option>
                                    <option value="mobile_transfer" {{ request('transfer_method') == 'mobile_transfer' ? 'selected' : '' }}>Mobile Transfer</option>
                                    <option value="will_call" {{ request('transfer_method') == 'will_call' ? 'selected' : '' }}>Will Call</option>
                                    <option value="in_person" {{ request('transfer_method') == 'in_person' ? 'selected' : '' }}>In Person</option>
                                </select>
                            </div>

                            <!-- Trade Toggle -->
                            <div class="flex items-center gap-3">
                                <input type="checkbox" name="trade_only" id="trade_only" value="1" {{ request('trade_only') ? 'checked' : '' }} class="w-4 h-4 rounded border-gray-600 text-[#E81F27] focus:ring-[#E81F27] bg-[#0D1B2A]">
                                <label for="trade_only" class="text-gray-300 text-sm">Trade OK</label>
                            </div>

                            <!-- Section -->
                            <div>
                                <label class="block text-gray-400 text-xs uppercase tracking-wider mb-2">Section</label>
                                <input type="text" name="section" placeholder="e.g. 105, GA" value="{{ request('section') }}" class="w-full bg-[#0D1B2A] border border-gray-700 text-gray-300 text-sm rounded-lg px-3 py-2 focus:border-[#E81F27] focus:outline-none">
                            </div>

                            <div class="flex gap-2 pt-2">
                                <button type="submit" class="flex-1 bg-[#E81F27] text-white text-sm font-semibold py-2 rounded-lg hover:bg-red-700 transition-colors">Apply</button>
                                <a href="{{ route('listings.index') }}" class="flex-1 text-center border border-gray-700 text-gray-400 text-sm py-2 rounded-lg hover:text-white hover:border-gray-500 transition-colors">Clear</a>
                            </div>
                        </div>
                    </form>
                </div>
            </aside>

            <!-- Listings Grid -->
            <div class="flex-1">
                @if($listings->isEmpty())
                <div class="text-center py-16 text-gray-500">
                    <p class="text-lg mb-2">No listings found.</p>
                    <p class="text-sm">Try adjusting your filters or <a href="{{ route('listings.index') }}" class="text-[#E81F27] hover:underline">clear all filters</a>.</p>
                </div>
                @else
                <div class="flex items-center justify-between mb-4">
                    <p class="text-gray-400 text-sm">{{ $listings->total() }} listing{{ $listings->total() !== 1 ? 's' : '' }} found</p>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
                    @foreach($listings as $listing)
                        @include('listings._card', ['listing' => $listing, 'featured' => $listing->is_featured])
                    @endforeach
                </div>
                <div class="mt-8">
                    {{ $listings->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
