<?php

return [
    'bot_token' => env('TELEGRAM_BOT_TOKEN'),
    'bot_username' => env('TELEGRAM_BOT_USERNAME', 'Inventaris_Alatika_bot'),
    'webhook_url' => env('TELEGRAM_WEBHOOK_URL'),
    'api_url' => 'https://api.telegram.org/bot',
];
