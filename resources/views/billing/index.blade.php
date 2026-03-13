<x-app-layout>
    <x-slot name="title">Billing — {{ setting('platform.site_name', 'ATL Ticket Exchange') }}</x-slot>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="font-['Bebas_Neue'] text-4xl text-white mb-2">Billing & Subscription</h1>
        <p class="text-gray-400 mb-8">Upgrade for more listings and features.</p>

        <!-- Current Plan -->
        <div class="bg-[#0a1520] border border-gray-800 rounded-2xl p-6 mb-8">
            <h2 class="text-white font-semibold mb-4">Current Plan</h2>
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-full {{ match($user->subscription_tier) {
                    'pro' => 'bg-purple-900/40 border border-purple-700/50',
                    'member' => 'bg-blue-900/40 border border-blue-700/50',
                    default => 'bg-gray-800'
                } }} flex items-center justify-center">
                    <span class="text-xl">{{ match($user->subscription_tier) { 'pro' => '👑', 'member' => '⭐', default => '🎟' } }}</span>
                </div>
                <div>
                    <p class="text-white font-semibold text-lg">{{ ucfirst($user->subscription_tier) }} Plan</p>
                    @if($subscription)
                    <p class="text-gray-500 text-sm">Renews {{ $subscription->ends_at?->format('M j, Y') ?? 'monthly' }}</p>
                    @else
                    <p class="text-gray-500 text-sm">Free tier — limited features</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Plan Comparison -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <!-- Free -->
            <div class="bg-[#0a1520] border {{ $user->subscription_tier === 'free' ? 'border-gray-600' : 'border-gray-800' }} rounded-2xl p-5">
                <h3 class="font-['Bebas_Neue'] text-2xl text-white mb-1">Free</h3>
                <p class="text-3xl font-bold text-white mb-4">$0<span class="text-gray-500 text-base font-normal">/mo</span></p>
                <ul class="space-y-2 text-sm text-gray-400">
                    <li>✓ 1 active listing at a time</li>
                    <li>✓ Community messaging</li>
                    <li>✓ Basic BST profile</li>
                    <li class="text-gray-600">✗ Saved searches</li>
                </ul>
                @if($user->subscription_tier === 'free')
                <div class="mt-5 py-2 text-center text-gray-500 text-sm border border-gray-700 rounded-lg">Current Plan</div>
                @endif
            </div>

            <!-- Member -->
            <div class="bg-[#0a1520] border {{ $user->subscription_tier === 'member' ? 'border-blue-500' : 'border-gray-800' }} rounded-2xl p-5">
                <div class="flex items-center justify-between mb-1">
                    <h3 class="font-['Bebas_Neue'] text-2xl text-white">Member</h3>
                    <span class="bg-blue-900/30 text-blue-400 text-xs font-bold px-2 py-0.5 rounded-full">Popular</span>
                </div>
                <p class="text-3xl font-bold text-white mb-4">$9<span class="text-gray-500 text-base font-normal">/mo</span></p>
                <ul class="space-y-2 text-sm text-gray-400">
                    <li>✓ Unlimited listings</li>
                    <li>✓ 5 saved searches</li>
                    <li>✓ Priority messaging</li>
                    <li>✓ Member badge</li>
                </ul>
                @if($user->subscription_tier === 'member')
                <div class="mt-5 py-2 text-center text-blue-400 text-sm border border-blue-700/50 rounded-lg">Current Plan</div>
                @elseif(!empty($memberPriceId))
                <button class="mt-5 w-full py-3 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition-colors text-sm">Upgrade to Member</button>
                @endif
            </div>

            <!-- Pro -->
            <div class="bg-[#0a1520] border {{ $user->subscription_tier === 'pro' ? 'border-[#E81F27]' : 'border-gray-800' }} rounded-2xl p-5">
                <div class="flex items-center justify-between mb-1">
                    <h3 class="font-['Bebas_Neue'] text-2xl text-white">Pro</h3>
                    <span class="bg-[#E81F27]/20 text-[#E81F27] text-xs font-bold px-2 py-0.5 rounded-full">Best</span>
                </div>
                <p class="text-3xl font-bold text-white mb-4">$19<span class="text-gray-500 text-base font-normal">/mo</span></p>
                <ul class="space-y-2 text-sm text-gray-400">
                    <li>✓ Everything in Member</li>
                    <li>✓ Unlimited saved searches</li>
                    <li>✓ Featured listing discounts</li>
                    <li>✓ Pro seller badge</li>
                </ul>
                @if($user->subscription_tier === 'pro')
                <div class="mt-5 py-2 text-center text-[#E81F27] text-sm border border-[#E81F27]/30 rounded-lg">Current Plan</div>
                @elseif(!empty($proPriceId))
                <button class="mt-5 w-full py-3 bg-[#E81F27] text-white font-semibold rounded-xl hover:bg-red-700 transition-colors text-sm">Upgrade to Pro</button>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
