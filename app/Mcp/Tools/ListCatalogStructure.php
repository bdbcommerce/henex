<?php

namespace App\Mcp\Tools;

use App\Mcp\Tools\Concerns\FormatsModels;
use App\Models\Attribute;
use App\Models\Category;
use App\Models\Region;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[IsReadOnly]
#[Description('Reference data: category tree (with product counts), attributes with their values, or regions with their resellers/service centers. Use it to find the slugs other tools expect.')]
class ListCatalogStructure extends Tool
{
    use FormatsModels;

    public function handle(Request $request): Response
    {
        $what = $request->validate(['what' => 'required|in:categories,attributes,resellers'])['what'];

        return Response::json(match ($what) {
            'categories' => Category::whereNull('parent_id')->withCount('products')
                ->with(['children' => fn ($q) => $q->withCount('products')->orderBy('sort_order')])
                ->orderBy('sort_order')->get()
                ->map(fn ($c) => [
                    'id' => $c->id, 'slug' => $c->slug, 'name' => $this->tr($c, 'name'),
                    'is_active' => (bool) $c->is_active, 'products' => $c->products_count,
                    'children' => $c->children->map(fn ($ch) => [
                        'id' => $ch->id, 'slug' => $ch->slug, 'name' => $this->tr($ch, 'name'),
                        'is_active' => (bool) $ch->is_active, 'products' => $ch->products_count,
                    ])->all(),
                ])->all(),

            'attributes' => Attribute::ordered()->with(['values' => fn ($q) => $q->withCount('products')])->get()
                ->map(fn ($a) => [
                    'id' => $a->id, 'slug' => $a->slug, 'name' => $this->tr($a, 'name'),
                    'is_multiple' => $a->is_multiple, 'is_filterable' => $a->is_filterable, 'is_active' => $a->is_active,
                    'values' => $a->values->map(fn ($v) => [
                        'id' => $v->id, 'value' => $this->tr($v, 'value'), 'products' => $v->products_count,
                    ])->all(),
                ])->all(),

            'resellers' => Region::orderBy('sort_order')->with(['resellers' => fn ($q) => $q->orderBy('sort_order')])->get()
                ->map(fn ($r) => [
                    'region' => $r->slug, 'name' => $this->label($r),
                    'resellers' => $r->resellers->map(fn ($s) => [
                        'id' => $s->id, 'name' => $s->name, 'type' => $s->type, 'is_active' => (bool) $s->is_active,
                        'phone' => $s->phone, 'email' => $s->email, 'website' => $s->website,
                        'address' => $this->label($s, 'address'),
                    ])->all(),
                ])->all(),
        });
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'what' => $schema->string()->enum(['categories', 'attributes', 'resellers'])->required(),
        ];
    }
}
