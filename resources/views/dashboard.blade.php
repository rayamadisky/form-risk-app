<x-app-layout>
    <x-slot name="header">
        <div class="space-y-1">
            <h2 class="font-semibold text-xl text-slate-900 leading-tight tracking-tight">
                {{ __('Dashboard Utama BPR') }}
            </h2>
            <p class="text-sm text-slate-500">Akses cepat ke pelaporan, review, dan kontrol master data dengan tampilan yang lebih bersih.</p>
        </div>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="page-shell page-stack">

            <div class="surface-card overflow-hidden border-l-4 border-indigo-500">
                <div class="section-pad text-slate-900">
                    <h3 class="text-xl sm:text-2xl font-extrabold text-slate-900 break-words tracking-tight">Selamat Datang, {{ Auth::user()->name }}!</h3>
                    <div class="mt-3 flex flex-col gap-2 sm:flex-row sm:flex-wrap sm:gap-4 text-sm text-slate-600">
                        <p>📍 Cabang: <span class="font-bold text-gray-900">{{ Auth::user()->branch->nama_cabang ?? 'Pusat (HQ)' }}</span></p>
                        <p>🏢 Jabatan: <span class="font-bold text-indigo-700 uppercase">{{ Auth::user()->roles->first()->name ?? 'Tidak Ada Jabatan' }}</span></p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 sm:gap-6">

                @hasanyrole('teller|ca|csr|security|kacab')
                <a href="{{ route('form.risiko', 'finansial') }}" class="block min-h-full p-6 bg-white border border-slate-200 rounded-2xl shadow-sm hover:bg-red-50/70 hover:border-red-200 transition border-t-4 border-t-red-500 group">
                    <div class="text-red-500 mb-3 text-3xl group-hover:scale-110 transition-transform">💸</div>
                    <h5 class="mb-3 text-xl font-bold tracking-tight text-slate-900">Risiko Finansial (Loss Event)</h5>
                    <p class="font-normal text-slate-600 text-sm leading-6">Laporkan selisih kas, salah input nominal, salah hitung margin, atau kejadian yang berdampak langsung pada kerugian uang.</p>
                </a>

                <a href="{{ route('form.risiko', 'non-finansial') }}" class="block min-h-full p-6 bg-white border border-slate-200 rounded-2xl shadow-sm hover:bg-orange-50/70 hover:border-orange-200 transition border-t-4 border-t-orange-500 group">
                    <div class="text-orange-500 mb-3 text-3xl group-hover:scale-110 transition-transform">⚠️</div>
                    <h5 class="mb-3 text-xl font-bold tracking-tight text-slate-900">Risiko Non-Finansial (Risk Event)</h5>
                    <p class="font-normal text-slate-600 text-sm leading-6">Laporkan komplain nasabah, sistem/jaringan down, pelanggaran SOP, atau potensi risiko operasional lainnya.</p>
                </a>

                <a href="{{ route('risk.history') }}" class="block min-h-full p-6 bg-white border border-slate-200 rounded-2xl shadow-sm hover:bg-blue-50/70 hover:border-blue-200 transition border-t-4 border-t-blue-500 group">
                    <div class="text-blue-500 mb-3 text-3xl group-hover:scale-110 transition-transform">🕒</div>
                    <h5 class="mb-3 text-xl font-bold tracking-tight text-slate-900">Riwayat & Progress Saya</h5>
                    <p class="font-normal text-slate-600 text-sm leading-6">Lihat riwayat laporan yang pernah Anda buat dan update progress penyelesaiannya di sini.</p>
                </a>
                @endhasanyrole

                @hasanyrole('kacab|korwil')
                <a href="{{ route('review.laporan') }}" class="block min-h-full p-6 bg-white border border-slate-200 rounded-2xl shadow-sm hover:bg-yellow-50/70 hover:border-yellow-200 transition border-t-4 border-t-yellow-500 group">
                    <div class="text-yellow-500 mb-3 text-3xl group-hover:scale-110 transition-transform">⚖️</div>
                    <h5 class="mb-3 text-xl font-bold tracking-tight text-slate-900">Review & Tindak Lanjut</h5>
                    <p class="font-normal text-slate-600 text-sm leading-6">Validasi laporan dari bawahan Anda (Approve/Reject) dan pantau status penyelesaiannya.</p>
                </a>
                @endhasanyrole

                @hasanyrole('korwil|manrisk')
                <a href="{{ route('risk.history') }}" class="block min-h-full p-6 bg-white border border-slate-200 rounded-2xl shadow-sm hover:bg-green-50/70 hover:border-green-200 transition border-t-4 border-t-green-500 group">
                    <div class="text-green-500 mb-3 text-3xl group-hover:scale-110 transition-transform">📊</div>
                    <h5 class="mb-3 text-xl font-bold tracking-tight text-slate-900">Riwayat & Monitoring</h5>
                    <p class="font-normal text-slate-600 text-sm leading-6">Akses pusat data laporan risiko. Analisa total kejadian dan akumulasi kerugian finansial.</p>
                </a>
                @endhasanyrole

                @hasrole('manrisk')
                <a href="{{ route('admin.risk_master.index') }}" class="block min-h-full p-6 bg-white border border-slate-200 rounded-2xl shadow-sm hover:bg-purple-50/70 hover:border-purple-200 transition border-t-4 border-t-purple-500 group">
                    <div class="text-purple-500 mb-3 text-3xl group-hover:scale-110 transition-transform">⚙️</div>
                    <h5 class="mb-3 text-xl font-bold tracking-tight text-slate-900">Master Data Risiko</h5>
                    <p class="font-normal text-slate-600 text-sm leading-6">Pusat kendali "Bank Soal". Kelola daftar pertanyaan, penyebab, dan mitigasi untuk semua cabang.</p>
                </a>

                <a href="{{ route('admin.users.index') }}" class="block min-h-full p-6 bg-white border border-slate-200 rounded-2xl shadow-sm hover:bg-red-50/70 hover:border-red-200 transition border-t-4 border-t-red-500 group">
                    <div class="text-red-500 mb-3 text-3xl group-hover:scale-110 transition-transform">👥</div>
                    <h5 class="mb-3 text-xl font-bold tracking-tight text-slate-900">Manajemen Pengguna</h5>
                    <p class="font-normal text-slate-600 text-sm leading-6">Kontrol akses seluruh pegawai BPR. Atur mutasi cabang dan non-aktifkan akun yang mencurigakan.</p>
                </a>
                
                <a href="{{ route('branches.index') }}" class="block min-h-full p-6 bg-white border border-slate-200 rounded-2xl shadow-sm hover:bg-red-50/70 hover:border-red-200 transition border-t-4 border-t-red-500 group">
                    <div class="text-red-500 mb-3 text-3xl group-hover:scale-110 transition-transform">🏦</div>
                    <h5 class="mb-3 text-xl font-bold tracking-tight text-slate-900">Manajemen Cabang</h5>
                    <p class="font-normal text-slate-600 text-sm leading-6">Kelola informasi dan struktur seluruh cabang BPR. Atur pengawasan dan koordinasi antar cabang.</p>
                </a>

                @endhasrole

            </div>
        </div>
    </div>
</x-app-layout>