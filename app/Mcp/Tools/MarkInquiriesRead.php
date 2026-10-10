<?php

namespace App\Mcp\Tools;

use App\Models\Inquiry;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsIdempotent;

#[IsIdempotent]
#[Description('Mark inquiries as read (or back to unread) by id.')]
class MarkInquiriesRead extends Tool
{
    public function handle(Request $request): Response
    {
        $v = $request->validate([
            'ids'     => 'required|array|min:1|max:500',
            'ids.*'   => 'integer',
            'is_read' => 'nullable|boolean',
        ]);

        $updated = Inquiry::whereIn('id', $v['ids'])->update(['is_read' => $v['is_read'] ?? true]);

        return Response::json([
            'updated'      => $updated,
            'not_found'    => count(array_unique($v['ids'])) - $updated,
            'unread_total' => Inquiry::where('is_read', false)->count(),
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'ids'     => $schema->array()->items($schema->integer())->required(),
            'is_read' => $schema->boolean()->description('Default true. false = mark unread.'),
        ];
    }
}
