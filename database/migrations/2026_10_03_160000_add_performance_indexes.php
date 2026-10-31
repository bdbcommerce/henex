<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Indexes for the filters/sorts every public page and admin list runs. */
return new class extends Migration
{
    private array $indexes = [
        'products'       => [['is_active', 'sort_order'], ['is_featured', 'is_active']],
        'categories'     => [['is_active', 'parent_id', 'sort_order']],
        'resellers'      => [['is_active', 'sort_order'], ['type']],
        'articles'       => [['is_published', 'published_at']],
        'slides'         => [['is_active', 'sort_order']],
        'inquiries'      => [['is_read', 'created_at'], ['created_at']],
        'regions'        => [['sort_order']],
        'telegram_users' => [['registered_at'], ['last_seen_at']],
    ];

    public function up(): void
    {
        foreach ($this->indexes as $table => $sets) {
            Schema::table($table, function (Blueprint $t) use ($table, $sets) {
                foreach ($sets as $columns) {
                    $t->index($columns, $this->name($table, $columns));
                }
            });
        }
    }

    public function down(): void
    {
        foreach ($this->indexes as $table => $sets) {
            Schema::table($table, function (Blueprint $t) use ($table, $sets) {
                foreach ($sets as $columns) {
                    $t->dropIndex($this->name($table, $columns));
                }
            });
        }
    }

    private function name(string $table, array $columns): string
    {
        return 'perf_' . $table . '_' . implode('_', $columns);
    }
};
