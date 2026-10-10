<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TelegramUser extends Model
{
    protected $fillable = [
        'telegram_id', 'username', 'tg_first_name', 'tg_last_name', 'full_name', 'phone',
        'industry_id', 'locale', 'state', 'state_data', 'registered_at', 'is_blocked', 'last_seen_at',
    ];

    protected function casts(): array
    {
        return [
            'state_data'    => 'array',
            'registered_at' => 'datetime',
            'last_seen_at'  => 'datetime',
            'is_blocked'    => 'boolean',
        ];
    }

    public function industry(): BelongsTo
    {
        return $this->belongsTo(Industry::class);
    }

    public function inquiries(): HasMany
    {
        return $this->hasMany(Inquiry::class);
    }

    public function isRegistered(): bool
    {
        return $this->registered_at !== null;
    }

    public function setState(?string $state, ?array $data = null): void
    {
        $this->forceFill(['state' => $state, 'state_data' => $data])->save();
    }

    public function displayName(): string
    {
        return $this->full_name ?: trim("{$this->tg_first_name} {$this->tg_last_name}") ?: ($this->username ? "@{$this->username}" : "#{$this->telegram_id}");
    }
}
