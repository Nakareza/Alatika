<?php

namespace App\Console\Commands;

use App\Http\Controllers\TelegramWebhookController;
use App\Services\TelegramService;
use Illuminate\Console\Command;
use Illuminate\Http\Request;

class TelegramPoll extends Command
{
    protected $signature = 'telegram:poll';
    protected $description = 'Poll for Telegram updates (for local development without webhook)';

    public function handle(): void
    {
        $this->info('🤖 Telegram Bot polling started. Press Ctrl+C to stop.');
        $this->info('Waiting for messages...');

        $telegram = new TelegramService();

        // Remove webhook first (required for getUpdates to work)
        $telegram->removeWebhook();

        $offset = 0;

        while (true) {
            try {
                $response = $telegram->getUpdates($offset);

                if (($response['ok'] ?? false) && !empty($response['result'])) {
                    foreach ($response['result'] as $update) {
                        $offset = $update['update_id'] + 1;

                        // Log the update
                        $from = $update['message']['from']['first_name'] ?? 'Unknown';
                        $text = $update['message']['text'] ?? $update['callback_query']['data'] ?? '[non-text]';
                        $this->line("[" . now()->format('H:i:s') . "] {$from}: {$text}");

                        // Process through the webhook controller
                        $controller = app(TelegramWebhookController::class);
                        $request = new Request();
                        $request->replace($update);
                        $controller->handle($request);
                    }
                }
            } catch (\Exception $e) {
                $this->error("Error: " . $e->getMessage());
                sleep(3);
            }

            usleep(500000); // 0.5 second delay between polls
        }
    }
}
