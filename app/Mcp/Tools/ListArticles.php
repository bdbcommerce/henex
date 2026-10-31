<?php

namespace App\Mcp\Tools;

use App\Mcp\Tools\Concerns\FormatsModels;
use App\Models\Article;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[IsReadOnly]
#[Description('List news/blog/guide articles with publish status and which locales have a title and content.')]
class ListArticles extends Tool
{
    use FormatsModels;

    public function handle(Request $request): Response
    {
        $v = $request->validate([
            'status' => 'nullable|in:all,published,draft',
            'limit'  => 'nullable|integer|min:1|max:200',
        ]);

        $q = Article::query()->latest('published_at')->latest();
        match ($v['status'] ?? 'all') {
            'published' => $q->where('is_published', true),
            'draft'     => $q->where('is_published', false),
            default     => null,
        };

        return Response::json($q->limit((int) ($v['limit'] ?? 50))->get()->map(fn ($a) => [
            'id'           => $a->id,
            'slug'         => $a->slug,
            'type'         => $a->type,
            'title'        => $this->tr($a, 'title'),
            'is_published' => (bool) $a->is_published,
            'published_at' => $a->published_at?->toDateTimeString(),
            'locales_with_content' => array_keys(array_filter($a->getTranslations('content'))),
        ])->all());
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'status' => $schema->string()->enum(['all', 'published', 'draft']),
            'limit'  => $schema->integer()->description('Max rows (default 50).'),
        ];
    }
}
