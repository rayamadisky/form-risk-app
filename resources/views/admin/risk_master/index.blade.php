<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Control Center: Manajemen Master Data Risiko') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ 
        filterRole: 'semua',
        filterKategori: 'semua',  // <--- TAMBAHIN BARIS INI
        activeModal: null,
        editCauseModal: false,
        editData: { id: '', penyebab: '', mitigasi: '' },
        openEdit(id, penyebab, mitigasi) {
            this.editData = { id, penyebab, mitigasi };
            this.editCauseModal = true;
        }
    }">
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
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 transition">
                            Simpan Pertanyaan
                        </button>
                    </div>
                </form>
            </div>

            <div class="bg-white shadow sm:rounded-lg overflow-hidden border border-gray-200">
                
                <div class="bg-gray-50 border-b border-gray-200 p-4 flex justify-between items-center">
                    <h3 class="text-md font-bold text-gray-700 uppercase hidden md:block">Daftar Pertanyaan Risiko</h3>
                    
                    <div class="flex items-center gap-4 w-full md:w-auto">
                        
                        <div class="flex items-center gap-2">
                            <label class="text-[10px] font-bold text-gray-500 uppercase">Kategori:</label>
                            <select x-model="filterKategori" class="text-xs rounded border-gray-300 focus:ring-blue-500 py-1 px-2 font-semibold">
                                <option value="semua">Semua</option>
                                <option value="finansial">Finansial</option>
                                <option value="non-finansial">Non-Finansial</option>
                            </select>
                        </div>

                        <div class="flex items-center gap-2">
                            <label class="text-[10px] font-bold text-gray-500 uppercase">Jabatan:</label>
                            <select x-model="filterRole" class="text-xs rounded border-gray-300 focus:ring-blue-500 py-1 px-2 font-semibold">
                                <option value="semua">Semua</option>
                                @foreach($riskItems->pluck('role_target')->unique() as $role)
                                    <option value="{{ strtolower($role) }}">{{ strtoupper($role) }}</option>
                                @endforeach
                            </select>
                        </div>

                    </div>
                </div>

                <div class="divide-y divide-gray-100">
                    @forelse($riskItems as $item)
                        <div x-show="(filterRole === 'semua' || filterRole === '{{ strtolower($item->role_target) }}') && (filterKategori === 'semua' || filterKategori === '{{ strtolower($item->kategori) }}')" 
                             x-transition
                             class="p-4 hover:bg-blue-50 cursor-pointer transition flex flex-col md:flex-row justify-between md:items-center gap-4 group"
                             @click="activeModal = {{ $item->id }}">
                            
                            <div>
                                <span class="px-2 py-0.5 inline-flex text-[10px] leading-5 font-bold rounded-full bg-blue-100 text-blue-800 uppercase mb-1">
                                    {{ $item->role_target }}
                                </span>
                                <h4 class="text-sm font-bold text-gray-900 group-hover:text-blue-700 transition">{{ $item->nama_risiko }}</h4>
                                <p class="text-xs text-gray-500 mt-1">{{ $item->causes->count() }} Penyebab terdaftar</p>
                            </div>

                            <div class="flex items-center gap-4">
                                <span class="text-xs font-bold text-blue-500 opacity-0 group-hover:opacity-100 transition">Lihat Detail &rarr;</span>
                                <form action="{{ route('admin.risk_master.destroy_item', $item->id) }}" method="POST" class="relative z-10" @click.stop="if(!confirm('Hapus pertanyaan ini?')) $event.preventDefault()">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-white hover:bg-red-500 border border-red-500 px-3 py-1 rounded text-xs font-bold uppercase transition">Hapus</button>
                                </form>
                            </div>
                        </div>

                        <div x-show="activeModal === {{ $item->id }}" style="display: none;" class="fixed inset-0 z-40 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                
                                <div x-show="activeModal === {{ $item->id }}" x-transition.opacity class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="activeModal = null" aria-hidden="true"></div>
                                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                                <div x-show="activeModal === {{ $item->id }}" x-transition class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl w-full">
                                    
                                    <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 flex justify-between items-center sticky top-0 z-10">
                                        <div>
                                            <span class="text-[10px] font-bold text-gray-500 uppercase">{{ $item->role_target }}</span>
                                            <h3 class="text-lg leading-6 font-bold text-gray-900" id="modal-title">{{ $item->nama_risiko }}</h3>
                                        </div>
                                        <button @click="activeModal = null" class="text-gray-400 hover:text-red-500 font-bold text-2xl focus:outline-none">&times;</button>
                                    </div>

                                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                        <h4 class="text-xs font-bold text-gray-500 uppercase mb-4 border-b pb-2">Daftar Penyebab & Mitigasi (Sebab-Akibat)</h4>
                                        
                                        <div class="space-y-4 max-h-[50vh] overflow-y-auto pr-2">
                                            @forelse($item->causes as $cause)
                                                @php $mitigasiTeks = $cause->mitigations->first()->mitigasi ?? ''; @endphp
                                                
                                                <div class="flex flex-col md:flex-row gap-0 md:gap-4 border border-gray-200 rounded-lg overflow-hidden group hover:border-blue-300 transition relative">
                                                    
                                                    <button type="button" @click="openEdit({{ $cause->id }}, '{{ addslashes($cause->penyebab) }}', '{{ addslashes($mitigasiTeks) }}')" class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 bg-yellow-100 text-yellow-700 hover:bg-yellow-200 px-2 py-1 rounded text-[10px] font-bold uppercase transition z-10 border border-yellow-300">
                                                        Edit
                                                    </button>

                                                    <div class="w-full md:w-1/2 bg-gray-50 p-3 border-b md:border-b-0 md:border-r border-gray-200">
                                                        <p class="text-[10px] font-bold text-red-500 uppercase mb-1">Akar Penyebab</p>
                                                        <p class="text-sm font-semibold text-gray-800 italic pr-8">"{{ $cause->penyebab }}"</p>
                                                    </div>
                                                    
                                                    <div class="w-full md:w-1/2 p-3 bg-white">
                                                        <p class="text-[10px] font-bold text-green-600 uppercase mb-1">Rekomendasi Mitigasi</p>
                                                        @if($mitigasiTeks)
                                                            <p class="text-sm text-gray-600 pr-8">{{ $mitigasiTeks }}</p>
                                                        @else
                                                            <p class="text-sm text-gray-400 italic pr-8">- Belum ada mitigasi -</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            @empty
                                                <div class="text-center py-6 bg-gray-50 rounded border border-dashed border-gray-300">
                                                    <p class="text-sm text-gray-500 italic">Belum ada penyebab yang ditambahkan untuk risiko ini.</p>
                                                </div>
                                            @endforelse
                                        </div>

                                        <div class="mt-6 bg-blue-50 border border-blue-100 rounded-lg p-4">
                                            <h4 class="text-xs font-bold text-blue-800 uppercase mb-2">Tambah Akar Penyebab Baru</h4>
                                            <form action="{{ route('admin.risk_master.store_cause', $item->id) }}" method="POST" class="flex flex-col md:flex-row gap-2">
                                                @csrf
                                                <input type="text" name="penyebab" required placeholder="Teks Penyebab..." class="text-sm flex-1 border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                                                <input type="text" name="mitigasi" placeholder="Teks Mitigasi (Opsional)..." class="text-sm flex-1 border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                                                <button type="submit" class="bg-blue-600 hover:bg-blue-800 text-white font-bold px-4 py-2 rounded text-xs transition shadow-sm uppercase">Simpan</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-10 text-center text-sm text-gray-500 italic bg-gray-50">
                            Belum ada data pertanyaan. Silakan tambah lewat form di atas.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

        <div x-show="editCauseModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="editCauseModal" x-transition.opacity class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" @click="editCauseModal = false" aria-hidden="true"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="editCauseModal" x-transition class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border-t-4 border-yellow-500">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="flex justify-between items-center mb-4 border-b pb-2">
                            <h3 class="text-lg font-bold text-gray-900 uppercase">Edit Penyebab & Mitigasi</h3>
                            <button @click="editCauseModal = false" class="text-gray-400 hover:text-red-500 font-bold text-xl">&times;</button>
                        </div>
                        
                        <form :action="`/admin/risk-master/cause/${editData.id}`" method="POST" class="space-y-4">
                            @csrf
                            @method('PATCH')
                            
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Teks Penyebab <span class="text-red-500">*</span></label>
                                <input type="text" name="penyebab" x-model="editData.penyebab" required class="block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-yellow-500 focus:border-yellow-500">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Teks Mitigasi <span class="text-gray-400 font-normal">(Kosongkan jika ingin dihapus)</span></label>
                                <input type="text" name="mitigasi" x-model="editData.mitigasi" class="block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-yellow-500 focus:border-yellow-500">
                            </div>
                            
                            <div class="mt-6 flex justify-end gap-2 pt-4 border-t border-gray-100">
                                <button type="button" @click="editCauseModal = false" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded text-sm transition">Batal</button>
                                <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded text-sm shadow transition">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>