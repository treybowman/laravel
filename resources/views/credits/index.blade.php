<x-app-layout>
    <x-slot name="title">Credits — {{ setting('platform.site_name', 'ATL Ticket Exchange') }}</x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="font-['Bebas_Neue'] text-4xl text-white">Credits</h1>
            <div class="bg-[#0a1520] border border-[#E81F27]/30 rounded-xl px-5 py-3 text-center">
                <p class="font-['Bebas_Neue'] text-3xl text-[#E81F27]">{{ auth()->user()->credits_balance }}</p>
                <p class="text-gray-500 text-xs uppercase tracking-wider">Balance</p>
            </div>
        </div>

        <p class="text-gray-400 mb-8">Use credits to feature your listings and boost visibility.</p>

        <!-- Packages -->
        <section class="mb-10">
            <h2 class="font-['Bebas_Neue'] text-2xl text-white mb-4">Buy Credits</h2>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4" id="packages-container">
                @foreach($packages as $package)
                <div class="bg-[#0a1520] border border-gray-800 rounded-2xl p-6 hover:border-[#E81F27]/50 transition-all">
                    <h3 class="font-['Bebas_Neue'] text-2xl text-white mb-1">{{ $package->name }}</h3>
                    <p class="font-['Bebas_Neue'] text-4xl text-[#E81F27] mb-1">{{ $package->credits }} <span class="text-xl text-gray-400">credits</span></p>
                    <p class="text-gray-400 text-2xl font-bold mb-4">${{ number_format($package->price_cents / 100, 2) }}</p>
                    <button
                        onclick="purchasePackage({{ $package->id }}, {{ $package->price_cents }}, '{{ $package->name }}')"
                        class="w-full py-3 bg-[#E81F27] text-white font-semibold rounded-xl hover:bg-red-700 transition-colors">
                        Buy Now
                    </button>
                </div>
                @endforeach
            </div>
        </section>

        <!-- Promo Code -->
        <section class="mb-10">
            <h2 class="font-['Bebas_Neue'] text-2xl text-white mb-4">Redeem Promo Code</h2>
            <form method="POST" action="{{ route('credits.redeem') }}" class="flex gap-3 max-w-sm">
                @csrf
                <input type="text" name="code" placeholder="Enter promo code" required
                    class="flex-1 bg-[#0a1520] border border-gray-700 text-gray-200 rounded-xl px-4 py-3 text-sm focus:border-[#E81F27] focus:outline-none uppercase">
                <button type="submit" class="px-5 py-3 border border-[#E81F27] text-[#E81F27] font-semibold rounded-xl hover:bg-[#E81F27] hover:text-white transition-colors text-sm">
                    Redeem
                </button>
            </form>
        </section>

        <!-- Transaction History -->
        <section>
            <h2 class="font-['Bebas_Neue'] text-2xl text-white mb-4">Transaction History</h2>
            @if($transactions->isEmpty())
            <p class="text-gray-500 text-sm">No transactions yet.</p>
            @else
            <div class="bg-[#0a1520] border border-gray-800 rounded-xl overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="border-b border-gray-800">
                        <tr>
                            <th class="text-left text-gray-500 text-xs uppercase tracking-wider px-5 py-3">Type</th>
                            <th class="text-left text-gray-500 text-xs uppercase tracking-wider px-5 py-3">Notes</th>
                            <th class="text-right text-gray-500 text-xs uppercase tracking-wider px-5 py-3">Credits</th>
                            <th class="text-right text-gray-500 text-xs uppercase tracking-wider px-5 py-3">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transactions as $tx)
                        <tr class="border-b border-gray-800/50 hover:bg-gray-900/30">
                            <td class="px-5 py-3 text-gray-300">{{ ucfirst(str_replace('_', ' ', $tx->type)) }}</td>
                            <td class="px-5 py-3 text-gray-500">{{ $tx->notes ?? '—' }}</td>
                            <td class="px-5 py-3 text-right {{ $tx->amount > 0 ? 'text-green-400' : 'text-red-400' }} font-semibold">
                                {{ $tx->amount > 0 ? '+' : '' }}{{ $tx->amount }}
                            </td>
                            <td class="px-5 py-3 text-right text-gray-600">{{ $tx->created_at?->format('M j, Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $transactions->links() }}</div>
            @endif
        </section>
    </div>
</x-app-layout>
