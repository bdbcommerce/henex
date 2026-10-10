<?php

namespace App\Mcp\Tools;

use App\Livewire\Admin\Settings\SettingsForm;
use App\Models\Setting;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Validation\Rule;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Read site settings (contacts, social links, analytics IDs, verification codes), or update some of them by passing "set". Only the keys shown in the admin Settings page are allowed.')]
class ManageSettings extends Tool
{
    public function handle(Request $request): Response
    {
        $v = $request->validate([
            'set'   => 'nullable|array',
            'set.*' => 'nullable|string|max:5000',
        ]);

        $allowed = SettingsForm::KEYS;
        $set = $v['set'] ?? [];

        if ($bad = array_diff(array_keys($set), $allowed)) {
            return Response::error('Unknown setting keys: ' . implode(', ', $bad) . '. Allowed: ' . implode(', ', $allowed));
        }

        foreach ($set as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value ?? '']);
            cache()->forget("setting_{$key}");
        }

        $current = Setting::whereIn('key', $allowed)->pluck('value', 'key');

        return Response::json([
            'updated'  => array_keys($set),
            'settings' => collect($allowed)->mapWithKeys(fn ($k) => [$k => $current[$k] ?? ''])->all(),
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'set' => $schema->object()->description('Optional map of key → new value, e.g. {"site_phone": "+998 71 000 00 00"}. Omit to only read.'),
        ];
    }
}
