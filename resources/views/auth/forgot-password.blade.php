<x-guest-layout>
    <h2 class="font-['Bebas_Neue'] text-3xl text-white text-center mb-2">Forgot Password</h2>
    <p class="text-gray-400 text-sm text-center mb-6">Enter your email and we'll send you a reset link.</p>

    @if(session('status'))
    <div class="bg-green-900 border border-green-700 text-green-200 text-sm px-4 py-3 rounded-lg mb-4">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf

        <div>
            <label class="block text-gray-300 text-sm mb-1.5" for="email">Email Address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                class="w-full bg-[#0D1B2A] border {{ $errors->has('email') ? 'border-red-500' : 'border-gray-700' }} text-gray-200 rounded-xl px-4 py-3 text-sm focus:border-[#E81F27] focus:outline-none">
            @error('email') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <button type="submit" class="w-full py-3 bg-[#E81F27] text-white font-semibold rounded-xl hover:bg-red-700 transition-colors">
            Send Reset Link
        </button>
    </form>

    <p class="text-center text-gray-500 text-sm mt-6">
        Remember your password? <a href="{{ route('login') }}" class="text-[#E81F27] hover:underline font-medium">Sign In</a>
    </p>
</x-guest-layout>
