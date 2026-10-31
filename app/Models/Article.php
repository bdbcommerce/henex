<?php

namespace App\Models;

use App\Models\Concerns\HasAutoSlug;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Article extends Model implements HasMedia
{
    use HasTranslations, InteractsWithMedia, HasAutoSlug;

    // Auto slug (public URL: slug follows the source, old slugs 301-redirect)
    protected array $slugSourceFields = ['title'];
    protected bool $slugFollowsSource = true;

    protected function slugSource(): string
    {
        return $this->firstTranslation('title');
    }

    protected $fillable = ['slug', 'user_id', 'type', 'is_published', 'published_at', 'cover_image', 'title', 'excerpt', 'content', 'meta_title', 'meta_description'];
    protected $casts = ['published_at' => 'datetime', 'is_published' => 'boolean'];
    public array $translatable = ['title', 'excerpt', 'content', 'meta_title', 'meta_description'];

    // Relation — actual DB column is user_id (see 2026_06_25_133041_create_articles_table.php)
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** @deprecated use author() */
    public function user(): BelongsTo
    {
        return $this->author();
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('is_published', true)->whereNotNull('published_at');
    }

    public function scopeOrdered($query)
    {
        return $query->orderByDesc('published_at');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('cover')->singleFile();
    }
}
