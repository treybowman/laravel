<x-guest-layout>
    <h2 class="font-['Bebas_Neue'] text-3xl text-white text-center mb-6">Sign In</h2>

    @if(session('status'))
    <div class="bg-green-900 border border-green-700 text-green-200 text-sm px-4 py-3 rounded-lg mb-4">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <div>
            <label class="block text-gray-300 text-sm mb-1.5" for="email">Email Address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                class="w-full bg-[#0D1B2A] border {{ $errors->has('email') ? 'border-red-500' : 'border-gray-700' }} text-gray-200 rounded-xl px-4 py-3 text-sm focus:border-[#E81F27] focus:outline-none">
            @error('email') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label class="text-gray-300 text-sm" for="password">Password</label>
                <a href="{{ route('password.request') }}" class="text-[#E81F27] text-xs hover:underline">Forgot password?</a>
            </div>
            <input id="password" type="password" name="password" required autocomplete="current-password"
                class="w-full bg-[#0D1B2A] border {{ $errors->has('password') ? 'border-red-500' : 'border-gray-700' }} text-gray-200 rounded-xl px-4 py-3 text-sm focus:border-[#E81F27] focus:outline-none">
            @error('password') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-center gap-2">
            <input type="checkbox" id="remember" name="remember" class="rounded border-gray-600 bg-gray-800 accent-[#E81F27]">
            <label for="remember" class="text-gray-400 text-sm">Remember me</label>
        </div>

        <button type="submit" class="w-full py-3 bg-[#E81F27] text-white font-semibold rounded-xl hover:bg-red-700 transition-colors">
            Sign In
        </button>
    </form>

    <p class="text-center text-gray-500 text-sm mt-6">
        Don't have an account? <a href="{{ route('register') }}" class="text-[#E81F27] hover:underline font-medium">Join Free</a>
    </p>
</x-guest-layout>
