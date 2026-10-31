<?php

namespace App\Models;

use App\Models\Concerns\HasAutoSlug;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class Attribute extends Model
{
    use HasTranslations, HasAutoSlug;

    // Auto slug (fixed after creation: referenced by seeders / bot / MCP)
    protected array $slugSourceFields = ['name'];
    protected bool $slugFollowsSource = false;

    protected function slugSource(): string
    {
        return $this->firstTranslation('name');
    }

    protected $fillable = ['slug', 'name', 'is_multiple', 'is_filterable', 'is_active', 'sort_order'];
    public array $translatable = ['name'];

    protected function casts(): array
    {
        return [
            'is_multiple'   => 'boolean',
            'is_filterable' => 'boolean',
            'is_active'     => 'boolean',
        ];
    }

    public function values(): HasMany
    {
        return $this->hasMany(AttributeValue::class)->orderBy('sort_order');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}
