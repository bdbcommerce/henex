<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Slide extends Model implements HasMedia
{
    use HasTranslations, InteractsWithMedia;

    protected $fillable = ['sort_order', 'is_active', 'link', 'title', 'subtitle', 'button_text'];
    public array $translatable = ['title', 'subtitle', 'button_text'];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('slide')->singleFile();
    }
}
