{{-- <!-- Modal Konten Detail Role -->
<div class="bg-white rounded-lg shadow-lg max-w-md w-full p-6 relative border-t-4 border-blue-600">

    <button id="modal-close" class="absolute top-3 right-3 text-gray-500 hover:text-gray-700 text-xl cursor-pointer">
        <i class="fas fa-times"></i>
    </button>

    <h2 class="text-xl font-semibold text-center">Detail Data Role</h2>
    <div class="w-[185px] h-1 bg-yellow-400 mx-auto mt-1 mb-6 rounded"></div>

    <!-- Detail Informasi -->

    <div class="ms-8 my-16">
        <p class="font-semibold text-gray-500">Kode Role</p>
        <p>{{ $role->kode_role ?? '-' }}</p>
    </div>
    <div class="ms-8 mt-12 mb-20">
        <p class="font-semibold text-gray-500">Nama Role</p>
        <p>{{ $role->nama_role ?? '-' }}</p>
    </div>
</div> --}}

<!-- Modal Bobot Pelapor -->
<div class="bg-white rounded-lg shadow-lg max-w-md w-full p-6 relative border-t-4 border-blue-600">

    {{-- <div id="modal-bobot" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50">
        --}}
        <button id="modal-close"
            class="absolute top-3 right-3 text-gray-500 hover:text-gray-700 text-xl cursor-pointer">
            <i class="fas fa-times"></i>
        </button>
        <div class="bg-white rounded-lg w-96 p-6 relative">
            {{-- <button id="modal-close" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button> --}}
            <h2 class="text-lg font-semibold mb-4">Perhitungan Bobot Pelapor</h2>
            <p class="text-sm">
                Bobot Pelapor dihitung berdasarkan jumlah pelapor dari berbagai role:
            </p>
            <ul class="list-disc pl-6 mt-2 text-sm">
                <li>Mahasiswa x <strong>1</strong></li>
                <li>Dosen x <strong>3</strong></li>
                <li>Tendik x <strong>2</strong></li>
            </ul>
            <p class="text-sm mt-3">Contoh: Jika 1 Mahasiswa, 1 Dosen, dan 1 Tendik melapor → Skor Bobot = <strong>1x1 +
                    1x3 + 1x2 = 6</strong></p>
        </div>
    </div>
</div>


<script>
    $(document).on('click', '#modal-close', function () {
        $('#myModal').addClass('hidden').removeClass('flex').html('');
    });
</script>