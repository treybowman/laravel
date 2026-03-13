<x-app-layout>
    <x-slot name="title">{{ ucwords($teamName) }} Tickets — {{ setting('platform.site_name', 'ATL Ticket Exchange') }}</x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="font-['Bebas_Neue'] text-5xl text-white mb-2">{{ ucwords($teamName) }} Tickets</h1>
        <p class="text-gray-400 mb-8">{{ $listings->total() }} {{ Str::plural('listing', $listings->total()) }} available</p>

        @if($listings->isEmpty())
        <div class="text-center py-16 bg-[#0a1520] border border-gray-800 rounded-xl">
            <p class="text-gray-500 text-lg">No listings found for this team.</p>
        </div>
        @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 mb-6">
            @foreach($listings as $listing)
                @include('listings._card', ['listing' => $listing])
            @endforeach
        </div>
        {{ $listings->links() }}
        @endif
    </div>
</x-app-layout>
