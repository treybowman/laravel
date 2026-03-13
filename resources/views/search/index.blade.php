<x-app-layout>
    <x-slot name="title">Search{{ $query ? ': ' . $query : '' }} — {{ setting('platform.site_name', 'ATL Ticket Exchange') }}</x-slot>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="font-['Bebas_Neue'] text-4xl text-white mb-6">Search</h1>

        <!-- Search Input -->
        <form method="GET" action="{{ route('search') }}" class="mb-8">
            <div class="flex gap-3">
                <input type="text" name="q" value="{{ $query }}" placeholder="Search events, teams, listings..."
                    autofocus
                    class="flex-1 bg-[#0a1520] border border-gray-700 text-gray-200 rounded-xl px-5 py-4 text-lg focus:border-[#E81F27] focus:outline-none">
                <button type="submit" class="px-6 py-4 bg-[#E81F27] text-white font-semibold rounded-xl hover:bg-red-700 transition-colors">
                    Search
                </button>
            </div>
        </form>

        @if(strlen($query) > 0 && strlen($query) < 2)
        <p class="text-gray-500">Enter at least 2 characters to search.</p>
        @elseif(strlen($query) >= 2)

        @php
            $listingResults = $results->where('type', 'listing');
            $eventResults = $results->where('type', 'event');
            $venueResults = $results->where('type', 'venue');
        @endphp

        @if($results->isEmpty())
        <p class="text-gray-400">No results found for "<strong class="text-white">{{ $query }}</strong>".</p>
        @else
        <p class="text-gray-500 text-sm mb-6">{{ $results->count() }} results for "<strong class="text-white">{{ $query }}</strong>"</p>

        <!-- Listings -->
        @if($listingResults->isNotEmpty())
        <div class="mb-8">
            <h2 class="font-['Bebas_Neue'] text-2xl text-white mb-4">Ticket Listings ({{ $listingResults->count() }})</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($listingResults as $result)
                    @include('listings._card', ['listing' => $result['item']])
                @endforeach
            </div>
        </div>
        @endif

        <!-- Events -->
        @if($eventResults->isNotEmpty())
        <div class="mb-8">
            <h2 class="font-['Bebas_Neue'] text-2xl text-white mb-4">Events ({{ $eventResults->count() }})</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @foreach($eventResults as $result)
                @php $event = $result['item']; @endphp
                <a href="{{ route('events.show', ['venue' => $event->venue?->slug, 'event' => $event->slug]) }}" class="block bg-[#0a1520] border border-gray-800 rounded-xl p-4 hover:border-[#E81F27]/50 transition-colors group">
                    <p class="text-[#E81F27] text-xs font-bold mb-1">{{ $event->event_date->format('M d, Y') }}</p>
                    <p class="text-white font-semibold group-hover:text-[#E81F27] transition-colors">{{ $event->name }}</p>
                    <p class="text-gray-500 text-xs mt-1">{{ $event->venue?->name }}</p>
                </a>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Venues -->
        @if($venueResults->isNotEmpty())
        <div class="mb-8">
            <h2 class="font-['Bebas_Neue'] text-2xl text-white mb-4">Venues ({{ $venueResults->count() }})</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @foreach($venueResults as $result)
                @php $venue = $result['item']; @endphp
                <a href="{{ route('venues.show', $venue->slug) }}" class="block bg-[#0a1520] border border-gray-800 rounded-xl p-4 hover:border-[#E81F27]/50 transition-colors">
                    <p class="text-white font-semibold">{{ $venue->name }}</p>
                    <p class="text-gray-500 text-xs mt-1">{{ $venue->address }}</p>
                </a>
                @endforeach
            </div>
        </div>
        @endif
        @endif
        @endif
    </div>
</x-app-layout>
