<x-app-layout>
    <x-slot name="title">List Tickets — Preview — {{ setting('platform.site_name', 'ATL Ticket Exchange') }}</x-slot>

    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Progress -->
        <div class="mb-8">
            <div class="flex items-center gap-2 mb-3">
                @foreach([1,2,3,4,5] as $i)
                <div class="flex-1 h-1 rounded-full bg-[#E81F27]"></div>
                @endforeach
            </div>
            <p class="text-gray-400 text-sm">Step 5 of 5 — Preview & Submit</p>
        </div>

        <h1 class="font-['Bebas_Neue'] text-4xl text-white mb-6">Review your listing</h1>

        @php
            $event = \App\Models\Event::find($data['event_id'] ?? null);
            $venue = \App\Models\Venue::find($data['venue_id'] ?? null);
        @endphp

        <div class="bg-[#0a1520] border border-gray-800 rounded-xl p-6 mb-6 space-y-5">
            <!-- Event Info -->
            <div>
                <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Event</p>
                <p class="text-white font-semibold">{{ $event?->name ?? 'Unknown' }}</p>
                <p class="text-gray-400 text-sm">{{ $event?->event_date?->format('l, F j, Y') }} · {{ $venue?->name }}</p>
            </div>

            <div class="border-t border-gray-800"></div>

            <!-- Listing Details -->
            <div>
                <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Listing Title</p>
                <p class="text-white font-semibold">{{ $data['title'] ?? '' }}</p>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                <div>
                    <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Quantity</p>
                    <p class="text-white">{{ $data['quantity'] ?? '' }} {{ Str::plural('Ticket', $data['quantity'] ?? 1) }}</p>
                </div>
                @if(!empty($data['section']))
                <div>
                    <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Section</p>
                    <p class="text-white">{{ $data['section'] }}</p>
                </div>
                @endif
                @if(!empty($data['row']))
                <div>
                    <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Row</p>
                    <p class="text-white">{{ $data['row'] }}</p>
                </div>
                @endif
            </div>

            <div class="border-t border-gray-800"></div>

            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Asking Price</p>
                    <p class="font-['Bebas_Neue'] text-3xl text-[#E81F27]">${{ number_format($data['asking_price'] ?? 0, 2) }}</p>
                    <p class="text-gray-500 text-xs">per ticket</p>
                </div>
                @if(!empty($data['willing_to_trade']))
                <span class="bg-blue-900/30 border border-blue-700/40 text-blue-400 text-sm px-3 py-1 rounded-full">Trade OK</span>
                @endif
            </div>

            <div class="border-t border-gray-800"></div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Transfer Method</p>
                    <p class="text-white text-sm">{{ str_replace('_', ' ', ucfirst($data['transfer_method'] ?? '')) }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Payment</p>
                    <p class="text-white text-sm">{{ implode(', ', array_map('ucfirst', $data['payment_methods'] ?? [])) }}</p>
                </div>
            </div>

            @if(!empty($data['notes']))
            <div>
                <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Notes</p>
                <p class="text-gray-300 text-sm">{{ $data['notes'] }}</p>
            </div>
            @endif
        </div>

        @if(auth()->user()->isOnProbation())
        <div class="bg-orange-950 border border-orange-800 rounded-xl p-4 mb-6">
            <p class="text-orange-300 text-sm font-semibold">⚠️ Listing Requires Approval</p>
            <p class="text-orange-400 text-xs mt-1">As a new member, your listing will be reviewed before going live. This is lifted after your first few approved listings.</p>
        </div>
        @endif

        <form method="POST" action="{{ route('listings.store') }}">
            @csrf
            <div class="flex justify-between">
                <a href="{{ route('listings.create') }}" class="px-6 py-3 border border-gray-700 text-gray-400 rounded-xl hover:text-white hover:border-gray-500 transition-colors">
                    ← Back
                </a>
                <button type="submit" class="px-8 py-3 bg-[#E81F27] text-white font-semibold rounded-xl hover:bg-red-700 transition-colors text-lg">
                    🎟 Submit Listing
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
