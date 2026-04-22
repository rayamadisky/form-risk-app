<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Utama BPR') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8 border-l-4 border-indigo-500">
                <div class="p-6 text-gray-900">
                    <h3 class="text-2xl font-extrabold text-gray-800">Selamat Datang, {{ Auth::user()->name }}!</h3>
                    <div class="mt-2 flex gap-4 text-sm text-gray-600">
                        <p>📍 Cabang: <span class="font-bold text-gray-900">{{ Auth::user()->branch->nama_cabang ?? 'Pusat (HQ)' }}</span></p>
                        <p>🏢 Jabatan: <span class="font-bold text-indigo-700 uppercase">{{ Auth::user()->roles->first()->name ?? 'Tidak Ada Jabatan' }}</span></p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                @hasanyrole('teller|ca|csr|security|kacab')
                <a href="{{ route('form.risiko', 'finansial') }}" class="block p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-red-50 transition border-t-4 border-t-red-500 group">
                    <div class="text-red-500 mb-2 text-3xl group-hover:scale-110 transition-transform">💸</div>
                    <h5 class="mb-2 text-xl font-bold tracking-tight text-gray-900">Risiko Finansial (Loss Event)</h5>
                    <p class="font-normal text-gray-600 text-sm">Laporkan selisih kas, salah input nominal, salah hitung margin, atau kejadian yang berdampak langsung pada kerugian uang.</p>
                </a>

                <a href="{{ route('form.risiko', 'non-finansial') }}" class="block p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-orange-50 transition border-t-4 border-t-orange-500 group">
                    <div class="text-orange-500 mb-2 text-3xl group-hover:scale-110 transition-transform">⚠️</div>
                    <h5 class="mb-2 text-xl font-bold tracking-tight text-gray-900">Risiko Non-Finansial (Risk Event)</h5>
                    <p class="font-normal text-gray-600 text-sm">Laporkan komplain nasabah, sistem/jaringan down, pelanggaran SOP, atau potensi risiko operasional lainnya.</p>
                </a>


                <a href="{{ route('risk.history') }}" class="block p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-blue-50 transition border-t-4 border-t-blue-500 group">
                    <div class="text-blue-500 mb-2 text-3xl group-hover:scale-110 transition-transform">🕒</div>
                    <h5 class="mb-2 text-xl font-bold tracking-tight text-gray-900">Riwayat & Progress Saya</h5>
                    <p class="font-normal text-gray-600 text-sm">Lihat riwayat laporan yang pernah Anda buat dan update progress penyelesaiannya di sini.</p>
                </a>
                @endhasanyrole

                @hasanyrole('kacab|korwil')
                <a href="{{ route('review.laporan') }}" class="block p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-yellow-50 transition border-t-4 border-t-yellow-500 group">
                    <div class="text-yellow-500 mb-2 text-3xl group-hover:scale-110 transition-transform">⚖️</div>
                    <h5 class="mb-2 text-xl font-bold tracking-tight text-gray-900">Review & Tindak Lanjut</h5>
                    <p class="font-normal text-gray-600 text-sm">Validasi laporan dari bawahan Anda (Approve/Reject) dan pantau status penyelesaiannya.</p>
                </a>
                @endhasanyrole

                @hasanyrole('kacab|korwil|manrisk')
                <a href="{{ route('risk.history') }}" class="block p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-green-50 transition border-t-4 border-t-green-500 group">
                    <div class="text-green-500 mb-2 text-3xl group-hover:scale-110 transition-transform">📊</div>
                    <h5 class="mb-2 text-xl font-bold tracking-tight text-gray-900">Riwayat & Monitoring</h5>
                    <p class="font-normal text-gray-600 text-sm">Akses pusat data laporan risiko. Analisa total kejadian dan akumulasi kerugian finansial.</p>
                </a>
                @endhasanyrole

                @hasrole('manrisk')
                <a href="{{ route('admin.risk_master.index') }}" class="block p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-purple-50 transition border-t-4 border-t-purple-500 group">
                    <div class="text-purple-500 mb-2 text-3xl group-hover:scale-110 transition-transform">⚙️</div>
                    <h5 class="mb-2 text-xl font-bold tracking-tight text-gray-900">Master Data Risiko</h5>
                    <p class="font-normal text-gray-600 text-sm">Pusat kendali "Bank Soal". Kelola daftar pertanyaan, penyebab, dan mitigasi untuk semua cabang.</p>
                </a>

                <a href="{{ route('admin.users.index') }}" class="block p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-red-50 transition border-t-4 border-t-red-500 group">
                    <div class="text-red-500 mb-2 text-3xl group-hover:scale-110 transition-transform">👥</div>
                    <h5 class="mb-2 text-xl font-bold tracking-tight text-gray-900">Manajemen Pengguna</h5>
                    <p class="font-normal text-gray-600 text-sm">Kontrol akses seluruh pegawai BPR. Atur mutasi cabang dan non-aktifkan akun yang mencurigakan.</p>
                </a>
                @endhasrole

            </div>
        </div>
    </div>
</x-app-layout>