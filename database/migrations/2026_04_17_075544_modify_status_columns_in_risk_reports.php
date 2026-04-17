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
            // 1. Kita buang kolom status lama yang cupu
            $table->dropColumn('status');

            // 2. Kita pasang Jalur Persetujuan (Approval)
            // Isinya nanti: pending_kacab, pending_korwil, approved, rejected
            $table->string('approval_status')->default('pending_kacab')->after('dampak_finansial');

            // 3. Kita pasang Jalur Tindak Lanjut (Resolution)
            // Isinya nanti: open, monitoring, closed
            $table->string('resolution_status')->default('open')->after('approval_status');
        });
    }

    public function down(): void
    {
        Schema::table('risk_reports', function (Blueprint $table) {
            $table->string('status')->default('pending');
            $table->dropColumn(['approval_status', 'resolution_status']);
        });
    }
};
