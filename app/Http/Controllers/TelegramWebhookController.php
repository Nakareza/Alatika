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

            // Handle messages (text or photo)
            if (isset($update['message']['text']) || isset($update['message']['photo'])) {
                $this->handleMessage($update['message']);
            }
        } catch (\Exception $e) {
            Log::error('Telegram webhook error', ['message' => $e->getMessage()]);
        }

        return response()->json(['ok' => true]);
    }

    /**
     * Handle incoming messages (text or photo)
     */
    protected function handleMessage(array $message): void
    {
        $chatId = (string) $message['chat']['id'];
        $text = trim($message['text'] ?? $message['caption'] ?? '');
        $firstName = $message['from']['first_name'] ?? 'User';

        // Get highest resolution photo if exists
        $photo = isset($message['photo']) ? end($message['photo']) : null;

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
                '/kembali' => $this->commandKembali($chatId, $arg1, $photo),
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

        $roleLabel = $this->getRoleLabel($user->role);

        if (in_array($user->role, ['mahasiswa', 'dosen'])) {
            $peminjaman = \App\Models\Peminjaman::where('user_id', $user->id)
                ->whereIn('status', ['pending', 'disetujui', 'dipinjam', 'menunggu_verifikasi', 'disetujui'])
                ->get();
                
            if ($peminjaman->isEmpty()) {
                $this->telegram->sendMessage($chatId,
                    "📊 <b>Status Peminjaman</b>\n\n"
                    . "👤 {$user->name} ({$roleLabel})\n"
                    . "━━━━━━━━━━━━━━━━━━━━━━\n\n"
                    . "📭 Tidak ada peminjaman aktif saat ini.\n\n"
                    . "💡 Ajukan peminjaman melalui web Alatika."
                );
                return;
            }

            $message = "📊 <b>Status Peminjaman Aktif</b>\n\n";
            $message .= "👤 {$user->name} ({$roleLabel})\n";
            $message .= "━━━━━━━━━━━━━━━━━━━━━━\n\n";

            foreach ($peminjaman as $p) {
                $icon = match($p->status) {
                    'pending' => '⏳',
                    'disetujui' => '✅',
                    'dipinjam' => '📦',
                    'menunggu_verifikasi' => '🔍',
                    default => '⚙️',
                };
                
                $message .= "{$icon} <b>{$p->alat->nama}</b> ({$p->jumlah} unit)\n";
                $message .= "📋 Kode: <code>{$p->kode_peminjaman}</code>\n";
                $message .= "📅 Status: <b>{$p->status_label}</b>\n";
                if ($p->status === 'dipinjam') {
                    $message .= "⏰ Deadline: {$p->tanggal_kembali->format('d M Y')}\n";
                }
                $message .= "\n";
            }
            
            $this->telegram->sendMessage($chatId, $message);
        } else {
            $targetRole = $user->role === 'admin' ? 'mahasiswa' : 'dosen';
            $pendingCount = \App\Models\Peminjaman::whereHas('user', function($q) use ($targetRole) {
                $q->where('role', $targetRole);
            })->where('status', 'pending')->count();
            
            $returnCount = \App\Models\Peminjaman::whereHas('user', function($q) use ($targetRole) {
                $q->where('role', $targetRole);
            })->where('status', 'menunggu_verifikasi')->count();

            $this->telegram->sendMessage($chatId,
                "📊 <b>Status Sistem</b>\n\n"
                . "👤 {$user->name} ({$roleLabel})\n"
                . "━━━━━━━━━━━━━━━━━━━━━━\n\n"
                . "📝 <b>Peminjaman Pending:</b> {$pendingCount} antrean\n"
                . "🔍 <b>Menunggu Verifikasi (Pengembalian):</b> {$returnCount} alat\n\n"
                . "Ketik /pending untuk melihat detail pengajuan."
            );
        }
    }

    /**
     * /kembali KODE - Confirm return of borrowed item
     */
    protected function commandKembali(string $chatId, ?string $kode, ?array $photo = null): void
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
                . "💡 Jika diminta bukti foto, kirim foto alat dengan caption: <code>/kembali KODE_PEMINJAMAN</code>"
            );
            return;
        }

        $peminjaman = \App\Models\Peminjaman::where(
            'kode_peminjaman',
            strtoupper($kode)
        )
        ->where('user_id', $user->id)
        ->whereIn('status', ['dipinjam', 'disetujui'])
        ->first();

        

        if (!$peminjaman) {
            $this->telegram->sendMessage($chatId,
                "❌ Peminjaman tidak ditemukan, tidak aktif, atau bukan milik Anda.\n"
                . "Cek kode peminjaman dengan perintah <code>/status</code>"
            );
            return;
        }

        if (!$photo) {
            $this->telegram->sendMessage($chatId,
                "📸 <b>Bukti Foto Diperlukan!</b>\n\n"
                . "Mohon kirimkan <b>foto kondisi alat</b> beserta kabel/komponen lain secara lengkap.\n\n"
                . "👉 <b>Caranya:</b>\n"
                . "1. Buka kamera / upload foto di Telegram\n"
                . "2. Ketik di bagian 'Caption': <code>/kembali {$kode}</code>\n"
                . "3. Kirim fotonya ke sini."
            );
            return;
        }

        $fileId = $photo['file_id'];
        $fileInfo = $this->telegram->getFile($fileId);
        
        if (isset($fileInfo['ok']) && $fileInfo['ok']) {
            $filePath = $fileInfo['result']['file_path'];
            $fileData = $this->telegram->downloadFile($filePath);
            
            if ($fileData) {
                $ext = pathinfo($filePath, PATHINFO_EXTENSION);
                if (empty($ext)) $ext = 'jpg';
                $fileName = 'bukti-' . strtolower($kode) . '-' . time() . '.' . $ext;
                $savePath = 'bukti-pengembalian/' . $fileName;
                
                \Illuminate\Support\Facades\Storage::disk('public')->put($savePath, $fileData);
                
                $peminjaman->update([
                    'status' => 'menunggu_verifikasi',
                    'foto_bukti_kembali' => $savePath,
                    'telegram_photo_file_id' => $fileId,
                    'tanggal_dikembalikan' => now(),
                ]);

                $this->telegram->sendMessage($chatId,
                    "✅ <b>Bukti Foto Diterima!</b>\n\n"
                    . "📋 Kode: <code>{$kode}</code>\n"
                    . "🔧 Alat: {$peminjaman->alat->nama}\n\n"
                    . "Pengajuan pengembalian telah dikirim ke petugas.\n"
                    . "Silakan serahkan fisik alat ke laboratorium."
                );

                $approverRole = $user->role === 'mahasiswa' ? 'admin' : 'kalab';
                $approvers = User::where('role', $approverRole)->whereNotNull('telegram_chat_id')->get();

                foreach ($approvers as $approver) {
                    $this->telegram->notifyReturnConfirmation($approver, [
                        'peminjam_nama' => $user->name,
                        'alat' => $peminjaman->alat->nama . " (Kode: {$kode})",
                        'kode' => $kode,
                        'telegram_photo_file_id' => $fileId,
                    ]);
                }
                return;
            }
        }
        
        $this->telegram->sendMessage($chatId, "❌ Gagal mengunduh foto dari Telegram. Silakan coba kirim ulang.");
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

        $peminjaman = \App\Models\Peminjaman::where('kode_peminjaman', strtoupper($kode))->first();
        if (!$peminjaman) {
            $this->telegram->sendMessage($chatId, "❌ Peminjaman tidak ditemukan.");
            return;
        }

        if ($peminjaman->status !== 'pending') {
            $this->telegram->sendMessage($chatId, "⚠️ Peminjaman ini sudah diproses sebelumnya (Status: {$peminjaman->status}).");
            return;
        }

        // Logic routing role
        if ($user->role === 'admin' && $peminjaman->user->role !== 'mahasiswa') {
            $this->telegram->sendMessage($chatId, "⚠️ Admin hanya bisa menyetujui peminjaman dari Mahasiswa.");
            return;
        }
        if ($user->role === 'kalab' && $peminjaman->user->role !== 'dosen') {
            $this->telegram->sendMessage($chatId, "⚠️ Kepala Lab hanya memproses peminjaman Dosen.");
            return;
        }

        $peminjaman->update([
            'status' => 'dipinjam',
            'approved_by' => $user->id,
            'approved_at' => now(),
        ]);

        $this->telegram->notifyPeminjamanApproved($peminjaman->user, [
            'kode' => $peminjaman->kode_peminjaman,
            'alat' => $peminjaman->alat->nama,
            'jumlah' => $peminjaman->jumlah,
            'deadline' => $peminjaman->tanggal_kembali->format('d M Y'),
            'approver_role' => $user->role === 'kalab' ? 'Kepala Lab' : 'Admin',
        ]);

        $roleDesc = $user->role === 'admin' ? 'mahasiswa' : 'dosen';

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

        $peminjaman = \App\Models\Peminjaman::where('kode_peminjaman', strtoupper($kode))->first();
        if (!$peminjaman) {
            $this->telegram->sendMessage($chatId, "❌ Peminjaman tidak ditemukan.");
            return;
        }

        if ($peminjaman->status !== 'pending') {
            $this->telegram->sendMessage($chatId, "⚠️ Peminjaman ini sudah diproses sebelumnya.");
            return;
        }

        // Logic routing role
        if ($user->role === 'admin' && $peminjaman->user->role !== 'mahasiswa') {
            $this->telegram->sendMessage($chatId, "⚠️ Admin hanya bisa menolak peminjaman Mahasiswa.");
            return;
        }
        if ($user->role === 'kalab' && $peminjaman->user->role !== 'dosen') {
            $this->telegram->sendMessage($chatId, "⚠️ Kepala Lab hanya memproses peminjaman Dosen.");
            return;
        }

        $peminjaman->update([
            'status' => 'ditolak',
            'rejected_reason' => $alasan,
            'approved_by' => $user->id,
            'approved_at' => now(),
        ]);

        $this->telegram->notifyPeminjamanRejected($peminjaman->user, [
            'kode' => $peminjaman->kode_peminjaman,
            'alat' => $peminjaman->alat->nama,
            'alasan' => $alasan,
        ]);

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

        $roleDesc = $user->role === 'admin' ? 'mahasiswa' : 'dosen';
        $targetRole = $roleDesc;

        $pending = \App\Models\Peminjaman::whereHas('user', function($q) use ($targetRole) {
            $q->where('role', $targetRole);
        })->where('status', 'pending')->get();

        if ($pending->isEmpty()) {
            $this->telegram->sendMessage($chatId,
                "📋 <b>Pengajuan Pending</b>\n\n"
                . "👤 {$user->name} ({$this->getRoleLabel($user->role)})\n"
                . "━━━━━━━━━━━━━━━━━━━━━━\n\n"
                . "📭 Tidak ada pengajuan {$roleDesc} yang pending saat ini.\n\n"
                . "💡 Anda akan mendapat notifikasi saat ada pengajuan baru."
            );
            return;
        }

        $message = "📋 <b>Pengajuan Pending ({$roleDesc})</b>\n\n";
        foreach ($pending as $p) {
            $message .= "👤 <b>{$p->user->name}</b>\n";
            $message .= "🔧 Alat: {$p->alat->nama} ({$p->jumlah} unit)\n";
            $message .= "📋 Kode: <code>{$p->kode_peminjaman}</code>\n";
            $message .= "📅 Pinjam: {$p->tanggal_pinjam->format('d/m')} s.d. {$p->tanggal_kembali->format('d/m/Y')}\n";
            $message .= "📝 Tujuan: <i>{$p->keperluan}</i>\n\n";
        }
        $message .= "Ketik <code>/approve KODE</code> atau <code>/reject KODE alasan</code> untuk memproses.";

        $this->telegram->sendMessage($chatId, $message);
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

        $this->telegram->answerCallbackQuery(
            $callbackId,
            'Akun tidak terhubung!',
            true
        );

        return;
    }

    // =========================================
    // APPROVE RETURN
    // =========================================

    if (str_starts_with($data, 'return_approve_')) {

        $kode = str_replace('return_approve_', '', $data);

        $this->commandApproveReturn($chatId, $kode);

        $this->telegram->answerCallbackQuery(
            $callbackId,
            "✅ Pengembalian diterima!"
        );

        return;
    }

    // =========================================
    // REJECT RETURN
    // =========================================

    if (str_starts_with($data, 'return_reject_')) {

        $kode = str_replace('return_reject_', '', $data);

        $this->telegram->answerCallbackQuery(
            $callbackId,
            "❌ Fitur penolakan belum dibuat",
            true
        );

        return;
    }

    // =========================================
    // APPROVE PEMINJAMAN
    // =========================================

    if (str_starts_with($data, 'approve_')) {

        $kode = str_replace('approve_', '', $data);

        $this->commandApprove($chatId, $kode);

        $this->telegram->answerCallbackQuery(
            $callbackId,
            "✅ {$kode} disetujui!"
        );

        return;
    }

    // =========================================
    // REJECT PEMINJAMAN
    // =========================================

    if (str_starts_with($data, 'reject_')) {

        $kode = str_replace('reject_', '', $data);

        $this->telegram->answerCallbackQuery(
            $callbackId,
            "Kirim: /reject {$kode} [alasan]",
            true
        );

        return;
    }
}

