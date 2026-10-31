<?php

namespace App\Mcp\Servers;

use App\Mcp\Tools;
use Laravel\Mcp\Server;
use Laravel\Mcp\Server\Attributes\Instructions;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Attributes\Version;

#[Name('Henex.uz Admin')]
#[Version('1.0.0')]
#[Instructions(<<<'TXT'
Admin access to henex.uz — the Uzbekistan distributor site for HENEX barcode scanners.
Content is trilingual: uz (default, required), ru, en. Products are referenced by id, SKU or slug.
Start with get_statistics for an overview; use list_catalog_structure to discover category/attribute slugs.
Write tools never delete anything. Confirm with the user before bulk edits (more than ~5 products).
TXT)]
class HenexServer extends Server
{
    protected array $tools = [
        // Read
        Tools\GetStatistics::class,
        Tools\SearchProducts::class,
        Tools\GetProduct::class,
        Tools\ListInquiries::class,
        Tools\ListCatalogStructure::class,
        Tools\ListArticles::class,
        // Safe edits (no deletes)
        Tools\UpdateProduct::class,
        Tools\SetProductAttributes::class,
        Tools\SaveAttribute::class,
        Tools\MarkInquiriesRead::class,
        Tools\ManageSettings::class,
    ];
}
