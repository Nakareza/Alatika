<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peminjaman', function (Blueprint $table) {
            $table->id();
            $table->string('kode_peminjaman')->unique(); // PMJ-XXXX
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('alat_id')->constrained('alat')->onDelete('cascade');
            $table->integer('jumlah')->default(1);
            $table->text('keperluan')->nullable();
            $table->date('tanggal_pinjam');
            $table->date('tanggal_kembali'); // deadline
            $table->enum('status', [
                'pending',              // Menunggu persetujuan
                'disetujui',            // Disetujui, belum diambil
                'dipinjam',             // Alat sudah diambil/dipinjam
                'menunggu_verifikasi',  // Mahasiswa sudah kembalikan + kirim foto, menunggu admin verifikasi
                'selesai',              // Pengembalian selesai diverifikasi
                'ditolak',              // Peminjaman ditolak
            ])->default('pending');

            // Approval
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('rejected_reason')->nullable();

            // Pengembalian
            $table->string('foto_bukti_kembali')->nullable(); // path foto dari Telegram
            $table->string('telegram_photo_file_id')->nullable(); // Telegram file_id for re-sending
            $table->timestamp('tanggal_dikembalikan')->nullable();
            $table->text('catatan_kondisi')->nullable();
            $table->enum('kondisi_kembali', ['baik', 'rusak_ringan', 'rusak_berat'])->nullable();

            // Reminder tracking (prevent duplicate notifications)
            $table->boolean('reminder_h1_sent')->default(false);
            $table->boolean('reminder_hday_sent')->default(false);
            $table->boolean('overdue_d1_sent')->default(false);
            $table->boolean('overdue_d3_sent')->default(false);
            $table->boolean('overdue_d7_sent')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peminjaman');
    }
};
