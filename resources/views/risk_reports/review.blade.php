<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Review Laporan Risiko (Checker)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-8">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-8 border-l-4 border-yellow-500">
                <h3 class="text-lg font-bold border-b pb-2 mb-4 text-gray-800">1. Menunggu Persetujuan Anda</h3>

                @if($reports->isEmpty())
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
                    <p class="text-yellow-700 italic">Saat ini tidak ada laporan risiko baru yang butuh persetujuan Anda.</p>
                </div>
                @else
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white border border-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="py-3 px-4 border-b text-left text-xs font-semibold text-gray-700 uppercase">Tgl (Lapor/Kejadian/Diket)</th>
                                <th class="py-3 px-4 border-b text-left text-xs font-semibold text-gray-700 uppercase">Pelapor</th>
                                <th class="py-3 px-4 border-b text-left text-xs font-semibold text-gray-700 uppercase">Kategori</th>
                                <th class="py-3 px-4 border-b text-left text-xs font-semibold text-gray-700 uppercase w-1/3">Risiko, Penyebab & Mitigasi</th>
                                <th class="py-3 px-4 border-b text-left text-xs font-semibold text-gray-700 uppercase">Dampak</th>
                                <th class="py-3 px-4 border-b text-right text-xs font-semibold text-gray-700 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reports as $report)
                            <tr class="hover:bg-gray-50 align-top">
                                <td class="py-3 px-4 border-b whitespace-nowrap">
                                    <div class="text-xs font-bold text-blue-700">Lapor: {{ $report->created_at->format('d-m-Y') }}</div>
                                    <div class="text-xs text-gray-600 mt-1">Kejadian: {{ \Carbon\Carbon::parse($report->tanggal_kejadian)->format('d-m-Y') }}</div>
                                    <div class="text-xs text-gray-600">Diketahui: {{ \Carbon\Carbon::parse($report->tanggal_diketahui)->format('d-m-Y') }}</div>
                                </td>

                                <td class="py-3 px-4 border-b text-sm font-bold">{{ $report->user->name }}</td>

                                <td class="py-3 px-4 border-b">
                                    @if($report->kategori === 'finansial')
                                    <span class="px-2 py-1 bg-red-100 text-red-800 rounded font-bold text-[10px] uppercase border border-red-200">Finansial</span>
                                    @else
                                    <span class="px-2 py-1 bg-orange-100 text-orange-800 rounded font-bold text-[10px] uppercase border border-orange-200">Non-Finansial</span>
                                    @endif
                                </td>

                                <td class="py-3 px-4 border-b">
                                    <span class="block text-sm font-bold text-gray-900">{{ $report->item->nama_risiko ?? $report->other_item_description }}</span>
                                    <span class="block text-xs text-red-600 font-semibold mt-1">Sebab: {{ $report->cause->penyebab ?? $report->other_cause_description }}</span>

                                    <div class="mt-2 p-2 bg-green-50 rounded border border-green-200">
                                        <span class="block text-[10px] font-extrabold text-green-800 uppercase tracking-wider mb-1">Tindakan Mitigasi:</span>
                                        <div class="text-xs text-green-700 space-y-1">
                                            @if($report->cause && $report->cause->mitigations->isNotEmpty())
                                            <ul class="list-disc list-inside">
                                                @foreach($report->cause->mitigations as $mitigasi)
                                                <li>{{ $mitigasi->mitigasi }}</li>
                                                @endforeach
                                            </ul>
                                            @endif
                                            @if($report->mitigasi_tambahan)
                                            <div class="flex gap-1 mt-1 pt-1 border-t border-green-200">
                                                <span class="font-bold">Tambahan:</span>
                                                <span class="italic text-gray-800">{{ $report->mitigasi_tambahan }}</span>
                                            </div>
                                            @endif
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

                                <td class="py-3 px-4 border-b text-right">
                                    <div class="flex flex-col gap-2 items-end">
                                        <form action="{{ route('risk_reports.update_status', $report->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="status" value="approved">
                                            <button type="submit" class="w-20 bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-2 rounded text-xs border border-green-600">Approve</button>
                                        </form>
                                        <form action="{{ route('risk_reports.update_status', $report->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="status" value="rejected">
                                            <button type="submit" class="w-20 bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded text-xs border border-red-600">Reject</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-blue-500">
                <h3 class="text-lg font-bold border-b pb-2 mb-4 text-gray-800">2. Menunggu Tindak Lanjut (Penyelesaian)</h3>

                @if($tindakLanjut->isEmpty())
                <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-4">
                    <p class="text-blue-700 italic">Semua laporan yang di-Approve sudah selesai ditindaklanjuti.</p>
                </div>
                @else
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white border border-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="py-3 px-4 border-b text-left text-xs font-semibold text-gray-700 uppercase">Tgl (Lapor/Kejadian)</th>
                                <th class="py-3 px-4 border-b text-left text-xs font-semibold text-gray-700 uppercase">Kategori</th>
                                <th class="py-3 px-4 border-b text-left text-xs font-semibold text-gray-700 uppercase w-1/3">Risiko, Penyebab & Mitigasi</th>
                                <th class="py-3 px-4 border-b text-left text-xs font-semibold text-gray-700 uppercase">Status Tindak Lanjut</th>
                                <th class="py-3 px-4 border-b text-right text-xs font-semibold text-gray-700 uppercase">Update Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tindakLanjut as $tl)
                            <tr class="hover:bg-gray-50 align-top">
                                <td class="py-3 px-4 border-b whitespace-nowrap">
                                    <div class="text-xs font-bold text-blue-700">Lapor: {{ $tl->created_at->format('d-m-Y') }}</div>
                                    <div class="text-xs text-gray-600 mt-1">Kejadian: {{ \Carbon\Carbon::parse($tl->tanggal_kejadian)->format('d-m-Y') }}</div>
                                </td>

                                <td class="py-3 px-4 border-b">
                                    @if($tl->kategori === 'finansial')
                                    <span class="px-2 py-1 bg-red-100 text-red-800 rounded font-bold text-[10px] uppercase border border-red-200">Finansial</span>
                                    @else
                                    <span class="px-2 py-1 bg-orange-100 text-orange-800 rounded font-bold text-[10px] uppercase border border-orange-200">Non-Finansial</span>
                                    @endif
                                </td>

                                <td class="py-3 px-4 border-b">
                                    <span class="block text-sm font-bold text-gray-900">{{ $tl->item->nama_risiko ?? $tl->other_item_description }}</span>
                                    <span class="block text-xs text-red-600 font-semibold mt-1">Sebab: {{ $tl->cause->penyebab ?? $tl->other_cause_description }}</span>
                                    <div class="mt-2 p-2 bg-green-50 rounded border border-green-200">
                                        <span class="block text-[10px] font-extrabold text-green-800 uppercase tracking-wider mb-1">Tindakan Mitigasi:</span>
                                        <div class="text-xs text-green-700 space-y-1">
                                            @if($tl->cause && $tl->cause->mitigations->isNotEmpty())
                                            <ul class="list-disc list-inside">
                                                @foreach($tl->cause->mitigations as $mitigasi)
                                                <li>{{ $mitigasi->mitigasi }}</li>
                                                @endforeach
                                            </ul>
                                            @endif
                                            @if($tl->mitigasi_tambahan)
                                            <div class="flex gap-1 mt-1 pt-1 border-t border-green-200">
                                                <span class="font-bold">Tambahan:</span>
                                                <span class="italic text-gray-800">{{ $tl->mitigasi_tambahan }}</span>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <td class="py-3 px-4 border-b text-sm">
                                    <span class="px-2 py-1 {{ $tl->resolution_status == 'monitoring' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800' }} rounded font-bold text-xs uppercase border {{ $tl->resolution_status == 'monitoring' ? 'border-yellow-300' : 'border-blue-300' }}">
                                        {{ $tl->resolution_status }}
                                    </span>
                                </td>

                                <td class="py-3 px-4 border-b text-right">
                                    <form action="{{ route('risk_reports.update_resolution', $tl->id) }}" method="POST" class="flex flex-col items-end gap-2">
                                        @csrf
                                        <select name="resolution_status" class="text-xs border-gray-300 rounded-md py-1 w-32">
                                            <option value="monitoring" {{ $tl->resolution_status == 'monitoring' ? 'selected' : '' }}>Masih Monitoring</option>
                                            <option value="closed">Selesai (Closed)</option>
                                        </select>
                                        <button type="submit" class="w-32 bg-blue-600 hover:bg-blue-800 text-white font-bold py-1 px-3 rounded text-xs">Simpan Status</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>