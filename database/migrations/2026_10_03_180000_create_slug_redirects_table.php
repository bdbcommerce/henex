<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Old public slugs → current record, so renamed products/categories/news 301-redirect instead of 404. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('slug_redirects', function (Blueprint $table) {
            $table->id();
            $table->string('model_type', 120);
            $table->unsignedBigInteger('model_id');
            $table->string('old_slug', 190);
            $table->timestamps();
            $table->unique(['model_type', 'old_slug']);
            $table->index(['model_type', 'model_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('slug_redirects');
    }
};
