<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Form Input Risiko Operasional (Maker)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <form action="{{ route('form.risiko.store') }}" method="POST" id="riskForm">
                    @csrf

                    <input type="hidden" name="kategori" value="{{ $kategori }}">

                    <h3 class="text-lg font-bold border-b pb-2 mb-4 text-blue-700">1. Identitas Kejadian</h3>
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tanggal Kejadian <span class="text-red-500">*</span></label>
                            <input type="date" name="tanggal_kejadian" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tanggal Diketahui <span class="text-red-500">*</span></label>
                            <input type="date" name="tanggal_diketahui" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                        </div>
                    </div>

                    <h3 class="text-lg font-bold border-b pb-2 mb-4 text-blue-700">2. Detail Risiko</h3>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Pilih Potensi Risiko <span class="text-red-500">*</span></label>
                        <select id="riskItemSelect" name="risk_item_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                            <option value="">-- Pilih Potensi Risiko --</option>
                            @foreach($riskItems as $item)
                            <option value="{{ $item->id }}">{{ $item->nama_risiko }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div id="otherItemContainer" class="mt-3 hidden p-3 bg-red-50 border border-red-200 rounded">
                        <label class="block text-sm font-medium text-red-700">Sebutkan Potensi Risiko Tersebut <span class="text-red-500">*</span></label>
                        <input type="text" name="other_item_description" id="otherItemInput" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" placeholder="Ketik jenis risiko di sini...">
                    </div>
                    <div id="otherCauseContainer" class="mt-3 hidden p-3 bg-yellow-50 border border-yellow-200 rounded">
                        <label class="block text-sm font-medium text-yellow-700">Sebutkan Penyebab Risiko Tersebut <span class="text-red-500">*</span></label>
                        <input type="text" name="other_cause_description" id="otherCauseInput" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" placeholder="Ketik penyebab detail di sini...">
                    </div>
                    <div id="causeContainer" class="mb-4 hidden">
                        <label class="block text-sm font-medium text-gray-700">Apa penyebabnya? <span class="text-red-500">*</span></label>
                        <select id="riskCauseSelect" name="risk_cause_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            <option value="">-- Pilih Penyebab --</option>
                        </select>
                    </div>

                    <div id="otherCauseContainer" class="mb-4 hidden p-3 bg-gray-50 border border-gray-200 rounded">
                        <label class="block text-sm font-medium text-gray-700">Tuliskan Penyebab Lainnya <span class="text-red-500">*</span></label>
                        <input type="text" name="other_cause_description" id="otherCauseInput" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    </div>

                    <div id="mitigationContainer" class="mb-4 hidden">
                        <label class="block text-sm font-medium text-gray-700">Mitigasi Standar (Sistem)</label>
                        <textarea id="mitigationText" readonly class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm" rows="3"></textarea>
                    </div>
                    <div id="mitigationContainer" class="mt-4 mb-2 hidden p-4 bg-blue-50 border-l-4 border-blue-500 rounded-r-md">
                        <p class="text-xs font-bold text-blue-800 uppercase tracking-wide">Mitigasi Standar (SOP)</p>
                        <p id="mitigationText" class="text-sm text-blue-700 mt-1 font-medium"></p>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Apakah ada mitigasi tambahan yang lain?</label>
                        <textarea name="mitigasi_tambahan" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" rows="2" placeholder="Ketik mitigasi tambahan di sini..."></textarea>
                    </div>

                    @if($kategori === 'finansial')
                    <div class="mt-8 border-b pb-2 mb-4">
                        <h3 class="text-lg font-bold text-blue-600">3. Dampak Finansial</h3>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Seberapa besar kerugian (Rp) <span class="text-red-500">*</span></label>
                        <input type="number" name="dampak_finansial" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Contoh: 150000">
                    </div>
                    @else
                    <div class="mt-8 border-b pb-2 mb-4">
                        <h3 class="text-lg font-bold text-orange-600">3. Analisa Dampak Non-Finansial</h3>
                    </div>

                    <div class="mb-6 bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <label class="block text-sm font-bold text-gray-800 mb-2">Seberapa besar dampak kerugian yang timbul? <span class="text-red-500">*</span></label>
                        <div class="space-y-3">
                            <label class="flex items-center p-3 border border-gray-300 rounded hover:bg-white cursor-pointer transition">
                                <input type="radio" name="skala_dampak" value="Sangat Tinggi" required class="h-4 w-4 text-orange-600 focus:ring-orange-500">
                                <span class="ml-3 text-sm font-bold text-gray-700">Sangat Tinggi</span>
                            </label>
                            <label class="flex items-center p-3 border border-gray-300 rounded hover:bg-white cursor-pointer transition">
                                <input type="radio" name="skala_dampak" value="Tinggi" class="h-4 w-4 text-orange-600 focus:ring-orange-500">
                                <span class="ml-3 text-sm font-bold text-gray-700">Tinggi</span>
                            </label>
                            <label class="flex items-center p-3 border border-gray-300 rounded hover:bg-white cursor-pointer transition">
                                <input type="radio" name="skala_dampak" value="Sedang" class="h-4 w-4 text-orange-600 focus:ring-orange-500">
                                <span class="ml-3 text-sm font-bold text-gray-700">Sedang</span>
                            </label>
                            <label class="flex items-center p-3 border border-gray-300 rounded hover:bg-white cursor-pointer transition">
                                <input type="radio" name="skala_dampak" value="Rendah" class="h-4 w-4 text-orange-600 focus:ring-orange-500">
                                <span class="ml-3 text-sm font-bold text-gray-700">Rendah</span>
                            </label>
                            <label class="flex items-center p-3 border border-gray-300 rounded hover:bg-white cursor-pointer transition">
                                <input type="radio" name="skala_dampak" value="Sangat Rendah" class="h-4 w-4 text-orange-600 focus:ring-orange-500">
                                <span class="ml-3 text-sm font-bold text-gray-700">Sangat Rendah</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-800">Penjelasan Dampaknya: <span class="text-red-500">*</span></label>
                        <p class="text-xs text-gray-500 mb-2">Berikan penjelasan mengenai dampak riil dari potensi risiko tersebut.</p>
                        <textarea name="dampak_non_finansial" rows="4" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm" placeholder="Ketik kronologi atau rincian dampak di sini..."></textarea>
                    </div>
                    @endif

                    <div class="mt-8 border-b pb-2 mb-4">
                        <h3 class="text-lg font-bold text-gray-800">4. Penanganan Awal & Status</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-bold text-gray-700">Tindakan Penanganan Awal (Opsional)</label>
                            <p class="text-xs text-gray-500 mb-1">Jika masalah sudah langsung ditangani saat kejadian, ceritakan di sini.</p>
                            <textarea name="tindakan_awal" rows="2" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Contoh: Selisih kas sudah langsung diganti sore itu juga / Nasabah sudah ditelepon untuk TT ulang..."></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700">Status Penyelesaian Saat Ini <span class="text-red-500">*</span></label>
                            <select name="status_awal" required class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="open">Open (Belum ada tindakan sama sekali)</option>
                                <option value="in_progress">In Progress (Sedang dalam proses penyelesaian)</option>
                                <option value="monitoring">Monitoring (Selesai, tapi butuh dipantau)</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-black font-bold py-2 px-6 rounded">
                            Submit Laporan
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <script>
        // 1. Ambil data master dari backend, ubah jadi format JSON biar bisa dibaca Javascript
        const riskData = @json($riskItems);

        const itemSelect = document.getElementById('riskItemSelect');
        const causeSelect = document.getElementById('riskCauseSelect');
        const causeContainer = document.getElementById('causeContainer');
        const otherContainer = document.getElementById('otherCauseContainer');
        const otherInput = document.getElementById('otherCauseInput');
        const mitigationContainer = document.getElementById('mitigationContainer');
        const mitigationText = document.getElementById('mitigationText');

        // Logika saat Potensi Risiko dipilih
        itemSelect.addEventListener('change', function() {
            const selectedItemId = this.value;
            const selectedText = this.options[this.selectedIndex].text.toLowerCase();

            // Ambil elemen
            const otherItemContainer = document.getElementById('otherItemContainer');
            const otherItemInput = document.getElementById('otherItemInput');
            const otherCauseContainer = document.getElementById('otherCauseContainer');
            const otherCauseInput = document.getElementById('otherCauseInput');

            // Reset dulu
            causeSelect.innerHTML = '<option value="">-- Pilih Penyebab --</option>';
            mitigationContainer.classList.add('hidden');
            otherItemContainer.classList.add('hidden');
            otherCauseContainer.classList.add('hidden');

            // Logika "Lainnya"
            if (selectedText.includes('lainnya') || selectedText.includes('other')) {
                // 1. Munculin Teks Nama Risiko
                otherItemContainer.classList.remove('hidden');
                otherItemInput.setAttribute('required', 'required');

                // 2. OTOMATIS Munculin Teks Penyebab (karena dropdown penyebab pasti kosong)
                otherCauseContainer.classList.remove('hidden');
                otherCauseInput.setAttribute('required', 'required');

                causeContainer.classList.add('hidden'); // Sembunyiin dropdown penyebab
            } else {
                otherItemInput.removeAttribute('required');
                otherItemInput.value = '';
                otherCauseInput.removeAttribute('required');
                otherCauseInput.value = '';

                // ... (Lanjutin kode narik data cause dari riskData kayak biasa di sini) ...
                if (selectedItemId) {
                    const selectedItem = riskData.find(item => item.id == selectedItemId);
                    if (selectedItem && selectedItem.causes && selectedItem.causes.length > 0) {
                        causeContainer.classList.remove('hidden');
                        selectedItem.causes.forEach(cause => {
                            let option = new Option(cause.penyebab, cause.id);
                            // Kalau ada mitigasi, selipin di dataset
                            if (cause.mitigations && cause.mitigations.length > 0) {
                                option.setAttribute('data-mitigasi', cause.mitigations[0].mitigasi);
                            }
                            causeSelect.add(option);
                        });
                        // Tambahin opsi Other di paling bawah dropdown
                        causeSelect.add(new Option('Lainnya / Other', 'other'));
                    } else {
                        causeContainer.classList.add('hidden');
                    }
                }
            }
        });

        // Trigger saat milih dropdown penyebab
        causeSelect.addEventListener('change', function() {
            const otherCauseContainer = document.getElementById('otherCauseContainer');
            const otherCauseInput = document.getElementById('otherCauseInput');

            const mitigationContainer = document.getElementById('mitigationContainer');
            const mitigationText = document.getElementById('mitigationText');

            // Logika Penyebab "Lainnya" (Skenario kemarin)
            if (this.value === 'other') {
                otherCauseContainer.classList.remove('hidden');
                otherCauseInput.setAttribute('required', 'required');
            } else {
                otherCauseContainer.classList.add('hidden');
                otherCauseInput.removeAttribute('required');
                otherCauseInput.value = '';
            }

            // Logika Munculin Mitigasi Otomatis
            const selectedOption = this.options[this.selectedIndex];
            const mitigasi = selectedOption.getAttribute('data-mitigasi');

            if (mitigasi && this.value !== 'other' && this.value !== '') {
                mitigationContainer.classList.remove('hidden');
                mitigationText.textContent = mitigasi;
            } else {
                // Sembunyiin kalau kosong atau pilih "Lainnya"
                mitigationContainer.classList.add('hidden');
                mitigationText.textContent = '';
            }
        });

        // Logika Format Uang (Masking Ribuan)
        const nominalInput = document.getElementById('nominalInput');
        const nominalReal = document.getElementById('nominalReal');

        nominalInput.addEventListener('keyup', function(e) {
            // Hapus semua huruf/simbol, cuma sisain angka
            let val = this.value.replace(/[^0-9]/g, '');
            // Masukin nilai aslinya (tanpa koma) ke input hidden buat dikirim ke database
            nominalReal.value = val;

            // Format tampilannya pakai koma (150,000)
            if (val != '') {
                val = parseInt(val, 10).toLocaleString('en-US');
            }
            this.value = val;
        });
    </script>
</x-app-layout>