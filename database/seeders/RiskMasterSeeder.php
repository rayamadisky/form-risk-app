<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RiskItem;
use App\Models\RiskCause;
use App\Models\RiskMitigation;

class RiskMasterSeeder extends Seeder
{
    public function run(): void
    {
        // 1. KAMUS MITIGASI (Tetap sama, ini otak otomatisnya)
        $mitigasiKamus = [
            'Tidak ada / lemahnya double check' => 'Meningkatkan double check',
            'Lalai: Karena tidak paham' => 'Coaching',
            'Lalai: Karena volume banyak / beban kerja banyak' => 'Penilaian kembali volume / beban kerja',
            'Lalai: Karena lupa / tidak sengaja' => 'Atasan melakukan arahan / pemberitahuan',
            'Software belum ada / masih manual' => 'Pengadaan Software baru',
            'Itikad buruk karyawan' => 'Melakukan teguran / punishment',
            'Kurangnya komunikasi, koordinasi dan kerja sama di unit kerja' => 'Meningkatkan komunikasi, koordinasi dan kerja sama di unit kerja',
            'Banyaknya volume / beban kerja' => 'Penilaian kembali volume / beban kerja',
            'Tidak terampil dan cekatan' => 'Coaching',
            'Peralatan pendukung operasional rusak / tidak memadai' => 'Pengadaan / perbaikan peralatan pendukung operasional agar memadai',
            'Software sudah usang' => 'Pengadaan Software baru',
            'Bugs (Salah Pemrograman)' => 'Perbaikan program',
            'Kabel Jaringan Rusak / Tidak Memadai' => 'Memperbaiki kabel yang rusak / tidak memadai',
            'Kurang pemahaman / penguasaan akan produk / prosedur / kebijakan bank' => 'Coaching',
            'Tidak ada motivasi, tidak bersemangat kerja' => 'Meningkatkan semangat kerja menjadi lebih baik',
            'Lemahnya pengawasan dan kontrol atasan' => 'Meningkatkan pengawasan dan kontrol atasan',
            'Aktivitas kriminal eksternal' => 'Meningkatkan keamanan terhadap aktivitas kriminal eksternal',
            'Lemahnya pengawasan dan kontrol karyawan' => 'Meningkatkan pengawasan dan kontrol karyawan',
            'Tidak patuh terhadap prosedur / kebijakan' => 'Teguran / Punishment',
        ];

        // 2. DATA ITEM & PENYEBAB (Digabung: TELLER & KACAB)
        $masterData = [
            // ==========================================
            // SOAL UNTUK TELLER
            // ==========================================
            [
                'nama_risiko' => 'Selisih kurang pada perhitungan uang kas besar teller',
                'role_target' => 'teller',
                'causes' => [
                    'Tidak ada / lemahnya double check',
                    'Lalai: Karena volume banyak / beban kerja banyak',
                    'Lalai: Karena lupa / tidak sengaja',
                    'Itikad buruk karyawan'
                ]
            ],
            [
                'nama_risiko' => 'Salah input data transaksi',
                'role_target' => 'teller',
                'causes' => [
                    'Tidak ada / lemahnya double check',
                    'Lalai: Karena volume banyak / beban kerja banyak',
                    'Lalai: Karena lupa / tidak sengaja',
                    'Itikad buruk karyawan'
                ]
            ],
            [
                'nama_risiko' => 'Saldo ATM / CRM melebihi saldo yang dapat diasuransikan',
                'role_target' => 'teller',
                'causes' => [
                    'Tidak ada / lemahnya double check',
                    'Lalai: Karena volume banyak / beban kerja banyak',
                    'Lalai: Karena lupa / tidak sengaja'
                ]
            ],
            [
                'nama_risiko' => 'Selisih kurang pada perhitungan uang kas kecil teller',
                'role_target' => 'teller',
                'causes' => [
                    'Tidak ada / lemahnya double check',
                    'Lalai: Karena volume banyak / beban kerja banyak',
                    'Lalai: Karena lupa / tidak sengaja',
                    'Itikad buruk karyawan'
                ]
            ],
            [
                'nama_risiko' => 'Potensi Risiko Finansial Lainnya',
                'role_target' => 'teller',
                'causes' => [] 
            ],

            // ==========================================
            // SOAL UNTUK KACAB (Sesuai Gambar Lu)
            // ==========================================
            [
                'nama_risiko' => 'Salah perhitungan insentif pencairan kredit',
                'role_target' => 'kacab',
                'causes' => [
                    'Tidak ada / lemahnya double check',
                    'Lalai: Karena tidak paham',
                    'Lalai: Karena volume banyak / beban kerja banyak',
                    'Software belum ada / masih manual'
                ]
            ],
            [
                'nama_risiko' => 'Selisih kurang pada perhitungan jumlah materai',
                'role_target' => 'kacab',
                'causes' => [
                    'Tidak ada / lemahnya double check',
                    'Lalai: Karena volume banyak / beban kerja banyak',
                    'Lalai: Karena lupa / tidak sengaja',
                    'Itikad buruk karyawan'
                ]
            ],
            [
                'nama_risiko' => 'Selisih kurang pada perhitungan uang kas besar teller',
                'role_target' => 'kacab',
                'causes' => [
                    'Tidak ada / lemahnya double check',
                    'Lalai: Karena volume banyak / beban kerja banyak',
                    'Lalai: Karena lupa / tidak sengaja',
                    'Itikad buruk karyawan'
                ]
            ],
            [
                'nama_risiko' => 'Saldo ATM / CRM melebihi saldo yang dapat diasuransikan',
                'role_target' => 'kacab',
                'causes' => [
                    'Tidak ada / lemahnya double check',
                    'Lalai: Karena volume banyak / beban kerja banyak',
                    'Lalai: Karena lupa / tidak sengaja'
                ]
            ],
            [
                'nama_risiko' => 'Potensi Risiko Finansial Lainnya',
                'role_target' => 'kacab',
                'causes' => [] 
            ]
        ];

        // 3. EKSEKUSI PENYUNTIKAN
        foreach ($masterData as $data) {
            $item = RiskItem::firstOrCreate([
                'nama_risiko' => $data['nama_risiko'],
                'role_target' => $data['role_target']
            ]);

            foreach ($data['causes'] as $causeText) {
                $cause = RiskCause::firstOrCreate([
                    'risk_item_id' => $item->id,
                    'penyebab' => $causeText
                ]);

                // Suntik mitigasi otomatis kalau ada di kamus
                if (isset($mitigasiKamus[$causeText])) {
                    RiskMitigation::firstOrCreate([
                        'risk_cause_id' => $cause->id,
                        'mitigasi' => $mitigasiKamus[$causeText]
                    ]);
                }
            }
        }
        
        $this->command->info('Kamus Risiko & Mitigasi Cerdas berhasil diisi!');
    }
}