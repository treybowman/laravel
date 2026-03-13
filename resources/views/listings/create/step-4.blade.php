<x-app-layout>
    <x-slot name="title">List Tickets — Step 4 of 5 — {{ setting('platform.site_name', 'ATL Ticket Exchange') }}</x-slot>

    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Progress -->
        <div class="mb-8">
            <div class="flex items-center gap-2 mb-3">
                @foreach([1,2,3,4,5] as $i)
                <div class="flex-1 h-1 rounded-full {{ $i <= 4 ? 'bg-[#E81F27]' : 'bg-gray-800' }}"></div>
                @endforeach
            </div>
            <p class="text-gray-400 text-sm">Step 4 of 5 — Transfer & Payment</p>
        </div>

        <h1 class="font-['Bebas_Neue'] text-4xl text-white mb-6">Transfer & payment details</h1>

        <form method="POST" action="{{ route('listings.store') }}" x-data="{ socialPush: {{ ($data['social_push'] ?? false) ? 'true' : 'false' }} }">
            @csrf

            <div class="bg-[#0a1520] border border-gray-800 rounded-xl p-6 space-y-6">
                <!-- Transfer Method -->
                <div>
                    <label class="block text-white font-semibold mb-3">How will you transfer the tickets?</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach([
                            'pdf_download' => ['label' => 'PDF Download', 'desc' => 'Send a PDF file'],
                            'email_forward' => ['label' => 'Email Forward', 'desc' => 'Forward confirmation email'],
                            'mobile_transfer' => ['label' => 'Mobile Transfer', 'desc' => 'App-to-app transfer'],
                            'will_call' => ['label' => 'Will Call', 'desc' => 'Pickup at box office'],
                            'in_person' => ['label' => 'In Person', 'desc' => 'Physical tickets'],
                        ] as $value => $info)
                        <label class="flex items-start gap-3 p-3 border border-gray-700 rounded-xl cursor-pointer hover:border-[#E81F27]/50 has-[:checked]:border-[#E81F27] has-[:checked]:bg-[#E81F27]/5 transition-colors">
                            <input type="radio" name="transfer_method" value="{{ $value }}" {{ ($data['transfer_method'] ?? '') === $value ? 'checked' : '' }} required class="mt-0.5 accent-[#E81F27]">
                            <div>
                                <p class="text-white text-sm font-semibold">{{ $info['label'] }}</p>
                                <p class="text-gray-500 text-xs">{{ $info['desc'] }}</p>
                            </div>
                        </label>
                        @endforeach
                    </div>
                    @error('transfer_method') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Payment Methods -->
                <div>
                    <label class="block text-white font-semibold mb-3">Accepted payment methods</label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                        @foreach(['venmo' => 'Venmo', 'cashapp' => 'Cash App', 'zelle' => 'Zelle', 'paypal' => 'PayPal', 'cash' => 'Cash', 'other' => 'Other'] as $value => $label)
                        <label class="flex items-center gap-2 p-3 border border-gray-700 rounded-xl cursor-pointer hover:border-[#E81F27]/50 has-[:checked]:border-[#E81F27] has-[:checked]:bg-[#E81F27]/5 transition-colors">
                            <input type="checkbox" name="payment_methods[]" value="{{ $value }}"
                                {{ in_array($value, $data['payment_methods'] ?? []) ? 'checked' : '' }}
                                class="accent-[#E81F27]">
                            <span class="text-gray-300 text-sm">{{ $label }}</span>
                        </label>
                        @endforeach
                    </div>
                    @error('payment_methods') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Notes -->
                <div>
                    <label class="block text-white font-semibold mb-2">Additional Notes <span class="text-gray-500 font-normal">(optional)</span></label>
                    <textarea name="notes" rows="3" maxlength="1000"
                        placeholder="Any additional details about the transaction..."
                        class="w-full bg-[#0D1B2A] border border-gray-700 text-gray-300 rounded-xl px-4 py-3 focus:border-[#E81F27] focus:outline-none text-sm">{{ old('notes', $data['notes'] ?? '') }}</textarea>
                </div>

                <!-- Social Push -->
                @if(setting('features.social_push_enabled', false))
                <div class="border-t border-gray-800 pt-5">
                    <div class="flex items-center gap-3 cursor-pointer" @click="socialPush = !socialPush">
                        <div class="w-10 h-6 rounded-full transition-colors" :class="socialPush ? 'bg-[#E81F27]' : 'bg-gray-700'">
                            <div class="w-4 h-4 bg-white rounded-full mt-1 mx-1 transition-transform" :class="socialPush ? 'translate-x-4' : 'translate-x-0'"></div>
                        </div>
                        <label class="text-white font-semibold cursor-pointer">Post to Social Media</label>
                    </div>
                    <input type="hidden" name="social_push" :value="socialPush ? '1' : '0'">
                    <p class="text-gray-500 text-xs mt-2">Automatically share this listing to our Facebook and Twitter pages.</p>
                </div>
                @else
                <input type="hidden" name="social_push" value="0">
                @endif
            </div>

            <div class="flex justify-between mt-6">
                <a href="{{ route('listings.create') }}" class="px-6 py-3 border border-gray-700 text-gray-400 rounded-xl hover:text-white hover:border-gray-500 transition-colors">
                    ← Back
                </a>
                <button type="submit" class="px-8 py-3 bg-[#E81F27] text-white font-semibold rounded-xl hover:bg-red-700 transition-colors">
                    Preview Listing →
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
