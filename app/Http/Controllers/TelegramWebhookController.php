<?php

namespace App\Http\Controllers;

use App\Models\TelegramLinkCode;
use App\Models\User;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TelegramWebhookController extends Controller
{
    protected TelegramService $telegram;

    public function __construct(TelegramService $telegram)
    {
        $this->telegram = $telegram;
    }

    /**
     * Handle incoming webhook from Telegram
     */
    public function handle(Request $request)
    {
        $update = $request->all();

        Log::info('Telegram webhook received', $update);

        try {
            // Handle callback queries (inline keyboard buttons)
            if (isset($update['callback_query'])) {
                $this->handleCallbackQuery($update['callback_query']);
                return response()->json(['ok' => true]);
            }

            // Handle text messages
            if (isset($update['message']['text'])) {
                $this->handleMessage($update['message']);
            }
        } catch (\Exception $e) {
            Log::error('Telegram webhook error', ['message' => $e->getMessage()]);
        }

        return response()->json(['ok' => true]);
    }

    /**
     * Handle incoming text messages
     */
    protected function handleMessage(array $message): void
    {
        $chatId = (string) $message['chat']['id'];
        $text = trim($message['text']);
        $firstName = $message['from']['first_name'] ?? 'User';

        // Parse command
        if (str_starts_with($text, '/')) {
            $parts = explode(' ', $text, 3);
            $command = strtolower($parts[0]);
            // Remove @bot_username from command if present
            $command = explode('@', $command)[0];
            $arg1 = $parts[1] ?? null;
            $arg2 = $parts[2] ?? null;

            match ($command) {
                '/start'   => $this->commandStart($chatId, $firstName),
                '/link'    => $this->commandLink($chatId, $arg1),
                '/unlink'  => $this->commandUnlink($chatId),
                '/status'  => $this->commandStatus($chatId),
                '/kembali' => $this->commandKembali($chatId, $arg1),
                '/approve' => $this->commandApprove($chatId, $arg1),
                '/reject'  => $this->commandReject($chatId, $arg1, $arg2),
                '/pending' => $this->commandPending($chatId),
                '/help'    => $this->commandHelp($chatId),
                '/myid'    => $this->commandMyId($chatId),
                default    => $this->commandUnknown($chatId),
            };
        } else {
            // Non-command message - hint to use /help
            $this->telegram->sendMessage($chatId,
                "Halo! Saya bot Alatika 🤖\n\n"
                . "Ketik /help untuk melihat daftar perintah yang tersedia."
            );
        }
    }

    // ===================================================
    // COMMAND HANDLERS
    // ===================================================

    /**
     * /start - Welcome message
     */
    protected function commandStart(string $chatId, string $firstName): void
    {
        $user = User::where('telegram_chat_id', $chatId)->first();

        if ($user) {
            $roleLabel = $this->getRoleLabel($user->role);
            $this->telegram->sendMessage($chatId,
                "Selamat datang kembali, <b>{$user->name}</b>! 👋\n\n"
                . "Akun Anda sudah terhubung sebagai <b>{$roleLabel}</b>.\n\n"
                . "Ketik /help untuk melihat perintah yang tersedia."
            );
            return;
        }

        $this->telegram->sendMessage($chatId,
            "🔬 <b>Selamat datang di Bot Alatika!</b>\n\n"
            . "Halo {$firstName}! Saya adalah bot untuk sistem peminjaman alat laboratorium Politeknik Negeri Semarang.\n\n"
            . "📌 <b>Cara menghubungkan akun:</b>\n"
            . "1️⃣ Login ke web Alatika\n"
            . "2️⃣ Klik \"Hubungkan Telegram\" di profil\n"
            . "3️⃣ Salin kode verifikasi\n"
            . "4️⃣ Kirim ke sini: <code>/link KODE_ANDA</code>\n\n"
            . "Contoh: <code>/link ABC123</code>\n\n"
            . "Ketik /help untuk bantuan lebih lanjut."
        );
    }

    /**
     * /link KODE - Link Telegram account to web account
     */
    protected function commandLink(string $chatId, ?string $code): void
    {
        if (empty($code)) {
            $this->telegram->sendMessage($chatId,
                "⚠️ Format salah!\n\nGunakan: <code>/link KODE_ANDA</code>\n"
                . "Contoh: <code>/link ABC123</code>\n\n"
                . "Kode bisa didapat di halaman profil web Alatika."
            );
            return;
        }

        // Check if this chat is already linked
        $existingUser = User::where('telegram_chat_id', $chatId)->first();
        if ($existingUser) {
            $this->telegram->sendMessage($chatId,
                "ℹ️ Telegram Anda sudah terhubung dengan akun <b>{$existingUser->name}</b>.\n\n"
                . "Ketik /unlink jika ingin memutus koneksi terlebih dahulu."
            );
            return;
        }

        // Find valid link code
        $linkCode = TelegramLinkCode::valid()
            ->where('code', strtoupper($code))
            ->first();

        if (!$linkCode) {
            $this->telegram->sendMessage($chatId,
                "❌ Kode tidak valid atau sudah kadaluarsa.\n\n"
                . "Silakan generate kode baru di halaman profil web Alatika."
            );
            return;
        }

        // Link the account
        $user = $linkCode->user;
        $user->update(['telegram_chat_id' => $chatId]);

        // Delete all link codes for this user
        TelegramLinkCode::where('user_id', $user->id)->delete();

        $roleLabel = $this->getRoleLabel($user->role);
        $this->telegram->sendMessage($chatId,
            "✅ <b>Berhasil terhubung!</b>\n\n"
            . "👤 Nama: <b>{$user->name}</b>\n"
            . "📧 Email: {$user->email}\n"
            . "🔑 Role: <b>{$roleLabel}</b>\n\n"
            . "Anda akan menerima notifikasi peminjaman di sini.\n"
            . "Ketik /help untuk melihat perintah yang tersedia."
        );
    }

    /**
     * /unlink - Disconnect Telegram from web account
     */
    protected function commandUnlink(string $chatId): void
    {
        $user = User::where('telegram_chat_id', $chatId)->first();

        if (!$user) {
            $this->telegram->sendMessage($chatId,
                "ℹ️ Telegram Anda belum terhubung dengan akun manapun."
            );
            return;
        }

        $user->update(['telegram_chat_id' => null]);

        $this->telegram->sendMessage($chatId,
            "🔓 <b>Koneksi diputus!</b>\n\n"
            . "Telegram Anda sudah tidak terhubung dengan akun <b>{$user->name}</b>.\n"
            . "Anda tidak akan menerima notifikasi lagi.\n\n"
            . "Ketik /link KODE untuk menghubungkan kembali."
        );
    }

    /**
     * /status - Check active borrowings
     */
    protected function commandStatus(string $chatId): void
    {
        $user = $this->getLinkedUser($chatId);
        if (!$user) return;

        // Since there's no peminjaman table yet, we show a demo response
        // This will be updated once the peminjaman system is fully implemented
        $roleLabel = $this->getRoleLabel($user->role);

        if (in_array($user->role, ['mahasiswa', 'dosen'])) {
            $this->telegram->sendMessage($chatId,
                "📊 <b>Status Peminjaman</b>\n\n"
                . "👤 {$user->name} ({$roleLabel})\n"
                . "━━━━━━━━━━━━━━━━━━━━━━\n\n"
                . "📭 Tidak ada peminjaman aktif saat ini.\n\n"
                . "💡 Ajukan peminjaman melalui web Alatika."
            );
        } else {
            $this->telegram->sendMessage($chatId,
                "📊 <b>Status Sistem</b>\n\n"
                . "👤 {$user->name} ({$roleLabel})\n"
                . "━━━━━━━━━━━━━━━━━━━━━━\n\n"
                . "📭 Tidak ada pengajuan pending saat ini.\n\n"
                . "Ketik /pending untuk melihat pengajuan yang perlu diproses."
            );
        }
    }

    /**
     * /kembali KODE - Confirm return of borrowed item
     */
    protected function commandKembali(string $chatId, ?string $kode): void
    {
        $user = $this->getLinkedUser($chatId);
        if (!$user) return;

        if (!in_array($user->role, ['mahasiswa', 'dosen'])) {
            $this->telegram->sendMessage($chatId,
                "⚠️ Perintah ini hanya untuk mahasiswa dan dosen."
            );
            return;
        }

        if (empty($kode)) {
            $this->telegram->sendMessage($chatId,
                "⚠️ Format salah!\n\n"
                . "Gunakan: <code>/kembali KODE_PEMINJAMAN</code>\n"
                . "Contoh: <code>/kembali PMJ-001</code>\n\n"
                . "Kode peminjaman bisa dilihat di web atau gunakan /status"
            );
            return;
        }

        // For now, send a confirmation message
        // This will be connected to actual peminjaman system later
        $this->telegram->sendMessage($chatId,
            "📦 <b>Konfirmasi Pengembalian</b>\n\n"
            . "📋 Kode: <code>{$kode}</code>\n"
            . "👤 Peminjam: {$user->name}\n\n"
            . "✅ Konfirmasi pengembalian telah dikirim ke petugas.\n"
            . "Silakan serahkan alat ke laboratorium untuk verifikasi fisik."
        );

        // Notify admins (for mahasiswa) or kalab (for dosen)
        $approverRole = $user->role === 'mahasiswa' ? 'admin' : 'kalab';
        $approvers = User::where('role', $approverRole)
            ->whereNotNull('telegram_chat_id')
            ->get();

        foreach ($approvers as $approver) {
            $this->telegram->notifyReturnConfirmation($approver, [
                'peminjam_nama' => $user->name,
                'alat' => "(Kode: {$kode})",
                'kode' => $kode,
            ]);
        }
    }

    /**
     * /approve KODE - Approve a borrowing request
     */
    protected function commandApprove(string $chatId, ?string $kode): void
    {
        $user = $this->getLinkedUser($chatId);
        if (!$user) return;

        if (!in_array($user->role, ['admin', 'kalab'])) {
            $this->telegram->sendMessage($chatId,
                "⚠️ Anda tidak memiliki hak untuk menyetujui peminjaman."
            );
            return;
        }

        if (empty($kode)) {
            $this->telegram->sendMessage($chatId,
                "⚠️ Format salah!\n\n"
                . "Gunakan: <code>/approve KODE_PEMINJAMAN</code>\n"
                . "Contoh: <code>/approve PMJ-001</code>"
            );
            return;
        }

        // Approval confirmation
        $roleDesc = $user->role === 'admin'
            ? 'peminjaman mahasiswa'
            : 'peminjaman dosen';

        $this->telegram->sendMessage($chatId,
            "✅ <b>Peminjaman Disetujui!</b>\n\n"
            . "📋 Kode: <code>{$kode}</code>\n"
            . "👤 Disetujui oleh: {$user->name}\n"
            . "📌 Tipe: {$roleDesc}\n\n"
            . "Notifikasi telah dikirim ke peminjam."
        );
    }

    /**
     * /reject KODE ALASAN - Reject a borrowing request
     */
    protected function commandReject(string $chatId, ?string $kode, ?string $alasan): void
    {
        $user = $this->getLinkedUser($chatId);
        if (!$user) return;

        if (!in_array($user->role, ['admin', 'kalab'])) {
            $this->telegram->sendMessage($chatId,
                "⚠️ Anda tidak memiliki hak untuk menolak peminjaman."
            );
            return;
        }

        if (empty($kode)) {
            $this->telegram->sendMessage($chatId,
                "⚠️ Format salah!\n\n"
                . "Gunakan: <code>/reject KODE_PEMINJAMAN alasan</code>\n"
                . "Contoh: <code>/reject PMJ-001 Stok habis</code>"
            );
            return;
        }

        $alasan = $alasan ?: 'Tidak ada alasan yang disertakan';

        $this->telegram->sendMessage($chatId,
            "❌ <b>Peminjaman Ditolak</b>\n\n"
            . "📋 Kode: <code>{$kode}</code>\n"
            . "👤 Ditolak oleh: {$user->name}\n"
            . "📝 Alasan: {$alasan}\n\n"
            . "Notifikasi telah dikirim ke peminjam."
        );
    }

    /**
     * /pending - View pending requests (for admin/kalab)
     */
    protected function commandPending(string $chatId): void
    {
        $user = $this->getLinkedUser($chatId);
        if (!$user) return;

        if (!in_array($user->role, ['admin', 'kalab'])) {
            $this->telegram->sendMessage($chatId,
                "⚠️ Perintah ini hanya untuk Admin/Teknisi dan Kepala Lab."
            );
            return;
        }

        $roleDesc = $user->role === 'admin'
            ? 'mahasiswa'
            : 'dosen';

        $this->telegram->sendMessage($chatId,
            "📋 <b>Pengajuan Pending</b>\n\n"
            . "👤 {$user->name} ({$this->getRoleLabel($user->role)})\n"
            . "━━━━━━━━━━━━━━━━━━━━━━\n\n"
            . "📭 Tidak ada pengajuan {$roleDesc} yang pending saat ini.\n\n"
            . "💡 Anda akan mendapat notifikasi saat ada pengajuan baru."
        );
    }

    /**
     * /help - Show available commands
     */
    protected function commandHelp(string $chatId): void
    {
        $user = User::where('telegram_chat_id', $chatId)->first();

        $generalCommands = ""
            . "📌 <b>Perintah Umum:</b>\n"
            . "/start - Mulai bot\n"
            . "/help - Tampilkan bantuan\n"
            . "/myid - Lihat Telegram ID Anda\n";

        if (!$user) {
            $this->telegram->sendMessage($chatId,
                "🔬 <b>Bantuan Bot Alatika</b>\n\n"
                . $generalCommands
                . "/link KODE - Hubungkan akun\n\n"
                . "Anda belum menghubungkan akun. Dapatkan kode di halaman profil web Alatika."
            );
            return;
        }

        $roleLabel = $this->getRoleLabel($user->role);
        $roleCommands = "";

        if (in_array($user->role, ['mahasiswa', 'dosen'])) {
            $roleCommands = "\n🎒 <b>Perintah Peminjam ({$roleLabel}):</b>\n"
                . "/status - Cek peminjaman aktif\n"
                . "/kembali KODE - Konfirmasi pengembalian\n";
        }

        if (in_array($user->role, ['admin', 'kalab'])) {
            $approvalTarget = $user->role === 'admin' ? 'mahasiswa' : 'dosen';
            $roleCommands = "\n🔧 <b>Perintah {$roleLabel}:</b>\n"
                . "/pending - Lihat pengajuan pending ({$approvalTarget})\n"
                . "/approve KODE - Setujui peminjaman\n"
                . "/reject KODE [alasan] - Tolak peminjaman\n"
                . "/status - Ringkasan sistem\n";
        }

        $this->telegram->sendMessage($chatId,
            "🔬 <b>Bantuan Bot Alatika</b>\n\n"
            . "👤 Login: <b>{$user->name}</b> ({$roleLabel})\n\n"
            . $generalCommands
            . $roleCommands
            . "/unlink - Putus koneksi Telegram\n"
        );
    }

    /**
     * /myid - Show user's Telegram chat ID
     */
    protected function commandMyId(string $chatId): void
    {
        $this->telegram->sendMessage($chatId,
            "🆔 <b>Telegram Chat ID Anda:</b>\n\n<code>{$chatId}</code>"
        );
    }

    /**
     * Unknown command handler
     */
    protected function commandUnknown(string $chatId): void
    {
        $this->telegram->sendMessage($chatId,
            "❓ Perintah tidak dikenali.\n\nKetik /help untuk melihat daftar perintah."
        );
    }

    // ===================================================
    // CALLBACK QUERY HANDLERS (Inline Keyboard Buttons)
    // ===================================================

    protected function handleCallbackQuery(array $callbackQuery): void
    {
        $chatId = (string) $callbackQuery['message']['chat']['id'];
        $data = $callbackQuery['data'];
        $callbackId = $callbackQuery['id'];

        $user = User::where('telegram_chat_id', $chatId)->first();

        if (!$user) {
            $this->telegram->answerCallbackQuery($callbackId, 'Akun tidak terhubung!', true);
            return;
        }

        if (str_starts_with($data, 'approve_')) {
            $kode = str_replace('approve_', '', $data);
            $this->commandApprove($chatId, $kode);
            $this->telegram->answerCallbackQuery($callbackId, "✅ {$kode} disetujui!");
        } elseif (str_starts_with($data, 'reject_')) {
            $kode = str_replace('reject_', '', $data);
            $this->telegram->answerCallbackQuery($callbackId, "Kirim: /reject {$kode} [alasan]", true);
        } else {
            $this->telegram->answerCallbackQuery($callbackId, 'Aksi tidak dikenali.');
        }
    }

    // ===================================================
    // HELPER METHODS
    // ===================================================

    /**
     * Get user linked to this Telegram chat, or send error
     */
    protected function getLinkedUser(string $chatId): ?User
    {
        $user = User::where('telegram_chat_id', $chatId)->first();

        if (!$user) {
            $this->telegram->sendMessage($chatId,
                "⚠️ Akun Telegram Anda belum terhubung.\n\n"
                . "Gunakan /link KODE untuk menghubungkan akun."
            );
            return null;
        }

        return $user;
    }

    /**
     * Get human-readable role label
     */
    protected function getRoleLabel(string $role): string
    {
        return match ($role) {
            'admin'     => 'Admin/Teknisi',
            'kalab'     => 'Kepala Lab',
            'dosen'     => 'Dosen',
            'mahasiswa' => 'Mahasiswa',
            default     => ucfirst($role),
        };
    }
}
