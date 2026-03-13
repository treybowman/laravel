<x-filament-panels::page>
    <div class="space-y-6">
        @php $groups = $this->getGroups(); @endphp

        @foreach($groups as $group)
        @php $groupSettings = $this->getGroupSettings($group); @endphp
        <x-filament::section :collapsible="true">
            <x-slot name="heading">{{ ucfirst($group) }} Settings</x-slot>

            <div class="space-y-4">
                @foreach($groupSettings as $setting)
                <div class="grid grid-cols-3 gap-4 items-start">
                    <div>
                        <p class="font-medium text-sm">{{ $setting->label }}</p>
                        @if($setting->description)
                        <p class="text-xs text-gray-500 mt-0.5">{{ $setting->description }}</p>
                        @endif
                        <p class="text-xs text-gray-600 font-mono mt-1">{{ $setting->key }}</p>
                    </div>
                    <div class="col-span-2">
                        @if($setting->type === 'boolean')
                        <input type="checkbox" wire:model="settingsData.{{ $setting->key }}"
                            class="rounded border-gray-600 bg-gray-800"
                            {{ ($settingsData[$setting->key] ?? false) ? 'checked' : '' }}>
                        @elseif($setting->type === 'integer')
                        <input type="number" wire:model="settingsData.{{ $setting->key }}"
                            value="{{ $settingsData[$setting->key] ?? '' }}"
                            class="w-full rounded-lg border-gray-700 bg-gray-900 text-sm px-3 py-2">
                        @else
                        <input type="text" wire:model="settingsData.{{ $setting->key }}"
                            value="{{ $settingsData[$setting->key] ?? '' }}"
                            class="w-full rounded-lg border-gray-700 bg-gray-900 text-sm px-3 py-2">
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

            <x-slot name="footerActions">
                <x-filament::button wire:click="saveGroup('{{ $group }}')">
                    Save {{ ucfirst($group) }} Settings
                </x-filament::button>
            </x-slot>
        </x-filament::section>
        @endforeach
    </div>
</x-filament-panels::page>
