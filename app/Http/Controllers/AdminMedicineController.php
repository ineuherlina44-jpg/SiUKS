<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminMedicineController extends Controller
{
    private const CRITICAL_STOCK_THRESHOLD = 5;

    public function index(Request $request): View
    {
        $user = $this->currentAdmin();
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'in:all,aman,kritis'],
        ]);

        $medicines = DB::table('obat');
        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $medicines->where(function ($query) use ($search): void {
                $query->where('nama_obat', 'like', '%'.$search.'%')
                    ->orWhere('kode_obat', 'like', '%'.$search.'%')
                    ->orWhere('kategori', 'like', '%'.$search.'%')
                    ->orWhere('jenis', 'like', '%'.$search.'%');
            });
        }

        if (! empty($filters['category'])) {
            $medicines->where('kategori', $filters['category']);
        }

        if (($filters['status'] ?? 'all') === 'kritis') {
            $medicines->where('stok_tersedia', '<=', self::CRITICAL_STOCK_THRESHOLD);
        } elseif (($filters['status'] ?? 'all') === 'aman') {
            $medicines->where('stok_tersedia', '>', self::CRITICAL_STOCK_THRESHOLD);
        }

        return view('admin-master-obat', [
            'user' => $user,
            'filters' => $filters,
            'medicines' => $medicines->orderBy('nama_obat')->paginate(15)->withQueryString(),
            'categories' => DB::table('obat')->select('kategori')->distinct()->orderBy('kategori')->pluck('kategori'),
            'totalMedicines' => DB::table('obat')->count(),
            'criticalMedicines' => DB::table('obat')->where('stok_tersedia', '<=', self::CRITICAL_STOCK_THRESHOLD)->count(),
            'criticalStockThreshold' => self::CRITICAL_STOCK_THRESHOLD,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->currentAdmin();
        $validated = $request->validate($this->rules());
        DB::table('obat')->insert([
            ...$validated,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.obat.index')->with('status', 'Obat berhasil ditambahkan.');
    }

    public function update(Request $request, int $medicine): RedirectResponse
    {
        $this->currentAdmin();
        abort_unless(DB::table('obat')->where('id', $medicine)->exists(), 404);
        $validated = $request->validate($this->rules($medicine));
        DB::table('obat')->where('id', $medicine)->update([
            ...$validated,
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.obat.index')->with('status', 'Data obat berhasil diperbarui.');
    }

    public function destroy(int $medicine): RedirectResponse
    {
        $this->currentAdmin();
        $deleted = DB::transaction(function () use ($medicine): bool {
            $record = DB::table('obat')->where('id', $medicine)->lockForUpdate()->first();
            abort_unless($record, 404);

            if (DB::table('detail_pemberian_obat')->where('id_obat', $record->id)->exists()) {
                return false;
            }

            DB::table('obat')->where('id', $record->id)->delete();

            return true;
        });

        if (! $deleted) {
            return redirect()->route('admin.obat.index')
                ->with('error', 'Obat tidak dapat dihapus karena sudah tercatat pada riwayat pemberian.');
        }

        return redirect()->route('admin.obat.index')->with('status', 'Obat berhasil dihapus.');
    }

    private function rules(?int $medicine = null): array
    {
        return [
            'kode_obat' => ['required', 'string', 'max:255', Rule::unique('obat', 'kode_obat')->ignore($medicine)],
            'nama_obat' => ['required', 'string', 'max:255'],
            'jenis' => ['required', 'string', 'max:255'],
            'kategori' => ['required', 'string', 'max:255'],
            'stok_tersedia' => ['required', 'integer', 'min:0'],
            'satuan' => ['required', 'string', 'max:255'],
        ];
    }

    private function currentAdmin(): User
    {
        $user = Auth::user();
        abort_unless($user && $user->role === 'admin', 403);

        return $user;
    }
}
