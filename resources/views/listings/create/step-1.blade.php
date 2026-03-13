<x-app-layout>
    <x-slot name="title">List Tickets — Step 1 of 5 — {{ setting('platform.site_name', 'ATL Ticket Exchange') }}</x-slot>

    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Progress -->
        <div class="mb-8">
            <div class="flex items-center gap-2 mb-3">
                @foreach([1,2,3,4,5] as $i)
                <div class="flex-1 h-1 rounded-full {{ $i === 1 ? 'bg-[#E81F27]' : 'bg-gray-800' }}"></div>
                @endforeach
            </div>
            <p class="text-gray-400 text-sm">Step 1 of 5 — Choose Venue & Event</p>
        </div>

        <h1 class="font-['Bebas_Neue'] text-4xl text-white mb-6">What event are you selling tickets for?</h1>

        <form method="POST" action="{{ route('listings.store') }}" x-data="{ venueId: '{{ $data['venue_id'] ?? '' }}', events: [] }" x-init="
            if (venueId) {
                fetch('/api/venues/' + venueId + '/events')
                    .then(r => r.json())
                    .then(d => events = d);
            }
        ">
            @csrf

            <div class="bg-[#0a1520] border border-gray-800 rounded-xl p-6 space-y-6">
                <!-- Venue -->
                <div>
                    <label class="block text-white font-semibold mb-2">Venue</label>
                    <select name="venue_id" x-model="venueId" @change="
                        events = [];
                        if (venueId) {
                            fetch('/api/venues/' + venueId + '/events')
                                .then(r => r.json())
                                .then(d => events = d);
                        }
                    " required class="w-full bg-[#0D1B2A] border border-gray-700 text-gray-300 rounded-xl px-4 py-3 focus:border-[#E81F27] focus:outline-none @error('venue_id') border-red-500 @enderror">
                        <option value="">Select a venue...</option>
                        @foreach($venues as $venue)
                        <option value="{{ $venue->id }}" {{ ($data['venue_id'] ?? '') == $venue->id ? 'selected' : '' }}>{{ $venue->name }}</option>
                        @endforeach
                    </select>
                    @error('venue_id') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Event -->
                <div>
                    <label class="block text-white font-semibold mb-2">Event</label>
                    <select name="event_id" required class="w-full bg-[#0D1B2A] border border-gray-700 text-gray-300 rounded-xl px-4 py-3 focus:border-[#E81F27] focus:outline-none @error('event_id') border-red-500 @enderror">
                        <option value="">Select an event...</option>
                        <template x-for="event in events" :key="event.id">
                            <option :value="event.id" x-text="event.name + ' — ' + event.event_date"></option>
                        </template>
                        @if(!empty($data['event_id']))
                        <option value="{{ $data['event_id'] }}" selected>Previously selected event</option>
                        @endif
                    </select>
                    @error('event_id') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    <p class="text-gray-500 text-xs mt-2">Can't find your event? <a href="{{ route('venues.index') }}" class="text-[#E81F27] hover:underline">Browse all venues</a> or contact us.</p>
                </div>
            </div>

            <div class="flex justify-end mt-6">
                <button type="submit" class="px-8 py-3 bg-[#E81F27] text-white font-semibold rounded-xl hover:bg-red-700 transition-colors">
                    Next: Ticket Details →
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
