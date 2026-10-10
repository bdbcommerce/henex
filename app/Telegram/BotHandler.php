<?php

namespace App\Telegram;

use App\Models\Industry;
use App\Models\Inquiry;
use App\Models\KnowledgeArticle;
use App\Models\Product;
use App\Models\TelegramUser;
use App\Services\BotBrain;
use App\Services\InquiryNotifier;
use Illuminate\Support\Facades\Log;

/**
 * Processes one Telegram update.
 *
 * Flow: /start → language → share contact → full name → industry → main menu.
 * Menu: request (→ inquiry), scanner advisor (AI), support (knowledge base + AI),
 *       catalog / where to buy (Mini App), profile, language.
 */
class BotHandler
{
    private const LOCALES = ['uz' => "🇺🇿 O'zbekcha", 'ru' => '🇷🇺 Русский', 'en' => '🇬🇧 English'];

    private TelegramUser $user;
    private int $chatId;

    public function __construct(
        private TelegramApi $tg,
        private BotBrain $brain,
        private InquiryNotifier $notifier,
    ) {}

    public function handle(array $update): void
    {
        try {
            match (true) {
                isset($update['message'])        => $this->onMessage($update['message']),
                isset($update['callback_query']) => $this->onCallback($update['callback_query']),
                isset($update['my_chat_member']) => $this->onMembership($update['my_chat_member']),
                default => null,
            };
        } catch (\Throwable $e) {
            Log::error('Telegram update failed: ' . $e->getMessage(), ['update' => $update, 'trace' => $e->getTraceAsString()]);
            if (isset($this->chatId)) {
                $this->say(__('bot.error'));
            }
        }
    }

    // ── Update types ────────────────────────────────────────────────────────

    private function onMessage(array $msg): void
    {
        if (($msg['chat']['type'] ?? 'private') !== 'private') {
            return; // the bot only talks in private chats (admin group gets notifications only)
        }

        $this->boot($msg['from'], $msg['chat']['id']);
        $text = trim($msg['text'] ?? '');

        if (isset($msg['contact'])) {
            $this->onContact($msg['contact']);
            return;
        }

        // Commands work in any state.
        if (str_starts_with($text, '/')) {
            $this->onCommand(strtolower(strtok(substr($text, 1), ' @')));
            return;
        }

        if (! $this->user->locale) {
            $this->askLanguage();
            return;
        }

        if ($text !== '' && $text === __('bot.cancel')) {
            $this->user->setState($this->user->isRegistered() ? null : $this->user->state);
            $this->say(__('bot.cancelled'), $this->user->isRegistered() ? $this->menuKeyboard() : null);
            return;
        }

        // Registration steps
        switch ($this->user->state) {
            case 'reg_contact':
                $this->askContact();
                return;
            case 'reg_name':
                $this->onName($text);
                return;
            case 'reg_industry':
                $this->askIndustry();
                return;
        }

        if (! $this->user->isRegistered()) {
            $this->startRegistration();
            return;
        }

        if ($text === '') {
            return; // stickers, photos… ignored for now
        }

        if ($action = $this->menuAction($text)) {
            $this->onMenu($action);
            return;
        }

        match ($this->user->state) {
            'inquiry' => $this->onInquiryText($text),
            'advisor' => $this->onAdvisorText($text),
            default   => $this->onSupportText($text), // free text = support question
        };
    }

    private function onCallback(array $cb): void
    {
        $this->boot($cb['from'], $cb['message']['chat']['id'] ?? $cb['from']['id']);
        $this->tg->answerCallback($cb['id']);
        [$type, $value] = array_pad(explode(':', $cb['data'] ?? '', 2), 2, null);

        match ($type) {
            'lang'    => $this->onLanguage($value),
            'ind'     => $this->onIndustry((int) $value),
            'kb'      => $this->sendArticle((int) $value),
            'manager' => $this->forwardToManager(),
            'reg'     => $this->startRegistration(),
            default   => null,
        };
    }

    private function onMembership(array $member): void
    {
        $status = $member['new_chat_member']['status'] ?? null;
        TelegramUser::where('telegram_id', $member['from']['id'])
            ->update(['is_blocked' => in_array($status, ['kicked', 'left'], true)]);
    }

    // ── Commands & menu ─────────────────────────────────────────────────────

