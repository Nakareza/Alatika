<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->foreignId('kaprodi_approved_by')
                ->nullable()
                ->after('admin_approved_at')
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('kaprodi_approved_at')
                ->nullable()
                ->after('kaprodi_approved_by');
        });
    }

    public function down(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->dropConstrainedForeignId('kaprodi_approved_by');
            $table->dropColumn('kaprodi_approved_at');
        });
    }
};
