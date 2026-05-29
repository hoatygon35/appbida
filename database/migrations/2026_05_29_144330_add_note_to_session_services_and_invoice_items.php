<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('session_services', function (Blueprint $table) {
            $table->string('note')->nullable()->after('quantity');
        });

        Schema::table('invoice_items', function (Blueprint $table) {
            $table->string('note')->nullable()->after('quantity');
        });
    }

    public function down(): void
    {
        Schema::table('session_services', function (Blueprint $table) {
            $table->dropColumn('note');
        });

        Schema::table('invoice_items', function (Blueprint $table) {
            $table->dropColumn('note');
        });
    }
};