    private function onCommand(string $command): void
    {
        match ($command) {
            'start'  => $this->user->locale ? ($this->user->isRegistered() ? $this->showMenu(__('bot.welcome')) : $this->startRegistration()) : $this->askLanguage(),
            'lang', 'language' => $this->askLanguage(),
            'cancel' => $this->onMessage(['chat' => ['id' => $this->chatId, 'type' => 'private'], 'from' => ['id' => $this->user->telegram_id], 'text' => __('bot.cancel')]),
            'profile' => $this->onMenu('profile'),
            default  => $this->user->isRegistered() ? $this->showMenu() : $this->startRegistration(),
        };
    }

    private function onMenu(string $action): void
    {
        $this->user->setState(null);

        match ($action) {
            'inquiry' => $this->prompt('inquiry', __('bot.inquiry_prompt')),
            'advisor' => $this->prompt('advisor', __('bot.advisor_prompt')),
            'support' => $this->prompt('support', __('bot.support_prompt')),
            'catalog' => $this->say(__('bot.catalog_text'), $this->webAppButton(__('bot.open_catalog'), 'tg.home')),
            'resellers' => $this->say(__('bot.resellers_text'), $this->webAppButton(__('bot.open_resellers'), 'tg.resellers')),
            'language' => $this->askLanguage(),
            'profile' => $this->say(__('bot.profile', [
                'name'     => $this->user->full_name,
                'phone'    => $this->user->phone,
                'industry' => $this->user->industry?->getTranslation('name', $this->user->locale) ?? '—',
            ]), ['inline_keyboard' => [[['text' => __('bot.edit_profile'), 'callback_data' => 'reg']]]]),
        };
    }

    /** Which main-menu button was pressed (matches any locale's label). */
    private function menuAction(string $text): ?string
    {
        foreach (array_keys(self::LOCALES) as $locale) {
            foreach (trans('bot.menu', [], $locale) as $action => $label) {
                if ($label === $text) {
                    return $action;
                }
            }
        }

        return null;
    }

    // ── Registration ────────────────────────────────────────────────────────

    private function askLanguage(): void
    {
        $this->say(__('bot.choose_language'), ['inline_keyboard' => [
            collect(self::LOCALES)->map(fn ($label, $code) => ['text' => $label, 'callback_data' => "lang:{$code}"])->values()->all(),
        ]]);
    }

    private function onLanguage(?string $code): void
    {
        if (! isset(self::LOCALES[$code])) {
            return;
        }
        $this->user->update(['locale' => $code]);
        app()->setLocale($code);

        if ($this->user->isRegistered()) {
            $this->showMenu(__('bot.language_set'));
        } else {
            $this->say(__('bot.welcome'));
            $this->startRegistration();
        }
    }

    private function startRegistration(): void
    {
        $this->user->setState('reg_contact');
        $this->askContact();
    }

    private function askContact(): void
    {
        $this->say(__('bot.ask_contact'), [
            'keyboard' => [[['text' => __('bot.share_contact'), 'request_contact' => true]]],
            'resize_keyboard' => true,
            'one_time_keyboard' => true,
        ]);
    }

    private function onContact(array $contact): void
    {
        // Only accept the user's own number (shared via the button), not a forwarded contact.
        if (($contact['user_id'] ?? null) !== $this->user->telegram_id) {
            $this->say(__('bot.contact_not_own'));
            $this->askContact();
            return;
        }

        $phone = '+' . ltrim(preg_replace('/[^\d+]/', '', $contact['phone_number']), '+');
        $this->user->update(['phone' => $phone]);

        if ($this->user->state === 'reg_contact' || ! $this->user->isRegistered()) {
            $this->user->setState('reg_name');
            $this->say(__('bot.ask_name'), ['remove_keyboard' => true]);
        }
    }

    private function onName(string $text): void
    {
        $name = preg_replace('/\s+/u', ' ', $text);
        if (mb_strlen($name) < 3 || mb_strlen($name) > 120 || ! preg_match('/\p{L}/u', $name) || str_starts_with($name, '/')) {
            $this->say(__('bot.name_invalid'));
            return;
        }

        $this->user->update(['full_name' => $name]);
        $this->user->setState('reg_industry');
        $this->askIndustry();
    }

    private function askIndustry(): void
    {
        $buttons = Industry::active()->ordered()->get()
            ->map(fn ($i) => ['text' => trim("{$i->emoji} " . $i->getTranslation('name', $this->user->locale)), 'callback_data' => "ind:{$i->id}"])
            ->chunk(2)->map(fn ($row) => $row->values()->all())->values()->all();

        $this->say(__('bot.ask_industry'), ['inline_keyboard' => $buttons]);
    }

