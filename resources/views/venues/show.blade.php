<x-app-layout>
    <x-slot name="title">{{ $venue->name }} — {{ setting('platform.site_name', 'ATL Ticket Exchange') }}</x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Venue Header -->
        <div class="mb-8">
            <nav class="text-xs text-gray-500 mb-4 flex items-center gap-2">
                <a href="{{ route('venues.index') }}" class="hover:text-gray-300">Venues</a>
                <span>/</span>
                <span class="text-gray-400">{{ $venue->name }}</span>
            </nav>
            <h1 class="font-['Bebas_Neue'] text-5xl text-white mb-1">{{ $venue->name }}</h1>
            <p class="text-gray-400 text-sm">{{ $venue->address }}</p>
            @if($venue->seating_chart_url)
            <a href="{{ $venue->seating_chart_url }}" target="_blank" class="inline-flex items-center gap-1 text-[#E81F27] text-sm mt-2 hover:underline">
                View Seating Chart →
            </a>
            @endif
        </div>

        <!-- Upcoming Events -->
        <h2 class="font-['Bebas_Neue'] text-3xl text-white mb-4">Upcoming Events</h2>

        @if($events->isEmpty())
        <p class="text-gray-500">No upcoming events found.</p>
        @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
            @foreach($events as $event)
            <a href="{{ route('events.show', ['venue' => $venue->slug, 'event' => $event->slug]) }}" class="block bg-[#0a1520] border border-gray-800 rounded-xl p-5 hover:border-[#E81F27]/50 transition-colors group">
                <p class="text-[#E81F27] text-sm font-bold mb-1">{{ $event->event_date->format('D, M j, Y') }}</p>
                @if($event->event_time)
                <p class="text-gray-500 text-xs mb-2">{{ \Carbon\Carbon::parse($event->event_time)->format('g:i A') }}</p>
                @endif
                <h3 class="text-white font-semibold leading-snug group-hover:text-[#E81F27] transition-colors">{{ $event->name }}</h3>
                @php $count = $event->listings()->where('status', 'active')->count(); @endphp
                <p class="text-gray-500 text-xs mt-2">{{ $count }} {{ Str::plural('listing', $count) }} available</p>
            </a>
            @endforeach
        </div>
        {{ $events->links() }}
        @endif
    </div>
</x-app-layout>
