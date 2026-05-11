<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    protected string $token;
    protected string $apiUrl;

    public function __construct()
    {
        $this->token = config('telegram.bot_token');
        $this->apiUrl = config('telegram.api_url') . $this->token;
    }

    /**
     * Send a text message to a Telegram chat
     */
    public function sendMessage($chatId, $text, $replyMarkup = null)
{
    $payload = [
        'chat_id' => $chatId,
        'text' => $text,
        'parse_mode' => 'HTML',
    ];

    // tambahan tombol
    if ($replyMarkup) {
        $payload['reply_markup'] = json_encode($replyMarkup);
    }

    return Http::post(
        $this->apiUrl . '/sendMessage',
        $payload
    )->json();
}

    /**
     * Send message with inline keyboard buttons
     */
    public function sendMessageWithButtons(string $chatId, string $message, array $buttons): array
    {
        return $this->sendMessage($chatId, $message, [
        'inline_keyboard' => $buttons,
        ]);
    }

    /**
     * Answer a callback query (from inline keyboard)
     */
    public function answerCallbackQuery(string $callbackQueryId, string $text = '', bool $showAlert = false): array
    {
        return $this->request('answerCallbackQuery', [
            'callback_query_id' => $callbackQueryId,
            'text' => $text,
            'show_alert' => $showAlert,
        ]);
    }

    /**
     * Set webhook URL
     */
    public function setWebhook(string $url): array
    {
        return $this->request('setWebhook', [
            'url' => $url,
            'allowed_updates' => json_encode(['message', 'callback_query']),
        ]);
    }

    /**
     * Remove webhook
     */
    public function removeWebhook(): array
    {
        return $this->request('deleteWebhook');
    }

    /**
     * Get webhook info
     */
    public function getWebhookInfo(): array
    {
        return $this->request('getWebhookInfo');
    }

    /**
     * Get available updates (for polling mode)
     */
    public function getUpdates(int $offset = 0): array
    {
        return $this->request('getUpdates', [
            'offset' => $offset,
            'timeout' => 30,
        ]);
    }

    // ===================================================
    // NOTIFICATION METHODS (Web → Telegram)
    // ===================================================

    /**
     * Notify user that their peminjaman has been approved
     */
    public function notifyPeminjamanApproved(User $user, array $data): bool
    {
        if (!$user->hasTelegram()) return false;

        $approverRole = $data['approver_role'] ?? 'Admin';
        $message = "✅ <b>Peminjaman Disetujui!</b>\n\n"
            . "📋 Kode: <code>{$data['kode']}</code>\n"
            . "🔧 Alat: <b>{$data['alat']}</b>\n"
            . "📦 Jumlah: {$data['jumlah']} unit\n"
            . "📅 Deadline: {$data['deadline']}\n"
            . "👤 Disetujui oleh: {$approverRole}\n\n"
            . "Silakan ambil alat di laboratorium. 🏫";

        $result = $this->sendMessage($user->telegram_chat_id, $message);
        return $result['ok'] ?? false;
    }

    /**
     * Notify user that their peminjaman has been rejected
     */
    public function notifyPeminjamanRejected(User $user, array $data): bool
    {
        if (!$user->hasTelegram()) return false;

        $message = "❌ <b>Peminjaman Ditolak</b>\n\n"
            . "📋 Kode: <code>{$data['kode']}</code>\n"
            . "🔧 Alat: <b>{$data['alat']}</b>\n"
            . "📝 Alasan: {$data['alasan']}\n\n"
            . "Silakan hubungi petugas untuk informasi lebih lanjut.";

        $result = $this->sendMessage($user->telegram_chat_id, $message);
        return $result['ok'] ?? false;
    }

    /**
     * Send deadline reminder
     */
    public function notifyDeadlineReminder(User $user, array $data): bool
    {
        if (!$user->hasTelegram()) return false;

        $message = "⏰ <b>Reminder Deadline Pengembalian</b>\n\n"
            . "📋 Kode: <code>{$data['kode']}</code>\n"
            . "🔧 Alat: <b>{$data['alat']}</b>\n"
            . "📅 Deadline: <b>{$data['deadline']}</b>\n\n"
            . "⚠️ Jangan lupa mengembalikan alat tepat waktu!\n"
            . "Ketik <code>/kembali {$data['kode']}</code> saat mengembalikan.";

        $result = $this->sendMessage($user->telegram_chat_id, $message);
        return $result['ok'] ?? false;
    }

    /**
     * Notify Kalab about escalated overdue return
     */
    public function notifyEscalation(User $kalab, array $data): bool
    {
        if (!$kalab->hasTelegram()) return false;

        $message = "🚨 <b>ESKALASI KETERLAMBATAN</b> 🚨\n\n"
            . "📋 Kode: <code>{$data['kode']}</code>\n"
            . "👤 Peminjam: <b>{$data['peminjam_nama']}</b>\n"
            . "🔧 Alat: <b>{$data['alat']}</b>\n"
            . "⏳ Terlambat: <b>{$data['hari_terlambat']} hari</b>\n\n"
            . "Mohon tindak lanjut dari Kepala Laboratorium.";

        $result = $this->sendMessage($kalab->telegram_chat_id, $message);
        return $result['ok'] ?? false;
    }

    /**
     * Notify user about overdue return
     */
    public function notifyOverdue(User $user, array $data): bool
    {
        if (!$user->hasTelegram()) return false;

        $message = "🚨 <b>TERLAMBAT MENGEMBALIKAN!</b>\n\n"
            . "📋 Kode: <code>{$data['kode']}</code>\n"
            . "🔧 Alat: <b>{$data['alat']}</b>\n"
            . "📅 Deadline: {$data['deadline']}\n"
            . "⏳ Terlambat: <b>{$data['hari_terlambat']} hari</b>\n\n"
            . "Segera kembalikan alat ke laboratorium! 🏃";

        $result = $this->sendMessage($user->telegram_chat_id, $message);
        return $result['ok'] ?? false;
    }

    /**
     * Notify admin/kalab about new borrowing request
     */
    public function notifyNewRequest(User $approver, array $data): bool
    {
        if (!$approver->hasTelegram()) return false;

        $roleLabel = $data['peminjam_role'] === 'mahasiswa' ? 'Mahasiswa' : 'Dosen';

        $message = "📩 <b>Pengajuan Peminjaman Baru</b>\n\n"
            . "👤 Peminjam: <b>{$data['peminjam_nama']}</b> ({$roleLabel})\n"
            . "🔧 Alat: <b>{$data['alat']}</b>\n"
            . "📦 Jumlah: {$data['jumlah']} unit\n"
            . "📋 Kode: <code>{$data['kode']}</code>\n\n"
            . "Ketik <code>/approve {$data['kode']}</code> untuk menyetujui\n"
            . "Ketik <code>/reject {$data['kode']} [alasan]</code> untuk menolak";

        $buttons = [
            [
                ['text' => '✅ Setujui', 'callback_data' => "approve_{$data['kode']}"],
                ['text' => '❌ Tolak', 'callback_data' => "reject_{$data['kode']}"],
            ]
        ];

        $result = $this->sendMessageWithButtons($approver->telegram_chat_id, $message, $buttons);
        return $result['ok'] ?? false;
    }

    /**
     * Notify admin about return confirmation from user
     */
    public function notifyReturnConfirmation(User $admin, array $data): bool
{
    if (!$admin->hasTelegram()) return false;

    $message = "📦 <b>Konfirmasi Pengembalian</b>\n\n"
        . "👤 Peminjam: <b>{$data['peminjam_nama']}</b>\n"
        . "🔧 Alat: <b>{$data['alat']}</b>\n"
        . "📋 Kode: <code>{$data['kode']}</code>\n\n"
        . "📸 Silakan cek kondisi alat pada foto.\n"
        . "⚠️ Pastikan alat lengkap dan tidak rusak.";

    // INLINE KEYBOARD
    $replyMarkup = [
        'inline_keyboard' => [
            [
                [
                    'text' => '✅ Terima',
                    'callback_data' => 'return_approve_' . $data['kode']
                ],
                [
                    'text' => '❌ Tolak',
                    'callback_data' => 'return_reject_' . $data['kode']
                ]
            ]
        ]
    ];

    if (!empty($data['telegram_photo_file_id'])) {

        $result = $this->sendPhoto(
            $admin->telegram_chat_id,
            $data['telegram_photo_file_id'],
            $message,
            $replyMarkup
        );

    } else {

        $result = $this->sendMessage(
            $admin->telegram_chat_id,
            $message,
            $replyMarkup
        );
    }

    return $result['ok'] ?? false;
}

    /**
     * Notify user that return has been verified
     */
    public function notifyReturnVerified(User $user, array $data): bool
    {
        if (!$user->hasTelegram()) return false;

        $message = "✅ <b>Pengembalian Selesai!</b>\n\n"
            . "📋 Kode: <code>{$data['kode']}</code>\n"
            . "🔧 Alat: <b>{$data['alat']}</b>\n\n"
            . "Terima kasih telah mengembalikan alat. 🙏";

        $result = $this->sendMessage($user->telegram_chat_id, $message);
        return $result['ok'] ?? false;
    }

    /**
     * Send a photo to a Telegram chat
     */
    public function sendPhoto($chatId, $photo, $caption = null, $replyMarkup = null)
{
    $payload = [
        'chat_id' => $chatId,
        'photo' => $photo,
        'caption' => $caption,
        'parse_mode' => 'HTML',
    ];

    // tambahan tombol
    if ($replyMarkup) {
        $payload['reply_markup'] = json_encode($replyMarkup);
    }

    return Http::post(
        $this->apiUrl . '/sendPhoto',
        $payload
    )->json();
}

    /**
     * Notify user that the item in their waitlist is available
     */
    public function notifyWaitlistRestock(User $user, array $data): bool
    {
        if (!$user->hasTelegram()) return false;

        $message = "🔔 <b>ALAT TERSEDIA!</b>\n\n"
            . "Halo <b>{$user->name}</b>,\n"
            . "Alat <b>{$data['alat']}</b> yang Anda masukkan dalam daftar tunggu telah tersedia di Laboratorium sekarang!\n\n"
            . "Segera login ke web Alatika dan ajukan peminjaman sebelum dipinjam oleh orang lain! 🏃‍♂️💨";

        $result = $this->sendMessage($user->telegram_chat_id, $message);
        return $result['ok'] ?? false;
    }

    /**
     * Get file info from Telegram
     */
    public function getFile(string $fileId): array
    {
        return $this->request('getFile', ['file_id' => $fileId]);
    }

    /**
     * Download file from Telegram
     */
    public function downloadFile(string $filePath): ?string
    {
        $url = "https://api.telegram.org/file/bot{$this->token}/{$filePath}";
        try {
            $response = Http::get($url);
            if ($response->successful()) {
                return $response->body();
            }
            return null;
        } catch (\Exception $e) {
            Log::error("Telegram download error", ['message' => $e->getMessage()]);
            return null;
        }
    }

    // ===================================================
    // PRIVATE METHODS
    // ===================================================

    /**
     * Make HTTP request to Telegram API
     */
    protected function request(string $method, array $params = []): array
    {
        try {
            $response = Http::timeout(10)->post("{$this->apiUrl}/{$method}", $params);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error("Telegram API error [{$method}]", [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return ['ok' => false, 'error' => $response->body()];
        } catch (\Exception $e) {
            Log::error("Telegram API exception [{$method}]", [
                'message' => $e->getMessage(),
            ]);

            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }
}
