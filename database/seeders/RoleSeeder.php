<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // 1. System Admin
        Role::firstOrCreate(['name' => 'manrisk']);
        
        // 2. Checker Tier
        Role::firstOrCreate(['name' => 'korwil']);
        Role::firstOrCreate(['name' => 'kacab']);
        
        // 3. Maker Tier
        Role::firstOrCreate(['name' => 'ca']); 
        Role::firstOrCreate(['name' => 'teller']);
        Role::firstOrCreate(['name' => 'csr']);
        Role::firstOrCreate(['name' => 'security']);

        $this->command->info('Hierarki Jabatan Perbankan berhasil disinkronisasi!');
    }
}