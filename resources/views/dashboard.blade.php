<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    @role('manrisk')
                    <h3 class="text-lg font-bold text-green-600">Area Khusus ManRisk</h3>
                    <p>Ada 5 laporan risiko dari bawahan Anda yang menunggu persetujuan (Approve/Reject).</p>
                    <a href="{{ route('admin.users.index') }}" class="inline-block mt-4 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                        + Edit User
                    </a>
                    <a href="{{ route('admin.risk_master.index') }}" class="inline-block mt-4 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                        Lihat Riwayat Laporan
                    </a>

                    <hr class="my-4">
                    <p class="text-sm text-gray-500">Anda juga bisa input risiko tingkat cabang:</p>
                    <a href="{{ route('form.risiko') }}" class="inline-block mt-4 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                        + Input Form Kacab
                    </a>

                    @endrole

                    @role('admin')
                    <h3 class="text-lg font-bold text-red-600">Area Khusus Admin</h3>
                    <p>Lu bisa setting master data cabang, form pertanyaan, dan ngatur user di sini.</p>
                    <button class="mt-4 bg-red-500 text-white px-4 py-2 rounded">Menu Setting Admin</button>
                    @endrole

                    @if (session('success'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                        <strong class="font-bold">Sukses!</strong>
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                    @endif

                    @role('teller')
                    <h3 class="text-lg font-bold text-blue-600">Area Teller (Maker)</h3>
                    <p>Silakan input laporan kejadian risiko operasional harian di loket Anda.</p>
                    <a href="{{ route('form.risiko') }}" class="inline-block mt-4 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                        + Input Form Risiko
                    </a>
                    @endrole

                    @role('kacab')
                    <h3 class="text-lg font-bold text-green-600">Area Kepala Cabang (Checker)</h3>
                    <p>Ada 5 laporan risiko dari bawahan Anda yang menunggu persetujuan (Approve/Reject).</p>
                    <a href="{{ route('review.laporan') }}" class="inline-block mt-4 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                        + Review Laporan Bawahan
                    </a>
                    <a href="{{ route('risk.history') }}" class="inline-block mt-4 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                        Lihat Riwayat Laporan
                    </a>

                    <hr class="my-4">
                    <p class="text-sm text-gray-500">Anda juga bisa input risiko tingkat cabang:</p>
                    <a href="{{ route('form.risiko') }}" class="inline-block mt-4 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                        + Input Form Kacab
                    </a>

                    @endrole

                    @role('cs')
                    <div class="mt-4 p-4 bg-purple-100 border-l-4 border-purple-500">
                        <h3 class="text-lg font-bold text-purple-700">Area Customer Service (Maker)</h3>
                        <p>Input potensi risiko operasional dari sisi pelayanan nasabah di sini.</p>
                        <button class="mt-2 bg-purple-500 text-white px-4 py-2 rounded">+ Form Risiko CS</button>
                    </div>
                    @endrole

                    @role('security')
                    <div class="mt-4 p-4 bg-yellow-100 border-l-4 border-yellow-500">
                        <h3 class="text-lg font-bold text-yellow-700">Area Security (Maker)</h3>
                        <p>Lapor kejadian terkait keamanan fisik atau fasilitas kantor di sini.</p>
                        <button class="mt-2 bg-yellow-600 text-white px-4 py-2 rounded">+ Form Risiko Keamanan</button>
                    </div>
                    @endrole
                </div>
            </div>
        </div>
    </div>
</x-app-layout>