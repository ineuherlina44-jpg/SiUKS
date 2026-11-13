<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\StudentImportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminWaliKelasController extends Controller
{
    public function index(Request $request): View
    {
        $user = $this->currentAdmin();
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'jurusan' => ['nullable', 'string', 'max:100'],
            'kelas' => ['nullable', 'string', 'max:100'],
        ]);

        $teachers = DB::table('wali_kelas')
            ->join('users', 'users.id', '=', 'wali_kelas.id_user')
            ->select(
                'wali_kelas.id',
                'wali_kelas.nip',
                'wali_kelas.kelas_dampu',
                'wali_kelas.jurusan',
                'wali_kelas.status',
                'users.name',
                'users.email'
            );

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $teachers->where(function ($query) use ($search): void {
                $query->where('users.name', 'like', '%' . $search . '%')
                    ->orWhere('wali_kelas.nip', 'like', '%' . $search . '%');
            });
        }

        if (! empty($filters['jurusan'])) {
            $teachers->where('wali_kelas.jurusan', $filters['jurusan']);
        }

        if (! empty($filters['kelas'])) {
            $teachers->where('wali_kelas.kelas_dampu', $filters['kelas']);
        }

        $classes = collect(
            array_merge(
                DB::table('siswa')->select('kelas')->union(DB::table('wali_kelas')->select('kelas_dampu as kelas'))->orderBy('kelas')->pluck('kelas')->all(),
                StudentImportService::getPresetClasses()
            )
        )
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->all();

        return view('admin-data-wali-kelas', [
            'user' => $user,
            'filters' => $filters,
            'teachers' => $teachers->orderBy('users.name')->paginate(15)->withQueryString(),
            'jurusans' => DB::table('wali_kelas')->whereNotNull('jurusan')->where('jurusan', '<>', '')->distinct()->orderBy('jurusan')->pluck('jurusan'),
            'classes' => $classes,
            'totalTeachers' => DB::table('wali_kelas')->count(),
            'activeTeachers' => DB::table('wali_kelas')->where('status', 'Aktif')->count(),
            'assignedClasses' => DB::table('wali_kelas')->distinct()->count('kelas_dampu'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->currentAdmin();
        $validated = $request->validate($this->rules());

        DB::transaction(function () use ($validated): void {
            $teacherUser = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'role' => 'wali_kelas',
                'password' => $validated['password'],
            ]);

            DB::table('wali_kelas')->insert([
                'id_user' => $teacherUser->id,
                'nip' => $validated['nip'],
                'kelas_dampu' => $validated['kelas_dampu'],
                'jurusan' => $validated['jurusan'],
                'status' => $validated['status'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        return redirect()->route('admin.wali-kelas.index')->with('status', 'Data wali kelas dan akun login berhasil dibuat.');
    }

    public function update(Request $request, int $homeroomTeacher): RedirectResponse
    {
        $this->currentAdmin();
        $teacher = DB::table('wali_kelas')->where('id', $homeroomTeacher)->first();
        abort_unless($teacher, 404);

        $validated = $request->validate($this->rules($teacher->id_user, $teacher->id, false));

        DB::transaction(function () use ($validated, $teacher): void {
            DB::table('users')->where('id', $teacher->id_user)->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'updated_at' => now(),
            ]);
            DB::table('wali_kelas')->where('id', $teacher->id)->update([
                'nip' => $validated['nip'],
                'kelas_dampu' => $validated['kelas_dampu'],
                'jurusan' => $validated['jurusan'],
                'status' => $validated['status'],
                'updated_at' => now(),
            ]);
        });

        return redirect()->route('admin.wali-kelas.index')->with('status', 'Data wali kelas berhasil diperbarui.');
    }

    public function destroy(int $homeroomTeacher): RedirectResponse
    {
        $this->currentAdmin();
        $deleted = DB::transaction(function () use ($homeroomTeacher): bool {
            $teacher = DB::table('wali_kelas')->where('id', $homeroomTeacher)->lockForUpdate()->first();
            abort_unless($teacher, 404);

            if (DB::table('sinyal_darurat')->where('id_pelapor', $teacher->id_user)->exists()) {
                return false;
            }

            User::where('id', $teacher->id_user)->where('role', 'wali_kelas')->delete();

            return true;
        });

        if (! $deleted) {
            return redirect()->route('admin.wali-kelas.index')
                ->with('error', 'Wali kelas tidak dapat dihapus karena akunnya tercatat sebagai pelapor SOS.');
        }

        return redirect()->route('admin.wali-kelas.index')->with('status', 'Data wali kelas berhasil dihapus.');
    }

    private function rules(?int $userId = null, ?int $teacherId = null, bool $includePassword = true): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'nip' => ['required', 'string', 'max:30', Rule::unique('wali_kelas', 'nip')->ignore($teacherId)],
            'kelas_dampu' => ['required', 'string', 'max:100'],
            'jurusan' => ['required', 'string', 'max:100'],
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