/**
 * Approve return from Telegram inline button
 */
protected function commandApproveReturn(string $chatId, ?string $kode): void
{
    $user = $this->getLinkedUser($chatId);

    if (!$user) {
        return;
    }

    // hanya admin / kalab
    if (!in_array($user->role, ['admin', 'kalab'])) {

        $this->telegram->sendMessage(
            $chatId,
            "⚠️ Anda tidak punya akses."
        );

        return;
    }

    // cari peminjaman
    $peminjaman = \App\Models\Peminjaman::where(
        'kode_peminjaman',
        strtoupper($kode)
    )
    ->where('status', 'menunggu_verifikasi')
    ->first();

    if (!$peminjaman) {

        $this->telegram->sendMessage(
            $chatId,
            "❌ Data pengembalian tidak ditemukan."
        );

        return;
    }

    // update status
    $peminjaman->update([
        'status' => 'selesai',
        'approved_by' => $user->id,
        'approved_at' => now(),
    ]);

    // kembalikan stok alat
    if ($peminjaman->alat) {

        $peminjaman->alat->increment(
            'stok_tersedia',
            $peminjaman->jumlah
        );
    }

    // notif user
    if ($peminjaman->user?->telegram_chat_id) {

        $this->telegram->sendMessage(
            $peminjaman->user->telegram_chat_id,
            "✅ <b>Pengembalian Diverifikasi</b>\n\n"
            . "📋 Kode: <code>{$kode}</code>\n"
            . "🔧 Alat: {$peminjaman->alat->nama}\n\n"
            . "Terima kasih telah mengembalikan alat 🙏"
        );
    }

    // notif admin
    $this->telegram->sendMessage(
        $chatId,
        "✅ Pengembalian <code>{$kode}</code> berhasil diverifikasi."
    );
}

// ===================================================
// HELPER METHODS
// ===================================================

/**
 * Get user linked to this Telegram chat
 */
protected function getLinkedUser(string $chatId): ?User
{
    $user = User::where('telegram_chat_id', $chatId)->first();

    if (!$user) {

        $this->telegram->sendMessage(
            $chatId,
            "⚠️ Akun Telegram belum terhubung.\n\n"
            . "Gunakan /link KODE"
        );

        return null;
    }

    return $user;
}

/**
 * Human readable role
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