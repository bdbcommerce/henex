<?php

namespace App\Mcp\Tools;

use App\Mcp\Tools\Concerns\FormatsModels;
use App\Models\Attribute;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsIdempotent;

#[IsIdempotent]
#[Description('Set which values of one attribute a product has (replaces that attribute\'s current values on the product; an empty list clears it). Values are matched by text in any locale; they must already exist — use save_attribute to add new ones.')]
class SetProductAttributes extends Tool
{
    use FormatsModels;

    public function handle(Request $request): Response
    {
        $v = $request->validate([
            'product'   => 'required',
            'attribute' => 'required|string',
            'values'    => 'present|array',
            'values.*'  => 'string',
        ]);

        $p = $this->findProduct($v['product']);
        if (! $p) {
            return Response::error("No product found for '{$v['product']}'.");
        }

        $attr = Attribute::with('values')->where('slug', $v['attribute'])->first();
        if (! $attr) {
            return Response::error("No attribute with slug '{$v['attribute']}'. Use list_catalog_structure(what: attributes).");
        }

        $ids = [];
        $unknown = [];
        foreach ($v['values'] as $text) {
            $needle = mb_strtolower(trim($text));
            $match = $attr->values->first(fn ($val) => in_array($needle, array_map('mb_strtolower', $val->getTranslations('value')), true));
            $match ? $ids[] = $match->id : $unknown[] = $text;
        }

        if ($unknown) {
            return Response::error("Unknown values for '{$attr->slug}': " . implode(', ', $unknown)
                . '. Known: ' . $attr->values->map(fn ($x) => $this->label($x, 'value'))->join(', '));
        }
        if (! $attr->is_multiple && count($ids) > 1) {
            return Response::error("'{$attr->slug}' allows only one value per product.");
        }

        // Replace only this attribute's values, keep the product's other attributes.
        $p->attributeValues()->detach($attr->values->pluck('id'));
        $p->attributeValues()->attach(array_unique($ids));

        return Response::json([
            'product'   => $this->productSummary($p),
            'attribute' => $attr->slug,
            'values'    => $attr->values->whereIn('id', $ids)->map(fn ($x) => $this->label($x, 'value'))->values(),
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'product'   => $schema->string()->description('Product id, SKU or slug.')->required(),
            'attribute' => $schema->string()->description('Attribute slug, e.g. "interface".')->required(),
            'values'    => $schema->array()->items($schema->string())->description('Value texts, e.g. ["USB", "RS232"]. Empty array clears.')->required(),
        ];
    }
}
