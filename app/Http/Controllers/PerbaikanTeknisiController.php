<?php

namespace App\Http\Controllers;

use App\Http\Enums\Status;
use App\Models\Aduan;
use App\Models\Fasilitas;
use App\Models\Notifikasi;
use App\Models\Perbaikan;
use App\Models\Periode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PerbaikanTeknisiController extends Controller
{
    public function index(Request $request)
    {
        $breadcrumb = (object) [
            'title' => 'Perbaikan',
            'list' => ['Home', 'Perbaikan']
        ];

        $page = (object) [
            'title' => 'Perbaikan'
        ];

        $activeMenu = 'perbaikan';

        // Query untuk mengambil data melalui tabel Aduan
        // $query = Fasilitas::query();
        $query = Perbaikan::with(['inspeksi', 'inspeksi.fasilitas', 'inspeksi.periode', 'inspeksi.fasilitas.aduan']);


        // Filter periode
        // if ($request->id_periode) {
        //     $query->whereHas('inspeksi', function ($q) use ($request) {
        //         $q->whereHas('perbaikan', function ($q) use ($request) {
        //             $q->where('id_periode', $request->id_periode);
        //         });
        //     });
        // }else{
        //     // throw new \Exception('Periode tidak ditemukan');
        // }

        // filter periode
        if ($request->filled('id_periode')) {
            $query->whereHas('inspeksi', function ($q) use ($request) {
                $q->where('id_periode', $request->id_periode);
            });
        }
        // Filter berdasarkan pencarian
        if ($request->search) {
            $query->whereHas('inspeksi', function ($q) use ($request) {
                $q->whereHas('fasilitas', function ($q) use ($request) {
                    $q->where('nama_fasilitas', 'like', "%{$request->search}%");
                });
            });
        }

        // Filter status
        // if ($request->filled('status')) {
        //     $query->whereHas('inspeksi', function ($q) use ($request) {
        //         $q->whereHas('fasilitas', function ($q) use ($request) {
        //             $q->whereHas('aduan', function ($q) use ($request) {
        //                 $q->where('status', $request->status);
        //             });
        //         });
        //     });
        // }

        $query = $query->whereHas('inspeksi', function ($q) {
            $q->whereHas('fasilitas', function ($q) {
                $q->whereHas('aduan', function ($q) {
                    $q->where('status', Status::SEDANG_DIPERBAIKI->value);
                });
            });
        })->orderBy('tanggal_mulai', 'desc');


        $perPage = $request->input('per_page', 10);
        $perbaikan = $query->paginate($perPage);

        $periode = Periode::all();
        $status = Status::cases();


        if ($request->ajax()) {
            $html = view('teknisi.perbaikan.perbaikan_table', compact('perbaikan'))->render();
            return response()->json(['html' => $html]);
        }

        return view('teknisi.perbaikan.index', compact('breadcrumb', 'page', 'activeMenu', 'perbaikan', 'periode', 'status'));
    }
    public function show($id)
    {
        try {
            $perbaikan = Perbaikan::with(['inspeksi', 'inspeksi.fasilitas', 'inspeksi.fasilitas.aduan', 'inspeksi.periode', 'inspeksi.biaya'])->findOrFail($id);
            $inspeksi = $perbaikan->inspeksi;
            $fasilitas = $inspeksi->fasilitas;
            // dd($inspeksi);
            $statusAduan = $inspeksi->status_aduan->value ?? '-';
            $biaya = $inspeksi->biaya;
            return view('teknisi.perbaikan.detail', compact('perbaikan', 'inspeksi', 'fasilitas', 'statusAduan', 'biaya'));
        } catch (\Throwable $th) {
            throw $th;
        }
    }
    public function confirm($id)
    {
        try {
            $perbaikan = Perbaikan::with(['inspeksi', 'inspeksi.fasilitas', 'inspeksi.fasilitas.aduan', 'inspeksi.periode'])->findOrFail($id);
            $inspeksi = $perbaikan->inspeksi;
            $fasilitas = $inspeksi->fasilitas;
            // dd($inspeksi);
            $statusAduan = $inspeksi->status_aduan->value ?? '-';

            // Kembalikan view confirm dengan data yang relevan
            return view('teknisi.perbaikan.confirm', compact('perbaikan', 'inspeksi', 'fasilitas', 'statusAduan'));
        } catch (\Throwable $th) {
            // Log error jika terjadi masalah
            Log::error('Gagal menampilkan halaman konfirmasi: ' . $th->getMessage());
            return redirect()->back()->withErrors(['general' => 'Gagal menampilkan halaman konfirmasi.']);
        }
    }
    public function submit(Request $request, $id_perbaikan)
    {
        // dd($request->all());
        try {
            // Validasi input
            $request->validate([
                'work_notes' => 'nullable|string',
                'work_images' => 'nullable|array',
            ]);

            // Temukan perbaikan berdasarkan ID
            $perbaikan = Perbaikan::with(['inspeksi', 'inspeksi.fasilitas'])->findOrFail($id_perbaikan);

            // Simpan work_notes ke detail_perbaikan
            $perbaikan->detail_perbaikan = $request->work_notes;

            // Upload gambar dan simpan URL ke gambar_perbaikan
            $uploadedImages = [];
            if ($request->hasFile('work_images')) {
                foreach ($request->file('work_images') as $image) {
                    $save = $image->storeAs('uploads/img/bukti_foto', uniqid() . '.' . $image->getClientOriginalExtension(), 'public');
                    $path = 'storage/'.$save; // Simpan path gambar
                    $uploadedImages[] = $path;
                }
            }
            // $perbaikan->gambar_perbaikan = implode(',', $uploadedImages); // Simpan URL gambar sebagai string yang dipisahkan koma

            foreach ($uploadedImages as $imagePath) {
                $perbaikan->gambarPerbaikan()->create([
                    'path_gambar' => $imagePath,
                ]);
            }

            $perbaikan->tanggal_selesai = now();

            $aduan = Aduan::where('id_fasilitas', $perbaikan->inspeksi->fasilitas->id_fasilitas)->where('status', Status::SEDANG_DIPERBAIKI->value)->get();
            $fasilitas = Fasilitas::where('id_fasilitas', $perbaikan->inspeksi->fasilitas->id_fasilitas)->value('nama_fasilitas');
            foreach ($aduan as $a) {
                $a->update(['status' => Status::SELESAI->value]);

                // Notifikasi ke pelapor (versi panjang)
                Notifikasi::create([
                    'pesan' => 'Fasilitas <b class="text-red-500">' . $fasilitas . '</b> yang Anda laporkan telah selesai diperbaiki. Terima kasih atas partisipasinya.',
                    'waktu_kirim' => now(),
                    'id_user' => $a->pelapor->id_user,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Simpan perubahan pada perbaikan
            $perbaikan->save();

            return redirect()->back()->with('success', 'Data perbaikan berhasil diperbarui.');
        } catch (\Throwable $th) {
            Log::error('Gagal memperbarui data perbaikan: ' . $th->getMessage());
            // return redirect()->back()->withErrors(['general' => 'Gagal memperbarui data perbaikan.']);
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage()
            ], 500);
        }
    }
    public function cycle($id)
    {
        try {
            // Kode Eril
            $perbaikan = Perbaikan::findOrFail($id);

            // Bukan Kode Eril
            $inspeksi = $perbaikan->inspeksi;
            $fasilitas = Fasilitas::where('id_fasilitas', $inspeksi->id_fasilitas)->value('nama_fasilitas');

            // Kode Eril
            if ($perbaikan->teknisi_selesai) {
                $perbaikan->tanggal_selesai = null;

                // Bukan Kode Eril
                // Notifikasi ke sarpras
                Notifikasi::create([
                    'pesan' => 'Teknisi membatalkan status selesai Perbaikan untuk fasilitas <b class="text-red-500">' . $fasilitas . '</b>.',
                    'waktu_kirim' => now(),
                    'id_user' => $inspeksi->id_user_sarpras,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $pesan = 'Berhasil membatalkan menandai perbaikan sebagai selesai.';

                // Kode Eril
            } else {
                $perbaikan->tanggal_selesai = now();

                // Bukan Kode Eril
                // Notifikasi ke sarpras
                Notifikasi::create([
                    'pesan' => 'Teknisi telah menyelesaikan Perbaikan untuk fasilitas <b class="text-red-500">' . $fasilitas . '</b>. Silakan tinjau hasilnya.',
                    'waktu_kirim' => now(),
                    'id_user' => $inspeksi->id_user_sarpras,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $pesan = 'Berhasil menandai perbaikan sebagai selesai';
            }
            $perbaikan->update();



            return redirect()->back()->with('success', $pesan);
        } catch (\Exception $e) {
            Log::error('Gagal menyelesaikan tugas perbaikan. : ' . $e->getMessage());
            return redirect()->back()->withErrors(['general' => 'Gagal menyelesaikan tugas perbaikan.']);
        }
    }
}
