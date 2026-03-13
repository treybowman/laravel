<x-app-layout>
    <x-slot name="title">Announcements — {{ setting('platform.site_name', 'ATL Ticket Exchange') }}</x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="font-['Bebas_Neue'] text-5xl text-white mb-8">Announcements</h1>

        @if($announcements->isEmpty())
        <p class="text-gray-500">No announcements yet.</p>
        @else
        <div class="space-y-4">
            @foreach($announcements as $announcement)
            <div class="bg-[#0a1520] border {{ $announcement->is_pinned ? 'border-[#E81F27]/40' : 'border-gray-800' }} rounded-xl p-6">
                <div class="flex items-start gap-3 mb-3">
                    @if($announcement->is_pinned)
                    <span class="bg-[#E81F27]/10 text-[#E81F27] text-xs font-bold px-2 py-0.5 rounded uppercase shrink-0">📌 Pinned</span>
                    @endif
                    <div class="flex-1">
                        <h2 class="text-white font-semibold text-xl mb-1">{{ $announcement->title }}</h2>
                        <p class="text-gray-500 text-xs">{{ $announcement->published_at?->format('F j, Y') }}</p>
                    </div>
                </div>
                <div class="text-gray-300 text-sm leading-relaxed prose prose-invert max-w-none">
                    {!! $announcement->body !!}
                </div>
            </div>
            @endforeach
        </div>
        {{ $announcements->links() }}
        @endif
    </div>
</x-app-layout>
