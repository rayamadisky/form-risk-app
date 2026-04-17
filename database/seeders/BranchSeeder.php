<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Branch;

class BranchSeeder extends Seeder
{
    public function run(): void
    {
        $branches = [
            ['id' => 1, 'nama_cabang' => 'Cabang Pusat'],
            ['id' => 2, 'nama_cabang' => 'Cabang Sudirman'],
            ['id' => 3, 'nama_cabang' => 'Cabang Cianjur'],
            ['id' => 4, 'nama_cabang' => 'Cabang Tasikmalaya'],
        ];

        foreach ($branches as $branch) {
            Branch::updateOrCreate(['id' => $branch['id']], $branch);
        }

        $this->command->info('Daftar Cabang BPR berhasil disinkronisasi!');
    }
}