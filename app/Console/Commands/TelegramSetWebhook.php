<?php

namespace App\Console\Commands;

use App\Services\TelegramService;
use Illuminate\Console\Command;

class TelegramSetWebhook extends Command
{
    protected $signature = 'telegram:set-webhook {url? : The webhook URL}';
    protected $description = 'Set or remove the Telegram bot webhook';

    public function handle(): void
    {
        $telegram = new TelegramService();
        $url = $this->argument('url');

        if ($url) {
            $result = $telegram->setWebhook($url);
            if ($result['ok'] ?? false) {
                $this->info("✅ Webhook set successfully: {$url}");
            } else {
                $this->error("❌ Failed to set webhook: " . ($result['description'] ?? 'Unknown error'));
            }
        } else {
            // Show current webhook info
            $info = $telegram->getWebhookInfo();
            if ($info['ok'] ?? false) {
                $webhookUrl = $info['result']['url'] ?? 'Not set';
                $this->info("Current webhook URL: {$webhookUrl}");
                $this->info("Pending updates: " . ($info['result']['pending_update_count'] ?? 0));

                if ($this->confirm('Do you want to remove the webhook?')) {
                    $result = $telegram->removeWebhook();
                    $this->info($result['ok'] ? '✅ Webhook removed.' : '❌ Failed to remove webhook.');
                }
            }
        }
    }
}
