<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class Reseller extends Model
{
    use HasTranslations;

    protected $fillable = ['region_id', 'name', 'type', 'phone', 'phone2', 'email', 'website', 'address', 'latitude', 'longitude', 'is_active', 'sort_order'];
    public array $translatable = ['address'];

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }
}
