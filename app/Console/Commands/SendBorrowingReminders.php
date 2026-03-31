<?php

namespace App\Console\Commands;

use App\Models\Peminjaman;
use App\Models\User;
use App\Services\TelegramService;
use Illuminate\Console\Command;

class SendBorrowingReminders extends Command
{
    protected $signature = 'telegram:send-reminders';
    protected $description = 'Send automated reminders for borrowing deadlines to Telegram';

    public function handle(TelegramService $telegram)
    {
        $this->info('Starting to send reminders...');

        // 1. H-1 Reminder
        $h1Reminders = Peminjaman::where('status', 'dipinjam')
            ->where('tanggal_kembali', '=', now()->addDay()->toDateString())
            ->where('reminder_h1_sent', false)
            ->get();

        foreach ($h1Reminders as $p) {
            if ($p->user->hasTelegram()) {
                $telegram->notifyDeadlineReminder($p->user, [
                    'kode' => $p->kode_peminjaman,
                    'alat' => $p->alat->nama,
                    'deadline' => "BESOK ({$p->tanggal_kembali->format('d M Y')})",
                ]);
                $p->update(['reminder_h1_sent' => true]);
                $this->info("H-1 reminder sent for {$p->kode_peminjaman}");
            }
        }

        // 2. Hari-H Reminder
        $hDayReminders = Peminjaman::where('status', 'dipinjam')
            ->where('tanggal_kembali', '=', now()->toDateString())
            ->where('reminder_hday_sent', false)
            ->get();

        foreach ($hDayReminders as $p) {
            if ($p->user->hasTelegram()) {
                $telegram->notifyDeadlineReminder($p->user, [
                    'kode' => $p->kode_peminjaman,
                    'alat' => $p->alat->nama,
                    'deadline' => "HARI INI BUKAN MAEN",
                ]);
                $p->update(['reminder_hday_sent' => true]);
                $this->info("H-Day reminder sent for {$p->kode_peminjaman}");
            }
        }

        // 3. Overdue (Terlambat) Notifications
        $overdueSet = Peminjaman::where('status', 'dipinjam')
            ->where('tanggal_kembali', '<', now()->toDateString())
            ->get();

        foreach ($overdueSet as $p) {
            $daysOverdue = floor(now()->startOfDay()->diffInDays($p->tanggal_kembali, true));

            // Overdue 1 day
            if ($daysOverdue == 1 && !$p->overdue_d1_sent) {
                $telegram->notifyOverdue($p->user, [
                    'kode' => $p->kode_peminjaman,
                    'alat' => $p->alat->nama,
                    'deadline' => $p->tanggal_kembali->format('d M Y'),
                    'hari_terlambat' => 1,
                ]);
                $p->update(['overdue_d1_sent' => true]);
                $this->error("Overdue D+1 sent for {$p->kode_peminjaman}");
            }

            // Overdue 3 days (Escalation to User & Admin/Kalab)
            if ($daysOverdue == 3 && !$p->overdue_d3_sent) {
                // Notify user strongly
                $telegram->sendMessage($p->user->telegram_chat_id, "🚨 <b>PERINGATAN KERAS!</b>\n\nAlat {$p->alat->nama} ({$p->kode_peminjaman}) sudah terlambat 3 hari. Segera kembalikan atau akun akan diblokir.");
                
                // Escalate to Kalab
                $kalabs = User::where('role', 'kalab')->whereNotNull('telegram_chat_id')->get();
                foreach ($kalabs as $kalab) {
                    $telegram->notifyEscalation($kalab, [
                        'kode' => $p->kode_peminjaman,
                        'peminjam_nama' => $p->user->name,
                        'alat' => $p->alat->nama,
                        'hari_terlambat' => 3,
                    ]);
                }
                $p->update(['overdue_d3_sent' => true]);
                $this->error("Overdue D+3 Escalate sent for {$p->kode_peminjaman}");
            }
        }

        $this->info('Reminders sent successfully!');
    }
}
