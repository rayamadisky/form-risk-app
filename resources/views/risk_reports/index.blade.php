<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Riwayat & Monitoring Risiko Operasional') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
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

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Tgl Lapor</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Maker</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Risiko / Penyebab</th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase">Nominal</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($reports as $report)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm">{{ $report->created_at->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 text-sm font-semibold">{{ $report->user->name }}</td>
                            <td class="px-6 py-4 text-sm">
                                <span class="block font-bold text-gray-700">{{ $report->item->nama_risiko }}</span>
                                <span class="text-xs text-gray-400 italic">Sebab: {{ $report->cause->penyebab ?? $report->other_cause_description }}</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-right font-mono">
                                {{ number_format($report->dampak_finansial, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 border-b text-center">
                                @if($report->approval_status === 'pending_kacab')
                                <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full font-bold text-xs uppercase">Pending Kacab</span>
                                @elseif($report->approval_status === 'pending_korwil')
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full font-bold text-xs uppercase">Pending Korwil</span>
                                @elseif($report->approval_status === 'rejected')
                                <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full font-bold text-xs uppercase">Rejected</span>
                                @elseif($report->approval_status === 'approved')
                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full font-bold text-xs uppercase">Approved</span>

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
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>