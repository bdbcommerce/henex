<?php

namespace App\Telegram;

/** Registers the webhook, command list and Mini App menu button with Telegram. */
class BotSetup
{
    public function __construct(private TelegramApi $tg) {}

    public function webhookUrl(): string
    {
        return rtrim(config('services.telegram.webapp_url') ?: config('app.url'), '/') . route('telegram.webhook', absolute: false);
    }

    /** @return array<string, bool|string> step => ok / error text */
    public function run(): array
    {
        if (! $this->tg->isConfigured()) {
            return ['token' => 'TELEGRAM_BOT_TOKEN is empty in .env'];
        }
        if (! config('services.telegram.webhook_secret')) {
            return ['secret' => 'TELEGRAM_WEBHOOK_SECRET is empty in .env'];
        }
        if (! str_starts_with($this->webhookUrl(), 'https://')) {
            return ['https' => 'Telegram requires HTTPS. Set APP_URL (or TELEGRAM_WEBAPP_URL) to https://… — now: ' . $this->webhookUrl()];
        }

        $result = [];

        $result['webhook'] = (bool) $this->tg->call('setWebhook', [
            'url'             => $this->webhookUrl(),
            'secret_token'    => config('services.telegram.webhook_secret'),
            'allowed_updates' => ['message', 'callback_query', 'my_chat_member'],
            'drop_pending_updates' => false,
        ]);

        foreach (['uz', 'ru', 'en'] as $locale) {
            $commands = collect(trans('bot.commands', [], $locale))
                ->map(fn ($description, $command) => ['command' => $command, 'description' => $description])->values()->all();
            $result["commands_{$locale}"] = (bool) $this->tg->call('setMyCommands', ['commands' => $commands, 'language_code' => $locale]);
        }
        $result['commands_default'] = (bool) $this->tg->call('setMyCommands', [
            'commands' => collect(trans('bot.commands', [], 'uz'))->map(fn ($d, $c) => ['command' => $c, 'description' => $d])->values()->all(),
        ]);

        $result['menu_button'] = (bool) $this->tg->call('setChatMenuButton', ['menu_button' => [
            'type'    => 'web_app',
            'text'    => 'Katalog',
            'web_app' => ['url' => rtrim(config('services.telegram.webapp_url') ?: config('app.url'), '/') . route('tg.home', absolute: false)],
        ]]);

        return $result;
    }

    public function status(): array
    {
        $bot = $this->tg->isConfigured() ? $this->tg->call('getMe') : null;
        $webhook = $this->tg->isConfigured() ? $this->tg->call('getWebhookInfo') : null;

        return [
            'configured' => $this->tg->isConfigured(),
            'bot'        => is_array($bot) ? $bot : null,
            'webhook'    => is_array($webhook) ? $webhook : [],
            'expected_url' => $this->webhookUrl(),
        ];
    }
}
