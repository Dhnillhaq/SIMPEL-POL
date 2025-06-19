<div id="confirmationContainer" class="bg-white rounded-lg shadow-lg max-w-3xl w-full p-6 relative space-y-6 border-t-4 border-blue-600 max-h-[90vh] overflow-y-auto">
    <button id="modal-close" class="absolute top-3 right-3 text-gray-500 hover:text-gray-700 cursor-pointer">
        <i class="fas fa-times"></i>
    </button>

    <h2 class="text-xl font-semibold mb-4 text-center">Konfirmasi Pekerjaan</h2>
    <div class="w-12 h-1 bg-yellow-400 mx-auto mt-1 mb-6 rounded"></div>

     <div>
        <h2 class="font-semibold flex items-center space-x-2 mb-4 border-b pb-2">
            <i class="fa-solid fa-file" style="color: #0342b0;"></i>
            <span>Detail Fasilitas</span>
        </h2>

        <div class="flex flex-col lg:flex-row gap-6">
            <div class="lg:w-48">
                <img src="{{ asset($fasilitas->foto_fasilitas ?? 'img/no-image.svg') }}"
                    alt="Gambar Fasilitas"
                    class="w-full h-32 object-cover rounded-lg border">
                <div class="mt-2">
                    <p class="font-semibold text-black-700">{{ $fasilitas->nama_fasilitas ?? '-' }}</p>
                    <p class="text-gray-700">{{ ucwords($fasilitas->kategori->nama_kategori ?? '-') }}</p>
                </div>
            </div>
            <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-gray-500">Lokasi</p>
                    <p class="mt-2 font-semibold">
                        {{ $fasilitas->ruangan->lantai->gedung->nama_gedung ?? '-' }},
                        {{ $fasilitas->ruangan->lantai->nama_lantai ?? '-' }},
                        {{ $fasilitas->ruangan->nama_ruangan ?? '-' }}
                    </p>
                </div>
                <div>
                    <p class="text-gray-500">Urgensi</p>
                    <span class="inline-block mt-2 px-8 py-1 text-xs font-semibold text-white bg-red-600 rounded-lg">
                        {{ Str::ucfirst(Str::lower($fasilitas->urgensi->value ?? '-')) }}
                    </span>
                </div>
                <div>
                    <p class="text-gray-500">Status Aduan</p>
                    <span class="inline-block mt-2 px-8 py-1 text-xs font-semibold text-white bg-blue-600 rounded-lg">
                        {{ $statusAduan }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="border border-dashed rounded-md p-4">
        <h2 class="font-semibold flex items-center space-x-2 mb-4">
            <i class="fa-solid fa-address-card" style="color: #0342b0;"></i>
            <span>Hasil Inspeksi</span>
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm text-gray-700">
            <div>
                <p class="text-gray-500">Tingkat Kerusakan</p>
                <span class="inline-block mt-2 px-8 py-1 text-xs font-semibold text-white bg-yellow-600 rounded-lg">
                    {{ Str::ucfirst(Str::lower($inspeksi->tingkat_kerusakan->value ?? '-')) }}
                </span>
            </div>
            <div>
                <p class="text-gray-500">Deskripsi Pekerjaan</p>
                <p class="mt-2 font-semibold">{{ $inspeksi->deskripsi ?? '-' }}</p>
            </div>
        </div>
    </div>

   <form action="{{ route('teknisi.perbaikan.submit', $perbaikan->id_perbaikan) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- Status Pekerjaan -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Status Pekerjaan</label>
            <select name="work_status" id="workStatus" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" required>
                <option value="SEDANG_DIPERBAIKI" selected>Sedang Diperbaiki</option>
                <option value="SELESAI">Selesai</option>
            </select>
        </div>

        <!-- Catatan Pekerjaan -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Pekerjaan</label>
            <textarea name="work_notes" id="workNotes" rows="3" 
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                placeholder="Masukkan detail pekerjaan yang telah dilakukan..."></textarea>
        </div>

        <!-- Upload Gambar -->
        <div class="mb-6">
            <label for="work_images" class="block text-sm font-medium mb-1">
                Upload Gambar
            </label>

            <div class="flex items-center border border-gray-300 rounded-md bg-white overflow-hidden">
                <input type="text" id="file-name-display" placeholder="Pilih File"
                    class="flex-grow px-3 py-2 text-sm text-gray-500 bg-gray-50 border-none focus:ring-0 focus:outline-none"
                    readonly>
                <label for="work_images"
                    class="font-semibold px-4 py-2 text-sm text-black bg-gray-300 hover:bg-gray-400 cursor-pointer">
                    Browse</label>
                <input type="file" id="work_images" name="work_images[]" accept=".jpg,.jpeg,.png" multiple class="hidden"
                    onchange="const input = document.getElementById('file-name-display'); 
                            input.value = Array.from(this.files).map(file => file.name).join(', '); 
                            input.classList.remove('text-gray-500'); 
                            input.classList.add('text-black');">
            </div>
            <p class="mt-1 text-xs text-gray-500">
                Format yang didukung: JPG, PNG, JPEG. Ukuran maksimal: 2MB
            </p>
            <span id="work_images-error" class="text-xs text-red-500 mt-1 error-text"></span>
        </div>

        <!-- Checklist Konfirmasi -->
        <div class="mb-6">
            <label class="flex items-center space-x-2">
                <input type="checkbox" name="confirmation" id="confirmationCheckbox" required 
                    class="w-5 h-5 text-green-500 border-gray-300 rounded focus:ring-green-500">
                <span class="text-sm text-gray-700">Saya menyatakan bahwa saya melakukan pekerjaan ini dengan sadar dan bertanggung jawab.</span>
            </label>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="w-full bg-green-600 text-white py-3 rounded-lg hover:bg-green-700 transition-colors cursor-pointer flex items-center justify-center">
            <i class="fas fa-paper-plane mr-2"></i>
            Submit Konfirmasi
        </button>
    </form>
</div>



