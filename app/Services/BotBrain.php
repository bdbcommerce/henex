<?php

namespace App\Services;

use App\Models\KnowledgeArticle;
use App\Models\Product;
use App\Models\TelegramUser;
use Illuminate\Support\Collection;

/**
 * The bot's "thinking": scanner recommendations and support answers.
 * Uses the AI provider when configured, otherwise deterministic fallbacks.
 */
class BotBrain
{
    private const LANGUAGES = ['uz' => "Uzbek (Latin script)", 'ru' => 'Russian', 'en' => 'English'];

    public function __construct(private AiAssistant $ai, private KnowledgeSearch $kb) {}

    /**
     * @return array{text: ?string, products: Collection<int, Product>}
     *         text null → caller shows its own fallback intro
     */
    public function recommend(TelegramUser $user, string $need, array $history = []): array
    {
        $catalog = $this->catalog();
        $industry = $user->industry;

        $system = implode("\n", [
            'You are a sales consultant of "Henex Uzbekistan", the official distributor of HENEX barcode scanners in Uzbekistan.',
            'Recommend 1–3 products from the CATALOG below that best fit the client\'s need. Use ONLY products from the catalog and always cite their exact SKU.',
            'Briefly explain why (1D/2D, wired/wireless, screen codes, range, durability). Ask one clarifying question if the need is unclear.',
            'Reply in ' . self::LANGUAGES[$user->locale ?? 'uz'] . '. Plain text, no markdown, no tables, max 900 characters. Do not invent prices or stock.',
            $industry ? 'Client industry: ' . $industry->getTranslation('name', 'en') . '. Categories usually suitable: '
                . $industry->categories->map(fn ($c) => $c->getTranslation('name', 'en'))->join(', ') . '.' : '',
            '',
            'CATALOG (SKU | name | categories | description | attributes):',
            $catalog->map(fn ($p) => $p['line'])->join("\n"),
        ]);

        $reply = $this->ai->chat($system, [...$history, ['role' => 'user', 'content' => $need]]);

        if ($reply !== null) {
            $mentioned = $catalog->filter(fn ($p) => $p['sku'] && preg_match('/\b' . preg_quote($p['sku'], '/') . '\b/i', $reply))
                ->take(3)->pluck('id');

            return ['text' => $reply, 'products' => Product::whereIn('id', $mentioned)->get()];
        }

        // Fallback: featured products of the industry's recommended categories.
        $categoryIds = $industry?->categories->pluck('id') ?? collect();
        $products = Product::where('is_active', true)
            ->when($categoryIds->isNotEmpty(), fn ($q) => $q->whereHas('categories', fn ($c) => $c->whereIn('categories.id', $categoryIds)))
            ->orderByDesc('is_featured')->orderBy('sort_order')
            ->limit(5)->get();

        return ['text' => null, 'products' => $products];
    }

    /**
     * @return array{text: ?string, articles: Collection<int, KnowledgeArticle>}
     *         text null + no articles → nothing found
     */
    public function support(TelegramUser $user, string $question, array $history = []): array
    {
        $locale = $user->locale ?? 'uz';
        $articles = $this->kb->search($question, 3);

        $context = $articles->map(fn (KnowledgeArticle $a) => '### ' . ($a->getTranslation('title', $locale, true) ?: $a->slug) . "\n"
            . mb_substr(trim(strip_tags($a->getTranslation('answer', $locale, true))), 0, 1500)
            . ($a->getMedia('barcodes')->isNotEmpty() ? "\n(This article has configuration barcode images that will be sent to the client.)" : '')
        )->join("\n\n");

        $system = implode("\n", [
            'You are the technical support assistant of "Henex Uzbekistan", official distributor of HENEX barcode scanners.',
            'Answer the client\'s question using the KNOWLEDGE BASE excerpts below. If they do not cover it, give general, safe barcode-scanner guidance',
            'and say a manager can help further (support phone: ' . (settings('site_phone') ?: 'see website') . '). Never invent configuration barcodes.',
            'Reply in ' . self::LANGUAGES[$locale] . '. Plain text, no markdown, short numbered steps, max 900 characters.',
            '',
            'KNOWLEDGE BASE:',
            $context ?: '(no matching articles)',
        ]);

        $reply = $this->ai->chat($system, [...$history, ['role' => 'user', 'content' => $question]]);

        return ['text' => $reply, 'articles' => $articles];
    }

    /** Compact catalog for the AI prompt (cached 10 min). */
    private function catalog(): Collection
    {
        return collect(cache()->remember('bot_catalog_context', 600, fn () => Product::where('is_active', true)
            ->with(['categories', 'attributeValues.attribute'])->orderBy('sort_order')->get()
            ->map(fn (Product $p) => [
                'id'   => $p->id,
                'sku'  => $p->sku,
                'line' => implode(' | ', [
                    $p->sku ?: $p->slug,
                    $p->getTranslation('name', 'en'),
                    $p->categories->map(fn ($c) => $c->getTranslation('name', 'en'))->join(', '),
                    mb_substr(trim(preg_replace('/\s+/', ' ', strip_tags($p->getTranslation('short_description', 'en') . ' ' . $p->getTranslation('description', 'en')))), 0, 260),
                    $p->attributeValues->map(fn ($v) => $v->attribute->getTranslation('name', 'en') . ': ' . $v->getTranslation('value', 'en'))->join('; '),
                ]),
            ])->all()));
    }
}
