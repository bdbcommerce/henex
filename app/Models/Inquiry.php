<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Inquiry extends Model
{
    protected $fillable = ['name', 'company', 'phone', 'email', 'message', 'product_id', 'locale', 'is_read', 'source', 'telegram_user_id', 'industry_id'];
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class)->withDefault();
    }
    public function telegramUser(): BelongsTo
    {
        return $this->belongsTo(TelegramUser::class);
    }
    public function industry(): BelongsTo
    {
        return $this->belongsTo(Industry::class);
    }
}