    private function onIndustry(int $id): void
    {
        if (! in_array($this->user->state, ['reg_industry', null], true) || ! $this->user->phone || ! $this->user->full_name) {
            return;
        }
        if (! Industry::active()->whereKey($id)->exists()) {
            return;
        }

        $this->user->forceFill(['industry_id' => $id, 'registered_at' => $this->user->registered_at ?? now()])->save();
        $this->user->setState(null);
        $this->showMenu(__('bot.registered', ['name' => $this->user->full_name]));
    }

    // ── Features ────────────────────────────────────────────────────────────

    private function onInquiryText(string $text): void
    {
        if (mb_strlen($text) < 5) {
            $this->say(__('bot.inquiry_short'));
            return;
        }

        $inquiry = $this->createInquiry($text);
        $this->user->setState(null);
        $this->say(__('bot.inquiry_sent', ['id' => $inquiry->id, 'phone' => $this->user->phone]), $this->menuKeyboard());
    }

    private function onAdvisorText(string $text): void
    {
        $this->tg->sendChatAction($this->chatId);
        $history = $this->user->state_data['history'] ?? [];

        $result = $this->brain->recommend($this->user->load('industry.categories'), $text, $history);

        if ($result['text'] === null && $result['products']->isEmpty()) {
            $this->say(__('bot.advisor_none'), $this->webAppButton(__('bot.open_catalog'), 'tg.home'));
            return;
        }

        $intro = $result['text'] ?? __('bot.advisor_fallback', [
            'industry' => $this->user->industry?->getTranslation('name', $this->user->locale) ?? '—',
        ]) . "\n\n" . $result['products']->map(fn ($p) => "• {$p->sku} — " . strip_tags($p->getTranslation('short_description', $this->user->locale)))->join("\n");

        $this->say($intro, $this->productButtons($result['products']));

        // Keep a short dialogue so follow-up answers ("wireless please") have context.
        if ($result['text'] !== null) {
            $history = array_slice([...$history, ['role' => 'user', 'content' => $text], ['role' => 'assistant', 'content' => $result['text']]], -6);
            $this->user->setState('advisor', ['history' => $history]);
        }
    }

    private function onSupportText(string $text): void
    {
        $this->tg->sendChatAction($this->chatId);
        $history = $this->user->state === 'support' ? ($this->user->state_data['history'] ?? []) : [];

        $result = $this->brain->support($this->user, $text, $history);
        $articles = $result['articles'];

        $buttons = $articles->map(fn ($a) => [[
            'text' => mb_substr(__('bot.more_details', ['title' => $a->getTranslation('title', $this->user->locale, true)]), 0, 60),
            'callback_data' => "kb:{$a->id}",
        ]])->values()->all();
        $buttons[] = [['text' => __('bot.send_to_manager'), 'callback_data' => 'manager']];

        if ($result['text'] !== null) {
            $this->say($result['text'], ['inline_keyboard' => $buttons]);
            // Send the setting barcodes of the best match right away — they are the actual fix.
            if ($best = $articles->first()) {
                $this->sendArticleMedia($best, onlyBarcodes: true);
            }
        } elseif ($articles->isNotEmpty()) {
            $this->say(__('bot.kb_found'), ['inline_keyboard' => $buttons]);
        } else {
            $this->say(__('bot.kb_none'), ['inline_keyboard' => $buttons]);
        }

        $history = array_slice([...$history, ['role' => 'user', 'content' => $text], ...($result['text'] ? [['role' => 'assistant', 'content' => $result['text']]] : [])], -6);
        $this->user->setState('support', ['history' => $history, 'last_question' => $text]);
    }

    private function sendArticle(int $id): void
    {
        $article = KnowledgeArticle::active()->with('media')->find($id);
        if (! $article) {
            return;
        }
        $article->increment('views');

        $locale = $this->user->locale ?? 'uz';
        $body = trim(html_entity_decode(strip_tags(str_replace(['<br>', '<br/>', '<br />', '</p>', '</li>'], "\n", $article->getTranslation('answer', $locale, true)))));
        $this->say('📘 ' . $article->getTranslation('title', $locale, true) . ($body !== '' ? "\n\n{$body}" : ''));
        $this->sendArticleMedia($article);
    }

    private function sendArticleMedia(KnowledgeArticle $article, bool $onlyBarcodes = false): void
    {
        $barcodes = $article->getMedia('barcodes');
        if ($barcodes->isNotEmpty()) {
            $this->say(__('bot.kb_barcodes'));
            foreach ($barcodes->take(10) as $m) {
                $this->tg->sendPhoto($this->chatId, $m->getPath(), $m->getCustomProperty('caption') ?: $m->name);
            }
        }
        if (! $onlyBarcodes) {
            foreach ($article->getMedia('manuals')->take(5) as $m) {
                $this->tg->sendDocument($this->chatId, $m->getPath(), $m->name);
            }
        }
    }

