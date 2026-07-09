<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->string('borrowable_type')->nullable()->after('user_id');
            $table->unsignedBigInteger('borrowable_id')->nullable()->after('borrowable_type');
            $table->index(['borrowable_type', 'borrowable_id']);
        });

        // Migrate existing data: set borrowable_type and borrowable_id using the existing 'alat_id'
        DB::table('peminjaman')->whereNotNull('alat_id')->update([
            'borrowable_type' => 'App\Models\Alat',
            'borrowable_id' => DB::raw('alat_id')
        ]);
    }

    public function down(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->dropIndex(['borrowable_type', 'borrowable_id']);
            $table->dropColumn(['borrowable_type', 'borrowable_id']);
        });
    }
};
