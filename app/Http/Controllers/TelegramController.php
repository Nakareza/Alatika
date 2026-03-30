<?php

namespace App\Http\Controllers;

use App\Models\TelegramLinkCode;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TelegramController extends Controller
{
    /**
     * Generate a new link code for the authenticated user
     */
    public function generateLinkCode(Request $request)
    {
        $user = $request->user();

        // Delete old codes for this user
        TelegramLinkCode::where('user_id', $user->id)->delete();

        // Generate new code (6 characters, uppercase)
        $code = strtoupper(Str::random(6));

        TelegramLinkCode::create([
            'user_id'    => $user->id,
            'code'       => $code,
            'expires_at' => now()->addMinutes(10),
        ]);

        return response()->json([
            'success' => true,
            'code'    => $code,
            'expires' => '10 menit',
            'message' => "Kirim perintah berikut ke @Inventaris_Alatika_bot di Telegram:\n/link {$code}",
        ]);
    }

    /**
     * Disconnect Telegram from user account (via web)
     */
    public function disconnect(Request $request)
    {
        $user = $request->user();
        $user->update(['telegram_chat_id' => null]);

        return response()->json([
            'success' => true,
            'message' => 'Telegram berhasil diputus dari akun Anda.',
        ]);
    }

    /**
     * Get Telegram connection status
     */
    public function status(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'connected'   => $user->hasTelegram(),
            'bot_username' => config('telegram.bot_username'),
        ]);
    }

    /**
     * Test send a notification to the authenticated user
     */
    public function testNotification(Request $request)
    {
        $user = $request->user();

        if (!$user->hasTelegram()) {
            return response()->json([
                'success' => false,
                'message' => 'Telegram belum terhubung.',
            ], 400);
        }

        $telegram = new TelegramService();
        $result = $telegram->sendMessage($user->telegram_chat_id,
            "🔔 <b>Test Notifikasi</b>\n\n"
            . "Halo {$user->name}! Ini adalah pesan test dari sistem Alatika.\n"
            . "Notifikasi Telegram Anda berfungsi dengan baik! ✅"
        );

        return response()->json([
            'success' => $result['ok'] ?? false,
            'message' => ($result['ok'] ?? false)
                ? 'Notifikasi test berhasil dikirim!'
                : 'Gagal mengirim notifikasi.',
        ]);
    }
}
