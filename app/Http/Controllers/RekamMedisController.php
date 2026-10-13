<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RekamMedisController extends Controller
{
    /**
     * S2-02: Menampilkan Riwayat Rekam Medis Pasien UKS
     */
    public function index()
    {
        $records = DB::table('rekam_medis')
            ->join('siswa', 'rekam_medis.id_siswa', '=', 'siswa.id')
            ->join('users', 'siswa.id_user', '=', 'users.id')
            ->select('rekam_medis.*', 'users.name as nama_siswa', 'siswa.kelas')
            ->latest('rekam_medis.created_at')
            ->get();

        return view('rekam_medis.index', compact('records'));
    }

    /**
     * S2-02: Form Pemeriksaan Siswa & Pemilihan Obat
     */
    public function create()
    {
        // Mengambil daftar siswa
        $siswaList = DB::table('siswa')
            ->join('users', 'siswa.id_user', '=', 'users.id')
            ->select('siswa.id', 'users.name', 'siswa.kelas', 'siswa.riwayat_alergi')
            ->get();

        // Mengambil daftar obat yang masih memiliki stok
        $obatList = DB::table('obat')
            ->where('stok_tersedia', '>', 0)
            ->get();

        return view('rekam_medis.create', compact('siswaList', 'obatList'));
    }

    /**
     * S2-03 & S2-06: Simpan Pemeriksaan & Potong Stok Obat dengan Database Transaction
     */
    public function store(Request $request)
    {
        // 1. Validasi Input Sisi Server (S2-05)
        $validated = $request->validate([
            'id_siswa'        => 'required|exists:siswa,id',
            'keluhan'         => 'required|string|min:3',
            'status_penyakit' => 'required|string',
            'tindakan'        => 'required|string',
            'id_obat'         => 'nullable|exists:obat,id',
            'jumlah_dosis'    => 'nullable|integer|min:1',
            'aturan_pakai'    => 'nullable|string',
        ], [
            'id_siswa.required'        => 'Pilih siswa yang diperiksa.',
            'keluhan.required'         => 'Keluhan siswa wajib diisi.',
            'status_penyakit.required' => 'Diagnosa penyakit wajib diisi.',
            'tindakan.required'        => 'Tindakan penanganan wajib diisi.',
        ]);

        // Ambil ID Petugas PMR yang sedang login
        $petugasId = DB::table('petugas_pmr')
            ->where('id_user', Auth::id())
            ->value('id') ?? 1; // Fallback ke ID 1 jika sedang login sebagai Admin

        // 2. Mulai Database Transaction (ACID - S2-06)
        DB::beginTransaction();
        try {
            // A. Simpan data ke tabel rekam_medis
            $recordId = DB::table('rekam_medis')->insertGetId([
                'id_siswa'          => $validated['id_siswa'],
                'id_petugas'        => $petugasId,
                'waktu_pemeriksaan' => now(),
                'keluhan'           => $validated['keluhan'],
                'tindakan'          => $validated['tindakan'],
                'status_penyakit'   => $validated['status_penyakit'],
                'status'            => 'diizinkan_pulang',
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);

            // B. Jika ada obat yang diberikan kepada siswa
            if (!empty($validated['id_obat']) && !empty($validated['jumlah_dosis'])) {
                $obat = DB::table('obat')->where('id', $validated['id_obat'])->first();

                // Cek ketersediaan stok obat (Pencegahan minus stok)
                if ($obat->stok_tersedia < $validated['jumlah_dosis']) {
                    DB::rollBack(); // Batalkan transaksi
                    return back()->withInput()->with('error', "Stok obat {$obat->nama_obat} tidak mencukupi! Sisa stok hanya: {$obat->stok_tersedia}");
                }

                // Catat ke detail_pemberian_obat
                DB::table('detail_pemberian_obat')->insert([
                    'id_rekam_medis' => $recordId,
                    'id_obat'        => $validated['id_obat'],
                    'jumlah_dosis'   => $validated['jumlah_dosis'],
                    'aturan_pakai'   => $validated['aturan_pakai'] ?? '1x sehari setelah makan',
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);

                // Kurangi stok fisik obat di apotek UKS
                DB::table('obat')
                    ->where('id', $validated['id_obat'])
                    ->decrement('stok_tersedia', $validated['jumlah_dosis']);
            }

            // Jika semua query di atas sukses, simpan secara permanen
            DB::commit();

            return redirect()->route('rekam-medis.index')
                ->with('success', 'Pemeriksaan siswa dan pemberian obat berhasil disimpan! Stok obat telah diperbarui.');

        } catch (\Exception $e) {
            // Jika terjadi error, batalkan semua perubahan
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal menyimpan pemeriksaan: ' . $e->getMessage());
        }
    }
}
