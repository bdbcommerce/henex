<?php

namespace App\Telegram;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/** Thin wrapper over the Telegram Bot API (https://core.telegram.org/bots/api). */
class TelegramApi
{
    public function __construct(private ?string $token = null)
    {
        $this->token ??= config('services.telegram.bot_token');
    }

    public function isConfigured(): bool
    {
        return filled($this->token);
    }

    /** Call any Bot API method; returns the decoded "result" or null on failure. */
    public function call(string $method, array $params = [], array $files = []): mixed
    {
        if (! $this->isConfigured()) {
            Log::warning("Telegram {$method} skipped: TELEGRAM_BOT_TOKEN is empty.");
            return null;
        }

        $request = Http::timeout(20)->retry(2, 300, throw: false);

        foreach ($files as $field => [$path, $filename]) {
            $request = $request->attach($field, fopen($path, 'r'), $filename);
        }

        // Nested arrays (reply_markup, media, …) must be JSON strings.
        $params = array_map(fn ($v) => is_array($v) ? json_encode($v, JSON_UNESCAPED_UNICODE) : $v, $params);

        /** @var Response $response */
        $response = $files
            ? $request->post($this->url($method), $params)
            : $request->asForm()->post($this->url($method), $params);

        if (! $response->json('ok')) {
            Log::warning("Telegram {$method} failed", ['status' => $response->status(), 'body' => $response->json() ?? $response->body()]);
            return null;
        }

        return $response->json('result');
    }

    public function sendMessage(int $chatId, string $text, array $extra = []): mixed
    {
        // Telegram limit is 4096 chars per message.
        $chunks = mb_str_split($text, 4000);
        $last = null;
        foreach ($chunks as $i => $chunk) {
            $last = $this->call('sendMessage', ['chat_id' => $chatId, 'text' => $chunk]
                + ($i === array_key_last($chunks) ? $extra : array_diff_key($extra, ['reply_markup' => 1])));
        }

        return $last;
    }

    public function sendPhoto(int $chatId, string $path, ?string $caption = null): mixed
    {
        return $this->call('sendPhoto', array_filter(['chat_id' => $chatId, 'caption' => $caption]), ['photo' => [$path, basename($path)]]);
    }

    public function sendDocument(int $chatId, string $path, ?string $caption = null): mixed
    {
        return $this->call('sendDocument', array_filter(['chat_id' => $chatId, 'caption' => $caption]), ['document' => [$path, basename($path)]]);
    }

    public function answerCallback(string $callbackId, ?string $text = null): mixed
    {
        return $this->call('answerCallbackQuery', array_filter(['callback_query_id' => $callbackId, 'text' => $text]));
    }

    public function sendChatAction(int $chatId, string $action = 'typing'): mixed
    {
        return $this->call('sendChatAction', ['chat_id' => $chatId, 'action' => $action]);
    }

    private function url(string $method): string
    {
        return "https://api.telegram.org/bot{$this->token}/{$method}";
    }
}
