<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {

            // Approval Kalab
            $table->foreignId('kalab_approved_by')
                ->nullable()
                ->after('approved_by')
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('kalab_approved_at')
                ->nullable();

            // Approval Admin/Teknisi
            $table->foreignId('admin_approved_by')
                ->nullable()
                ->after('kalab_approved_at')
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('admin_approved_at')
                ->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {

            $table->dropConstrainedForeignId('kalab_approved_by');
            $table->dropConstrainedForeignId('admin_approved_by');

            $table->dropColumn([
                'kalab_approved_at',
                'admin_approved_at'
            ]);
        });
    }
};
