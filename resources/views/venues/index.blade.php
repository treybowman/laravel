<x-app-layout>
    <x-slot name="title">Venues — {{ setting('platform.site_name', 'ATL Ticket Exchange') }}</x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="font-['Bebas_Neue'] text-5xl text-white mb-2">Atlanta Venues</h1>
        <p class="text-gray-400 mb-8">Browse tickets by venue</p>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($venues as $venue)
            <a href="{{ route('venues.show', $venue->slug) }}" class="block bg-[#0a1520] border border-gray-800 rounded-2xl overflow-hidden hover:border-[#E81F27]/50 transition-all group">
                @if($venue->image_url)
                <div class="h-40 bg-cover bg-center" style="background-image: url('{{ $venue->image_url }}')"></div>
                @else
                <div class="h-40 bg-gradient-to-br from-gray-900 to-[#0D1B2A] flex items-center justify-center">
                    <span class="font-['Bebas_Neue'] text-5xl text-gray-700">{{ strtoupper(substr($venue->name, 0, 2)) }}</span>
                </div>
                @endif
                <div class="p-5">
                    <h3 class="font-['Bebas_Neue'] text-2xl text-white mb-1 group-hover:text-[#E81F27] transition-colors">{{ $venue->name }}</h3>
                    <p class="text-gray-500 text-sm">{{ $venue->address }}</p>
                    @if($venue->capacity)
                    <p class="text-gray-600 text-xs mt-1">Capacity: {{ number_format($venue->capacity) }}</p>
                    @endif
                </div>
            </a>
            @endforeach
        </div>
    </div>
</x-app-layout>
