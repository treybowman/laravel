<x-app-layout>
    <x-slot name="title">List Tickets — Step 3 of 5 — {{ setting('platform.site_name', 'ATL Ticket Exchange') }}</x-slot>

    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Progress -->
        <div class="mb-8">
            <div class="flex items-center gap-2 mb-3">
                @foreach([1,2,3,4,5] as $i)
                <div class="flex-1 h-1 rounded-full {{ $i <= 3 ? 'bg-[#E81F27]' : 'bg-gray-800' }}"></div>
                @endforeach
            </div>
            <p class="text-gray-400 text-sm">Step 3 of 5 — Pricing</p>
        </div>

        <h1 class="font-['Bebas_Neue'] text-4xl text-white mb-6">Set your price</h1>

        <form method="POST" action="{{ route('listings.store') }}" x-data="{ tradeOk: {{ ($data['willing_to_trade'] ?? false) ? 'true' : 'false' }} }">
            @csrf

            <div class="bg-[#0a1520] border border-gray-800 rounded-xl p-6 space-y-5">
                <!-- Asking Price -->
                <div>
                    <label class="block text-white font-semibold mb-2">Asking Price (per ticket)</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-semibold">$</span>
                        <input type="number" name="asking_price" value="{{ old('asking_price', $data['asking_price'] ?? '') }}"
                            step="0.01" min="0.01" required placeholder="0.00"
                            class="w-full bg-[#0D1B2A] border border-gray-700 text-gray-300 rounded-xl pl-8 pr-4 py-3 focus:border-[#E81F27] focus:outline-none text-lg @error('asking_price') border-red-500 @enderror">
                    </div>
                    @error('asking_price') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Trade Toggle -->
                <div>
                    <div class="flex items-center gap-3 cursor-pointer" @click="tradeOk = !tradeOk">
                        <div class="w-10 h-6 rounded-full transition-colors" :class="tradeOk ? 'bg-[#E81F27]' : 'bg-gray-700'">
                            <div class="w-4 h-4 bg-white rounded-full mt-1 mx-1 transition-transform" :class="tradeOk ? 'translate-x-4' : 'translate-x-0'"></div>
                        </div>
                        <label class="text-white font-semibold cursor-pointer">Open to Trade</label>
                    </div>
                    <input type="hidden" name="willing_to_trade" :value="tradeOk ? '1' : '0'">
                    <p class="text-gray-500 text-xs mt-2 ml-13">Let buyers know you're willing to trade for other tickets.</p>

                    <div x-show="tradeOk" x-transition class="mt-3">
                        <label class="block text-gray-300 text-sm mb-2">Trade notes <span class="text-gray-500">(optional)</span></label>
                        <textarea name="trade_notes" rows="2" maxlength="500"
                            placeholder="e.g. Looking for Hawks tickets of equal value"
                            class="w-full bg-[#0D1B2A] border border-gray-700 text-gray-300 rounded-xl px-4 py-3 focus:border-[#E81F27] focus:outline-none text-sm">{{ old('trade_notes', $data['trade_notes'] ?? '') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="flex justify-between mt-6">
                <a href="{{ route('listings.create') }}" class="px-6 py-3 border border-gray-700 text-gray-400 rounded-xl hover:text-white hover:border-gray-500 transition-colors">
                    ← Back
                </a>
                <button type="submit" class="px-8 py-3 bg-[#E81F27] text-white font-semibold rounded-xl hover:bg-red-700 transition-colors">
                    Next: Transfer & Payment →
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
