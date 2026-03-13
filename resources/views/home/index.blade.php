<x-app-layout>
    <x-slot name="title">{{ setting('platform.site_name', 'ATL Ticket Exchange') }} — {{ setting('platform.tagline', "Atlanta's Peer-to-Peer Ticket Marketplace") }}</x-slot>

    <!-- Hero Section -->
    <section class="relative bg-[#0a1520] border-b border-gray-800 overflow-hidden">
        <div class="absolute inset-0 opacity-5">
            <div class="absolute inset-0" style="background: radial-gradient(ellipse at center, #E81F27 0%, transparent 70%);"></div>
        </div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 text-center">
            <div class="inline-flex items-center gap-2 bg-[#E81F27]/10 border border-[#E81F27]/30 text-[#E81F27] text-xs font-semibold px-3 py-1 rounded-full mb-6 uppercase tracking-wider">
                🍑 Atlanta's Official Fan Ticket Marketplace
            </div>
            <h1 class="font-['Bebas_Neue'] text-6xl md:text-8xl text-white leading-none mb-4">
                BUY. SELL. <span class="text-[#E81F27]">TRADE.</span>
            </h1>
            <p class="text-gray-300 text-xl max-w-2xl mx-auto mb-8">
                {{ setting('platform.tagline', "Atlanta's Peer-to-Peer Ticket Marketplace") }}. No middlemen. No hidden fees. Pure Atlanta fan-to-fan.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('listings.index') }}" class="inline-flex items-center justify-center px-8 py-4 bg-[#E81F27] text-white font-semibold rounded-xl hover:bg-red-700 transition-colors text-lg">
                    Browse Tickets
                </a>
                <a href="{{ route('listings.create') }}" class="inline-flex items-center justify-center px-8 py-4 bg-transparent border-2 border-white text-white font-semibold rounded-xl hover:bg-white hover:text-[#0D1B2A] transition-colors text-lg">
                    List Your Tickets
                </a>
            </div>
        </div>
    </section>

    <!-- Announcements (pinned) -->
    @if($announcements->isNotEmpty())
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8">
        @foreach($announcements as $announcement)
        <div class="bg-[#E81F27]/10 border border-[#E81F27]/30 rounded-xl px-6 py-4 mb-3 flex items-start gap-3">
            <span class="text-[#E81F27] mt-0.5">📢</span>
            <div>
                <p class="text-white font-semibold text-sm">{{ $announcement->title }}</p>
                <p class="text-gray-400 text-sm mt-1">{{ Str::limit(strip_tags($announcement->body), 120) }}</p>
            </div>
        </div>
        @endforeach
    </section>
    @endif

    <!-- Featured Listings -->
    @if($featuredListings->isNotEmpty())
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="flex items-center justify-between mb-6">
            <h2 class="font-['Bebas_Neue'] text-3xl text-white">Featured Listings</h2>
            <a href="{{ route('listings.index') }}" class="text-[#E81F27] text-sm font-medium hover:underline">View All →</a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($featuredListings as $listing)
                @include('listings._card', ['listing' => $listing, 'featured' => true])
            @endforeach
        </div>
    </section>
    @endif

    <!-- Upcoming Events -->
    @if($upcomingEvents->isNotEmpty())
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="font-['Bebas_Neue'] text-3xl text-white">Upcoming Events</h2>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
            @foreach($upcomingEvents as $event)
            <a href="{{ route('events.show', ['venue' => $event->venue->slug, 'event' => $event->slug]) }}" class="block bg-[#0a1520] border border-gray-800 rounded-xl p-4 hover:border-[#E81F27]/50 transition-colors group">
                <p class="text-[#E81F27] text-xs font-semibold uppercase mb-1">{{ $event->event_date->format('M d') }}</p>
                <p class="text-white text-sm font-semibold leading-snug group-hover:text-[#E81F27] transition-colors">{{ $event->name }}</p>
                <p class="text-gray-500 text-xs mt-1">{{ $event->venue->name }}</p>
            </a>
            @endforeach
        </div>
    </section>
    @endif

    <!-- Recent Listings -->
    @if($recentListings->isNotEmpty())
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 pb-16">
        <div class="flex items-center justify-between mb-6">
            <h2 class="font-['Bebas_Neue'] text-3xl text-white">Recently Listed</h2>
            <a href="{{ route('listings.index') }}" class="text-[#E81F27] text-sm font-medium hover:underline">View All →</a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach($recentListings as $listing)
                @include('listings._card', ['listing' => $listing])
            @endforeach
        </div>
    </section>
    @endif

    <!-- How It Works -->
    <section class="bg-[#0a1520] border-t border-gray-800 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="font-['Bebas_Neue'] text-4xl text-white text-center mb-10">How It Works</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="w-16 h-16 rounded-full bg-[#E81F27]/10 border border-[#E81F27]/30 flex items-center justify-center mx-auto mb-4">
                        <span class="font-['Bebas_Neue'] text-3xl text-[#E81F27]">1</span>
                    </div>
                    <h3 class="font-['Bebas_Neue'] text-2xl text-white mb-2">List Your Tickets</h3>
                    <p class="text-gray-400 text-sm">Post your tickets in minutes. Set your price, describe your seats, and connect with buyers.</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 rounded-full bg-[#E81F27]/10 border border-[#E81F27]/30 flex items-center justify-center mx-auto mb-4">
                        <span class="font-['Bebas_Neue'] text-3xl text-[#E81F27]">2</span>
                    </div>
                    <h3 class="font-['Bebas_Neue'] text-2xl text-white mb-2">Connect Directly</h3>
                    <p class="text-gray-400 text-sm">Message sellers directly. Negotiate price and transfer method. No middlemen.</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 rounded-full bg-[#E81F27]/10 border border-[#E81F27]/30 flex items-center justify-center mx-auto mb-4">
                        <span class="font-['Bebas_Neue'] text-3xl text-[#E81F27]">3</span>
                    </div>
                    <h3 class="font-['Bebas_Neue'] text-2xl text-white mb-2">Build Your Rep</h3>
                    <p class="text-gray-400 text-sm">Leave feedback after each transaction. Build your BST score and earn trust in the community.</p>
                </div>
            </div>
            <div class="text-center mt-10">
                <a href="/how-it-works" class="inline-flex items-center px-6 py-3 border border-gray-700 text-gray-300 rounded-xl hover:text-white hover:border-gray-500 transition-colors text-sm">
                    Learn More →
                </a>
            </div>
        </div>
    </section>
</x-app-layout>
