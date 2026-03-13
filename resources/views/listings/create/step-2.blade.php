<x-app-layout>
    <x-slot name="title">List Tickets — Step 2 of 5 — {{ setting('platform.site_name', 'ATL Ticket Exchange') }}</x-slot>

    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Progress -->
        <div class="mb-8">
            <div class="flex items-center gap-2 mb-3">
                @foreach([1,2,3,4,5] as $i)
                <div class="flex-1 h-1 rounded-full {{ $i <= 2 ? 'bg-[#E81F27]' : 'bg-gray-800' }}"></div>
                @endforeach
            </div>
            <p class="text-gray-400 text-sm">Step 2 of 5 — Ticket Details</p>
        </div>

        <h1 class="font-['Bebas_Neue'] text-4xl text-white mb-6">Tell us about your tickets</h1>

        <form method="POST" action="{{ route('listings.store') }}">
            @csrf

            <div class="bg-[#0a1520] border border-gray-800 rounded-xl p-6 space-y-5">
                <!-- Title -->
                <div>
                    <label class="block text-white font-semibold mb-2">Listing Title</label>
                    <input type="text" name="title" value="{{ old('title', $data['title'] ?? '') }}" required maxlength="255"
                        placeholder="e.g. Braves vs Phillies — 2 Lower Level Tickets"
                        class="w-full bg-[#0D1B2A] border border-gray-700 text-gray-300 rounded-xl px-4 py-3 focus:border-[#E81F27] focus:outline-none @error('title') border-red-500 @enderror">
                    @error('title') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Quantity -->
                <div>
                    <label class="block text-white font-semibold mb-2">Number of Tickets</label>
                    <select name="quantity" required class="w-full bg-[#0D1B2A] border border-gray-700 text-gray-300 rounded-xl px-4 py-3 focus:border-[#E81F27] focus:outline-none">
                        @foreach(range(1, 20) as $q)
                        <option value="{{ $q }}" {{ ($data['quantity'] ?? 1) == $q ? 'selected' : '' }}>{{ $q }} {{ Str::plural('Ticket', $q) }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Section / Row / Seats -->
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-white font-semibold mb-2">Section <span class="text-gray-500 font-normal">(optional)</span></label>
                        <input type="text" name="section" value="{{ old('section', $data['section'] ?? '') }}" maxlength="50"
                            placeholder="e.g. 105"
                            class="w-full bg-[#0D1B2A] border border-gray-700 text-gray-300 rounded-xl px-4 py-3 focus:border-[#E81F27] focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-white font-semibold mb-2">Row <span class="text-gray-500 font-normal">(optional)</span></label>
                        <input type="text" name="row" value="{{ old('row', $data['row'] ?? '') }}" maxlength="20"
                            placeholder="e.g. H"
                            class="w-full bg-[#0D1B2A] border border-gray-700 text-gray-300 rounded-xl px-4 py-3 focus:border-[#E81F27] focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-white font-semibold mb-2">Seats <span class="text-gray-500 font-normal">(optional)</span></label>
                        <input type="text" name="seat_numbers" value="{{ old('seat_numbers', $data['seat_numbers'] ?? '') }}" maxlength="100"
                            placeholder="e.g. 14, 15"
                            class="w-full bg-[#0D1B2A] border border-gray-700 text-gray-300 rounded-xl px-4 py-3 focus:border-[#E81F27] focus:outline-none">
                    </div>
                </div>
            </div>

            <div class="flex justify-between mt-6">
                <a href="{{ route('listings.create') }}?step=1" class="px-6 py-3 border border-gray-700 text-gray-400 rounded-xl hover:text-white hover:border-gray-500 transition-colors">
                    ← Back
                </a>
                <button type="submit" class="px-8 py-3 bg-[#E81F27] text-white font-semibold rounded-xl hover:bg-red-700 transition-colors">
                    Next: Pricing →
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
