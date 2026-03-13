<x-app-layout>
    <x-slot name="title">{{ $event->name }} Tickets — {{ setting('platform.site_name', 'ATL Ticket Exchange') }}</x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Breadcrumb -->
        <nav class="text-xs text-gray-500 mb-6 flex items-center gap-2">
            <a href="{{ route('venues.index') }}" class="hover:text-gray-300">Venues</a>
            <span>/</span>
            <a href="{{ route('venues.show', $venue->slug) }}" class="hover:text-gray-300">{{ $venue->name }}</a>
            <span>/</span>
            <span class="text-gray-400">{{ $event->name }}</span>
        </nav>

        <!-- Event Header -->
        <div class="mb-8">
            <div class="inline-flex items-center gap-1 bg-gray-800 text-gray-300 text-xs px-2 py-1 rounded-full mb-3 uppercase tracking-wider">
                {{ ucfirst($event->category) }}
            </div>
            <h1 class="font-['Bebas_Neue'] text-5xl text-white mb-2">{{ $event->name }}</h1>
            <div class="flex flex-wrap items-center gap-4 text-gray-400 text-sm">
                <span>📅 {{ $event->event_date->format('l, F j, Y') }}</span>
                @if($event->event_time)
                <span>🕐 {{ \Carbon\Carbon::parse($event->event_time)->format('g:i A') }}</span>
                @endif
                <span>📍 <a href="{{ route('venues.show', $venue->slug) }}" class="hover:text-white">{{ $venue->name }}</a></span>
            </div>
        </div>

        <!-- Listings -->
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-['Bebas_Neue'] text-3xl text-white">{{ $listings->total() }} {{ Str::plural('Ticket Listing', $listings->total()) }}</h2>
            @auth
            <a href="{{ route('listings.create') }}" class="inline-flex items-center px-4 py-2 bg-[#E81F27] text-white text-sm font-semibold rounded-lg hover:bg-red-700 transition-colors">
                + List Tickets for This Event
            </a>
            @endauth
        </div>

        @if($listings->isEmpty())
        <div class="text-center py-16 bg-[#0a1520] border border-gray-800 rounded-xl">
            <p class="text-gray-500 text-lg mb-2">No tickets listed yet.</p>
            @auth
            <a href="{{ route('listings.create') }}" class="text-[#E81F27] hover:underline text-sm">Be the first to list tickets →</a>
            @else
            <a href="{{ route('register') }}" class="text-[#E81F27] hover:underline text-sm">Create an account to sell tickets →</a>
            @endauth
        </div>
        @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
            @foreach($listings as $listing)
                @include('listings._card', ['listing' => $listing, 'featured' => $listing->is_featured])
            @endforeach
        </div>
        {{ $listings->links() }}
        @endif
    </div>
</x-app-layout>