    private function forwardToManager(): void
    {
        if (! $this->user->isRegistered()) {
            $this->say(__('bot.need_register'));
            $this->startRegistration();
            return;
        }

        $question = $this->user->state_data['last_question'] ?? null;
        if (! $question) {
            $this->prompt('inquiry', __('bot.inquiry_prompt'));
            return;
        }

        $inquiry = $this->createInquiry("[Support] {$question}");
        $this->user->setState(null);
        $this->say(__('bot.forwarded', ['id' => $inquiry->id]), $this->menuKeyboard());
    }

    private function createInquiry(string $message, ?int $productId = null): Inquiry
    {
        $inquiry = Inquiry::create([
            'name'             => $this->user->full_name ?? $this->user->displayName(),
            'phone'            => $this->user->phone ?? '',
            'message'          => $message,
            'product_id'       => $productId,
            'locale'           => $this->user->locale ?? 'uz',
            'source'           => 'telegram',
            'telegram_user_id' => $this->user->id,
            'industry_id'      => $this->user->industry_id,
        ]);
        $this->notifier->notify($inquiry);

        return $inquiry;
    }

    // ── Helpers ─────────────────────────────────────────────────────────────

    private function boot(array $from, int $chatId): void
    {
        $this->chatId = $chatId;
        $this->user = TelegramUser::firstOrCreate(['telegram_id' => $from['id']]);
        $this->user->forceFill([
            'username'      => $from['username'] ?? null,
            'tg_first_name' => $from['first_name'] ?? null,
            'tg_last_name'  => $from['last_name'] ?? null,
            'is_blocked'    => false,
            'last_seen_at'  => now(),
        ])->save();

        app()->setLocale($this->user->locale ?? $this->guessLocale($from['language_code'] ?? null));
    }

    private function guessLocale(?string $code): string
    {
        return match ($code) { 'ru', 'uk', 'be', 'kk' => 'ru', 'en' => 'en', default => 'uz' };
    }

    private function say(string $text, ?array $markup = null): void
    {
        $this->tg->sendMessage($this->chatId, $text, $markup ? ['reply_markup' => $markup] : []);
    }

    private function prompt(string $state, string $text): void
    {
        $this->user->setState($state);
        $this->say($text, ['keyboard' => [[['text' => __('bot.cancel')]]], 'resize_keyboard' => true]);
    }

    private function showMenu(?string $text = null): void
    {
        $this->user->setState(null);
        $this->say($text ?? __('bot.main_menu'), $this->menuKeyboard());
    }

    private function menuKeyboard(): array
    {
        $m = trans('bot.menu');

        return [
            'keyboard' => [
                [['text' => $m['inquiry']], ['text' => $m['advisor']]],
                [['text' => $m['support']], ['text' => $m['catalog'], 'web_app' => ['url' => $this->webAppUrl('tg.home')]]],
                [['text' => $m['resellers'], 'web_app' => ['url' => $this->webAppUrl('tg.resellers')]]],
                [['text' => $m['profile']], ['text' => $m['language']]],
            ],
            'resize_keyboard' => true,
            'is_persistent' => true,
        ];
    }

    private function webAppButton(string $label, string $route, array $params = []): array
    {
        return ['inline_keyboard' => [[['text' => $label, 'web_app' => ['url' => $this->webAppUrl($route, $params)]]]]];
    }

    private function productButtons($products): ?array
    {
        if ($products->isEmpty()) {
            return null;
        }

        return ['inline_keyboard' => $products->map(fn (Product $p) => [[
            'text'    => __('bot.open_product', ['name' => $p->sku ?: $p->getTranslation('name', $this->user->locale)]),
            'web_app' => ['url' => $this->webAppUrl('tg.product', ['product' => $p->slug])],
        ]])->values()->all()];
    }

    /** Mini App URL. TELEGRAM_WEBAPP_URL overrides the host (must be HTTPS for Telegram). */
    private function webAppUrl(string $route, array $params = []): string
    {
        $path = route($route, $params + ['lang' => $this->user->locale ?? 'uz'], absolute: false);
        $base = rtrim(config('services.telegram.webapp_url') ?: config('app.url'), '/');

        return $base . $path;
    }
}
