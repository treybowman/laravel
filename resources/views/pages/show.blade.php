<x-app-layout>
    <x-slot name="title">{{ $page->meta_title ?? $page->title }} — {{ setting('platform.site_name', 'ATL Ticket Exchange') }}</x-slot>
    @if($page->meta_description)
    <x-slot name="description">{{ $page->meta_description }}</x-slot>
    @endif

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h1 class="font-['Bebas_Neue'] text-5xl text-white mb-8">{{ $page->title }}</h1>
        <div class="prose prose-invert prose-sm max-w-none text-gray-300 leading-relaxed">
            {!! $page->body !!}
        </div>
    </div>
</x-app-layout>
