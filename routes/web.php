<?php

use App\Http\Controllers\RekamMedisController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminStudentController;
use App\Http\Controllers\AdminWaliKelasController;
use App\Http\Controllers\AdminPmrController;
use App\Http\Controllers\AdminMedicineController;
use App\Http\Controllers\AdminEmergencyController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\ObatController;
use App\Http\Controllers\PetugasPmrDashboardController;
use App\Http\Controllers\RekamController;
use App\Http\Controllers\SiswaDashboardController;
use App\Http\Controllers\WaliKelasDashboardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('landing'))->name('home');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'authenticate'])->name('login.store');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::get('/register/{role}', [AuthController::class, 'showRegistration'])->name('register.role');
Route::post('/register/{role}', [AuthController::class, 'register'])->name('register.store');

Route::middleware('auth')->group(function () {
    Route::get('/pmr/laporan', [LaporanController::class, 'index'])->name('pmr.laporan.index');
    Route::get('/pmr/laporan/{type}/pdf', [LaporanController::class, 'download'])->name('pmr.laporan.download');
    Route::get('/admin/laporan', [LaporanController::class, 'adminIndex'])->name('admin.laporan.index');
    Route::get('/admin/laporan/pdf', [LaporanController::class, 'adminDownload'])->name('admin.laporan.download');
    Route::get('/pmr/obat', [ObatController::class, 'index'])->name('pmr.obat.index');
    Route::post('/pmr/obat', [ObatController::class, 'store'])->name('pmr.obat.store');
    Route::put('/pmr/obat/{obat}', [ObatController::class, 'update'])->whereNumber('obat')->name('pmr.obat.update');
    Route::delete('/pmr/obat/{obat}', [ObatController::class, 'destroy'])->whereNumber('obat')->name('pmr.obat.destroy');
    Route::get('/pmr/rekam-medis', [RekamController::class, 'index'])->name('pmr.rekam-medis.index');
    Route::get('/pmr/rekam-medis/create', [RekamController::class, 'create'])->name('pmr.rekam-medis.create');
    Route::post('/pmr/rekam-medis', [RekamController::class, 'store'])->name('pmr.rekam-medis.store');
    Route::get('/siswa/riwayat-kesehatan', [SiswaDashboardController::class, 'healthHistory'])->name('siswa.riwayat-kesehatan');
    Route::get('/siswa/stok-obat', [SiswaDashboardController::class, 'medicineStocks'])->name('siswa.stok-obat');
    Route::post('/siswa/sos', [SiswaDashboardController::class, 'sendEmergency'])->name('siswa.sos.store');
    
    Route::get('/dashboard', function (Request $request) {
        return match ($request->user()->role) {
            'siswa' => app(SiswaDashboardController::class)->index(),
            'petugas_pmr' => app(PetugasPmrDashboardController::class)->index(),
            'wali_kelas' => app(WaliKelasDashboardController::class)->index(),
            'admin' => view('dashboard-admin'),
            default => abort(403, 'Dashboard untuk peran ini belum tersedia.'),
        };
    })->name('dashboard');

    Route::get('/wali-kelas/rekap', [WaliKelasDashboardController::class, 'downloadRecap'])->name('wali-kelas.rekap');
    Route::get('/wali-kelas/monitoring', [WaliKelasDashboardController::class, 'monitoring'])->name('wali-kelas.monitoring');
    Route::get('/wali-kelas/siswa', [WaliKelasDashboardController::class, 'students'])->name('wali-kelas.siswa.index');
    Route::post('/wali-kelas/siswa', [WaliKelasDashboardController::class, 'storeStudent'])->name('wali-kelas.siswa.store');
    Route::put('/wali-kelas/siswa/{student}', [WaliKelasDashboardController::class, 'updateStudent'])->whereNumber('student')->name('wali-kelas.siswa.update');
    Route::delete('/wali-kelas/siswa/{student}', [WaliKelasDashboardController::class, 'destroyStudent'])->whereNumber('student')->name('wali-kelas.siswa.destroy');
    
    Route::get('/admin/siswa', [AdminStudentController::class, 'index'])->name('admin.siswa.index');
    Route::post('/admin/siswa', [AdminStudentController::class, 'store'])->name('admin.siswa.store');
    Route::put('/admin/siswa/{student}', [AdminStudentController::class, 'update'])->whereNumber('student')->name('admin.siswa.update');
    Route::delete('/admin/siswa/{student}', [AdminStudentController::class, 'destroy'])->whereNumber('student')->name('admin.siswa.destroy');
    
    Route::get('/admin/wali-kelas', [AdminWaliKelasController::class, 'index'])->name('admin.wali-kelas.index');
    Route::post('/admin/wali-kelas', [AdminWaliKelasController::class, 'store'])->name('admin.wali-kelas.store');
    Route::put('/admin/wali-kelas/{homeroomTeacher}', [AdminWaliKelasController::class, 'update'])->whereNumber('homeroomTeacher')->name('admin.wali-kelas.update');
    Route::delete('/admin/wali-kelas/{homeroomTeacher}', [AdminWaliKelasController::class, 'destroy'])->whereNumber('homeroomTeacher')->name('admin.wali-kelas.destroy');
    
    Route::get('/admin/petugas-pmr', [AdminPmrController::class, 'index'])->name('admin.pmr.index');
    Route::post('/admin/petugas-pmr', [AdminPmrController::class, 'store'])->name('admin.pmr.store');
    Route::put('/admin/petugas-pmr/{staff}', [AdminPmrController::class, 'update'])->whereNumber('staff')->name('admin.pmr.update');
    Route::delete('/admin/petugas-pmr/{staff}', [AdminPmrController::class, 'destroy'])->whereNumber('staff')->name('admin.pmr.destroy');
    
    Route::get('/admin/obat', [AdminMedicineController::class, 'index'])->name('admin.obat.index');
    Route::post('/admin/obat', [AdminMedicineController::class, 'store'])->name('admin.obat.store');
    Route::put('/admin/obat/{medicine}', [AdminMedicineController::class, 'update'])->whereNumber('medicine')->name('admin.obat.update');
    Route::delete('/admin/obat/{medicine}', [AdminMedicineController::class, 'destroy'])->whereNumber('medicine')->name('admin.obat.destroy');
    
    Route::get('/admin/monitoring-sos', [AdminEmergencyController::class, 'index'])->name('admin.sos.index');
    Route::patch('/admin/monitoring-sos/{alert}/status', [AdminEmergencyController::class, 'updateStatus'])->whereNumber('alert')->name('admin.sos.status');
    Route::get('/admin/rekam-medis/pdf', [LaporanController::class, 'adminMedicalRecordsDownload'])->name('admin.rekam-medis.download');
    Route::get('/admin/rekam-medis', [LaporanController::class, 'adminMedicalRecords'])->name('admin.rekam-medis.index');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // S2-01 & S2-02: Rute Rekam Medis Khusus Petugas PMR dan Admin
    Route::middleware('role:admin,petugas_pmr')->group(function () {
        Route::get('/rekam-medis', [RekamMedisController::class, 'index'])->name('rekam-medis.index');
        Route::get('/rekam-medis/create', [RekamMedisController::class, 'create'])->name('rekam-medis.create');
        Route::post('/rekam-medis/store', [RekamMedisController::class, 'store'])->name('rekam-medis.store');
    });
});