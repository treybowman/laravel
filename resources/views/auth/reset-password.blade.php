<x-guest-layout>
    <h2 class="font-['Bebas_Neue'] text-3xl text-white text-center mb-6">Reset Password</h2>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div>
            <label class="block text-gray-300 text-sm mb-1.5" for="email">Email Address</label>
            <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username"
                class="w-full bg-[#0D1B2A] border {{ $errors->has('email') ? 'border-red-500' : 'border-gray-700' }} text-gray-200 rounded-xl px-4 py-3 text-sm focus:border-[#E81F27] focus:outline-none">
            @error('email') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-gray-300 text-sm mb-1.5" for="password">New Password</label>
            <input id="password" type="password" name="password" required autocomplete="new-password"
                class="w-full bg-[#0D1B2A] border {{ $errors->has('password') ? 'border-red-500' : 'border-gray-700' }} text-gray-200 rounded-xl px-4 py-3 text-sm focus:border-[#E81F27] focus:outline-none">
            @error('password') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-gray-300 text-sm mb-1.5" for="password_confirmation">Confirm New Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                class="w-full bg-[#0D1B2A] border border-gray-700 text-gray-200 rounded-xl px-4 py-3 text-sm focus:border-[#E81F27] focus:outline-none">
        </div>

        <button type="submit" class="w-full py-3 bg-[#E81F27] text-white font-semibold rounded-xl hover:bg-red-700 transition-colors">
            Reset Password
        </button>
    </form>
</x-guest-layout>
