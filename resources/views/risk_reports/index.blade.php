<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Riwayat & Monitoring Risiko Operasional') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-4 shadow sm:rounded-lg mb-6 border border-gray-200">
                <form method="GET" action="{{ route('risk.history') }}" class="flex flex-col md:flex-row items-end gap-4">

                    @if(in_array($role, ['manrisk', 'korwil']))
                    <div class="w-full md:w-1/3">
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Filter Cabang</label>
                        <select name="branch_id" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="">Semua Cabang (View All)</option>
                            @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                {{ $branch->nama_cabang }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <div class="w-full md:w-1/4">
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Dari Tanggal</label>
                        <input type="date" name="start_date" value="{{ request('start_date') }}" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                    </div>

                    <div class="w-full md:w-1/4">
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Sampai Tanggal</label>
                        <input type="date" name="end_date" value="{{ request('end_date') }}" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                    </div>

                    <div class="w-full md:w-auto flex gap-2">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-800 text-white font-bold py-2 px-4 rounded text-sm shadow">
                            Filter Data
                        </button>
                        <a href="{{ route('risk.history') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded text-sm shadow">
                            Reset
                        </a>
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
                                <th class="px-6 py-3 text-right text-xs font-extrabold text-gray-500 uppercase tracking-wider">Nominal (Rp)</th>
                                <th class="px-6 py-3 text-center text-xs font-extrabold text-gray-500 uppercase tracking-wider">Status</th>
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
                                    <div class="text-sm font-bold text-gray-900" title="Waktu Input ke Sistem">Lapor: {{ $report->created_at->format('d/m/Y') }}</div>
                                    <div class="text-xs text-gray-500 mt-1" title="Tanggal Kejadian Diketahui">Diket: {{ $tglDiketahui->format('d/m/Y') }}</div>

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

                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-gray-800">
                                    {{ number_format($report->dampak_finansial, 0, ',', '.') }}
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($report->approval_status === 'pending_kacab')
                                    <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded text-xs font-bold uppercase border border-yellow-300">Pending Kacab</span>
                                    @elseif($report->approval_status === 'pending_korwil')
                                    <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs font-bold uppercase border border-blue-300">Pending Korwil</span>
                                    @elseif($report->approval_status === 'rejected')
                                    <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-xs font-bold uppercase border border-red-300">Rejected</span>
                                    @elseif($report->approval_status === 'approved')
                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs font-bold uppercase border border-green-300">Approved</span>
                                    <div class="mt-2">
                                        @if($report->resolution_status === 'closed')
                                        <span class="px-2 py-0.5 bg-gray-200 text-gray-700 border border-gray-400 rounded text-[10px] font-bold uppercase">Selesai (Closed)</span>
                                        @else
                                        <span class="px-2 py-0.5 bg-orange-100 text-orange-700 border border-orange-400 rounded text-[10px] font-bold uppercase">Monitoring</span>
                                        @endif
                                    </div>
                                    @endif
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
</x-app-layout>