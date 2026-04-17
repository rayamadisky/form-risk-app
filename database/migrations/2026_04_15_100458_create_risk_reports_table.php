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
    Schema::create('risk_reports', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained('users'); // Siapa yang lapor
        $table->foreignId('branch_id')->constrained('branches'); // Di cabang mana
        
        $table->date('tanggal_kejadian');
        $table->date('tanggal_diketahui');
        
        $table->foreignId('risk_item_id')->constrained('risk_items');
        $table->foreignId('risk_cause_id')->constrained('risk_causes');
        
        // Kalau milih cause "Other", dia ngisi ini
        $table->string('other_cause_description')->nullable(); 
        
        // Mitigasi tambahan yang diketik manual (Point a3)
        $table->text('mitigasi_tambahan')->nullable(); 
        
        // Dampak finansial (BigInt biar ga pusing koma/titik)
        $table->bigInteger('dampak_finansial')->nullable();
        
        // Status untuk alur Maker-Checker
        $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
        
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('risk_reports');
    }
};
