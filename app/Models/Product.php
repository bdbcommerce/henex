<?php

namespace App\Models;

use App\Models\Concerns\HasAutoSlug;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Translatable\HasTranslations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Product extends Model implements HasMedia
{
    use HasTranslations, InteractsWithMedia, HasAutoSlug;

    // Auto slug (public URL: slug follows the source, old slugs 301-redirect)
    protected array $slugSourceFields = ['sku', 'name'];
    protected bool $slugFollowsSource = true;

    protected function slugSource(): string
    {
        return $this->sku ?: $this->firstTranslation('name');
    }

    protected $fillable = ['sku', 'slug', 'is_featured', 'is_active', 'sort_order', 'name', 'short_description', 'description', 'meta_title', 'meta_description'];
    protected $casts = ['is_featured' => 'boolean', 'is_active' => 'boolean'];
    public array $translatable = ['name', 'short_description', 'description', 'meta_title', 'meta_description'];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function attributeValues(): BelongsToMany
    {
        return $this->belongsToMany(AttributeValue::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('gallery');
        $this->addMediaCollection('documents');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(400)->height(400)
            ->format('webp')
            ->performOnCollections('gallery');

        $this->addMediaConversion('medium')
            ->width(800)
            ->format('webp')
            ->performOnCollections('gallery');
    }
}
