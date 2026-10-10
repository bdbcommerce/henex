<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Specifications are replaced by attributes. Almost every spec row only repeated a
 * line of the product description; the few that did not are appended to the
 * description (per locale) before the table is dropped, so no text is lost.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('product_specifications')) {
            return;
        }

        $norm = fn (?string $s) => mb_strtolower(preg_replace('/\s+/u', ' ', trim(html_entity_decode(strip_tags((string) $s)))));

        foreach (DB::table('product_specifications')->orderBy('sort_order')->get()->groupBy('product_id') as $productId => $specs) {
            $product = DB::table('products')->where('id', $productId)->first(['id', 'description']);
            if (! $product) {
                continue;
            }

            $description = json_decode($product->description ?? '{}', true) ?: [];
            $changed = false;

            foreach (['uz', 'ru', 'en'] as $locale) {
                $current = $description[$locale] ?? '';
                $missing = $specs
                    ->map(fn ($s) => (json_decode($s->value, true) ?: [])[$locale] ?? null)
                    ->filter(fn ($v) => filled($v) && ! str_contains($norm($current), $norm($v)))
                    ->unique();

                if ($missing->isNotEmpty()) {
                    $description[$locale] = $current . '<ul>' . $missing->map(fn ($v) => '<li>' . e($v) . '</li>')->join('') . '</ul>';
                    $changed = true;
                }
            }

            if ($changed) {
                DB::table('products')->where('id', $productId)
                    ->update(['description' => json_encode($description, JSON_UNESCAPED_UNICODE)]);
            }
        }

        Schema::drop('product_specifications');
    }

    public function down(): void
    {
        // Structure only — the rows were merged into descriptions and cannot be split back out.
        Schema::create('product_specifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->json('key');
            $table->json('value');
            $table->timestamps();
        });
    }
};
