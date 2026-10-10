<?php

namespace App\Mcp\Tools\Concerns;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;

trait FormatsModels
{
    protected const LOCALES = ['uz', 'ru', 'en'];

    /** All three locales of a translatable field, missing ones as null. */
    protected function tr(Model $model, string $field): array
    {
        $t = $model->getTranslations($field);

        return array_combine(self::LOCALES, array_map(fn ($l) => ($t[$l] ?? '') !== '' ? $t[$l] : null, self::LOCALES));
    }

    /** Best single-locale label (en → uz → ru). */
    protected function label(Model $model, string $field = 'name'): string
    {
        $t = $model->getTranslations($field);

        return $t['en'] ?? $t['uz'] ?? $t['ru'] ?? '';
    }

    /** Resolve a product by numeric id, SKU or slug. */
    protected function findProduct(string|int|null $ref): ?Product
    {
        if ($ref === null || $ref === '') {
            return null;
        }

        return Product::query()
            ->where(fn ($q) => $q->where('sku', $ref)->orWhere('slug', $ref)
                ->when(is_numeric($ref), fn ($q) => $q->orWhere('id', (int) $ref)))
            ->first();
    }

    protected function productSummary(Product $p): array
    {
        return [
            'id'          => $p->id,
            'sku'         => $p->sku,
            'slug'        => $p->slug,
            'name'        => $this->label($p),
            'is_active'   => (bool) $p->is_active,
            'is_featured' => (bool) $p->is_featured,
            'url'         => route('products.show', $p->slug),
            'admin_url'   => route('admin.products.edit', $p),
        ];
    }
}
