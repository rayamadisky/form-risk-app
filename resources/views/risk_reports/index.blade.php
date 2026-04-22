<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Riwayat & Monitoring Risiko Operasional') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-4 shadow sm:rounded-lg mb-6 border border-gray-200">
                <form method="GET" action="{{ route('risk.history') }}" class="grid grid-cols-1 md:grid-cols-6 gap-4 items-end mb-6">

                    @if(in_array($role, ['teller', 'ca', 'csr', 'security']))
                    <div class="md:col-span-3">
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Kategori Risiko</label>
                        <select name="kategori" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm h-[38px]">
                            <option value="">Semua Kategori</option>
                            <option value="finansial" {{ request('kategori') == 'finansial' ? 'selected' : '' }}>Finansial</option>
                            <option value="non-finansial" {{ request('kategori') == 'non-finansial' ? 'selected' : '' }}>Non-Finansial</option>
                        </select>
                    </div>

                    <div class="md:col-span-3">
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Status Penyelesaian</label>
                        <select name="resolution_status" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm h-[38px]">
                            <option value="">Semua Status</option>
                            <option value="open" {{ request('resolution_status') == 'open' ? 'selected' : '' }}>Open (Baru)</option>
                            <option value="in_progress" {{ request('resolution_status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="monitoring" {{ request('resolution_status') == 'monitoring' ? 'selected' : '' }}>Monitoring</option>
                            <option value="closed" {{ request('resolution_status') == 'closed' ? 'selected' : '' }}>Closed (Selesai)</option>
                        </select>
                    </div>

                    @else
                    @if(in_array($role, ['manrisk', 'korwil']))
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Filter Cabang</label>
                        <select name="branch_id" id="select-cabang" class="block w-full border-gray-300 rounded-md shadow-sm text-sm">
                            <option value="">Semua Cabang (View All)</option>
                            @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                {{ $branch->nama_cabang }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <div class="{{ in_array($role, ['manrisk', 'korwil']) ? 'md:col-span-1' : 'md:col-span-2' }}">
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Kategori</label>
                        <select name="kategori" class="block w-full border-gray-300 rounded-md shadow-sm text-sm h-[38px]">
                            <option value="">Semua</option>
                            <option value="finansial" {{ request('kategori') == 'finansial' ? 'selected' : '' }}>Finansial</option>
                            <option value="non-finansial" {{ request('kategori') == 'non-finansial' ? 'selected' : '' }}>Non-Finansial</option>
                        </select>
                    </div>

                    <div class="{{ in_array($role, ['manrisk', 'korwil']) ? 'md:col-span-1' : 'md:col-span-2' }}">
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Jabatan</label>
                        <select name="jabatan" class="block w-full border-gray-300 rounded-md shadow-sm text-sm h-[38px]">
                            <option value="">Semua</option>
                            <option value="teller" {{ request('jabatan') == 'teller' ? 'selected' : '' }}>Teller</option>
                            <option value="ca" {{ request('jabatan') == 'ca' ? 'selected' : '' }}>CA</option>
                            <option value="csr" {{ request('jabatan') == 'csr' ? 'selected' : '' }}>CSR</option>
                            <option value="security" {{ request('jabatan') == 'security' ? 'selected' : '' }}>Security</option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Status Penyelesaian</label>
                        <select name="resolution_status" class="block w-full border-gray-300 rounded-md shadow-sm text-sm h-[38px]">
                            <option value="">Semua Status</option>
                            <option value="open" {{ request('resolution_status') == 'open' ? 'selected' : '' }}>Open</option>
                            <option value="in_progress" {{ request('resolution_status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="monitoring" {{ request('resolution_status') == 'monitoring' ? 'selected' : '' }}>Monitoring</option>
                            <option value="closed" {{ request('resolution_status') == 'closed' ? 'selected' : '' }}>Closed</option>
                        </select>
                    </div>

                    <div class="md:col-span-4 flex gap-2">
                        <div class="flex-1">
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Dari Tgl</label>
                            <input type="date" name="start_date" value="{{ request('start_date') }}" class="block w-full border-gray-300 rounded-md shadow-sm text-sm h-[38px]">
                        </div>
                        <div class="flex-1">
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Sampai</label>
                            <input type="date" name="end_date" value="{{ request('end_date') }}" class="block w-full border-gray-300 rounded-md shadow-sm text-sm h-[38px]">
                        </div>
                    </div>
                    @endif

                    <div class="{{ in_array($role, ['teller', 'ca', 'csr', 'security']) ? 'md:col-span-6 mt-4' : 'md:col-span-2 mt-0' }} flex gap-2 justify-end items-end h-[38px]">
                        <a href="{{ route('risk.history') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-6 rounded text-sm shadow transition">
                            Reset
                        </a>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-800 text-white font-bold py-2 px-6 rounded text-sm shadow transition">
                            Filter
                        </button>
                    </div>
                </form>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-blue-500">
                    <p class="text-sm text-gray-500 font-bold uppercase">Total Kejadian Terdata</p>
                    <p class="text-2xl font-black">{{ $reports->count() }} <span class="text-sm font-normal text-gray-400">Kasus</span></p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-green-500">
                    <p class="text-sm text-gray-500 font-bold uppercase">Total Kerugian (Approved)</p>
                    <p class="text-2xl font-black text-green-600">Rp {{ number_format($totalLoss, 0, ',', '.') }}</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-red-500">
                    <p class="text-sm text-gray-500 font-bold uppercase">Laporan di-Reject</p>
                    <p class="text-2xl font-black text-red-600">{{ $reports->where('status', 'rejected')->count() }}</p>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-extrabold text-gray-500 uppercase tracking-wider">Tgl Lapor & Ketahui</th>
                                <th class="px-6 py-3 text-left text-xs font-extrabold text-gray-500 uppercase tracking-wider">Cabang</th>
                                <th class="px-6 py-3 text-left text-xs font-extrabold text-gray-500 uppercase tracking-wider">Maker</th>
                                <th class="px-6 py-3 text-left text-xs font-extrabold text-gray-500 uppercase tracking-wider">Risiko, Penyebab & Mitigasi</th>
                                <th class="px-6 py-3 text-right text-xs font-extrabold text-gray-500 uppercase tracking-wider">Dampak</th>
                                <th class="px-6 py-3 text-center">Status Approval</th>
                                <th class="px-6 py-3 text-center">Tindak Lanjut</th>
                                <th class="px-6 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($reports as $report)
                            @php
                            $tglDiketahui = \Carbon\Carbon::parse($report->tanggal_diketahui)->startOfDay();
                            $tglLapor = $report->created_at->startOfDay();

                            // Ngitung selisih hari. Kalau hasilnya positif, berarti lapornya setelah diketahui.
                            $selisihHari = $tglDiketahui->diffInDays($tglLapor, false);

                            // Flag merah kalau lewat 5 hari SLA
                            $isLate = $selisihHari > 5;
                            @endphp

                            <tr class="hover:bg-gray-50 transition duration-150 {{ $isLate ? 'bg-red-50' : '' }}">

                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-gray-900" title="Waktu Input ke Sistem">Lapor: <br> {{ $report->created_at->format('d/m/Y') }}</div>
                                    <div class="text-xs text-gray-500 mt-1" title="Tanggal Kejadian Diketahui">Diketahui: <br>{{ $tglDiketahui->format('d/m/Y') }}</div>

                                    @if($isLate)
                                    <div class="mt-2 flex items-center gap-1 text-red-700 font-extrabold text-[10px] uppercase bg-red-200 px-2 py-1 rounded-sm w-max border border-red-300">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                        </svg>
                                        Telat {{ $selisihHari }} Hari
                                    </div>
                                    @endif
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 font-semibold uppercase">
                                    {{ $report->branch->nama_cabang ?? 'HQ' }}
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 font-bold">
                                    {{ $report->user->name }}
                                </td>

                                <td class="px-6 py-4">
                                    <span class="block text-sm font-bold text-gray-900">{{ $report->item->nama_risiko ?? $report->other_item_description }}</span>
                                    <span class="block text-xs text-red-600 font-semibold mt-1">Sebab: {{ $report->cause->penyebab ?? $report->other_cause_description }}</span>

                                    <div class="mt-3 p-2 bg-green-50 rounded border border-green-200">
                                        <span class="block text-[10px] font-extrabold text-green-800 uppercase tracking-wider mb-1">Tindakan Mitigasi:</span>

                                        <div class="text-xs text-green-700 space-y-1">
                                            {{-- 1. Narik Mitigasi Standar dari Master Data (Jika Ada) --}}
                                            @if($report->cause && $report->cause->mitigations->isNotEmpty())
                                            <ul class="list-disc list-inside">
                                                @foreach($report->cause->mitigations as $mitigasi)
                                                <li>{{ $mitigasi->mitigasi }}</li>
                                                @endforeach
                                            </ul>
                                            @endif

                                            {{-- 2. Narik Mitigasi Tambahan Inputan User (Jika Ada) --}}
                                            @if($report->mitigasi_tambahan)
                                            <div class="flex gap-1 mt-1 pt-1 border-t border-green-200">
                                                <span class="font-bold">Tambahan:</span>
                                                <span class="italic text-gray-800">{{ $report->mitigasi_tambahan }}</span>
                                            </div>
                                            @endif

                                            {{-- 3. Kalau dua-duanya kosong sama sekali --}}
                                            @if((!$report->cause || $report->cause->mitigations->isEmpty()) && empty($report->mitigasi_tambahan))
                                            <span class="text-gray-500 italic">- Tidak ada mitigasi terdata -</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <td class="py-3 px-4 border-b text-sm text-gray-800">
                                    @if($report->kategori === 'finansial')
                                    <span class="font-bold">Rp {{ number_format($report->dampak_finansial, 0, ',', '.') }}</span>
                                    @else
                                    <span class="text-xs italic">{{ $report->dampak_non_finansial }}</span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-center">
                                    @if($report->approval_status === 'approved')
                                    <span class="px-2 py-1 bg-green-100 text-green-800 text-[10px] font-bold uppercase rounded border border-green-200">Approved</span>
                                    @elseif($report->approval_status === 'rejected')
                                    <span class="px-2 py-1 bg-red-100 text-red-800 text-[10px] font-bold uppercase rounded border border-red-200">Rejected</span>
                                    @else
                                    <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-[10px] font-bold uppercase rounded border border-yellow-200">Pending {{ str_replace('pending_', '', $report->approval_status) }}</span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-center">
                                    @php
                                    $resColors = [
                                    'open' => 'bg-gray-100 text-gray-600 border-gray-200',
                                    'in_progress' => 'bg-blue-100 text-blue-700 border-blue-200',
                                    'monitoring' => 'bg-orange-100 text-orange-700 border-orange-200',
                                    'closed' => 'bg-gray-800 text-white border-gray-900',
                                    ];
                                    $resClass = $resColors[$report->resolution_status] ?? $resColors['open'];
                                    @endphp
                                    <span class="px-2 py-1 text-[10px] font-bold uppercase rounded border {{ $resClass }}">
                                        {{ str_replace('_', ' ', $report->resolution_status) }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('risk_reports.show', $report->id) }}" class="inline-block bg-blue-600 hover:bg-blue-800 text-white font-bold py-1.5 px-3 rounded text-[10px] uppercase tracking-wider shadow transition">
                                        Detail & Progress
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-sm text-gray-500 italic">
                                    Tidak ada data riwayat laporan yang sesuai dengan filter Anda.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#select-cabang').select2({
                placeholder: "Ketik nama cabang...",
                allowClear: true,
                width: '100%'
            });
        });
    </script>

    <style>
        /* Styling biar Select2 nyatu sama form Tailwind lu */
        .select2-container .select2-selection--single {
            height: 38px !important;
            border-color: #d1d5db !important;
            border-radius: 0.375rem !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 38px !important;
            font-size: 0.875rem !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px !important;
        }
    </style>
</x-app-layout>