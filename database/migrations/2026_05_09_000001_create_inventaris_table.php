<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventaris', function (Blueprint $table) {
            $table->id();
            $table->string('nama_alat');
            $table->string('kategori')->index();
            $table->string('kode_barang')->nullable()->unique();
            $table->integer('jumlah_stok')->default(1);
            $table->string('lokasi_simpan')->nullable();
            $table->integer('tahun_perolehan')->nullable();
            $table->string('kondisi');
            $table->json('perlengkapan_detail')->nullable();
            $table->boolean('is_borrowable')->default(true);
            $table->timestamps();

            $table->index(['kategori', 'is_borrowable']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventaris');
    }
};
