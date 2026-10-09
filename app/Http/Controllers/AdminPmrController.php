<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminPmrController extends Controller
{
    public function index(Request $request): View
    {
        $user = $this->currentAdmin();
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'kelas' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'in:all,Aktif,Nonaktif'],
        ]);
        $staff = DB::table('petugas_pmr')
            ->join('users', 'users.id', '=', 'petugas_pmr.id_user')
            ->select(
                'petugas_pmr.id',
                'petugas_pmr.nis',
                'petugas_pmr.kelas',
                'petugas_pmr.jenis_kelamin',
                'petugas_pmr.status',
                'users.name',
                'users.email'
            );

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $staff->where(function ($query) use ($search): void {
                $query->where('users.name', 'like', '%'.$search.'%')
                    ->orWhere('petugas_pmr.nis', 'like', '%'.$search.'%')
                    ->orWhere('petugas_pmr.kelas', 'like', '%'.$search.'%');
            });
        }

        if (! empty($filters['kelas'])) {
            $staff->where('petugas_pmr.kelas', $filters['kelas']);
        }

        if (($filters['status'] ?? 'all') !== 'all') {
            $staff->where('petugas_pmr.status', $filters['status']);
        }

        return view('admin-data-petugas-pmr', [
            'user' => $user,
            'filters' => $filters,
            'staff' => $staff->orderBy('users.name')->paginate(15)->withQueryString(),
            'classes' => DB::table('siswa')->select('kelas')
                ->union(DB::table('petugas_pmr')->whereNotNull('kelas')->select('kelas'))
                ->orderBy('kelas')
                ->pluck('kelas'),
            'totalStaff' => DB::table('petugas_pmr')->count(),
            'activeStaff' => DB::table('petugas_pmr')->where('status', 'Aktif')->count(),
            'assignedClasses' => DB::table('petugas_pmr')->whereNotNull('kelas')->distinct()->count('kelas'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->currentAdmin();
        $validated = $request->validate($this->rules());

        DB::transaction(function () use ($validated): void {
            $staffUser = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'role' => 'petugas_pmr',
                'password' => $validated['password'],
            ]);

            DB::table('petugas_pmr')->insert([
                'id_user' => $staffUser->id,
                'nis' => $validated['nis'],
                'kelas' => $validated['kelas'],
                'jenis_kelamin' => $validated['jenis_kelamin'],
                'status' => $validated['status'],
                'status_piket' => 'Belum dijadwalkan',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        return redirect()->route('admin.pmr.index')->with('status', 'Data petugas PMR dan akun login berhasil dibuat.');
    }

    public function update(Request $request, int $staff): RedirectResponse
    {
        $this->currentAdmin();
        $staffRecord = DB::table('petugas_pmr')->where('id', $staff)->first();
        abort_unless($staffRecord, 404);
        $validated = $request->validate($this->rules($staffRecord->id_user, $staffRecord->id, false));

        DB::transaction(function () use ($validated, $staffRecord): void {
            DB::table('users')->where('id', $staffRecord->id_user)->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'updated_at' => now(),
            ]);
            DB::table('petugas_pmr')->where('id', $staffRecord->id)->update([
                'nis' => $validated['nis'],
                'kelas' => $validated['kelas'],
                'jenis_kelamin' => $validated['jenis_kelamin'],
                'status' => $validated['status'],
                'updated_at' => now(),
            ]);
        });

        return redirect()->route('admin.pmr.index')->with('status', 'Data petugas PMR berhasil diperbarui.');
    }

    public function destroy(int $staff): RedirectResponse
    {
        $this->currentAdmin();
        $deleted = DB::transaction(function () use ($staff): bool {
            $staffRecord = DB::table('petugas_pmr')->where('id', $staff)->lockForUpdate()->first();
            abort_unless($staffRecord, 404);

            $hasMedicalHistory = DB::table('rekam_medis')->where('id_petugas', $staffRecord->id)->exists();
            $hasReportedEmergency = DB::table('sinyal_darurat')->where('id_pelapor', $staffRecord->id_user)->exists();
            if ($hasMedicalHistory || $hasReportedEmergency) {
                return false;
            }

            User::where('id', $staffRecord->id_user)->where('role', 'petugas_pmr')->delete();

            return true;
        });

        if (! $deleted) {
            return redirect()->route('admin.pmr.index')
                ->with('error', 'Petugas tidak dapat dihapus karena sudah memiliki catatan rekam medis atau laporan SOS.');
        }

        return redirect()->route('admin.pmr.index')->with('status', 'Data petugas PMR berhasil dihapus.');
    }

    private function rules(?int $userId = null, ?int $staffId = null, bool $includePassword = true): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'nis' => ['required', 'string', 'max:30', Rule::unique('petugas_pmr', 'nis')->ignore($staffId)],
            'kelas' => ['required', 'string', 'max:100'],
            'jenis_kelamin' => ['required', Rule::in(['Laki-laki', 'Perempuan'])],
            'status' => ['required', Rule::in(['Aktif', 'Nonaktif'])],
        ];

        if ($includePassword) {
            $rules['password'] = ['required', 'string', 'min:8', 'confirmed'];
        }

        return $rules;
    }

    private function currentAdmin(): User
    {
        $user = Auth::user();
        abort_unless($user && $user->role === 'admin', 403);

        return $user;
    }
}
