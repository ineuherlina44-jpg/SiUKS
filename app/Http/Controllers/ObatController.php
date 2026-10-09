<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ObatController extends Controller
{
    private const CRITICAL_STOCK_THRESHOLD = 5;

    public function index(Request $request): View
    {
        $this->authorizePmr($request);

        $search = trim((string) $request->query('search', ''));
        $medicines = DB::table('obat')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('nama_obat', 'like', "%{$search}%")
                        ->orWhere('kode_obat', 'like', "%{$search}%")
                        ->orWhere('kategori', 'like', "%{$search}%")
                        ->orWhere('jenis', 'like', "%{$search}%");
                });
            })
            ->orderBy('nama_obat')
            ->paginate(10)
            ->withQueryString();

        $totalMedicines = DB::table('obat')->count();
        $criticalMedicines = DB::table('obat')
            ->where('stok_tersedia', '<=', self::CRITICAL_STOCK_THRESHOLD)
            ->count();

        return view('pmr-kelola-stok-obat', [
            'user' => $request->user(),
            'medicines' => $medicines,
            'search' => $search,
            'totalMedicines' => $totalMedicines,
            'criticalMedicines' => $criticalMedicines,
            'criticalStockThreshold' => self::CRITICAL_STOCK_THRESHOLD,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizePmr($request);
        $validated = $this->validateMedicine($request);

        DB::table('obat')->insert([
            ...$validated,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('pmr.obat.index')->with('status', 'Obat berhasil ditambahkan.');
    }

    public function update(Request $request, int $obat): RedirectResponse
    {
        $this->authorizePmr($request);
        abort_unless(DB::table('obat')->where('id', $obat)->exists(), 404);
        $validated = $this->validateMedicine($request, $obat);

        DB::table('obat')
            ->where('id', $obat)
            ->update([...$validated, 'updated_at' => now()]);

        return redirect()->route('pmr.obat.index')->with('status', 'Data obat berhasil diperbarui.');
    }

    public function destroy(Request $request, int $obat): RedirectResponse
    {
        $this->authorizePmr($request);
        abort_unless(DB::table('obat')->where('id', $obat)->exists(), 404);

        if (DB::table('detail_pemberian_obat')->where('id_obat', $obat)->exists()) {
            return Redirect::route('pmr.obat.index')
                ->withErrors(['medicine' => 'Obat ini sudah tercatat pada riwayat pemberian, sehingga tidak dapat dihapus.']);
        }

        $deleted = DB::table('obat')->where('id', $obat)->delete();
        abort_unless($deleted === 1, 500, 'Obat tidak berhasil dihapus.');

        return redirect()->route('pmr.obat.index')->with('status', 'Obat berhasil dihapus.');
    }

    private function validateMedicine(Request $request, ?int $obat = null): array
    {
        return $request->validate([
            'kode_obat' => ['required', 'string', 'max:255', 'unique:obat,kode_obat'.($obat ? ','.$obat : '')],
            'nama_obat' => ['required', 'string', 'max:255'],
            'jenis' => ['required', 'string', 'max:255'],
            'kategori' => ['required', 'string', 'max:255'],
            'stok_tersedia' => ['required', 'integer', 'min:0'],
            'satuan' => ['required', 'string', 'max:255'],
        ], [
            'kode_obat.required' => 'Kode obat wajib diisi.',
            'kode_obat.unique' => 'Kode obat sudah digunakan.',
            'nama_obat.required' => 'Nama obat wajib diisi.',
            'jenis.required' => 'Jenis obat wajib diisi.',
            'kategori.required' => 'Kategori obat wajib diisi.',
            'stok_tersedia.required' => 'Stok obat wajib diisi.',
            'stok_tersedia.integer' => 'Stok harus berupa bilangan bulat.',
            'stok_tersedia.min' => 'Stok tidak boleh kurang dari nol.',
            'satuan.required' => 'Satuan obat wajib diisi.',
        ]);
    }

    private function authorizePmr(Request $request): void
    {
        abort_unless($request->user() && $request->user()->role === 'petugas_pmr', 403);
    }
}
