<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('alat', function (Blueprint $table) {
            $table->foreignId('kategori_id')->nullable()->constrained('kategoris')->nullOnDelete();
            $table->string('kode_barang')->nullable()->unique();
            $table->string('nama_barang')->nullable();
            $table->string('merk')->nullable();
            $table->text('spesifikasi')->nullable();
            $table->integer('stok')->default(0);
            $table->string('satuan')->default('Unit');
            $table->year('tahun')->nullable();
            $table->text('keterangan')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('alat', function (Blueprint $table) {
            $table->dropForeign(['kategori_id']);
            $table->dropColumn([
                'kategori_id',
                'kode_barang',
                'nama_barang',
                'merk',
                'spesifikasi',
                'stok',
                'satuan',
                'tahun',
                'keterangan'
            ]);
        });
    }
};
