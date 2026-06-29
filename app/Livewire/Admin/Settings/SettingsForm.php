<?php
namespace App\Livewire\Admin\Settings;
use App\Models\Setting;
use Livewire\Component;
class SettingsForm extends Component
{
    public array $settings = [];
    protected array $keys = ['site_phone','site_phone2','site_email','address_uz','address_ru','address_en','working_hours','social_telegram','social_instagram','social_youtube','social_whatsapp','yandex_map_embed'];
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
