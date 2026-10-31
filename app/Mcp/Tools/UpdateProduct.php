<?php

namespace App\Mcp\Tools;

use App\Mcp\Tools\Concerns\FormatsModels;
use App\Models\Category;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsIdempotent;

#[IsIdempotent]
#[Description('Edit an existing product: status flags, sort order, categories, and translated text/SEO fields for one locale. Only the fields you pass are changed. Cannot create or delete products.')]
class UpdateProduct extends Tool
{
    use FormatsModels;

    private const TEXT_FIELDS = ['name', 'short_description', 'description', 'meta_title', 'meta_description'];

    public function handle(Request $request): Response
    {
        $v = $request->validate([
            'product'      => 'required',
            'is_active'    => 'nullable|boolean',
            'is_featured'  => 'nullable|boolean',
            'sort_order'   => 'nullable|integer|min:0|max:65535',
            'categories'   => 'nullable|array',
            'categories.*' => 'string',
            'locale'       => 'required_with:' . implode(',', self::TEXT_FIELDS) . '|nullable|in:uz,ru,en',
            'name'              => 'nullable|string|max:255',
            'short_description' => 'nullable|string|max:1000',
            'description'       => 'nullable|string|max:20000',
            'meta_title'        => 'nullable|string|max:255',
            'meta_description'  => 'nullable|string|max:500',
        ]);

        $p = $this->findProduct($v['product']);
        if (! $p) {
            return Response::error("No product found for '{$v['product']}'.");
        }

        // Resolve categories before touching anything, so a bad slug changes nothing.
        $categoryIds = null;
        if (isset($v['categories'])) {
            $categoryIds = Category::whereIn('slug', $v['categories'])->pluck('id', 'slug');
            $unknown = array_values(array_diff($v['categories'], $categoryIds->keys()->all()));
            if ($unknown) {
                return Response::error('Unknown category slugs: ' . implode(', ', $unknown) . '. Nothing was changed.');
            }
        }
        if (array_key_exists('name', $v) && ($v['locale'] ?? null) === 'uz' && trim((string) $v['name']) === '') {
            return Response::error('The Uzbek name cannot be empty. Nothing was changed.');
        }

        $changed = [];

        foreach (['is_active', 'is_featured', 'sort_order'] as $flag) {
            if (array_key_exists($flag, $v) && $v[$flag] !== null) {
                $p->{$flag} = $v[$flag];
                $changed[] = $flag;
            }
        }

        foreach (self::TEXT_FIELDS as $field) {
            if (array_key_exists($field, $v) && $v[$field] !== null) {
                $p->setTranslation($field, $v['locale'], $v[$field]);
                $changed[] = "{$field}.{$v['locale']}";
            }
        }

        $p->save();

        if ($categoryIds !== null) {
            $p->categories()->sync($categoryIds->values());
            $changed[] = 'categories';
        }

        foreach (['products_select', 'products_for_select'] as $key) {
            cache()->forget($key);
        }

        return $changed
            ? Response::json(['updated' => $changed, 'product' => $this->productSummary($p->fresh())])
            : Response::error('Nothing to update — pass at least one field.');
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'product'     => $schema->string()->description('Product id, SKU or slug.')->required(),
            'is_active'   => $schema->boolean()->description('Show/hide on the public site.'),
            'is_featured' => $schema->boolean()->description('Show in homepage featured products.'),
            'sort_order'  => $schema->integer(),
            'categories'  => $schema->array()->items($schema->string())->description('Full list of category slugs (replaces current categories).'),
            'locale'      => $schema->string()->enum(['uz', 'ru', 'en'])->description('Required when changing any text field.'),
            'name'              => $schema->string(),
            'short_description' => $schema->string(),
            'description'       => $schema->string()->description('HTML allowed.'),
            'meta_title'        => $schema->string()->description('SEO title, ideally ≤ 60 chars.'),
            'meta_description'  => $schema->string()->description('SEO description, ideally ≤ 160 chars.'),
        ];
    }
}
