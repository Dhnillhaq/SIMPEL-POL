<x-table>
    <x-slot name="head">
        @if (!$isFilterPerRole)
            <div class="text-sm text-gray-600 mb-2 font-medium">
                Menampilkan 10 fasilitas dengan skor bobot tertinggi berdasarkan jumlah pelapor dari berbagai peran
                (Mahasiswa x1, Dosen x3, Tendik x2).
            </div>
        @else
            <div class="text-sm text-gray-600 mb-2 font-medium">
                Menampilkan jumlah pelapor <strong>{{ $pelapor }}</strong> pada 10 fasilitas dengan skor bobot tertinggi.
            </div>
        @endif
        <x-table.heading>No</x-table.heading>
        <x-table.heading>Kode Fasilitas</x-table.heading>
        <x-table.heading>Nama Fasilitas</x-table.heading>
        <x-table.heading>Kategori</x-table.heading>
        <x-table.heading>Lokasi</x-table.heading>
        <x-table.heading>Urgensi</x-table.heading>
        <x-table.heading>
            <div class="flex flex-row min-w-max">
                @if (!$isFilterPerRole)
                    <button id="info-bobot" onclick="modalAction('{{ route('sarpras.pengaduan.info')}}')"
                        class="ml-1 mr-1 text-blue-500 hover:text-blue-700" title="Klik untuk info bobot">
                        <i class="fas fa-question-circle"></i>
                    </button>
                    Skor Bobot
                @else
                    <span class="ml-1 mr-1 text-gray-400" title="Menampilkan jumlah pelapor unik dari role ini">
                        <i class="fas fa-info-circle"></i>
                    </span>
                    Jumlah Pelapor
                @endif
            </div>
        </x-table.heading>
        <x-table.heading>Aksi</x-table.heading>
    </x-slot>

    <x-slot name="body">
        @forelse ($pengaduan as $index => $p)
            <x-table.row>
                <x-table.cell>{{ $index + 1 }}</x-table.cell>
                <x-table.cell>{{ strtoupper($p->kode_fasilitas) }}</x-table.cell>
                <x-table.cell>{{ $p->nama_fasilitas }}</x-table.cell>
                <x-table.cell>{{ ucfirst($p->kategori->nama_kategori ?? '-')}}</x-table.cell>
                @php
                    $ruangan = $p->ruangan;
                    $lantai = $ruangan->lantai;
                    $gedung = $lantai->gedung;
                @endphp
                <x-table.cell>
                    {{ $gedung->nama_gedung ?? '-' }}
                    {{ $lantai ? ', ' . $lantai->nama_lantai : '' }}
                    {{ $ruangan ? ', ' . $ruangan->kode_ruangan : '' }}
                </x-table.cell>
                <x-table.cell>
                    <div class="flex flex-col items-center font-semibold">
                        @if($p->urgensi)
                            <span class="py-1 rounded-full text-white text-sm
                                                                                            @if($p->urgensi === \App\Http\Enums\Urgensi::DARURAT)
                                                                                                px-3 bg-red-500
                                                                                            @elseif($p->urgensi === \App\Http\Enums\Urgensi::PENTING)
                                                                                                px-3 bg-yellow-500
                                                                                            @elseif($p->urgensi === \App\Http\Enums\Urgensi::BIASA)
                                                                                                px-6 bg-blue-500
                                                                                            @else
                                                                                                px-3 bg-gray-500
                                                                                            @endif
                                                                                        ">
                                {{ $p->urgensi->value ?? '-' }}
                            </span>
                        @else
                            <span class="px-3 py-1 rounded-full bg-gray-500 text-white text-sm">-</span>
                        @endif
                    </div>
                </x-table.cell>
                <x-table.cell>{{ $isFilterPerRole ? ($p->user_count ?? '-') : ($p->skor_bobot ?? '-') }}</x-table.cell>
                <x-table.cell>
                    <div class="flex items-center space-x-2 min-w-[120px]">
                        <button onclick="modalAction('{{ route('sarpras.pengaduan.show', $p->id_fasilitas) }}')"
                            class="text-blue-600 hover:underline text-sm cursor-pointer">
                            <img src="{{ asset('icons/solid/Detail.svg') }}" alt="" class="h-7 w-7 inline">
                        </button>
                        <button onclick="modalAction('{{ route('sarpras.pengaduan.edit', $p->id_fasilitas) }}')"
                            class="text-yellow-600 hover:underline text-sm cursor-pointer">
                            <img src="{{ asset('icons/crud/Case.svg') }}" alt="" class="h-7 w-7 inline">
                        </button>
                    </div>
                </x-table.cell>
            </x-table.row>
        @empty
            <tr class="border-1">
                <td colspan="9" class="text-center text-gray-500 py-4">
                    Tidak ada data Aduan{{ $pelapor }}.
                </td>
            </tr>
        @endforelse
    </x-slot>
</x-table>

<div class="mt-4">
    {{-- {{ $pengaduan->links() }} --}}
</div>