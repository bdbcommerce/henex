<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class Region extends Model
{
    use HasTranslations;

    protected $fillable = ['slug', 'name', 'sort_order'];
    public array $translatable = ['name'];

    public function resellers(): HasMany
    {
        return $this->hasMany(Reseller::class);
    }
}
