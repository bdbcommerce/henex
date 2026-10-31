<?php

namespace App\Mcp\Tools;

use App\Mcp\Tools\Concerns\FormatsModels;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[IsReadOnly]
#[Description('Full details of one product (by id, SKU or slug): all translations, SEO fields, categories, attributes, images, documents and inquiry count.')]
class GetProduct extends Tool
{
    use FormatsModels;

    public function handle(Request $request): Response
    {
        $ref = $request->validate(['product' => 'required'])['product'];
        $p = $this->findProduct($ref);

        if (! $p) {
            return Response::error("No product found for '{$ref}'.");
        }

        $p->load(['categories', 'attributeValues.attribute', 'media']);

        return Response::json(array_merge($this->productSummary($p), [
            'sort_order'        => $p->sort_order,
            'name'              => $this->tr($p, 'name'),
            'short_description' => $this->tr($p, 'short_description'),
            'description'       => $this->tr($p, 'description'),
            'meta_title'        => $this->tr($p, 'meta_title'),
            'meta_description'  => $this->tr($p, 'meta_description'),
            'categories'        => $p->categories->map(fn ($c) => ['id' => $c->id, 'slug' => $c->slug, 'name' => $this->label($c)])->all(),
            'attributes'        => $p->attributeValues->groupBy('attribute_id')->map(fn ($vals) => [
                'attribute' => $vals->first()->attribute->slug,
                'name'      => $this->label($vals->first()->attribute),
                'values'    => $vals->map(fn ($v) => $this->label($v, 'value'))->all(),
            ])->values()->all(),
            'images'            => $p->getMedia('gallery')->map->getUrl()->all(),
            'documents'         => $p->getMedia('documents')->map->getUrl()->all(),
            'inquiries'         => \App\Models\Inquiry::where('product_id', $p->id)->count(),
            'updated_at'        => $p->updated_at?->toDateTimeString(),
        ]));
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'product' => $schema->string()->description('Product id, SKU (e.g. "H310RC") or slug.')->required(),
        ];
    }
}
