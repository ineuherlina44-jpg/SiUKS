<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DaruratController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KategoriObatController;
use App\Http\Controllers\KategoriPenilaianController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\ObatController;
use App\Http\Controllers\PenilaianController as PenilaianSiswaController;
use App\Http\Controllers\RekamController as RekamMedisController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\SuratIzinController;
use App\Http\Controllers\TransaksiObatController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| SiUKS PMR - API Version 1
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Authentication
    |--------------------------------------------------------------------------
    */
    Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout']) ->middleware('auth:sanctum');
    Route::get('/me', [AuthController::class, 'me']) ->middleware('auth:sanctum');
    });


    /*
    |--------------------------------------------------------------------------
    | Protected Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware('auth:sanctum')->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Dashboard
        |--------------------------------------------------------------------------
        */
        Route::get('/dashboard', [DashboardController::class, 'index']);


        /*
        |--------------------------------------------------------------------------
        | Users
        |--------------------------------------------------------------------------
        */
        Route::apiResource('users', UserController::class);


        /*
        |--------------------------------------------------------------------------
        | Kelas
        |--------------------------------------------------------------------------
        */
        Route::apiResource('kelas', KelasController::class);

        Route::get(
            '/kelas/{kelas}/siswa',
            [KelasController::class, 'siswa']
        );


        /*
        |--------------------------------------------------------------------------
        | Siswa
        |--------------------------------------------------------------------------
        */
        Route::apiResource('siswa', SiswaController::class);

        Route::get(
            '/siswa/{siswa}/rekam-medis',
            [SiswaController::class, 'rekamMedis']
        );

        Route::get(
            '/siswa/{siswa}/penilaian',
            [SiswaController::class, 'penilaian']
        );


        /*
        |--------------------------------------------------------------------------
        | Rekam Medis UKS
        |--------------------------------------------------------------------------
        */
        Route::apiResource(
            'rekam-medis',
            RekamMedisController::class
        );

        Route::patch(
            '/rekam-medis/{rekam_medis}/status',
            [RekamMedisController::class, 'updateStatus']
        );


        /*
        |--------------------------------------------------------------------------
        | Kategori Obat
        |--------------------------------------------------------------------------
        */
        Route::apiResource(
            'kategori-obat',
            KategoriObatController::class
        );


        /*
        |--------------------------------------------------------------------------
        | Obat
        |--------------------------------------------------------------------------
        */
        Route::apiResource(
            'obat',
            ObatController::class
        );

        Route::get(
            '/obat/stok/minimum',
            [ObatController::class, 'stokMinimum']
        );

        Route::get(
            '/obat/stok/habis',
            [ObatController::class, 'stokHabis']
        );


        /*
        |--------------------------------------------------------------------------
        | Transaksi Obat
        |--------------------------------------------------------------------------
        */
        Route::apiResource(
            'transaksi-obat',
            TransaksiObatController::class
        )->only([
            'index',
            'store',
            'show'
        ]);


        /*
        |--------------------------------------------------------------------------
        | Darurat
        |--------------------------------------------------------------------------
        */
        Route::apiResource(
            'darurat',
            DaruratController::class
        )->only([
            'index',
            'store',
            'show'
        ]);

        Route::patch(
            '/darurat/{darurat}/status',
            [DaruratController::class, 'updateStatus']
        );


        /*
        |--------------------------------------------------------------------------
        | Kategori Penilaian
        |--------------------------------------------------------------------------
        */
        Route::apiResource(
            'kategori-penilaian',
            KategoriPenilaianController::class
        );


        /*
        |--------------------------------------------------------------------------
        | Penilaian Siswa
        |--------------------------------------------------------------------------
        */
        Route::apiResource(
            'penilaian',
            PenilaianSiswaController::class
        );

        Route::get(
            '/penilaian/siswa/{siswa}',
            [PenilaianSiswaController::class, 'bySiswa']
        );

        Route::get(
            '/penilaian/kelas/{kelas}',
            [PenilaianSiswaController::class, 'byKelas']
        );

        Route::patch(
            '/penilaian/{penilaian}/finalize',
            [PenilaianSiswaController::class, 'finalize']
        );


        /*
        |--------------------------------------------------------------------------
        | Surat Izin
        |--------------------------------------------------------------------------
        */
        Route::apiResource(
            'surat-izin',
            SuratIzinController::class
        )->only([
            'index',
            'store',
            'show'
        ]);

        Route::get(
            '/surat-izin/{surat_izin}/pdf',
            [SuratIzinController::class, 'pdf']
        );


        /*
        |--------------------------------------------------------------------------
        | Laporan
        |--------------------------------------------------------------------------
        */
        Route::prefix('laporan')->group(function () {

            Route::get(
                '/pasien',
                [LaporanController::class, 'pasien']
            );

            Route::get(
                '/obat',
                [LaporanController::class, 'obat']
            );

            Route::get(
                '/stok',
                [LaporanController::class, 'stok']
            );

            Route::get(
                '/darurat',
                [LaporanController::class, 'darurat']
            );

            Route::get(
                '/penilaian',
                [LaporanController::class, 'penilaian']
            );

            Route::get(
                '/penilaian/pdf',
                [LaporanController::class, 'penilaianPdf']
            );

            Route::get(
                '/bulanan',
                [LaporanController::class, 'bulanan']
            );

            Route::get(
                '/bulanan/pdf',
                [LaporanController::class, 'bulananPdf']
            );
        });

    });
});

