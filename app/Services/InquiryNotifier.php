<?php

namespace App\Services;

use App\Models\Inquiry;
use App\Telegram\TelegramApi;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/** Tells the team about a new inquiry: Telegram admin chat + e-mail (each only if configured). */
class InquiryNotifier
{
    public function __construct(private TelegramApi $telegram) {}

    public function notify(Inquiry $inquiry): void
    {
        $inquiry->loadMissing(['product', 'industry', 'telegramUser']);

        $lines = array_filter([
            "🆕 Inquiry #{$inquiry->id} · " . strtoupper($inquiry->source ?? 'website'),
            "👤 {$inquiry->name}" . ($inquiry->company ? " ({$inquiry->company})" : ''),
            "📱 {$inquiry->phone}",
            $inquiry->email ? "✉️ {$inquiry->email}" : null,
            $inquiry->industry ? '🏷 ' . $inquiry->industry->getTranslation('name', 'ru') : null,
            $inquiry->product_id && $inquiry->product?->exists ? "📦 {$inquiry->product->sku}" : null,
            $inquiry->telegramUser?->username ? "💬 @{$inquiry->telegramUser->username}" : null,
            "🌐 {$inquiry->locale}",
            '',
            $inquiry->message,
            '',
            route('admin.inquiries.index'),
        ], fn ($l) => $l !== null);
        $text = implode("\n", $lines);

        if ($chatId = config('services.telegram.admin_chat_id')) {
            $this->telegram->call('sendMessage', ['chat_id' => $chatId, 'text' => mb_substr($text, 0, 4000)]);
        }

        if ($email = config('services.inquiries.notify_email')) {
            try {
                Mail::raw($text, fn ($m) => $m->to($email)->subject("Henex: new inquiry #{$inquiry->id}"));
            } catch (\Throwable $e) {
                Log::warning('Inquiry e-mail failed: ' . $e->getMessage());
            }
        }
    }
}
