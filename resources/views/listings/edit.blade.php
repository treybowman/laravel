<x-app-layout>
    <x-slot name="title">Edit Listing — {{ setting('platform.site_name', 'ATL Ticket Exchange') }}</x-slot>

    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="font-['Bebas_Neue'] text-4xl text-white mb-6">Edit Listing</h1>

        <form method="POST" action="{{ route('listings.update', $listing) }}">
            @csrf @method('PUT')

            <div class="bg-[#0a1520] border border-gray-800 rounded-xl p-6 space-y-5">
                <div>
                    <label class="block text-white font-semibold mb-2">Listing Title</label>
                    <input type="text" name="title" value="{{ old('title', $listing->title) }}" required maxlength="255"
                        class="w-full bg-[#0D1B2A] border border-gray-700 text-gray-300 rounded-xl px-4 py-3 focus:border-[#E81F27] focus:outline-none @error('title') border-red-500 @enderror">
                    @error('title') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-white font-semibold mb-2">Quantity</label>
                        <select name="quantity" class="w-full bg-[#0D1B2A] border border-gray-700 text-gray-300 rounded-xl px-4 py-3 focus:border-[#E81F27] focus:outline-none">
                            @foreach(range(1, 20) as $q)
                            <option value="{{ $q }}" {{ $listing->quantity == $q ? 'selected' : '' }}>{{ $q }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-white font-semibold mb-2">Asking Price ($)</label>
                        <input type="number" name="asking_price" value="{{ old('asking_price', $listing->asking_price) }}" step="0.01" min="0.01" required
                            class="w-full bg-[#0D1B2A] border border-gray-700 text-gray-300 rounded-xl px-4 py-3 focus:border-[#E81F27] focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-white font-semibold mb-2">Section</label>
                        <input type="text" name="section" value="{{ old('section', $listing->section) }}" maxlength="50"
                            class="w-full bg-[#0D1B2A] border border-gray-700 text-gray-300 rounded-xl px-4 py-3 focus:border-[#E81F27] focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-white font-semibold mb-2">Row</label>
                        <input type="text" name="row" value="{{ old('row', $listing->row) }}" maxlength="20"
                            class="w-full bg-[#0D1B2A] border border-gray-700 text-gray-300 rounded-xl px-4 py-3 focus:border-[#E81F27] focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-white font-semibold mb-2">Seat Numbers</label>
                        <input type="text" name="seat_numbers" value="{{ old('seat_numbers', $listing->seat_numbers) }}" maxlength="100"
                            class="w-full bg-[#0D1B2A] border border-gray-700 text-gray-300 rounded-xl px-4 py-3 focus:border-[#E81F27] focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-white font-semibold mb-2">Transfer Method</label>
                    <select name="transfer_method" required class="w-full bg-[#0D1B2A] border border-gray-700 text-gray-300 rounded-xl px-4 py-3 focus:border-[#E81F27] focus:outline-none">
                        @foreach(['pdf_download' => 'PDF Download', 'email_forward' => 'Email Forward', 'mobile_transfer' => 'Mobile Transfer', 'will_call' => 'Will Call', 'in_person' => 'In Person'] as $value => $label)
                        <option value="{{ $value }}" {{ $listing->transfer_method === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-white font-semibold mb-3">Payment Methods</label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                        @foreach(['venmo' => 'Venmo', 'cashapp' => 'Cash App', 'zelle' => 'Zelle', 'paypal' => 'PayPal', 'cash' => 'Cash', 'other' => 'Other'] as $value => $label)
                        <label class="flex items-center gap-2 p-3 border border-gray-700 rounded-xl cursor-pointer hover:border-[#E81F27]/50 has-[:checked]:border-[#E81F27] has-[:checked]:bg-[#E81F27]/5 transition-colors">
                            <input type="checkbox" name="payment_methods[]" value="{{ $value }}"
                                {{ in_array($value, $listing->payment_methods ?? []) ? 'checked' : '' }}
                                class="accent-[#E81F27]">
                            <span class="text-gray-300 text-sm">{{ $label }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label class="block text-white font-semibold mb-2">Notes</label>
                    <textarea name="notes" rows="3" maxlength="1000"
                        class="w-full bg-[#0D1B2A] border border-gray-700 text-gray-300 rounded-xl px-4 py-3 focus:border-[#E81F27] focus:outline-none text-sm">{{ old('notes', $listing->notes) }}</textarea>
                </div>
            </div>

            <div class="flex justify-between mt-6">
                <a href="{{ route('dashboard.listings') }}" class="px-6 py-3 border border-gray-700 text-gray-400 rounded-xl hover:text-white hover:border-gray-500 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-8 py-3 bg-[#E81F27] text-white font-semibold rounded-xl hover:bg-red-700 transition-colors">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
