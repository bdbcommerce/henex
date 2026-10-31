<?php

namespace App\Services;

use App\Models\KnowledgeArticle;
use Illuminate\Support\Collection;

/** Simple multilingual keyword scoring over the knowledge base (no external search engine). */
class KnowledgeSearch
{
    /** @return Collection<int, KnowledgeArticle> best matches first */
    public function search(string $query, int $limit = 3): Collection
    {
        $words = $this->words($query);
        if (! $words) {
            return collect();
        }

        return KnowledgeArticle::active()->with('media')->get()
            ->map(function (KnowledgeArticle $a) use ($words) {
                $title    = $this->flatten($a->getTranslations('title'));
                $keywords = $this->flatten($a->getTranslations('keywords'));
                $answer   = $this->flatten($a->getTranslations('answer'));

                $score = 0;
                foreach ($words as $w) {
                    $score += 5 * substr_count($keywords, $w)
                            + 3 * substr_count($title, $w)
                            + min(3, substr_count($answer, $w));
                }
                $a->setAttribute('score', $score);

                return $a;
            })
            ->filter(fn ($a) => $a->score > 0)
            ->sortByDesc('score')
            ->take($limit)
            ->values();
    }

    /** Lowercased words of 3+ letters, minus very common filler words. */
    private function words(string $text): array
    {
        $stop = ['the', 'and', 'for', 'how', 'what', 'can', 'not', 'with', 'как', 'что', 'для', 'это', 'или', 'нет', 'мне',
                 'не', 'qanday', 'nima', 'uchun', 'bilan', 'men', 'kerak', 'bormi'];

        preg_match_all('/[\p{L}\p{N}]{3,}/u', mb_strtolower($text), $m);

        return array_values(array_unique(array_diff($m[0], $stop)));
    }

    private function flatten(array $translations): string
    {
        return mb_strtolower(strip_tags(implode(' ', array_filter($translations))));
    }
}
