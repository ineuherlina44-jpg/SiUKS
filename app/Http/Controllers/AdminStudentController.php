<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\StudentImportService;
use Illuminate\Support\Str;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminStudentController extends Controller
{
    public function index(Request $request): View
    {
        $user = $this->currentAdmin();
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'kelas' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'in:all,sakit,sehat'],
        ]);
        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();
        $monthlyVisits = fn () => DB::table('rekam_medis as monthly_visit')
            ->whereColumn('monthly_visit.id_siswa', 'siswa.id')
            ->whereBetween('monthly_visit.waktu_pemeriksaan', [$monthStart, $monthEnd]);

        $students = DB::table('siswa')
            ->join('users', 'users.id', '=', 'siswa.id_user')
            ->select('siswa.id', 'siswa.id_user', 'siswa.nisn', 'siswa.kelas', 'siswa.jenis_kelamin', 'siswa.riwayat_alergi', 'users.name as nama_siswa', 'users.email')
            ->selectSub($monthlyVisits()->selectRaw('1')->limit(1), 'berkunjung_bulan_ini');

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $students->where(function ($query) use ($search): void {
                $query->where('users.name', 'like', '%' . $search . '%')
                    ->orWhere('siswa.nisn', 'like', '%' . $search . '%');
            });
        }

        if (! empty($filters['kelas'])) {
            $students->where('siswa.kelas', $filters['kelas']);
        }

        if (($filters['status'] ?? 'all') === 'sakit') {
            $students->whereExists($monthlyVisits());
        } elseif (($filters['status'] ?? 'all') === 'sehat') {
            $students->whereNotExists($monthlyVisits());
        }

        $students = $students
            ->orderBy('siswa.kelas')
            ->orderBy('users.name')
            ->paginate(15)
            ->withQueryString();

        $classes = collect(
            array_merge(
                DB::table('siswa')->select('kelas')->distinct()->orderBy('kelas')->pluck('kelas')->all(),
                StudentImportService::getPresetClasses()
            )
        )
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->all();

        return view('admin-data-siswa', [
            'user' => $user,
            'filters' => $filters,
            'students' => $students,
            'classes' => $classes,
            'totalStudents' => DB::table('siswa')->count(),
            'sickStudents' => DB::table('siswa')->whereExists($monthlyVisits())->count(),
            'totalClasses' => DB::table('siswa')->distinct()->count('kelas'),
            'presetClasses' => StudentImportService::getPresetClasses(),
        ]);
    }

    public function import(Request $request): RedirectResponse
    {
        $this->currentAdmin();
        $validated = $request->validate([
            'import_type' => ['required', 'in:class,csv'],
            'kelas'       => ['required_if:import_type,class', 'nullable', 'string', 'max:100'],
            'csv_file'    => ['required_if:import_type,csv', 'nullable', 'file', 'mimes:csv,txt', 'max:2048'],
        ], [
            'kelas.required_if' => 'Pilih kelas yang akan diimpor.',
            'csv_file.required_if' => 'Unggah berkas CSV siswa.',
            'csv_file.mimes' => 'Format berkas harus CSV (.csv).',
            'csv_file.max' => 'Ukuran berkas CSV maksimal 2MB.',
        ]);

        try {
            if ($validated['import_type'] === 'class') {
                $result = StudentImportService::importRosterForClass($validated['kelas']);
                return redirect()->route('admin.siswa.index')
                    ->with('status', "Berhasil mendaftarkan seluruh siswa kelas {$result['class']} ({$result['imported']} siswa baru terdaftar, {$result['updated']} diperbarui).")
                    ->with('import_credentials', $result['credentials']);
            } else {
                $result = StudentImportService::importFromCsv($request->file('csv_file'), $request->input('kelas'));
                return redirect()->route('admin.siswa.index')
                    ->with('status', "Berhasil mengimpor data siswa dari berkas CSV ({$result['imported']} siswa baru, {$result['updated']} diperbarui).")
                    ->with('import_credentials', $result['credentials']);
            }
        } catch (\Throwable $e) {
            return redirect()->route('admin.siswa.index')
                ->with('error', 'Gagal mengimpor data siswa: ' . $e->getMessage());
        }
    }

    public function downloadTemplate(Request $request)
    {
        $this->currentAdmin();
        $kelas = $request->query('kelas', 'RPL 1');
        $csv = StudentImportService::generateSampleCsv($kelas);

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_siswa_' . Str::slug($kelas) . '.csv"',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->currentAdmin();
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'nisn' => ['required', 'string', 'max:20', 'unique:siswa,nisn'],
            'kelas' => ['required', 'string', 'max:100'],
            'jenis_kelamin' => ['required', 'in:Laki-laki,Perempuan'],
            'riwayat_alergi' => ['nullable', 'string', 'max:2000'],
        ], [
            'email.unique' => 'Email ini sudah terdaftar.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'nisn.unique' => 'NISN ini sudah terdaftar.',
        ]);

        DB::transaction(function () use ($validated): void {
            $studentUser = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'role' => 'siswa',
                'password' => $validated['password'],
            ]);

            DB::table('siswa')->insert([
                'id_user' => $studentUser->id,
                'nisn' => $validated['nisn'],
                'kelas' => $validated['kelas'],
                'jenis_kelamin' => $validated['jenis_kelamin'],
                'riwayat_alergi' => $validated['riwayat_alergi'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        return redirect()->route('admin.siswa.index')->with('status', 'Data siswa dan akun login berhasil dibuat.');
    }

    public function update(Request $request, int $student): RedirectResponse
    {
        $this->currentAdmin();
        $studentRecord = DB::table('siswa')->where('id', $student)->first();
        abort_unless($studentRecord, 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($studentRecord->id_user)],
            'nisn' => ['required', 'string', 'max:20', Rule::unique('siswa', 'nisn')->ignore($studentRecord->id)],
            'kelas' => ['required', 'string', 'max:100'],
            'jenis_kelamin' => ['required', 'in:Laki-laki,Perempuan'],
            'riwayat_alergi' => ['nullable', 'string', 'max:2000'],
        ], [
            'email.unique' => 'Email ini sudah digunakan akun lain.',
            'nisn.unique' => 'NISN ini sudah terdaftar.',
        ]);

        DB::transaction(function () use ($validated, $studentRecord): void {
            DB::table('users')->where('id', $studentRecord->id_user)->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'updated_at' => now(),
            ]);
            DB::table('siswa')->where('id', $studentRecord->id)->update([
                'nisn' => $validated['nisn'],
                'kelas' => $validated['kelas'],
                'jenis_kelamin' => $validated['jenis_kelamin'],
                'riwayat_alergi' => $validated['riwayat_alergi'] ?? null,
                'updated_at' => now(),
            ]);
        });

        return redirect()->route('admin.siswa.index')->with('status', 'Data siswa berhasil diperbarui.');
    }

    public function destroy(int $student): RedirectResponse
    {
        $this->currentAdmin();
        $deleted = DB::transaction(function () use ($student): bool {
            $studentRecord = DB::table('siswa')->where('id', $student)->lockForUpdate()->first();
            abort_unless($studentRecord, 404);

            $hasMedicalHistory = DB::table('rekam_medis')->where('id_siswa', $studentRecord->id)->exists();
            $hasEmergencyHistory = DB::table('sinyal_darurat')->where('id_siswa', $studentRecord->id)->exists();
            $hasReportedEmergency = DB::table('sinyal_darurat')->where('id_pelapor', $studentRecord->id_user)->exists();

            if ($hasMedicalHistory || $hasEmergencyHistory || $hasReportedEmergency) {
                return false;
            }

            DB::table('siswa')->where('id', $studentRecord->id)->delete();
            User::where('id', $studentRecord->id_user)->where('role', 'siswa')->delete();

            return true;
        });

        if (! $deleted) {
            return redirect()->route('admin.siswa.index')
                ->with('error', 'Siswa tidak dapat dihapus karena sudah memiliki riwayat UKS atau laporan SOS.');
        }

        return redirect()->route('admin.siswa.index')->with('status', 'Data siswa berhasil dihapus.');
    }

    private function currentAdmin(): User
    {
        $user = Auth::user();
        abort_unless($user && $user->role === 'admin', 403);

        return $user;
    }
}
