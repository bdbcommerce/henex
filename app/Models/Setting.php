<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    protected $primaryKey = 'key';
    public $timestamps = false;

    protected $fillable = ['key', 'value'];

    protected static function booted(): void
    {
        $flush = fn () => cache()->forget(\App\Support\SiteData::SETTINGS_KEY);
        static::saved($flush);
        static::deleted($flush);
    }
}
