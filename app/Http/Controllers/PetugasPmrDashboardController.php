<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PetugasPmrDashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        abort_unless($user && $user->role === 'petugas_pmr', 403);

        $staffId = DB::table('petugas_pmr')
            ->where('id_user', $user->id)
            ->value('id');
        abort_unless($staffId, 404);

        $records = DB::table('rekam_medis')
            ->join('siswa', 'siswa.id', '=', 'rekam_medis.id_siswa')
            ->join('users as siswa_users', 'siswa_users.id', '=', 'siswa.id_user');

        $patientsToday = (clone $records)
            ->whereDate('rekam_medis.waktu_pemeriksaan', today())
            ->distinct()
            ->count('rekam_medis.id_siswa');

        $patientsInCare = DB::table('rekam_medis')
            ->where('status', 'dirawat')
            ->distinct()
            ->count('id_siswa');

        $criticalMedicineThreshold = 5;
        $criticalMedicineCount = DB::table('obat')
            ->where('stok_tersedia', '<=', $criticalMedicineThreshold)
            ->count();

        $recentVisits = (clone $records)
            ->leftJoin('petugas_pmr', 'petugas_pmr.id', '=', 'rekam_medis.id_petugas')
            ->leftJoin('users as staff_users', 'staff_users.id', '=', 'petugas_pmr.id_user')
            ->select(
                'rekam_medis.id',
                'rekam_medis.waktu_pemeriksaan',
                'rekam_medis.keluhan',
                'rekam_medis.tindakan',
                'rekam_medis.status',
                'siswa.kelas',
                'siswa_users.name as nama_siswa',
                'staff_users.name as nama_petugas',
            )
            ->orderByDesc('rekam_medis.waktu_pemeriksaan')
            ->orderByDesc('rekam_medis.id')
            ->limit(8)
            ->get();

        $recentAlerts = DB::table('sinyal_darurat')
            ->join('siswa', 'siswa.id', '=', 'sinyal_darurat.id_siswa')
            ->join('users as siswa_users', 'siswa_users.id', '=', 'siswa.id_user')
            ->select(
                'sinyal_darurat.waktu_pemicu',
                'sinyal_darurat.status_respon',
                'siswa_users.name as nama_siswa',
                'siswa.kelas',
            )
            ->orderByDesc('sinyal_darurat.waktu_pemicu')
            ->limit(5)
            ->get();

        $criticalMedicines = DB::table('obat')
            ->where('stok_tersedia', '<=', $criticalMedicineThreshold)
            ->orderBy('stok_tersedia')
            ->orderBy('nama_obat')
            ->limit(5)
            ->get(['nama_obat', 'stok_tersedia', 'satuan']);

        return view('dashboard-petugas-pmr', compact(
            'user',
            'patientsToday',
            'patientsInCare',
            'criticalMedicineCount',
            'criticalMedicineThreshold',
            'recentVisits',
            'recentAlerts',
            'criticalMedicines',
        ));
    }
}