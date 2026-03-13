<x-guest-layout>
    <h2 class="font-['Bebas_Neue'] text-3xl text-white text-center mb-6">Join ATL Ticket Exchange</h2>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <div>
            <label class="block text-gray-300 text-sm mb-1.5" for="name">Full Name</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                class="w-full bg-[#0D1B2A] border {{ $errors->has('name') ? 'border-red-500' : 'border-gray-700' }} text-gray-200 rounded-xl px-4 py-3 text-sm focus:border-[#E81F27] focus:outline-none">
            @error('name') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-gray-300 text-sm mb-1.5" for="username">Username</label>
            <input id="username" type="text" name="username" value="{{ old('username') }}" required
                placeholder="e.g. braves_fan_ATL"
                class="w-full bg-[#0D1B2A] border {{ $errors->has('username') ? 'border-red-500' : 'border-gray-700' }} text-gray-200 rounded-xl px-4 py-3 text-sm focus:border-[#E81F27] focus:outline-none">
            @error('username') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-gray-300 text-sm mb-1.5" for="email">Email Address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required
                class="w-full bg-[#0D1B2A] border {{ $errors->has('email') ? 'border-red-500' : 'border-gray-700' }} text-gray-200 rounded-xl px-4 py-3 text-sm focus:border-[#E81F27] focus:outline-none">
            @error('email') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-gray-300 text-sm mb-1.5" for="password">Password</label>
            <input id="password" type="password" name="password" required autocomplete="new-password"
                class="w-full bg-[#0D1B2A] border {{ $errors->has('password') ? 'border-red-500' : 'border-gray-700' }} text-gray-200 rounded-xl px-4 py-3 text-sm focus:border-[#E81F27] focus:outline-none">
            @error('password') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-gray-300 text-sm mb-1.5" for="password_confirmation">Confirm Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required
                class="w-full bg-[#0D1B2A] border border-gray-700 text-gray-200 rounded-xl px-4 py-3 text-sm focus:border-[#E81F27] focus:outline-none">
        </div>

        <p class="text-gray-600 text-xs">By registering you agree to our <a href="/terms" class="text-gray-400 hover:underline">Terms of Service</a> and <a href="/privacy" class="text-gray-400 hover:underline">Privacy Policy</a>.</p>

        <button type="submit" class="w-full py-3 bg-[#E81F27] text-white font-semibold rounded-xl hover:bg-red-700 transition-colors">
            Create Account
        </button>
    </form>

    <p class="text-center text-gray-500 text-sm mt-6">
        Already have an account? <a href="{{ route('login') }}" class="text-[#E81F27] hover:underline font-medium">Sign In</a>
    </p>
</x-guest-layout>
