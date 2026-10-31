<?php

namespace App\Models;

use App\Models\Concerns\HasAutoSlug;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class KnowledgeArticle extends Model implements HasMedia
{
    use HasTranslations, InteractsWithMedia, HasAutoSlug;

    // Auto slug (fixed after creation: referenced by seeders / bot / MCP)
    protected array $slugSourceFields = ['title'];
    protected bool $slugFollowsSource = false;

    protected function slugSource(): string
    {
        return $this->firstTranslation('title');
    }

    protected $fillable = ['slug', 'title', 'keywords', 'answer', 'is_active', 'sort_order', 'views'];
    public array $translatable = ['title', 'keywords', 'answer'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function registerMediaCollections(): void
    {
        // Configuration barcodes the client scans with the device (sent as photos by the bot)
        $this->addMediaCollection('barcodes')->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif']);
        // Manuals / setting guides (sent as documents by the bot)
        $this->addMediaCollection('manuals')->acceptsMimeTypes(['application/pdf']);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
