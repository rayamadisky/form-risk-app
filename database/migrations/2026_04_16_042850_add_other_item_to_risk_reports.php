<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
    Schema::table('risk_reports', function (Blueprint $table) {
        // Kita selipin kolom baru di bawah risk_item_id
        $table->string('other_item_description')->nullable()->after('risk_item_id');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('risk_reports', function (Blueprint $table) {
            //
        });
    }
};
