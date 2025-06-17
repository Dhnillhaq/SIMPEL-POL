@extends('layouts.template')

@section('content')
    <div class="bg-white rounded-lg shadow p-6 border-t-4 border-blue-600">
        <div class="flex items-center justify-between mb-4">
            <div class="flex flex-col font-semibold py-1">
                <span class="text-md text-gray-700">Daftar Hasil Inspeksi dari Semua Periode Sebelumnya untuk
                Menugaskan Perbaikan
                </span>
                <span class="text-md text-gray-700">Periode Sekarang : {{ $periode_sekarang }}
                </span>
            </div>
        </div>
        <hr class="border-black opacity-30 mt-4">

        <!-- Filter Form -->
        <form id="filter-form" class="flex flex-wrap gap-4 mb-4 mt-8">
            <!-- Filter Periode -->
            <div class="flex items-center gap-2">
                <label for="id_periode" class="text-sm font-medium text-gray-700">Filter Periode:</label>
                <select id="id_periode" name="id_periode"
                    class="w-48 border border-gray-300 rounded-md shadow-sm sm:text-sm">
                    <option value="">Semua Periode</option>
                    @foreach ($periode as $p)
                        <option value="{{ $p->id_periode }}"
                            {{ request('id_periode') == $p->id_periode ? 'selected' : '' }}>{{ $p->kode_periode }}</option>
                    @endforeach
                </select>
            </div>
            <button type="button" onclick="reloadData()"
                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 cursor-pointer">Filter</button>
        </form>

        <div class="flex justify-between items-center mb-4">
            <!-- Pagination -->
            <div class="flex items-center gap-2">
                <label for="per_page" class="text-sm font-medium text-gray-700">Show:</label>
                <select id="per_page" name="per_page" class="border border-gray-300 rounded-md shadow-sm sm:text-sm">
                    @foreach ([10, 25, 50, 100] as $length)
                        <option value="{{ $length }}" {{ request('per_page', 10) == $length ? 'selected' : '' }}>
                            {{ $length }}
                        </option>
                    @endforeach
                </select>
                <span class="text-sm text-gray-700">entries</span>
            </div>
            <!-- Pencarian -->
            <div class="flex items-center gap-2">
                <label for="search" class="text-sm font-medium text-gray-700">Pencarian:</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}"
                    placeholder="Cari fasilitas..." class="w-64 border border-gray-300 rounded-md shadow-sm sm:text-sm" />
            </div>
        </div>

        <div id="inspeksi-table-body">
            @include('sarpras.penugasan.penugasan_table', ['penugasan' => $penugasan])
        </div>
    </div>

    <div id="myModal" class="fixed inset-0 z-50 hidden items-center justify-center backdrop-blur-sm bg-white/30"></div>
@endsection

@push('js')
    <script>
        function reloadData() {
            $.ajax({
                url: "{{ route('sarpras.penugasan') }}",
                method: "GET",
                data: {
                    search: $('#search').val(),
                    per_page: $('#per_page').val(),
                    id_periode: $('#id_periode').val(), // Tambahkan id_periode
                    id_kategori: $('#id_kategori').val(),
                    id_gedung: $('#id_gedung').val(),
                    kondisi: $('#kondisi').val(),
                    sort_column: $('#sort-column').val(),
                    sort_direction: $('#sort-direction').val()
                },
                success: function(response) {
                    $('#inspeksi-table-body').html(response.html);
                },
                error: function() {
                    Swal.fire('Error!', 'Gagal memuat data.', 'error');
                }
            });
        }

        $(document).ready(function() {
            // Event untuk jumlah data per halaman
            $('#per_page').on('change', function() {
                reloadData();
            });

            // Event untuk filter periode
            $('#id_periode').on('change', function() {
                reloadData();
            });

            // Event untuk pencarian (dengan debounce)
            let debounceTimer;
            $('#search').on('input', function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(function() {
                    reloadData();
                }, 300);
            });

            // Event untuk sorting jika ada
            $('#sort-column, #sort-direction').on('change', function() {
                reloadData();
            });
        });
    </script>
@endpush
