<x-app-layout>
    <x-slot name="title">Leave Feedback — {{ setting('platform.site_name', 'ATL Ticket Exchange') }}</x-slot>

    <div class="max-w-lg mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="font-['Bebas_Neue'] text-4xl text-white mb-2">Leave Feedback</h1>
        <p class="text-gray-400 mb-6">Rate your experience with this transaction.</p>

        <!-- Transaction Info -->
        <div class="bg-[#0a1520] border border-gray-800 rounded-xl p-5 mb-6">
            <p class="text-gray-500 text-xs uppercase tracking-wider mb-2">Transaction</p>
            <p class="text-white font-semibold">{{ $feedbackRequest->listing?->event?->name }}</p>
            <p class="text-gray-400 text-sm">{{ $feedbackRequest->listing?->title }}</p>
            <div class="mt-3 flex items-center gap-4">
                <div>
                    <p class="text-gray-600 text-xs">Seller</p>
                    <a href="{{ route('profile.show', $feedbackRequest->seller->username) }}" class="text-gray-300 text-sm hover:text-[#E81F27]">@{{ $feedbackRequest->seller->username }}</a>
                </div>
                <span class="text-gray-700">→</span>
                <div>
                    <p class="text-gray-600 text-xs">Buyer</p>
                    <a href="{{ route('profile.show', $feedbackRequest->buyer->username) }}" class="text-gray-300 text-sm hover:text-[#E81F27]">@{{ $feedbackRequest->buyer->username }}</a>
                </div>
            </div>
            <p class="text-gray-600 text-xs mt-3">Feedback window closes {{ $feedbackRequest->expires_at?->format('M j, Y') }}</p>
        </div>

        @if($alreadyGiven)
        <div class="bg-green-950 border border-green-800 rounded-xl p-5 text-center">
            <p class="text-green-400 font-semibold">✓ You have already submitted feedback for this transaction.</p>
        </div>
        @else
        <form method="POST" action="{{ route('feedback.store', $feedbackRequest->token) }}">
            @csrf
            <div class="bg-[#0a1520] border border-gray-800 rounded-xl p-6 space-y-5">
                <!-- Rating -->
                <div>
                    <label class="block text-white font-semibold mb-3">Your Rating</label>
                    <div class="grid grid-cols-3 gap-3">
                        @foreach(['positive' => ['label' => '👍 Positive', 'color' => 'green'], 'neutral' => ['label' => '😐 Neutral', 'color' => 'yellow'], 'negative' => ['label' => '👎 Negative', 'color' => 'red']] as $value => $info)
                        <label class="flex flex-col items-center gap-2 p-3 border border-gray-700 rounded-xl cursor-pointer hover:border-gray-500 has-[:checked]:border-{{ $info['color'] }}-500 has-[:checked]:bg-{{ $info['color'] }}-900/20 transition-colors">
                            <input type="radio" name="rating" value="{{ $value }}" required class="sr-only">
                            <span class="text-2xl">{{ substr($info['label'], 0, 2) }}</span>
                            <span class="text-gray-300 text-xs font-semibold">{{ substr($info['label'], 3) }}</span>
                        </label>
                        @endforeach
                    </div>
                    @error('rating') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Comment -->
                <div>
                    <label class="block text-white font-semibold mb-2">Comment <span class="text-gray-500 font-normal">(optional)</span></label>
                    <textarea name="comment" rows="4" maxlength="1000"
                        placeholder="Describe your experience..."
                        class="w-full bg-[#0D1B2A] border border-gray-700 text-gray-300 rounded-xl px-4 py-3 text-sm focus:border-[#E81F27] focus:outline-none">{{ old('comment') }}</textarea>
                </div>
            </div>

            <div class="mt-6">
                <button type="submit" class="w-full py-4 bg-[#E81F27] text-white font-semibold rounded-xl hover:bg-red-700 transition-colors text-lg">
                    Submit Feedback
                </button>
            </div>
        </form>
        @endif
    </div>
</x-app-layout>
