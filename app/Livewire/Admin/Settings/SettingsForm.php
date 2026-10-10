<?php
namespace App\Livewire\Admin\Settings;
use App\Models\Setting;
use Livewire\Component;
class SettingsForm extends Component
{
    public array $settings = [];
    protected array $keys = self::KEYS;

    public const KEYS = [
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
        $this->dispatch('toast', message: 'Settings saved.', type: 'success');
    }

    /** Rebuild Laravel's config/route/event/view caches — for hosting without SSH. */
    public function rebuildCaches(): void
    {
        try {
            \Illuminate\Support\Facades\Artisan::call('optimize:clear');
            \Illuminate\Support\Facades\Artisan::call('optimize');
            $this->dispatch('toast', message: 'Caches rebuilt.', type: 'success');
        } catch (\Throwable $e) {
            report($e);
            \Illuminate\Support\Facades\Artisan::call('optimize:clear');
            $this->dispatch('toast', message: 'Rebuild failed, caches cleared: ' . $e->getMessage(), type: 'error');
        }
    }

    /** Settings known to slow down shared hosting, with the recommended value. */
    private function performanceChecks(): array
    {
        return [
            ['APP_DEBUG',       config('app.debug') ? 'true' : 'false',          'false',  ! config('app.debug')],
            ['APP_ENV',         app()->environment(),                            'production', app()->isProduction()],
            ['CACHE_STORE',     config('cache.default'),                         'file',   config('cache.default') !== 'database'],
            ['SESSION_DRIVER',  config('session.driver'),                        'file',   config('session.driver') !== 'database'],
            ['LOG_LEVEL',       config('logging.channels.single.level', 'debug'), 'error', config('logging.channels.single.level') !== 'debug'],
            ['Config cached',   app()->configurationIsCached() ? 'yes' : 'no',  'yes',    app()->configurationIsCached()],
            ['Routes cached',   app()->routesAreCached() ? 'yes' : 'no',        'yes',    app()->routesAreCached()],
            ['OPcache',         function_exists('opcache_get_status') && @opcache_get_status(false) ? 'on' : 'off', 'on',
                                function_exists('opcache_get_status') && (bool) @opcache_get_status(false)],
        ];
    }

    public function render()
    {
        return view('livewire.admin.settings.form', ['checks' => $this->performanceChecks()])->layout('layouts.admin');
    }
}
