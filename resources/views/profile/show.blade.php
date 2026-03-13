<x-app-layout>
    <x-slot name="title">{{ $user->name }} (@{{ $user->username }}) — {{ setting('platform.site_name', 'ATL Ticket Exchange') }}</x-slot>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Profile Header -->
        <div class="bg-[#0a1520] border border-gray-800 rounded-2xl p-8 mb-8">
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6">
                <div class="w-20 h-20 rounded-full bg-[#E81F27]/20 flex items-center justify-center text-[#E81F27] font-bold text-3xl shrink-0">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div class="flex-1">
                    <div class="flex items-start flex-wrap gap-2 mb-1">
                        <h1 class="font-['Bebas_Neue'] text-3xl text-white">{{ $user->name }}</h1>
                        @if($user->is_verified_sth)
                        <span class="bg-yellow-900/30 border border-yellow-700/40 text-yellow-400 text-xs font-bold px-2 py-0.5 rounded-full mt-1">⭐ Verified STH</span>
                        @endif
                        @if($user->trust_level === 'power_seller')
                        <span class="bg-purple-900/30 border border-purple-700/40 text-purple-400 text-xs font-bold px-2 py-0.5 rounded-full mt-1">🏆 Power Seller</span>
                        @elseif($user->trust_level === 'trusted')
                        <span class="bg-green-900/30 border border-green-700/40 text-green-400 text-xs font-bold px-2 py-0.5 rounded-full mt-1">✓ Trusted</span>
                        @endif
                        @if($user->probation)
                        <span class="bg-orange-900/30 border border-orange-700/40 text-orange-400 text-xs font-bold px-2 py-0.5 rounded-full mt-1">New Member</span>
                        @endif
                    </div>
                    <p class="text-gray-500 text-sm mb-2">@{{ $user->username }}</p>
                    @if($user->bio)
                    <p class="text-gray-300 text-sm">{{ $user->bio }}</p>
                    @endif
                </div>
                <div class="grid grid-cols-2 gap-4 sm:text-right">
                    <div class="bg-[#0D1B2A] rounded-xl p-4 text-center">
                        <p class="font-['Bebas_Neue'] text-4xl text-[#E81F27]">{{ $user->bstScore() }}%</p>
                        <p class="text-gray-500 text-xs uppercase tracking-wider">BST Score</p>
                    </div>
                    <div class="bg-[#0D1B2A] rounded-xl p-4 text-center">
                        <p class="font-['Bebas_Neue'] text-4xl text-white">{{ $user->bstTransactionCount() }}</p>
                        <p class="text-gray-500 text-xs uppercase tracking-wider">Transactions</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Active Listings -->
            <div class="lg:col-span-2">
                <h2 class="font-['Bebas_Neue'] text-2xl text-white mb-4">Active Listings</h2>
                @if($listings->isEmpty())
                <p class="text-gray-500 text-sm">No active listings.</p>
                @else
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    @foreach($listings as $listing)
                        @include('listings._card', ['listing' => $listing])
                    @endforeach
                </div>
                {{ $listings->links() }}
                @endif
            </div>

            <!-- Feedback -->
            <div>
                <h2 class="font-['Bebas_Neue'] text-2xl text-white mb-4">Feedback</h2>
                @if($feedbacks->isEmpty())
                <p class="text-gray-500 text-sm">No feedback yet.</p>
                @else
                <div class="space-y-3">
                    @foreach($feedbacks as $feedback)
                    <div class="bg-[#0a1520] border border-gray-800 rounded-xl p-4">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="{{ match($feedback->rating) {
                                'positive' => 'text-green-400',
                                'negative' => 'text-red-400',
                                default => 'text-yellow-400',
                            } }} text-sm">{{ match($feedback->rating) {
                                'positive' => '👍 Positive',
                                'negative' => '👎 Negative',
                                default => '😐 Neutral',
                            } }}</span>
                            <span class="ml-auto text-gray-600 text-xs">{{ $feedback->created_at?->diffForHumans() }}</span>
                        </div>
                        @if($feedback->comment)
                        <p class="text-gray-300 text-sm">{{ $feedback->comment }}</p>
                        @endif
                        <div class="flex items-center gap-2 mt-2">
                            <a href="{{ route('profile.show', $feedback->reviewer->username) }}" class="text-gray-500 text-xs hover:text-[#E81F27]">
                                @{{ $feedback->reviewer->username }}
                            </a>
                            <span class="text-gray-700 text-xs">· {{ ucfirst($feedback->transaction_type) }}</span>
                            @if($feedback->listing?->event)
                            <span class="text-gray-700 text-xs">· {{ $feedback->listing->event->name }}</span>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
