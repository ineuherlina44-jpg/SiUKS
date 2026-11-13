<?php

namespace App\Http\Controllers;

use App\Services\StudentImportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RekamController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        abort_unless($user && $user->role === 'petugas_pmr', 403);

        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'kelas' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'in:dirawat,diizinkan_pulang,rujukan'],
            'date_from' => ['nullable', 'date_format:Y-m-d'],
            'date_to' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:date_from'],
        ]);

        $search = trim($filters['search'] ?? '');
        $records = DB::table('rekam_medis')
            ->join('siswa', 'siswa.id', '=', 'rekam_medis.id_siswa')
            ->join('users as siswa_users', 'siswa_users.id', '=', 'siswa.id_user')
            ->leftJoin('petugas_pmr', 'petugas_pmr.id', '=', 'rekam_medis.id_petugas')
            ->leftJoin('users as staff_users', 'staff_users.id', '=', 'petugas_pmr.id_user')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('siswa_users.name', 'like', "%{$search}%")
                        ->orWhere('siswa.nisn', 'like', "%{$search}%")
                        ->orWhere('siswa.kelas', 'like', "%{$search}%")
                        ->orWhere('rekam_medis.keluhan', 'like', "%{$search}%")
                        ->orWhere('rekam_medis.tindakan', 'like', "%{$search}%");
                });
            })
            ->when(! empty($filters['kelas']), fn ($query) => $query->where('siswa.kelas', $filters['kelas']))
            ->when(! empty($filters['status']), fn ($query) => $query->where('rekam_medis.status', $filters['status']))
            ->when(! empty($filters['date_from']), fn ($query) => $query->whereDate('rekam_medis.waktu_pemeriksaan', '>=', $filters['date_from']))
            ->when(! empty($filters['date_to']), fn ($query) => $query->whereDate('rekam_medis.waktu_pemeriksaan', '<=', $filters['date_to']))
            ->select(
                'rekam_medis.id',
                'rekam_medis.waktu_pemeriksaan',
                'rekam_medis.status_penyakit',
                'rekam_medis.keluhan',
                'rekam_medis.tindakan',
                'rekam_medis.status',
                'siswa.kelas',
                'siswa.nisn',
                'siswa_users.name as nama_siswa',
                'staff_users.name as nama_petugas',
            )
            ->orderBy('rekam_medis.waktu_pemeriksaan')
            ->orderBy('rekam_medis.id')
            ->paginate(12)
            ->withQueryString();

        $classes = collect(
            array_merge(
                DB::table('siswa')->distinct()->orderBy('kelas')->pluck('kelas')->all(),
                StudentImportService::getPresetClasses()
            )
        )
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->all();

        return view('pmr-riwayat-rekam-medis', compact('user', 'records', 'classes', 'search', 'filters'));
    }

    public function create(): View
    {
        $user = Auth::user();
        abort_unless($user && $user->role === 'petugas_pmr', 403);

        $students = DB::table('siswa')
            ->join('users', 'users.id', '=', 'siswa.id_user')
            ->orderBy('users.name')
            ->get([
                'siswa.id',
                'siswa.nisn',
                'siswa.kelas',
                'siswa.jenis_kelamin',
                'siswa.riwayat_alergi',
                'users.name',
            ]);

        $medicines = DB::table('obat')
            ->where('stok_tersedia', '>', 0)
            ->orderBy('nama_obat')
            ->get(['id', 'nama_obat', 'stok_tersedia', 'satuan', 'kategori']);

        return view('pmr-rekam-medis-create', compact('user', 'students', 'medicines'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user && $user->role === 'petugas_pmr', 403);

        $staffId = DB::table('petugas_pmr')
            ->where('id_user', $user->id)
            ->value('id');
        abort_unless($staffId, 404);

        if ($request->input('status_penyakit_select') === 'Lainnya') {
            $request->merge([
                'status_penyakit' => $request->input('status_penyakit_custom') ?: $request->input('status_penyakit')
            ]);
        } elseif ($request->filled('status_penyakit_select')) {
            $request->merge([
                'status_penyakit' => $request->input('status_penyakit_select')
            ]);
        }

        $validated = $request->validate([
            'student_id' => ['required', 'integer', 'exists:siswa,id'],
            'waktu_pemeriksaan' => ['required', 'date_format:Y-m-d\TH:i'],
            'status_penyakit' => ['required', 'string', 'max:150'],
            'keluhan' => ['required', 'string', 'max:5000'],
            'tindakan' => ['required', 'string', 'max:5000'],
            'status' => ['required', 'in:dirawat,diizinkan_pulang,rujukan'],
            'id_obat' => ['nullable', 'integer', 'exists:obat,id'],
            'jumlah_dosis' => ['nullable', 'integer', 'min:1'],
            'aturan_pakai' => ['nullable', 'string', 'max:255'],
        ], [
            'student_id.required' => 'Pilih siswa terlebih dahulu.',
            'waktu_pemeriksaan.required' => 'Tanggal dan waktu kunjungan wajib diisi.',
            'status_penyakit.required' => 'Status penyakit wajib diisi.',
            'keluhan.required' => 'Keluhan atau gejala wajib diisi.',
            'tindakan.required' => 'Tindakan atau penanganan wajib diisi.',
            'status.required' => 'Status kunjungan wajib dipilih.',
            'id_obat.exists' => 'Obat yang dipilih tidak valid.',
            'jumlah_dosis.min' => 'Jumlah dosis obat minimal 1.',
        ]);

        DB::transaction(function () use ($validated, $staffId): void {
            $recordId = DB::table('rekam_medis')->insertGetId([
                'id_siswa' => $validated['student_id'],
                'id_petugas' => $staffId,
                'waktu_pemeriksaan' => str_replace('T', ' ', $validated['waktu_pemeriksaan']).':00',
                'status_penyakit' => $validated['status_penyakit'],
                'keluhan' => $validated['keluhan'],
                'tindakan' => $validated['tindakan'],
                'status' => $validated['status'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Jika ada pemberian obat kepada siswa
            if (!empty($validated['id_obat']) && !empty($validated['jumlah_dosis'])) {
                $obat = DB::table('obat')
                    ->where('id', $validated['id_obat'])
                    ->lockForUpdate()
                    ->first();

                if (!$obat || $obat->stok_tersedia < $validated['jumlah_dosis']) {
                    throw ValidationException::withMessages([
                        'id_obat' => 'Stok obat ' . ($obat ? $obat->nama_obat : '') . ' tidak mencukupi (sisa stok: ' . ($obat ? $obat->stok_tersedia : 0) . ')',
                    ]);
                }

                DB::table('detail_pemberian_obat')->insert([
                    'id_rekam_medis' => $recordId,
                    'id_obat'        => $validated['id_obat'],
                    'jumlah_dosis'   => $validated['jumlah_dosis'],
                    'aturan_pakai'   => $validated['aturan_pakai'] ?? '1x sehari sesudah makan',
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);

                DB::table('obat')
                    ->where('id', $validated['id_obat'])
                    ->decrement('stok_tersedia', $validated['jumlah_dosis']);
            }
        });

        return redirect()
            ->route('pmr.rekam-medis.create')
            ->with('status', 'Catatan kunjungan berhasil disimpan.');
    }
}
