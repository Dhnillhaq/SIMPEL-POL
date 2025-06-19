<?php

namespace App\Http\Controllers;

use App\Http\Sheet\Sheet;
use App\Models\Aduan;
use App\Models\Fasilitas;
use App\Models\Perbaikan;
use App\Models\Periode;
use App\Models\UmpanBalik;
use Illuminate\Http\Request;

class AduanController extends Controller
{
    public function index(Request $request)
    {
        $breadcrumb = (object) [
            'title' => 'Laporan Perbaikan',
            'list' => ['Home', 'Riwayat Perbaikan']
        ];

        $page = (object) [
            'title' => 'Daftar aduan yang terdaftar dalam sistem'
        ];

        $activeMenu = 'aduan';

        // Query untuk aduan dengan status selesai
        $query = Perbaikan::with(['periode', 'inspeksi', 'inspeksi.fasilitas'])
            ->whereNotNull('tanggal_selesai');

        // Filter berdasarkan pencarian
        if ($request->search) {
            $query->whereHas('inspeksi', function ($q) use ($request) {
                $q->whereHas('fasilitas', function ($subQ) use ($request) {
                    $subQ->where('nama_fasilitas', 'like', "%{$request->search}%");
                });
            });
        }

        // filter periode
        if ($request->id_periode) {
            $query->where('id_periode', $request->id_periode);
        }

        // Sorting
        $sortColumn = $request->sort_column ?? 'tanggal_selesai';
        $sortDirection = $request->sort_direction ?? 'asc';
        $query->orderBy($sortColumn, $sortDirection);

        // Pagination
        $perPage = $request->input('per_page', 10);
        $perbaikan = $query->paginate($perPage);
        $perbaikan->appends(request()->query());

        // ambil data periode untuk filter
        $periode = Periode::all();

        if ($request->ajax()) {
            $html = view('admin.aduan.aduan_table', compact('perbaikan'))->render();
            return response()->json(['html' => $html]);
        }


        return view('admin.aduan.index', compact('breadcrumb', 'page', 'activeMenu', 'perbaikan', 'periode'));
    }

    public function show_ajax(Perbaikan $perbaikan)
    {
        // Ambil data aduan berdasarkan id_fasilitas
        // $aduan = Aduan::with(['fasilitas.inspeksi.perbaikan', 'fasilitas.inspeksi.biaya', 'fasilitas.ruangan.lantai.gedung',])
        //     ->where('id_fasilitas', $id_fasilitas)
        //     ->firstOrFail();

        // // Ambil data perbaikan terkait aduan
        // $perbaikan = $aduan->fasilitas->inspeksi->first()->perbaikan;
        // $biaya = $aduan->fasilitas->inspeksi->first()->biaya;

        // $fasilitas = Fasilitas::with('kategori')->findOrFail($perbaikan->inspeksi->fasilitas->id_fasilitas); // Ambil fasilitas beserta kategori
        // $kategori = $fasilitas->kategori;

        // $jumlahAduan = Aduan::where('id_fasilitas', $perbaikan->inspeksi->fasilitas->id_fasilitas)->count();

        // // Ambil rata-rata rating untuk fasilitas & tanggal aduan yang sama
        // $avgRating = null;
        // if ($aduan) {
        //     $avgRating = UmpanBalik::whereHas('aduan', function ($q) use ($aduan) {
        //         $q->where('id_fasilitas', $aduan->id_fasilitas)
        //             ->where('tanggal_aduan', $aduan->tanggal_aduan);
        //     })->avg('rating');
        //     $avgRating = $avgRating ? number_format($avgRating, 1) : null;
        // }

        // return view('sarpras.riwayat.detail', compact('aduan', 'biaya', 'perbaikan', 'fasilitas', 'avgRating', 'jumlahAduan'))->render();

        // $aduan = $perbaikan->aduan_tertangani;

        return view('admin.aduan.detail')->with([
            'aduan' => $perbaikan->aduan_tertangani,
            'perbaikan' => $perbaikan
        ]);
    }

    public function comment_ajax(Perbaikan $perbaikan)
    {
        // // Ambil semua aduan berdasarkan id_fasilitas
        // $aduan = Aduan::with(['pelapor', 'fasilitas', 'umpan_balik'])
        //     ->where('id_fasilitas', $id_fasilitas)
        //     ->get();

        // // Ambil semua umpan balik terkait aduan di fasilitas ini
        // $umpanBalik = UmpanBalik::whereIn('id_aduan', $aduan->pluck('id_aduan'))->get();

        // $aduan = $perbaikan->aduan_tertangani;

        return view('admin.aduan.comment')->with ([
            'aduan' => $perbaikan->aduan_tertangani,
            'perbaikan' => $perbaikan,
        ]);
    }

    private function set_sheet()
    {
        $perbaikan = Perbaikan::with(['periode', 'inspeksi', 'inspeksi.fasilitas'])
            ->whereNotNull('tanggal_selesai')
            ->get();

        $filename = 'riwayat_perbaikan_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
        $sheet = new Sheet(); // Pass the data and filename to the Sheet
        $sheet->title = 'Riwayat Perbaikan';
        $sheet->text = 'Berikut adalah daftar perbaikan fasilitas.';
        $sheet->footer = 'Dibuat oleh Sistem';
        $sheet->header = ['Periode', 'Nama Fasilitas', 'Lokasi', 'Kategori', 'Tanggal Mulai Perbaikan', 'Tanggal Selesai Perbaikan'];

        $sheet->data = $perbaikan->map(function ($item) {
            return [
                'periode' => $item->periode->kode_periode,
                'nama_fasilitas' => $item->inspeksi->fasilitas->nama_fasilitas,
                'lokasi' => $item->inspeksi->fasilitas->lokasi,
                'kategori' => $item->inspeksi->fasilitas->kategori->nama_kategori,
                'tanggal_mulai_perbaikan' => $item->tanggal_mulai,
                'tanggal_selesai_perbaikan' => $item->tanggal_selesai,
            ];
        })->toArray();
        $sheet->filename = $filename;

        return $sheet;
    }
    public function export_excel()
    {

        return $this->set_sheet()->toXls();
    }

    public function export_pdf()
    {
        return $this->set_sheet()->toPdf();
    }
}
