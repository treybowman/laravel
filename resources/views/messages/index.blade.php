<x-app-layout>
    <x-slot name="title">Messages — {{ setting('platform.site_name', 'ATL Ticket Exchange') }}</x-slot>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{ activeId: null }">
        <h1 class="font-['Bebas_Neue'] text-4xl text-white mb-6">Messages</h1>

        @if($conversations->isEmpty())
        <div class="text-center py-16 bg-[#0a1520] border border-gray-800 rounded-xl">
            <p class="text-gray-500 text-lg mb-2">No conversations yet.</p>
            <p class="text-gray-600 text-sm">Contact a seller by clicking "I'm Interested" on any listing.</p>
        </div>
        @else
        <div class="space-y-2">
            @foreach($conversations as $conversation)
            @php
                $user = auth()->user();
                $isBuyer = $conversation->buyer_id === $user->id;
                $other = $isBuyer ? $conversation->seller : $conversation->buyer;
                $unread = $isBuyer ? $conversation->buyer_unread : $conversation->seller_unread;
                $lastMsg = $conversation->messages->first();
            @endphp
            <a href="{{ route('messages.show', $conversation) }}" class="flex items-center gap-4 bg-[#0a1520] border {{ $unread > 0 ? 'border-[#E81F27]/40' : 'border-gray-800' }} rounded-xl p-4 hover:border-gray-600 transition-colors group">
                <div class="w-12 h-12 rounded-full bg-[#E81F27]/20 flex items-center justify-center text-[#E81F27] font-bold text-lg shrink-0">
                    {{ strtoupper(substr($other->name ?? '?', 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-0.5">
                        <p class="text-white font-semibold text-sm">{{ $other->name }}</p>
                        @if($unread > 0)
                        <span class="bg-[#E81F27] text-white text-xs font-bold px-1.5 py-0.5 rounded-full">{{ $unread }}</span>
                        @endif
                        <span class="ml-auto text-gray-600 text-xs">{{ $conversation->updated_at?->diffForHumans() }}</span>
                    </div>
                    <p class="text-gray-400 text-xs truncate">{{ $conversation->listing?->title }}</p>
                    @if($lastMsg)
                    <p class="text-gray-500 text-xs truncate mt-0.5">{{ Str::limit($lastMsg->body, 60) }}</p>
                    @endif
                </div>
            </a>
            @endforeach
        </div>
        @endif
    </div>
</x-app-layout>
