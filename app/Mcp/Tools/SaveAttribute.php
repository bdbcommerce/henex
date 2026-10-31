<?php

namespace App\Mcp\Tools;

use App\Mcp\Tools\Concerns\FormatsModels;
use App\Models\Attribute;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Str;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Create an attribute or update an existing one (matched by slug): its translated name, flags, and append new values. Existing values are never removed; a value already present (any locale, case-insensitive) is skipped.')]
class SaveAttribute extends Tool
{
    use FormatsModels;

    public function handle(Request $request): Response
    {
        $v = $request->validate([
            'slug'          => 'required|alpha_dash|max:120',
            'name'          => 'nullable|array',
            'name.uz'       => 'nullable|string|max:120',
            'name.ru'       => 'nullable|string|max:120',
            'name.en'       => 'nullable|string|max:120',
            'is_multiple'   => 'nullable|boolean',
            'is_filterable' => 'nullable|boolean',
            'is_active'     => 'nullable|boolean',
            'add_values'    => 'nullable|array',
            'add_values.*.uz' => 'required|string|max:120',
            'add_values.*.ru' => 'nullable|string|max:120',
            'add_values.*.en' => 'nullable|string|max:120',
        ]);

        $attr = Attribute::with('values')->firstOrNew(['slug' => Str::slug($v['slug'])]);
        $created = ! $attr->exists;

        if ($created && empty($v['name']['uz'])) {
            return Response::error('A new attribute needs at least name.uz.');
        }

        foreach ($v['name'] ?? [] as $locale => $text) {
            if ($text !== null && $text !== '') {
                $attr->setTranslation('name', $locale, $text);
            }
        }
        foreach (['is_multiple', 'is_filterable', 'is_active'] as $flag) {
            if (isset($v[$flag])) {
                $attr->{$flag} = $v[$flag];
            }
        }
        $attr->save();

        $existing = $attr->values->flatMap(fn ($x) => array_map('mb_strtolower', $x->getTranslations('value')))->all();
        $added = [];
        $skipped = [];
        $order = (int) $attr->values->max('sort_order');

        foreach ($v['add_values'] ?? [] as $row) {
            $row = array_filter($row, fn ($t) => $t !== null && $t !== '');
            if (array_intersect(array_map('mb_strtolower', $row), $existing)) {
                $skipped[] = $row['uz'];
                continue;
            }
            $value = $attr->values()->make(['sort_order' => ++$order]);
            $value->setTranslations('value', $row);
            $value->save();
            $existing = array_merge($existing, array_map('mb_strtolower', $row));
            $added[] = $row['uz'];
        }

        return Response::json([
            'created'        => $created,
            'attribute'      => ['id' => $attr->id, 'slug' => $attr->slug, 'name' => $this->tr($attr, 'name')],
            'values_added'   => $added,
            'values_skipped' => $skipped,
            'admin_url'      => route('admin.attributes.edit', $attr),
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        $locales = fn () => $schema->object([
            'uz' => $schema->string()->required(),
            'ru' => $schema->string(),
            'en' => $schema->string(),
        ]);

        return [
            'slug'          => $schema->string()->description('e.g. "interface", "scan-type". Existing slug = update.')->required(),
            'name'          => $schema->object([
                'uz' => $schema->string(), 'ru' => $schema->string(), 'en' => $schema->string(),
            ])->description('Translated attribute name. uz required when creating.'),
            'is_multiple'   => $schema->boolean()->description('Product may have several values (default true).'),
            'is_filterable' => $schema->boolean(),
            'is_active'     => $schema->boolean(),
            'add_values'    => $schema->array()->items($locales())->description('New values, e.g. [{"uz":"Simsiz","ru":"Беспроводной","en":"Wireless"}].'),
        ];
    }
}
