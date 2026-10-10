<?php

namespace App\Models;

use App\Models\Concerns\HasAutoSlug;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class Industry extends Model
{
    use HasTranslations, HasAutoSlug;

    // Auto slug (fixed after creation: referenced by seeders / bot / MCP)
    protected array $slugSourceFields = ['name'];
    protected bool $slugFollowsSource = false;

    protected function slugSource(): string
    {
        return $this->firstTranslation('name');
    }

    protected $fillable = ['slug', 'name', 'emoji', 'sort_order', 'is_active'];
    public array $translatable = ['name'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    /** Categories recommended to clients of this industry. */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function telegramUsers(): HasMany
    {
        return $this->hasMany(TelegramUser::class);
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
