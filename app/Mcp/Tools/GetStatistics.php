<?php

namespace App\Mcp\Tools;

use App\Mcp\Tools\Concerns\FormatsModels;
use App\Models\Article;
use App\Models\Attribute;
use App\Models\Category;
use App\Models\Inquiry;
use App\Models\Product;
use App\Models\Region;
use App\Models\Reseller;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[IsReadOnly]
#[Description('Site-wide statistics for the Henex.uz admin: catalog size, content health (missing translations, images, SEO), resellers per region, articles, and inquiry volume/trend/top products over a period.')]
class GetStatistics extends Tool
{
    use FormatsModels;

    public function handle(Request $request): Response
    {
        $days = (int) ($request->validate(['days' => 'nullable|integer|min:1|max:365'])['days'] ?? 30);
        $since = now()->subDays($days)->startOfDay();

        $products = Product::with('media')->get();
        $missing = fn (string $field, string $locale) => $products
            ->filter(fn ($p) => ($p->getTranslations($field)[$locale] ?? '') === '')->count();

        $inquiries = Inquiry::where('created_at', '>=', $since);

        return Response::json([
            'generated_at' => now()->toDateTimeString(),
            'period_days'  => $days,
            'products' => [
                'total'          => $products->count(),
                'active'         => $products->where('is_active', true)->count(),
                'inactive'       => $products->where('is_active', false)->count(),
                'featured'       => $products->where('is_featured', true)->count(),
                'without_images' => $products->filter(fn ($p) => $p->getMedia('gallery')->isEmpty())->count(),
                'without_category' => Product::doesntHave('categories')->count(),
                'missing_name'   => ['ru' => $missing('name', 'ru'), 'en' => $missing('name', 'en')],
                'missing_meta_description' => array_combine(self::LOCALES, array_map(fn ($l) => $missing('meta_description', $l), self::LOCALES)),
            ],
            'categories' => [
                'total'  => Category::count(),
                'active' => Category::where('is_active', true)->count(),
                'product_counts' => Category::withCount('products')->orderByDesc('products_count')->get()
                    ->map(fn ($c) => ['slug' => $c->slug, 'name' => $this->label($c), 'products' => $c->products_count])->all(),
            ],
            'attributes' => [
                'total'  => Attribute::count(),
                'values' => \App\Models\AttributeValue::count(),
            ],
            'resellers' => [
                'total'   => Reseller::count(),
                'active'  => Reseller::where('is_active', true)->count(),
                'by_type' => Reseller::selectRaw('type, count(*) as n')->groupBy('type')->pluck('n', 'type'),
                'regions_without_resellers' => Region::doesntHave('resellers')->get()->map(fn ($r) => $this->label($r))->values(),
            ],
            'articles' => [
                'published' => Article::where('is_published', true)->count(),
                'drafts'    => Article::where('is_published', false)->count(),
                'latest_published_at' => Article::where('is_published', true)->max('published_at'),
            ],
            'inquiries' => [
                'all_time'        => Inquiry::count(),
                'unread'          => Inquiry::where('is_read', false)->count(),
                'in_period'       => (clone $inquiries)->count(),
                'per_day'         => (clone $inquiries)->selectRaw('DATE(created_at) as day, count(*) as n')
                    ->groupBy('day')->orderBy('day')->pluck('n', 'day'),
                'by_locale'       => (clone $inquiries)->selectRaw('locale, count(*) as n')->groupBy('locale')->pluck('n', 'locale'),
                'top_products'    => (clone $inquiries)->whereNotNull('product_id')
                    ->selectRaw('product_id, count(*) as n')->groupBy('product_id')->orderByDesc('n')->limit(10)
                    ->get()->map(fn ($row) => [
                        'product'   => ($p = Product::find($row->product_id)) ? $this->label($p) . " ({$p->sku})" : "#{$row->product_id}",
                        'inquiries' => $row->n,
                    ])->all(),
            ],
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'days' => $schema->integer()->description('Look-back window for inquiry stats, in days (default 30, max 365).'),
        ];
    }
}
