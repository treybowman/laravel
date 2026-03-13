<x-guest-layout>
    <h2 class="font-['Bebas_Neue'] text-3xl text-white text-center mb-2">Confirm Password</h2>
    <p class="text-gray-400 text-sm text-center mb-6">
        This is a secure area. Please confirm your password before continuing.
    </p>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-4">
        @csrf

        <div>
            <label class="block text-gray-300 text-sm mb-1.5" for="password">Password</label>
            <input id="password" type="password" name="password" required autocomplete="current-password"
                class="w-full bg-[#0D1B2A] border {{ $errors->has('password') ? 'border-red-500' : 'border-gray-700' }} text-gray-200 rounded-xl px-4 py-3 text-sm focus:border-[#E81F27] focus:outline-none">
            @error('password') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <button type="submit" class="w-full py-3 bg-[#E81F27] text-white font-semibold rounded-xl hover:bg-red-700 transition-colors">
            Confirm
        </button>
    </form>
</x-guest-layout>
