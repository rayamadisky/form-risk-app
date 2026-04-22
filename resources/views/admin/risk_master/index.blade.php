<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Control Center: Manajemen Master Data Risiko') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg border-t-4 border-blue-600">
                <h3 class="text-lg font-bold mb-4 text-gray-900">Tambah Pertanyaan Risiko Baru</h3>
                <form action="{{ route('admin.risk_master.store_item') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Target Jabatan (Role)</label>
                        <select name="role_target" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm uppercase">
                            <option value="teller">Teller</option>
                            <option value="ca">Customer Assistant</option>
                            <option value="kacab">Kacab / KaOp</option>
                            <option value="korwil">Korwil</option>
                            <option value="security">Security</option>
                            <option value="csr">CSR</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Nama Risiko (Pertanyaan)</label>
                        <input type="text" name="nama_risiko" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Contoh: Selisih kurang pada perhitungan kas">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Kategori Risiko</label>
                        <select name="kategori" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm uppercase">
                            <option value="finansial">Finansial (Loss Event)</option>
                            <option value="non-finansial">Non-Finansial (Risk Event)</option>
                        </select>
                    </div>
                    <div class="md:col-span-3 text-right">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Simpan Pertanyaan
                        </button>
                    </div>
                </form>
            </div>

            <div class="bg-white shadow overflow-hidden sm:rounded-lg border border-gray-200">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Target Role</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pertanyaan & Detail Penyebab</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($riskItems as $item)
                        <tr class="hover:bg-gray-50 align-top">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 uppercase">
                                    {{ $item->role_target }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-gray-900 mb-2">{{ $item->nama_risiko }}</div>

                                <div class="bg-gray-50 rounded-lg p-3 space-y-3">
                                    <h4 class="text-xs font-bold text-gray-500 uppercase">Daftar Penyebab & Mitigasi:</h4>
                                    @foreach($item->causes as $cause)
                                        @php $mitigasiTeks = $cause->mitigations->first()->mitigasi ?? ''; @endphp
                                        
                                        <div class="text-xs border-l-2 border-blue-300 pl-3 relative group py-1">
                                            <p class="font-semibold text-gray-700 italic pr-10">"{{ $cause->penyebab }}"</p>
                                            
                                            @if($mitigasiTeks)
                                                <p class="text-green-600 mt-1 pr-10">→ Mitigasi: {{ $mitigasiTeks }}</p>
                                            @endif

                                            <button type="button" 
                                                data-id="{{ $cause->id }}" 
                                                data-penyebab="{{ $cause->penyebab }}" 
                                                data-mitigasi="{{ $mitigasiTeks }}" 
                                                onclick="openEditCauseModal(this)" 
                                                class="absolute top-1 right-1 opacity-0 group-hover:opacity-100 bg-yellow-100 text-yellow-700 hover:bg-yellow-200 hover:text-yellow-900 border border-yellow-300 font-bold px-2 py-0.5 rounded text-[10px] transition uppercase tracking-wider">
                                                Edit
                                            </button>
                                        </div>
                                    @endforeach

                                    <form action="{{ route('admin.risk_master.store_cause', $item->id) }}" method="POST" class="mt-2 pt-2 border-t border-gray-200 flex gap-2">
                                        @csrf
                                        <input type="text" name="penyebab" required placeholder="Tambah Penyebab baru..." class="text-xs flex-1 border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                        <input type="text" name="mitigasi" placeholder="Tambah Mitigasi (Opsional)..." class="text-xs flex-1 border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                        <button type="submit" class="bg-gray-800 text-white px-3 py-1 rounded text-xs hover:bg-black transition">Tambah</button>
                                    </form>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <form action="{{ route('admin.risk_master.destroy_item', $item->id) }}" method="POST" onsubmit="return confirm('Hapus pertanyaan ini? Seluruh penyebab & mitigasi di dalamnya akan ikut terhapus.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 text-xs font-bold uppercase tracking-tighter">Hapus Item</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-6 py-10 text-center text-sm text-gray-500 italic">
                                Belum ada data pertanyaan. Silakan tambah lewat form di atas.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
    <div id="modalEditCause" class="fixed inset-0 z-50 hidden bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full">
        <div class="relative top-32 mx-auto p-5 border w-full max-w-lg shadow-lg rounded-md bg-white border-t-4 border-t-yellow-500">
            <div class="flex justify-between items-center mb-4 border-b pb-2">
                <h3 class="text-lg font-bold text-gray-900 uppercase">Edit Penyebab & Mitigasi</h3>
                <button onclick="document.getElementById('modalEditCause').classList.add('hidden')" class="text-gray-400 hover:text-red-500 font-bold text-xl">&times;</button>
            </div>
            
            <form id="editCauseForm" method="POST" class="space-y-4">
                @csrf
                @method('PATCH')
                
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Teks Penyebab <span class="text-red-500">*</span></label>
                    <input type="text" name="penyebab" id="edit_penyebab_input" required class="block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-yellow-500 focus:border-yellow-500">
                </div>
                
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Teks Mitigasi <span class="text-gray-400 font-normal">(Kosongkan jika ingin dihapus)</span></label>
                    <input type="text" name="mitigasi" id="edit_mitigasi_input" class="block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-yellow-500 focus:border-yellow-500">
                </div>
                
                <div class="mt-6 flex justify-end gap-2 pt-4 border-t border-gray-100">
                    <button type="button" onclick="document.getElementById('modalEditCause').classList.add('hidden')" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded text-sm">Batal</button>
                    <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded text-sm shadow">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEditCauseModal(button) {
            // Tarik data dari atribut tombol
            const id = button.getAttribute('data-id');
            const penyebab = button.getAttribute('data-penyebab');
            const mitigasi = button.getAttribute('data-mitigasi');

            // Isi nilai ke form modal
            document.getElementById('edit_penyebab_input').value = penyebab;
            document.getElementById('edit_mitigasi_input').value = mitigasi;

            // Update URL Action Form
            document.getElementById('editCauseForm').action = `/admin/risk-master/cause/${id}`;

            // Tampilkan Modal
            document.getElementById('modalEditCause').classList.remove('hidden');
        }
    </script>
</x-app-layout>