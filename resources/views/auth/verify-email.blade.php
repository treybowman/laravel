<x-guest-layout>
    <h2 class="font-['Bebas_Neue'] text-3xl text-white text-center mb-4">Verify Your Email</h2>

    <p class="text-gray-400 text-sm text-center mb-6">
        Thanks for signing up! Before getting started, please verify your email address by clicking the link we just sent you.
        If you didn't receive the email, we'll gladly send you another.
    </p>

    @if(session('status') === 'verification-link-sent')
    <div class="bg-green-900 border border-green-700 text-green-200 text-sm px-4 py-3 rounded-lg mb-4">
        A new verification link has been sent to the email address you provided during registration.
    </div>
    @endif

    <div class="space-y-3">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="w-full py-3 bg-[#E81F27] text-white font-semibold rounded-xl hover:bg-red-700 transition-colors">
                Resend Verification Email
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full py-3 bg-transparent border border-gray-600 text-gray-400 font-semibold rounded-xl hover:border-gray-500 hover:text-gray-300 transition-colors">
                Log Out
            </button>
        </form>
    </div>
</x-guest-layout>
