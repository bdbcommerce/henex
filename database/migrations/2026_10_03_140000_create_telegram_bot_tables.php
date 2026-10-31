<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Client fields of activity: retail, pharmacy, HoReCa, production, …
        Schema::create('industries', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 60)->unique();
            $table->json('name');
            $table->string('emoji', 16)->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Categories recommended for an industry (used by the bot's scanner advisor)
        Schema::create('category_industry', function (Blueprint $table) {
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('industry_id')->constrained()->cascadeOnDelete();
            $table->primary(['category_id', 'industry_id']);
        });

        Schema::create('telegram_users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('telegram_id')->unique();
            $table->string('username', 64)->nullable();
            $table->string('tg_first_name', 128)->nullable();
            $table->string('tg_last_name', 128)->nullable();
            $table->string('full_name', 160)->nullable();   // entered by the user
            $table->string('phone', 32)->nullable();         // from shared contact
            $table->foreignId('industry_id')->nullable()->constrained()->nullOnDelete();
            $table->char('locale', 2)->nullable();
            $table->string('state', 40)->nullable();        // conversation step
            $table->json('state_data')->nullable();
            $table->timestamp('registered_at')->nullable();
            $table->boolean('is_blocked')->default(false);   // user blocked the bot
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();
        });

        Schema::table('inquiries', function (Blueprint $table) {
            $table->string('source', 20)->default('website')->after('locale');
            $table->foreignId('telegram_user_id')->nullable()->after('source')->constrained()->nullOnDelete();
            $table->foreignId('industry_id')->nullable()->after('telegram_user_id')->constrained()->nullOnDelete();
        });

        // Support knowledge base: answers, setting barcodes (images), manuals (PDF) via MediaLibrary
        Schema::create('knowledge_articles', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 160)->unique();
            $table->json('title');
            $table->json('keywords')->nullable();   // comma-separated per locale, helps matching
            $table->json('answer')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->unsignedInteger('views')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('knowledge_articles');
        Schema::table('inquiries', function (Blueprint $table) {
            $table->dropConstrainedForeignId('industry_id');
            $table->dropConstrainedForeignId('telegram_user_id');
            $table->dropColumn('source');
        });
        Schema::dropIfExists('telegram_users');
        Schema::dropIfExists('category_industry');
        Schema::dropIfExists('industries');
    }
};
