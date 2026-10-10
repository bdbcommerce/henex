<?php

namespace App\Mcp\Tools;

use App\Mcp\Tools\Concerns\FormatsModels;
use App\Models\Product;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[IsReadOnly]
#[Description('Search/list products by text (name in any locale, SKU, slug), category slug, status, or content gaps. Returns compact rows; use get_product for full details.')]
class SearchProducts extends Tool
{
    use FormatsModels;

    public function handle(Request $request): Response
    {
        $v = $request->validate([
            'query'    => 'nullable|string|max:100',
            'category' => 'nullable|string|max:120',
            'status'   => 'nullable|in:all,active,inactive,featured',
            'missing'  => 'nullable|in:images,category,ru_name,en_name,meta_description',
            'limit'    => 'nullable|integer|min:1|max:200',
        ]);

        $q = Product::query()->with(['categories', 'media']);

        if ($term = $v['query'] ?? null) {
            $q->where(fn ($q) => $q->where('sku', 'like', "%{$term}%")
                ->orWhere('slug', 'like', "%{$term}%")
                ->orWhere('name', 'like', "%{$term}%"));
        }
        if ($cat = $v['category'] ?? null) {
            $q->whereHas('categories', fn ($c) => $c->where('slug', $cat));
        }
        match ($v['status'] ?? 'all') {
            'active'   => $q->where('is_active', true),
            'inactive' => $q->where('is_active', false),
            'featured' => $q->where('is_featured', true),
            default    => null,
        };
        match ($v['missing'] ?? null) {
            'images'   => $q->whereDoesntHave('media', fn ($m) => $m->where('collection_name', 'gallery')),
            'category' => $q->doesntHave('categories'),
            default    => null,
        };

        $rows = $q->orderBy('sort_order')->orderBy('sku')->get();

        // JSON-locale gaps are easier to check in PHP than portably in SQL.
        $rows = match ($v['missing'] ?? null) {
            'ru_name' => $rows->filter(fn ($p) => ($p->getTranslations('name')['ru'] ?? '') === ''),
            'en_name' => $rows->filter(fn ($p) => ($p->getTranslations('name')['en'] ?? '') === ''),
            'meta_description' => $rows->filter(fn ($p) => count(array_filter($p->getTranslations('meta_description'))) < 3),
            default   => $rows,
        };

        $total = $rows->count();
        $limit = (int) ($v['limit'] ?? 50);

        return Response::json([
            'total'    => $total,
            'returned' => min($total, $limit),
            'products' => $rows->take($limit)->map(fn ($p) => $this->productSummary($p) + [
                'categories' => $p->categories->pluck('slug')->all(),
                'images'     => $p->getMedia('gallery')->count(),
            ])->values()->all(),
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'query'    => $schema->string()->description('Text to match against SKU, slug or name (any locale).'),
            'category' => $schema->string()->description('Category slug to filter by.'),
            'status'   => $schema->string()->enum(['all', 'active', 'inactive', 'featured'])->description('Default: all.'),
            'missing'  => $schema->string()->enum(['images', 'category', 'ru_name', 'en_name', 'meta_description'])
                ->description('Only products lacking this content.'),
            'limit'    => $schema->integer()->description('Max rows (default 50, max 200).'),
        ];
    }
}
