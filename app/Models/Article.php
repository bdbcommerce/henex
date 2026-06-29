<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Article extends Model implements HasMedia
{
    use HasTranslations, InteractsWithMedia;

    protected $fillable = ['slug', 'author_id', 'type', 'is_published', 'published_at', 'cover_image', 'title', 'excerpt', 'content', 'meta_title', 'meta_description'];
    protected $casts = ['published_at' => 'datetime', 'is_published' => 'boolean'];
    public array $translatable = ['title', 'excerpt', 'content', 'meta_title', 'meta_description'];

    // Relation — DB column is author_id per spec
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
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
