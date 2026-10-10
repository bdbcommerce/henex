<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'telegram' => [
        'bot_token'      => env('TELEGRAM_BOT_TOKEN'),
        'bot_username'   => env('TELEGRAM_BOT_USERNAME'),
        'webapp_url'     => env('TELEGRAM_WEBAPP_URL'),
        'webhook_secret' => env('TELEGRAM_WEBHOOK_SECRET'),
        'admin_chat_id'  => env('TELEGRAM_ADMIN_CHAT_ID'),   // inquiry notifications
    ],

    // Any OpenAI-compatible chat-completions API. Default: Google Gemini (free tier).
    // Groq:       https://api.groq.com/openai/v1/          model llama-3.3-70b-versatile
    // OpenRouter: https://openrouter.ai/api/v1/            model e.g. meta-llama/llama-3.3-70b-instruct:free
    'ai' => [
        'base_url' => env('AI_BASE_URL', 'https://generativelanguage.googleapis.com/v1beta/openai/'),
        'api_key'  => env('AI_API_KEY'),
        'model'    => env('AI_MODEL', 'gemini-2.5-flash'),
        'timeout'  => (int) env('AI_TIMEOUT', 30),
    ],

    // First admin account, created/updated by AdminSeeder.
    'admin' => [
        'name'     => env('ADMIN_NAME'),
        'email'    => env('ADMIN_EMAIL'),
        'password' => env('ADMIN_PASSWORD'),
    ],

    'mcp' => [
        'token' => env('MCP_TOKEN'),                         // bearer token for /mcp (empty = endpoint disabled)
    ],

    'inquiries' => [
        'notify_email' => env('INQUIRY_NOTIFY_EMAIL'),       // admin mailbox for new inquiries
    ],

];
