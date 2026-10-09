<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SiswaDashboardController extends Controller
{
    public function healthHistory(): View
    {
        $user = Auth::user();
        abort_unless($user && $user->role === 'siswa', 403);

        $student = DB::table('siswa')->where('id_user', $user->id)->first();
        abort_unless($student, 404);

        $visits = DB::table('rekam_medis')->where('id_siswa', $student->id);
        $totalVisits = (clone $visits)->count();
        $monthlyVisits = (clone $visits)
            ->whereYear('waktu_pemeriksaan', now()->year)
            ->whereMonth('waktu_pemeriksaan', now()->month)
            ->count();
        $totalAlerts = DB::table('sinyal_darurat')
            ->where('id_siswa', $student->id)
            ->count();

        $records = (clone $visits)
            ->select('id', 'waktu_pemeriksaan', 'keluhan', 'status')
            ->orderByDesc('waktu_pemeriksaan')
            ->orderByDesc('id')
            ->get();

        $recordMedicines = DB::table('detail_pemberian_obat as detail')
            ->join('obat', 'obat.id', '=', 'detail.id_obat')
            ->whereIn('detail.id_rekam_medis', $records->pluck('id'))
            ->select('detail.id_rekam_medis', 'obat.nama_obat')
            ->get()
            ->groupBy('id_rekam_medis');

        return view('siswa-riwayat-kesehatan', compact(
            'user',
            'student',
            'totalVisits',
            'monthlyVisits',
            'totalAlerts',
            'records',
            'recordMedicines',
        ));
    }

    public function medicineStocks(): View
    {
        $user = Auth::user();
        abort_unless($user && $user->role === 'siswa', 403);

        $student = DB::table('siswa')->where('id_user', $user->id)->first();
        abort_unless($student, 404);

        $criticalMedicineThreshold = 5;
        $medicines = DB::table('obat')
            ->select('nama_obat', 'kategori', 'stok_tersedia', 'satuan')
            ->orderBy('nama_obat')
            ->get();

        return view('siswa-stok-obat', [
            'user' => $user,
            'student' => $student,
            'medicines' => $medicines,
            'criticalMedicineThreshold' => $criticalMedicineThreshold,
            'criticalMedicineCount' => $medicines
                ->where('stok_tersedia', '<=', $criticalMedicineThreshold)
                ->count(),
        ]);
    }

    public function sendEmergency(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user && $user->role === 'siswa', 403);

        $created = DB::transaction(function () use ($user): bool {
            $student = DB::table('siswa')->where('id_user', $user->id)->lockForUpdate()->first();
            abort_unless($student, 404);

            $hasOpenAlert = DB::table('sinyal_darurat')
                ->where('id_siswa', $student->id)
                ->where('status_respon', '<>', 'selesai')
                ->exists();

            if ($hasOpenAlert) {
                return false;
            }

            DB::table('sinyal_darurat')->insert([
                'id_siswa' => $student->id,
                'id_pelapor' => $user->id,
                'waktu_pemicu' => now(),
                'status_respon' => 'menunggu',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return true;
        });

        return redirect()->route('dashboard')->with(
            'status',
            $created
                ? 'Sinyal SOS berhasil dikirim dan tercatat untuk petugas UKS.'
                : 'Sinyal SOS Anda masih menunggu penanganan. Petugas UKS sudah menerima laporan tersebut.'
        );
    }

    public function index(): View
    {
        $user = Auth::user();
        abort_unless($user && $user->role === 'siswa', 403);

        $student = DB::table('siswa')->where('id_user', $user->id)->first();
        abort_unless($student, 404);

        $visits = DB::table('rekam_medis')
            ->where('id_siswa', $student->id);

        $totalVisits = (clone $visits)->count();
        $monthlyVisits = (clone $visits)
            ->whereYear('waktu_pemeriksaan', now()->year)
            ->whereMonth('waktu_pemeriksaan', now()->month)
            ->count();

        $recentRecords = (clone $visits)
            ->select('id', 'waktu_pemeriksaan', 'keluhan', 'status')
            ->orderByDesc('waktu_pemeriksaan')
            ->limit(6)
            ->get();

        $recordMedicines = DB::table('detail_pemberian_obat as detail')
            ->join('obat', 'obat.id', '=', 'detail.id_obat')
            ->whereIn('detail.id_rekam_medis', $recentRecords->pluck('id'))
            ->select('detail.id_rekam_medis', 'obat.nama_obat')
            ->get()
            ->groupBy('id_rekam_medis');

        $recentAlerts = DB::table('sinyal_darurat')
            ->where('id_siswa', $student->id)
            ->select('waktu_pemicu', 'status_respon')
            ->orderByDesc('waktu_pemicu')
            ->limit(3)
            ->get();

        $totalAlerts = DB::table('sinyal_darurat')
            ->where('id_siswa', $student->id)
            ->count();

        $openAlert = DB::table('sinyal_darurat')
            ->where('id_siswa', $student->id)
            ->where('status_respon', '<>', 'selesai')
            ->orderByDesc('waktu_pemicu')
            ->first(['status_respon', 'waktu_pemicu']);

        $medicineStocks = DB::table('obat')
            ->select('nama_obat', 'stok_tersedia', 'satuan')
            ->orderBy('nama_obat')
            ->limit(5)
            ->get();

        return view('dashboard-siswa', compact(
            'user',
            'student',
            'totalVisits',
            'monthlyVisits',
            'totalAlerts',
            'recentRecords',
            'recordMedicines',
            'recentAlerts',
            'openAlert',
            'medicineStocks',
        ));
    }
}