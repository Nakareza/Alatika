<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alat', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('kode')->unique();
            $table->string('kategori'); // Microcontroller, Lab Equipment, Sensor & Aktuator, Komponen Elektronik
            $table->integer('stok_total')->default(0);
            $table->integer('stok_tersedia')->default(0);
            $table->string('lokasi')->nullable(); // Rak A1, B2, etc
            $table->text('deskripsi')->nullable();
            $table->enum('status', ['tersedia', 'maintenance'])->default('tersedia');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alat');
    }
};
