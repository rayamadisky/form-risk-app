<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('branches', function (Blueprint $table) {
            // Cek biar nggak kejadian error duplicate column kayak tadi
            if (!Schema::hasColumn('branches', 'kode_cabang')) {
                $table->string('kode_cabang', 10)->unique()->nullable()->after('id');
            }
            if (!Schema::hasColumn('branches', 'nickname_cabang')) {
                $table->string('nickname_cabang', 50)->nullable()->after('nama_cabang');
            }
        });
    }

    public function down()
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->dropColumn(['kode_cabang', 'nickname_cabang']);
        });
    }
};
