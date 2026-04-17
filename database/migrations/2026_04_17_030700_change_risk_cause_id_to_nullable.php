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
        // Tambahin ->nullable()->change() biar kolom ini legal untuk dikosongin
        $table->unsignedBigInteger('risk_cause_id')->nullable()->change();
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
