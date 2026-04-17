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
    Schema::create('risk_items', function (Blueprint $table) {
        $table->id();
        $table->string('nama_risiko'); // Contoh: Selisih kurang kas besar
        $table->enum('kategori', ['finansial', 'non_finansial']);
        $table->string('role_target'); // Contoh: teller, cs, security
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('risk_items');
    }
};
