<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Minimal client for any OpenAI-compatible chat-completions API
 * (Gemini, Groq, OpenRouter, …) configured in config/services.php → ai.
 */
class AiAssistant
{
    public function isEnabled(): bool
    {
        return filled(config('services.ai.api_key'));
    }

    /**
     * @param  array<int, array{role: string, content: string}>  $messages
     * @return string|null  null when disabled or the provider failed (callers fall back)
     */
    public function chat(string $system, array $messages, int $maxTokens = 900): ?string
    {
        if (! $this->isEnabled()) {
            return null;
        }

        try {
            $response = Http::withToken(config('services.ai.api_key'))
                ->timeout(config('services.ai.timeout', 30))
                ->post(rtrim(config('services.ai.base_url'), '/') . '/chat/completions', [
                    'model'       => config('services.ai.model'),
                    'messages'    => [['role' => 'system', 'content' => $system], ...$messages],
                    'max_tokens'  => $maxTokens,
                    'temperature' => 0.3,
                ]);
        } catch (\Throwable $e) {
            Log::warning('AI request failed: ' . $e->getMessage());
            return null;
        }

        if ($response->failed()) {
            Log::warning('AI request failed', ['status' => $response->status(), 'body' => mb_substr($response->body(), 0, 500)]);
            return null;
        }

        $text = trim((string) $response->json('choices.0.message.content'));

        return $text !== '' ? $text : null;
    }
}
