<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tool_sets', function (Blueprint $table) {
            $table->id();
            $table->string('kode_tool_set')->unique();
            $table->string('nama_tool_set');
            $table->foreignId('kategori_id')->nullable()->constrained('kategoris')->nullOnDelete();
            $table->integer('stok')->default(0);
            $table->integer('stok_tersedia')->default(0);
            $table->string('lokasi')->nullable();
            $table->string('kondisi')->nullable();
            $table->text('keterangan')->nullable();
            $table->year('tahun')->nullable();
            $table->timestamps();
        });

        Schema::create('tool_set_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tool_set_id')->constrained('tool_sets')->onDelete('cascade');
            $table->string('nama_komponen');
            $table->integer('jumlah')->default(1);
            $table->string('satuan')->default('Buah');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tool_set_details');
        Schema::dropIfExists('tool_sets');
    }
};
