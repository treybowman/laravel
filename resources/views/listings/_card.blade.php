<div class="bg-[#0a1520] border {{ $featured ?? false ? 'border-[#E81F27]/50' : 'border-gray-800' }} rounded-xl overflow-hidden hover:border-gray-600 transition-all group">
    @if($featured ?? false)
    <div class="bg-[#E81F27] text-white text-xs font-bold px-3 py-1 uppercase tracking-wider">
        ⭐ Featured
    </div>
    @endif
    <a href="{{ route('listings.show', $listing) }}" class="block p-4">
        <div class="flex items-start justify-between gap-2 mb-2">
            <h3 class="text-white font-semibold text-sm leading-snug group-hover:text-[#E81F27] transition-colors line-clamp-2">
                {{ $listing->title }}
            </h3>
            <span class="text-[#E81F27] font-bold text-lg whitespace-nowrap">${{ number_format($listing->asking_price, 2) }}</span>
        </div>
        <p class="text-gray-400 text-xs mb-1">{{ $listing->event->name ?? '' }}</p>
        <p class="text-gray-500 text-xs mb-3">{{ $listing->venue->name ?? '' }} • {{ $listing->event->event_date?->format('M d, Y') ?? '' }}</p>
        <div class="flex items-center gap-2 flex-wrap">
            <span class="bg-gray-800 text-gray-300 text-xs px-2 py-0.5 rounded">{{ $listing->quantity }} {{ Str::plural('ticket', $listing->quantity) }}</span>
            @if($listing->section)
            <span class="bg-gray-800 text-gray-300 text-xs px-2 py-0.5 rounded">Sec {{ $listing->section }}</span>
            @endif
            @if($listing->willing_to_trade)
            <span class="bg-blue-900/50 text-blue-400 text-xs px-2 py-0.5 rounded border border-blue-700/50">Trade OK</span>
            @endif
        </div>
        <div class="flex items-center gap-2 mt-3 pt-3 border-t border-gray-800/50">
            <div class="w-5 h-5 rounded-full bg-[#E81F27]/20 flex items-center justify-center text-[#E81F27] text-xs font-bold">
                {{ strtoupper(substr($listing->user->name ?? '?', 0, 1)) }}
            </div>
            <span class="text-gray-500 text-xs">@{{ $listing->user->username ?? '' }}</span>
            <span class="ml-auto text-gray-600 text-xs">{{ $listing->created_at?->diffForHumans() }}</span>
        </div>
    </a>
</div>
