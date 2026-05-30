<?php

namespace App\Console\Commands;

use App\Services\TelegramService;
use Illuminate\Console\Command;

class TelegramSetupBot extends Command
{
    protected $signature = 'telegram:setup';
    protected $description = 'Setup Telegram bot commands menu and menu button';

    public function handle(): void
    {
        $this->info('🤖 Setting up Telegram bot...');

        $telegram = new TelegramService();

        // Set commands menu
        $this->info('📝 Setting up command menu...');
        $commandsResult = $telegram->setCommands();
        if ($commandsResult['ok'] ?? false) {
            $this->info('✅ Command menu set successfully!');
        } else {
            $this->error('❌ Failed to set command menu: ' . ($commandsResult['description'] ?? 'Unknown error'));
        }

        // Set menu button
        $this->info('🔘 Setting up menu button...');
        $menuResult = $telegram->setMenuButton();
        if ($menuResult['ok'] ?? false) {
            $this->info('✅ Menu button set successfully!');
        } else {
            $this->error('❌ Failed to set menu button: ' . ($menuResult['description'] ?? 'Unknown error'));
        }

        $this->info('🎉 Telegram bot setup complete!');
    }
}
