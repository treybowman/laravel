<x-app-layout>
    <x-slot name="title">Conversation — {{ setting('platform.site_name', 'ATL Ticket Exchange') }}</x-slot>

    @php
        $user = auth()->user();
        $isBuyer = $conversation->buyer_id === $user->id;
        $other = $isBuyer ? $conversation->seller : $conversation->buyer;
    @endphp

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="flex items-center gap-4 mb-6">
            <a href="{{ route('messages.index') }}" class="text-gray-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-[#E81F27]/20 flex items-center justify-center text-[#E81F27] font-bold">
                    {{ strtoupper(substr($other->name ?? '?', 0, 1)) }}
                </div>
                <div>
                    <p class="text-white font-semibold">{{ $other->name }}</p>
                    <p class="text-gray-500 text-xs">@{{ $other->username }}</p>
                </div>
            </div>
        </div>

        <!-- Listing Reference -->
        <div class="bg-[#0a1520] border border-gray-800 rounded-xl p-4 mb-4">
            <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Regarding</p>
            <a href="{{ route('listings.show', $conversation->listing) }}" class="text-[#E81F27] text-sm font-semibold hover:underline">
                {{ $conversation->listing?->title }}
            </a>
            <p class="text-gray-500 text-xs mt-0.5">${{ number_format($conversation->listing?->asking_price, 2) }} · {{ $conversation->listing?->event?->name }}</p>
        </div>

        <!-- Messages -->
        <div class="bg-[#0a1520] border border-gray-800 rounded-xl p-4 mb-4 max-h-96 overflow-y-auto flex flex-col gap-3" id="messages-container">
            @if($messages->isEmpty())
            <p class="text-gray-500 text-sm text-center py-8">No messages yet. Start the conversation!</p>
            @endif
            @foreach($messages as $message)
            @php $isOwn = $message->sender_id === $user->id; @endphp
            <div class="flex {{ $isOwn ? 'justify-end' : 'justify-start' }}">
                <div class="max-w-sm">
                    <div class="{{ $isOwn ? 'bg-[#E81F27] text-white' : 'bg-gray-800 text-gray-200' }} rounded-2xl {{ $isOwn ? 'rounded-br-sm' : 'rounded-bl-sm' }} px-4 py-3 text-sm">
                        {{ $message->body }}
                    </div>
                    <p class="text-gray-600 text-xs mt-1 {{ $isOwn ? 'text-right' : 'text-left' }}">
                        {{ $message->created_at?->format('g:i A · M j') }}
                        @if($isOwn && $message->read_at)
                        · Read
                        @endif
                    </p>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Reply Form -->
        <form method="POST" action="{{ route('messages.store', $conversation) }}">
            @csrf
            <div class="flex gap-3">
                <textarea name="body" rows="2" required maxlength="2000"
                    placeholder="Type your message..."
                    class="flex-1 bg-[#0a1520] border border-gray-700 text-gray-300 rounded-xl px-4 py-3 text-sm focus:border-[#E81F27] focus:outline-none resize-none"
                    x-data @keydown.enter.prevent.meta="$el.closest('form').submit()"></textarea>
                <button type="submit" class="px-4 py-3 bg-[#E81F27] text-white rounded-xl hover:bg-red-700 transition-colors shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                </button>
            </div>
            @error('body') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </form>
    </div>

    @push('scripts')
    <script>
        // Scroll messages to bottom on load
        const container = document.getElementById('messages-container');
        if (container) container.scrollTop = container.scrollHeight;
    </script>
    @endpush
</x-app-layout>
