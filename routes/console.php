<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('telegram:setup', function (\App\Telegram\BotSetup $setup) {
    $this->info('Webhook URL: ' . $setup->webhookUrl());
    foreach ($setup->run() as $step => $ok) {
        $ok === true ? $this->line("  ✔ {$step}") : $this->error("  ✘ {$step}" . (is_string($ok) ? ": {$ok}" : ''));
    }
})->purpose('Register the Telegram webhook, bot commands and Mini App menu button');
