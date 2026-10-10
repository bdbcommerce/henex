<?php

namespace App\Models;

use App\Models\Concerns\HasAutoSlug;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Translatable\HasTranslations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Category extends Model implements HasMedia
{
    use HasTranslations, InteractsWithMedia, HasAutoSlug;

    // Auto slug (public URL: slug follows the source, old slugs 301-redirect)
    protected array $slugSourceFields = ['name'];
    protected bool $slugFollowsSource = true;

    protected function slugSource(): string
    {
        return $this->firstTranslation('name');
    }

    protected $fillable = ['slug', 'parent_id', 'image', 'sort_order', 'is_active', 'name', 'description', 'meta_title', 'meta_description'];
    public array $translatable = ['name', 'description', 'meta_title', 'meta_description'];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')->singleFile();
    }
}
