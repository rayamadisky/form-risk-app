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
                                <th class="py-3 px-4 border-b text-left text-sm font-semibold text-gray-700">Tanggal</th>
                                <th class="py-3 px-4 border-b text-left text-sm font-semibold text-gray-700">Pelapor</th>
                                <th class="py-3 px-4 border-b text-left text-sm font-semibold text-gray-700 w-1/3">Detail Risiko & Penyebab</th>
                                <th class="py-3 px-4 border-b text-left text-sm font-semibold text-gray-700">Nominal (Rp)</th>
                                <th class="py-3 px-4 border-b text-left text-sm font-semibold text-gray-700">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reports as $report)
                            <tr class="hover:bg-gray-50">
                                <td class="py-3 px-4 border-b text-sm">{{ \Carbon\Carbon::parse($report->tanggal_kejadian)->format('d-m-Y') }}</td>
                                <td class="py-3 px-4 border-b text-sm font-bold">{{ $report->user->name }}</td>
                                <td class="py-3 px-4 border-b">
                                    <span class="block text-sm font-bold text-red-600">
                                        {{ $report->item->nama_risiko ?? 'Potensi Risiko Lainnya' }}
                                    </span>
                                    @if($report->other_item_description)
                                    <span class="block text-sm font-semibold text-gray-800 mt-1">
                                        Detail: <span class="italic font-normal">{{ $report->other_item_description }}</span>
                                    </span>
                                    @endif
                                    <span class="block text-xs text-gray-500 mt-1">
                                        Sebab:
                                        @if($report->cause)
                                        {{ $report->cause->penyebab }}
                                        @elseif($report->other_cause_description)
                                        {{ $report->other_cause_description }}
                                        @endif
                                    </span>
                                </td>
                                <td class="py-3 px-4 border-b text-sm font-bold text-gray-800">
                                    {{ number_format($report->dampak_finansial, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-4 border-b text-sm">
                                    <form action="{{ route('risk_reports.update_status', $report->id) }}" method="POST" class="inline-flex gap-2">
                                        @csrf
                                        <input type="hidden" name="status" value="approved">
                                        <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-3 rounded text-xs border border-green-600">Approve</button>
                                    </form>
                                    <form action="{{ route('risk_reports.update_status', $report->id) }}" method="POST" class="inline-flex gap-2">
                                        @csrf
                                        <input type="hidden" name="status" value="rejected">
                                        <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-xs border border-red-600">Reject</button>
                                    </form>
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
                                <th class="py-3 px-4 border-b text-left text-sm font-semibold text-gray-700">Tanggal</th>
                                <th class="py-3 px-4 border-b text-left text-sm font-semibold text-gray-700 w-1/3">Risiko & Penyebab</th>
                                <th class="py-3 px-4 border-b text-left text-sm font-semibold text-gray-700">Nominal (Rp)</th>
                                <th class="py-3 px-4 border-b text-left text-sm font-semibold text-gray-700">Status Penyelesaian</th>
                                <th class="py-3 px-4 border-b text-left text-sm font-semibold text-gray-700">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tindakLanjut as $tl)
                            <tr class="hover:bg-gray-50">
                                <td class="py-3 px-4 border-b text-sm">{{ \Carbon\Carbon::parse($tl->tanggal_kejadian)->format('d-m-Y') }}</td>
                                <td class="py-3 px-4 border-b">
                                    <span class="block text-sm font-bold text-red-600">{{ $tl->item->nama_risiko ?? 'Potensi Risiko Lainnya' }}</span>
                                    <span class="block text-xs text-gray-500 mt-1">Sebab: {{ $tl->cause->penyebab ?? $tl->other_cause_description }}</span>
                                </td>
                                <td class="py-3 px-4 border-b text-sm font-bold text-gray-800">{{ number_format($tl->dampak_finansial, 0, ',', '.') }}</td>
                                <td class="py-3 px-4 border-b text-sm">
                                    <span class="px-2 py-1 {{ $tl->resolution_status == 'monitoring' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800' }} rounded font-bold text-xs uppercase">
                                        {{ $tl->resolution_status }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 border-b text-sm">
                                    <form action="{{ route('risk_reports.update_resolution', $tl->id) }}" method="POST" class="flex items-center gap-2">
                                        @csrf
                                        <select name="resolution_status" class="text-xs border-gray-300 rounded-md py-1">
                                            <option value="monitoring" {{ $tl->resolution_status == 'monitoring' ? 'selected' : '' }}>Monitoring</option>
                                            <option value="closed">Closed (Selesai)</option>
                                        </select>
                                        <button type="submit" class="bg-blue-600 hover:bg-blue-800 text-white font-bold py-1 px-3 rounded text-xs">Simpan</button>
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