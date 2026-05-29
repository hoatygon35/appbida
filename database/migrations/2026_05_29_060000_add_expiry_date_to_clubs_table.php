<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('clubs', function (Blueprint $table) {
            $table->timestamp('expiry_date')->nullable()->after('address');
        });

        // Set mặc định cho các Club cũ đã có là 30 ngày kể từ hôm nay
        DB::table('clubs')->whereNull('expiry_date')->update([
            'expiry_date' => now()->addDays(30)
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clubs', function (Blueprint $table) {
            $table->dropColumn('expiry_date');
        });
    }
};
