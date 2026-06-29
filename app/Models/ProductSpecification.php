<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class ProductSpecification extends Model
{
    use HasTranslations;

    protected $fillable = ['product_id', 'sort_order', 'key', 'value'];
    public array $translatable = ['key', 'value'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
