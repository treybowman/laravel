<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? setting('platform.site_name', 'ATL Ticket Exchange') }}</title>
    <meta name="description" content="{{ $description ?? setting('platform.tagline', 'Atlanta\'s Peer-to-Peer Ticket Marketplace') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,400&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @filamentStyles
</head>
<body class="bg-[#0D1B2A] text-gray-200 font-sans antialiased min-h-screen flex flex-col">

    <!-- Navigation -->
    <nav class="bg-[#0a1520] border-b border-gray-800 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Logo -->
                <a href="{{ route('home') }}" class="flex items-center gap-2 shrink-0">
                    <span class="font-['Bebas_Neue'] text-3xl text-[#E81F27] leading-none">ATL</span>
                    <span class="font-['Bebas_Neue'] text-xl text-white leading-none tracking-widest">TICKET EXCHANGE</span>
                </a>

                <!-- Desktop Nav -->
                <div class="hidden md:flex items-center gap-6">
                    <a href="{{ route('listings.index') }}" class="text-gray-300 hover:text-white text-sm font-medium transition-colors">Browse Tickets</a>
                    <a href="{{ route('venues.index') }}" class="text-gray-300 hover:text-white text-sm font-medium transition-colors">Venues</a>

                    <!-- Teams Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="text-gray-300 hover:text-white text-sm font-medium flex items-center gap-1 transition-colors">
                            Teams
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" @click.outside="open = false" x-transition class="absolute top-8 left-0 w-48 bg-[#0D1B2A] border border-gray-700 rounded-lg shadow-xl z-50 py-1">
                            <a href="{{ route('teams.show', 'atlanta-braves') }}" class="block px-4 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-800">Atlanta Braves</a>
                            <a href="{{ route('teams.show', 'atlanta-hawks') }}" class="block px-4 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-800">Atlanta Hawks</a>
                            <a href="{{ route('teams.show', 'atlanta-falcons') }}" class="block px-4 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-800">Atlanta Falcons</a>
                            <a href="{{ route('teams.show', 'atlanta-united') }}" class="block px-4 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-800">Atlanta United</a>
                            <a href="{{ route('teams.show', 'georgia-bulldogs') }}" class="block px-4 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-800">Georgia Bulldogs</a>
                            <a href="{{ route('teams.show', 'georgia-tech') }}" class="block px-4 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-800">Georgia Tech</a>
                        </div>
                    </div>

                    <a href="{{ route('search') }}" class="text-gray-300 hover:text-white text-sm font-medium transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </a>
                </div>

                <!-- Right side -->
                <div class="flex items-center gap-4">
                    @auth
                        <!-- Messages icon with unread badge -->
                        <a href="{{ route('messages.index') }}" class="relative text-gray-300 hover:text-white transition-colors" x-data="{ count: 0 }" x-init="
                            async function fetchCount() {
                                try {
                                    const r = await fetch('{{ route('messages.unread') }}');
                                    const d = await r.json();
                                    count = d.count;
                                } catch(e) {}
                            }
                            fetchCount();
                            setInterval(fetchCount, 10000);
                        ">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                            <span x-show="count > 0" x-text="count" class="absolute -top-1 -right-1 bg-[#E81F27] text-white text-xs rounded-full w-4 h-4 flex items-center justify-center font-bold"></span>
                        </a>

                        <!-- User dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center gap-2 text-gray-300 hover:text-white transition-colors">
                                <div class="w-8 h-8 rounded-full bg-[#E81F27] flex items-center justify-center text-white font-bold text-sm">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="open" @click.outside="open = false" x-transition class="absolute right-0 top-10 w-56 bg-[#0D1B2A] border border-gray-700 rounded-lg shadow-xl z-50 py-1">
                                <div class="px-4 py-2 border-b border-gray-700">
                                    <p class="text-sm font-semibold text-white">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-gray-400">@{{ auth()->user()->username }}</p>
                                </div>
                                <a href="{{ route('profile.show', auth()->user()->username) }}" class="block px-4 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-800">My Profile</a>
                                <a href="{{ route('dashboard.listings') }}" class="block px-4 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-800">My Listings</a>
                                <a href="{{ route('credits.index') }}" class="block px-4 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-800">
                                    Credits
                                    <span class="ml-1 text-xs text-[#E81F27] font-bold">{{ auth()->user()->credits_balance }}</span>
                                </a>
                                <a href="{{ route('billing.index') }}" class="block px-4 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-800">Billing</a>
                                @if(auth()->user()->is_admin)
                                    <a href="/admin" class="block px-4 py-2 text-sm text-[#E81F27] hover:bg-gray-800">Admin Panel</a>
                                @endif
                                <div class="border-t border-gray-700 mt-1 pt-1">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-800">Sign Out</button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Sell tickets CTA -->
                        <a href="{{ route('listings.create') }}" class="hidden md:inline-flex items-center px-4 py-2 bg-[#E81F27] text-white text-sm font-semibold rounded-lg hover:bg-red-700 transition-colors">
                            + List Tickets
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-300 hover:text-white text-sm font-medium transition-colors">Sign In</a>
                        <a href="{{ route('register') }}" class="inline-flex items-center px-4 py-2 bg-[#E81F27] text-white text-sm font-semibold rounded-lg hover:bg-red-700 transition-colors">
                            Join Free
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="bg-green-900 border border-green-700 text-green-200 px-4 py-3 text-sm" role="alert">
            <div class="max-w-7xl mx-auto flex items-center gap-2">
                <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-950 border border-red-800 text-red-300 px-4 py-3 text-sm" role="alert">
            <div class="max-w-7xl mx-auto flex items-center gap-2">
                <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                {{ session('error') }}
            </div>
        </div>
    @endif

    <!-- Main Content -->
    <main class="flex-1">
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="bg-[#0a1520] border-t border-gray-800 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="md:col-span-2">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="font-['Bebas_Neue'] text-2xl text-[#E81F27]">ATL</span>
                        <span class="font-['Bebas_Neue'] text-lg text-white tracking-widest">TICKET EXCHANGE</span>
                    </div>
                    <p class="text-gray-400 text-sm leading-relaxed">
                        {{ setting('platform.tagline', "Atlanta's Peer-to-Peer Ticket Marketplace") }}. Connect directly with buyers and sellers. No middleman fees.
                    </p>
                </div>
                <div>
                    <h4 class="text-white font-semibold text-sm uppercase tracking-wider mb-3">Browse</h4>
                    <ul class="space-y-2">
                        <li><a href="{{ route('listings.index') }}" class="text-gray-400 hover:text-white text-sm transition-colors">All Listings</a></li>
                        <li><a href="{{ route('venues.index') }}" class="text-gray-400 hover:text-white text-sm transition-colors">Venues</a></li>
                        <li><a href="{{ route('announcements.index') }}" class="text-gray-400 hover:text-white text-sm transition-colors">Announcements</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-semibold text-sm uppercase tracking-wider mb-3">Info</h4>
                    <ul class="space-y-2">
                        <li><a href="/about" class="text-gray-400 hover:text-white text-sm transition-colors">About</a></li>
                        <li><a href="/how-it-works" class="text-gray-400 hover:text-white text-sm transition-colors">How It Works</a></li>
                        <li><a href="/faq" class="text-gray-400 hover:text-white text-sm transition-colors">FAQ</a></li>
                        <li><a href="/terms" class="text-gray-400 hover:text-white text-sm transition-colors">Terms of Service</a></li>
                        <li><a href="/privacy" class="text-gray-400 hover:text-white text-sm transition-colors">Privacy Policy</a></li>
                    </ul>
                </div>
            </div>
            <div class="mt-8 pt-8 border-t border-gray-800 text-center text-gray-500 text-xs">
                &copy; {{ date('Y') }} {{ setting('platform.site_name', 'ATL Ticket Exchange') }}. Not affiliated with any team, venue, or ticketing company.
            </div>
        </div>
    </footer>

    @filamentScripts
    @stack('scripts')
</body>
</html>
