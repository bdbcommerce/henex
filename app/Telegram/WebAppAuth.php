<?php

namespace App\Telegram;

/**
 * Validates Telegram Mini App initData.
 * https://core.telegram.org/bots/webapps#validating-data-received-via-the-mini-app
 */
class WebAppAuth
{
    /** @return array|null the "user" object when the signature is valid and fresh, else null */
    public static function user(?string $initData, int $maxAgeSeconds = 86400): ?array
    {
        $token = config('services.telegram.bot_token');
        if (! $initData || ! $token) {
            return null;
        }

        parse_str($initData, $data);
        $hash = $data['hash'] ?? '';
        unset($data['hash']);
        ksort($data);

        $checkString = collect($data)->map(fn ($v, $k) => "{$k}={$v}")->join("\n");
        $secretKey = hash_hmac('sha256', $token, 'WebAppData', true);

        if (! hash_equals(hash_hmac('sha256', $checkString, $secretKey), $hash)) {
            return null;
        }
        if ((int) ($data['auth_date'] ?? 0) < time() - $maxAgeSeconds) {
            return null;
        }

        return json_decode($data['user'] ?? 'null', true);
    }
}
