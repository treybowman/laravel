<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Account Suspended — ATL Ticket Exchange</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@400;500&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
</head>
<body class="bg-[#0D1B2A] text-gray-200 font-sans min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full px-6 text-center">
        <div class="w-20 h-20 bg-red-950 border border-red-800 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
            </svg>
        </div>
        <h1 class="font-['Bebas_Neue'] text-5xl text-white mb-2">Account Suspended</h1>
        <p class="text-gray-400 mb-4">Your account has been suspended from ATL Ticket Exchange.</p>
        @if(session('ban_message'))
        <div class="bg-red-950 border border-red-800 rounded-xl p-4 mb-6 text-left">
            <p class="text-red-300 text-sm">{{ session('ban_message') }}</p>
        </div>
        @endif
        <p class="text-gray-500 text-sm">If you believe this is an error, please contact us.</p>
        <div class="mt-8">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="px-6 py-3 border border-gray-700 text-gray-400 rounded-xl hover:text-white hover:border-gray-500 transition-colors text-sm">
                    Sign Out
                </button>
            </form>
        </div>
    </div>
</body>
</html>
