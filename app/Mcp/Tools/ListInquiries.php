<?php

namespace App\Mcp\Tools;

use App\Mcp\Tools\Concerns\FormatsModels;
use App\Models\Inquiry;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[IsReadOnly]
#[Description('List customer inquiries / quote requests, newest first. Filter by unread, period, product or text. Includes contact details and message.')]
class ListInquiries extends Tool
{
    use FormatsModels;

    public function handle(Request $request): Response
    {
        $v = $request->validate([
            'unread_only' => 'nullable|boolean',
            'days'        => 'nullable|integer|min:1|max:3650',
            'product'     => 'nullable',
            'search'      => 'nullable|string|max:100',
            'limit'       => 'nullable|integer|min:1|max:200',
        ]);

        $q = Inquiry::query()->with('product')->latest();

        if ($v['unread_only'] ?? false) {
            $q->where('is_read', false);
        }
        if ($days = $v['days'] ?? null) {
            $q->where('created_at', '>=', now()->subDays($days));
        }
        if (isset($v['product'])) {
            $p = $this->findProduct($v['product']);
            if (! $p) {
                return Response::error("No product found for '{$v['product']}'.");
            }
            $q->where('product_id', $p->id);
        }
        if ($s = $v['search'] ?? null) {
            $q->where(fn ($q) => $q->where('name', 'like', "%{$s}%")->orWhere('company', 'like', "%{$s}%")
                ->orWhere('phone', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%")->orWhere('message', 'like', "%{$s}%"));
        }

        $total = (clone $q)->count();
        $rows = $q->limit((int) ($v['limit'] ?? 30))->get();

        return Response::json([
            'total'     => $total,
            'inquiries' => $rows->map(fn ($i) => [
                'id'         => $i->id,
                'created_at' => $i->created_at->toDateTimeString(),
                'is_read'    => (bool) $i->is_read,
                'name'       => $i->name,
                'company'    => $i->company,
                'phone'      => $i->phone,
                'email'      => $i->email,
                'locale'     => $i->locale,
                'product'    => $i->product_id && $i->product?->exists ? $this->label($i->product) . " ({$i->product->sku})" : null,
                'message'    => $i->message,
            ])->all(),
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'unread_only' => $schema->boolean()->description('Only unread inquiries.'),
            'days'        => $schema->integer()->description('Only inquiries from the last N days.'),
            'product'     => $schema->string()->description('Product id, SKU or slug.'),
            'search'      => $schema->string()->description('Match name, company, phone, email or message.'),
            'limit'       => $schema->integer()->description('Max rows (default 30, max 200).'),
        ];
    }
}
