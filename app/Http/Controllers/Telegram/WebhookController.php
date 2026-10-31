<?php

namespace App\Http\Controllers\Telegram;

use App\Http\Controllers\Controller;
use App\Telegram\BotHandler;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function __invoke(Request $request)
    {
        $secret = (string) config('services.telegram.webhook_secret');

        // Telegram echoes the secret_token given to setWebhook in this header.
        if ($secret === '' || ! hash_equals($secret, (string) $request->header('X-Telegram-Bot-Api-Secret-Token'))) {
            abort(403);
        }

        $update = $request->json()->all();

        // Answer Telegram immediately; slow work (AI calls) runs after the response is sent.
        dispatch(fn () => app(BotHandler::class)->handle($update))->afterResponse();

        return response()->json(['ok' => true]);
    }
}
