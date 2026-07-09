<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('keranjangs', function (Blueprint $table) {
            $table->string('cartable_type')->nullable()->after('user_id');
            $table->unsignedBigInteger('cartable_id')->nullable()->after('cartable_type');
            $table->index(['cartable_type', 'cartable_id']);
        });

        // Migrate existing keranjang items to point to App\Models\Alat
        DB::table('keranjangs')->whereNotNull('alat_id')->update([
            'cartable_type' => 'App\Models\Alat',
            'cartable_id' => DB::raw('alat_id')
        ]);
    }

    public function down(): void
    {
        Schema::table('keranjangs', function (Blueprint $table) {
            $table->dropIndex(['cartable_type', 'cartable_id']);
            $table->dropColumn(['cartable_type', 'cartable_id']);
        });
    }
};
