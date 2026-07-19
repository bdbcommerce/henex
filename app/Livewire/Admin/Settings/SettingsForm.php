<?php
namespace App\Livewire\Admin\Settings;
use App\Models\Setting;
use Livewire\Component;
class SettingsForm extends Component
{
    public array $settings = [];
    protected array $keys = [
        // Contacts
        'site_phone', 'site_phone2', 'site_email', 'working_hours',
        'address_uz', 'address_ru', 'address_en',
        // Social / messaging
        'social_telegram', 'social_whatsapp', 'social_instagram', 'social_youtube',
        // Integrations
        'yandex_map_embed',
        // Analytics
        'analytics_ga4_id', 'analytics_yandex_id',
        // Search console verification
        'google_verification', 'yandex_verification',
    ];
    public function mount(): void
    {
        foreach ($this->keys as $key) {
            $this->settings[$key] = Setting::find($key)?->value ?? '';
        }
    }
    public function save(): void
    {
        foreach ($this->keys as $key) {
            Setting::updateOrCreate(['key' => $key], ['value' => $this->settings[$key] ?? '']);
            cache()->forget("setting_{$key}");
        }
        session()->flash('success', 'Settings saved.');
    }
    public function render()
    {
        return view('livewire.admin.settings.form')->layout('layouts.admin');
    }
}
