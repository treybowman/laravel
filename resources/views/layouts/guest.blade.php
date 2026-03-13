<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? setting('platform.site_name', 'ATL Ticket Exchange') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,400&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#0D1B2A] text-gray-200 font-sans antialiased min-h-screen flex flex-col items-center justify-center">

    <div class="w-full max-w-md px-4 py-8">
        <!-- Logo -->
        <div class="text-center mb-8">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2">
                <span class="font-['Bebas_Neue'] text-4xl text-[#E81F27] leading-none">ATL</span>
                <span class="font-['Bebas_Neue'] text-2xl text-white leading-none tracking-widest">TICKET EXCHANGE</span>
            </a>
            <p class="text-gray-400 text-sm mt-2">{{ setting('platform.tagline', "Atlanta's Peer-to-Peer Ticket Marketplace") }}</p>
        </div>

        <!-- Card -->
        <div class="bg-[#0a1520] border border-gray-800 rounded-2xl p-8 shadow-2xl">
            {{ $slot }}
        </div>

        <!-- Back to home -->
        <div class="text-center mt-6">
            <a href="{{ route('home') }}" class="text-gray-500 hover:text-gray-300 text-sm transition-colors">&larr; Back to home</a>
        </div>
    </div>

</body>
</html>
