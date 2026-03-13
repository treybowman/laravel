<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use App\Models\SettingsAuditLog;
use App\Services\SettingsService;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class SettingsPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static string $view = 'filament.pages.settings-page';
    protected static ?string $navigationLabel = 'Settings';
    protected static ?string $navigationGroup = 'System';
    protected static ?int $navigationSort = 1;
    protected static ?string $slug = 'settings';

    public array $settingsData = [];

    public function mount(): void
    {
        $this->loadSettings();
    }

    private function loadSettings(): void
    {
        $settings = Setting::all();
        foreach ($settings as $setting) {
            $this->settingsData[$setting->key] = $setting->value;
        }
    }

    public function saveGroup(string $group): void
    {
        $settings = Setting::where('group', $group)->get();

        foreach ($settings as $setting) {
            $key = $setting->key;
            if (array_key_exists($key, $this->settingsData)) {
                $oldValue = $setting->value;
                $newValue = $this->settingsData[$key];

                if ($oldValue !== $newValue) {
                    $setting->update(['value' => $newValue]);

                    SettingsAuditLog::create([
                        'admin_id' => auth()->id(),
                        'setting_key' => $key,
                        'old_value' => $oldValue,
                        'new_value' => $newValue,
                        'ip_address' => request()->ip(),
                        'created_at' => now(),
                    ]);
                }
            }
        }

        app(SettingsService::class)->flush();

        Notification::make()
            ->title("Settings saved for group: {$group}")
            ->success()
            ->send();
    }

    public function getGroupSettings(string $group): \Illuminate\Support\Collection
    {
        return Setting::where('group', $group)->get();
    }

    public function getGroups(): array
    {
        return Setting::distinct()->pluck('group')->toArray();
    }
}
