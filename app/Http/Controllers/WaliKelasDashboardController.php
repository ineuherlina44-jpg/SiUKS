<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\StudentImportService;
use App\Services\WhatsappNumber;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class WaliKelasDashboardController extends Controller
{
    public function index(): View
    {
        [$user, $class] = $this->currentTeacher();
        $records = $this->classRecords($class);
        $monthStart = now()->startOfMonth()->toDateString();
        $monthEnd = now()->endOfMonth()->toDateString();

        $totalStudents = DB::table('siswa')->where('kelas', $class)->count();
        $studentsVisitedThisMonth = (clone $records)
            ->whereBetween('rekam_medis.waktu_pemeriksaan', [$monthStart, $monthEnd])
            ->distinct()
            ->count('rekam_medis.id_siswa');

        $sosThisMonth = DB::table('sinyal_darurat')
            ->join('siswa', 'siswa.id', '=', 'sinyal_darurat.id_siswa')
            ->where('siswa.kelas', $class)
            ->whereBetween('sinyal_darurat.waktu_pemicu', [now()->startOfMonth(), now()->endOfMonth()])
            ->count();

        $chartTotals = array_fill(1, 12, 0);
        (clone $records)
            ->whereBetween('rekam_medis.waktu_pemeriksaan', [now()->startOfYear()->toDateString(), now()->endOfYear()->toDateString()])
            ->select('rekam_medis.waktu_pemeriksaan')
            ->get()
            ->each(function ($record) use (&$chartTotals): void {
                $month = (int) date('n', strtotime($record->waktu_pemeriksaan));
                $chartTotals[$month]++;
            });

        $chartMaximum = max(1, max($chartTotals));
        $monthLabels = [1 => 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $chart = collect(range(1, 12))->map(fn (int $month) => [
            'label' => $monthLabels[$month],
            'total' => $chartTotals[$month],
            'height' => $chartTotals[$month] ? max(8, (int) round($chartTotals[$month] / $chartMaximum * 100)) : 0,
        ]);

        $recentVisits = (clone $records)
            ->orderByDesc('rekam_medis.waktu_pemeriksaan')
            ->limit(8)
            ->get();

        return view('dashboard-wali-kelas', compact(
            'user',
            'class',
            'totalStudents',
            'studentsVisitedThisMonth',
            'sosThisMonth',
            'chart',
            'recentVisits',
        ));
    }

    public function monitoring(Request $request, bool $management = false): View
    {
        [$user, $class] = $this->currentTeacher();
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'in:all,sakit,sehat'],
            'month' => ['nullable', 'date_format:Y-m'],
            'student' => ['nullable', 'integer', 'min:1'],
        ]);
        $month = $filters['month'] ?? now()->format('Y-m');
        $monthStart = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $monthEnd = $monthStart->copy()->endOfMonth();
        $monthlyVisits = fn () => DB::table('rekam_medis as monthly_visit')
            ->whereColumn('monthly_visit.id_siswa', 'siswa.id')
            ->whereBetween('monthly_visit.waktu_pemeriksaan', [$monthStart, $monthEnd]);

        $students = DB::table('siswa')
            ->join('users', 'users.id', '=', 'siswa.id_user')
            ->where('siswa.kelas', $class)
            ->select('siswa.id', 'siswa.nisn', 'siswa.kelas', 'siswa.jenis_kelamin', 'siswa.nomor_whatsapp_orang_tua', 'siswa.riwayat_alergi', 'users.name as nama_siswa')
            ->selectSub(
                DB::table('rekam_medis as latest_visit')
                    ->select('latest_visit.waktu_pemeriksaan')
                    ->whereColumn('latest_visit.id_siswa', 'siswa.id')
                    ->orderByDesc('latest_visit.waktu_pemeriksaan')
                    ->orderByDesc('latest_visit.id')
                    ->limit(1),
                'kunjungan_terakhir',
            )
            ->selectSub($monthlyVisits()->selectRaw('1')->limit(1), 'berkunjung_bulan_ini');

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $students->where(function ($query) use ($search): void {
                $query->where('users.name', 'like', '%' . $search . '%')
                    ->orWhere('siswa.nisn', 'like', '%' . $search . '%');
            });
        }

        if (($filters['status'] ?? 'all') === 'sakit') {
            $students->whereExists($monthlyVisits());
        } elseif (($filters['status'] ?? 'all') === 'sehat') {
            $students->whereNotExists($monthlyVisits());
        }

        $students = $students
            ->orderBy('users.name')
            ->paginate(15)
            ->withQueryString();

        $totalStudents = DB::table('siswa')->where('kelas', $class)->count();
        $sickStudents = DB::table('siswa')
            ->where('kelas', $class)
            ->whereExists($monthlyVisits())
            ->count();
        $visitedStudents = DB::table('siswa')
            ->where('kelas', $class)
            ->whereExists(
                fn ($query) => $query->selectRaw('1')
                    ->from('rekam_medis as any_visit')
                    ->whereColumn('any_visit.id_siswa', 'siswa.id'),
            )
            ->count();
        $sosThisMonth = DB::table('sinyal_darurat')
            ->join('siswa', 'siswa.id', '=', 'sinyal_darurat.id_siswa')
            ->where('siswa.kelas', $class)
            ->whereBetween('sinyal_darurat.waktu_pemicu', [$monthStart, $monthEnd])
            ->count();

        $selectedStudent = null;
        $selectedStudentVisits = collect();
        $selectedStudentSos = 0;
        if (! empty($filters['student'])) {
            $selectedStudent = DB::table('siswa')
                ->join('users', 'users.id', '=', 'siswa.id_user')
                ->where('siswa.id', $filters['student'])
                ->where('siswa.kelas', $class)
                ->select('siswa.id', 'siswa.nisn', 'siswa.kelas', 'siswa.jenis_kelamin', 'siswa.riwayat_alergi', 'users.name as nama_siswa')
                ->first();
            abort_unless($selectedStudent, 404);

            $selectedStudentVisits = DB::table('rekam_medis')
                ->where('id_siswa', $selectedStudent->id)
                ->select('waktu_pemeriksaan', 'status_penyakit', 'keluhan', 'tindakan', 'status')
                ->orderByDesc('waktu_pemeriksaan')
                ->orderByDesc('id')
                ->limit(10)
                ->get();
            $selectedStudentSos = DB::table('sinyal_darurat')
                ->where('id_siswa', $selectedStudent->id)
                ->whereBetween('waktu_pemicu', [$monthStart, $monthEnd])
                ->count();
        }

        return view('wali-kelas-monitoring', compact(
            'user',
            'class',
            'management',
            'month',
            'filters',
            'students',
            'totalStudents',
            'sickStudents',
            'visitedStudents',
            'sosThisMonth',
            'selectedStudent',
            'selectedStudentVisits',
            'selectedStudentSos',
        ));
    }

    public function students(Request $request): View
    {
        return $this->monitoring($request, true);
    }

    public function importStudents(Request $request): RedirectResponse
    {
        [, $class] = $this->currentTeacher();
        $validated = $request->validate([
            'import_type' => ['required', 'in:class,csv'],
            'csv_file'    => ['required_if:import_type,csv', 'nullable', 'file', 'mimes:csv,txt', 'max:2048'],
        ], [
            'csv_file.required_if' => 'Unggah berkas CSV siswa.',
            'csv_file.mimes' => 'Format berkas harus CSV (.csv).',
            'csv_file.max' => 'Ukuran berkas CSV maksimal 2MB.',
        ]);

        try {
            if ($validated['import_type'] === 'class') {
                $result = StudentImportService::importRosterForClass($class);
                return redirect()->route('wali-kelas.siswa.index')
                    ->with('status', "Berhasil mendaftarkan seluruh siswa kelas {$class} ({$result['imported']} siswa baru terdaftar, {$result['updated']} diperbarui).")
                    ->with('import_credentials', $result['credentials']);
            } else {
                $result = StudentImportService::importFromCsv($request->file('csv_file'), $class);
                return redirect()->route('wali-kelas.siswa.index')
                    ->with('status', "Berhasil mengimpor data siswa kelas {$class} dari berkas CSV ({$result['imported']} siswa baru, {$result['updated']} diperbarui).")
                    ->with('import_credentials', $result['credentials']);
            }
        } catch (\Throwable $e) {
            return redirect()->route('wali-kelas.siswa.index')
                ->with('error', 'Gagal mengimpor data siswa: ' . $e->getMessage());
        }
    }

    public function downloadTemplate(Request $request)
    {
        [, $class] = $this->currentTeacher();
        $csv = StudentImportService::generateSampleCsv($class);

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_siswa_' . Str::slug($class) . '.csv"',
        ]);
    }

    public function storeStudent(Request $request): RedirectResponse
    {
        [, $class] = $this->currentTeacher();
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'nisn' => ['required', 'string', 'max:20', 'unique:siswa,nisn'],
            'jenis_kelamin' => ['required', 'in:Laki-laki,Perempuan'],
            'nomor_whatsapp_orang_tua' => ['required', 'string', 'max:25', WhatsappNumber::validationRule()],
            'riwayat_alergi' => ['nullable', 'string', 'max:2000'],
        ], [
            'name.required' => 'Nama siswa wajib diisi.',
            'email.required' => 'Email akun wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar.',
            'password.required' => 'Password awal wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'nisn.required' => 'NISN wajib diisi.',
            'nisn.unique' => 'NISN ini sudah terdaftar.',
            'nomor_whatsapp_orang_tua.required' => 'Nomor WhatsApp orang tua/wali wajib diisi.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'jenis_kelamin.in' => 'Jenis kelamin yang dipilih tidak valid.',
            'riwayat_alergi.max' => 'Riwayat alergi maksimal 2000 karakter.',
        ]);

        DB::transaction(function () use ($validated, $class): void {
            $studentUser = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'role' => 'siswa',
                'password' => $validated['password'],
            ]);

            DB::table('siswa')->insert([
                'id_user' => $studentUser->id,
                'nisn' => $validated['nisn'],
                'kelas' => $class,
                'jenis_kelamin' => $validated['jenis_kelamin'],
                'nomor_whatsapp_orang_tua' => $validated['nomor_whatsapp_orang_tua'],
                'riwayat_alergi' => $validated['riwayat_alergi'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        return redirect()->route('wali-kelas.siswa.index')->with('status', 'Data siswa dan akun login berhasil dibuat.');
    }

    public function updateStudent(Request $request, int $student): RedirectResponse
    {
        [, $class] = $this->currentTeacher();
        $studentRecord = DB::table('siswa')->where('id', $student)->where('kelas', $class)->first();
        abort_unless($studentRecord, 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nisn' => ['required', 'string', 'max:20', Rule::unique('siswa', 'nisn')->ignore($studentRecord->id)],
            'jenis_kelamin' => ['required', 'in:Laki-laki,Perempuan'],
            'nomor_whatsapp_orang_tua' => ['required', 'string', 'max:25', WhatsappNumber::validationRule()],
            'riwayat_alergi' => ['nullable', 'string', 'max:2000'],
        ], [
            'name.required' => 'Nama siswa wajib diisi.',
            'nisn.required' => 'NISN wajib diisi.',
            'nisn.unique' => 'NISN ini sudah terdaftar.',
            'nomor_whatsapp_orang_tua.required' => 'Nomor WhatsApp orang tua/wali wajib diisi.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'jenis_kelamin.in' => 'Jenis kelamin yang dipilih tidak valid.',
            'riwayat_alergi.max' => 'Riwayat alergi maksimal 2000 karakter.',
        ]);

        DB::transaction(function () use ($validated, $studentRecord): void {
            DB::table('users')->where('id', $studentRecord->id_user)->update([
                'name' => $validated['name'],
                'updated_at' => now(),
            ]);
            DB::table('siswa')->where('id', $studentRecord->id)->update([
                'nisn' => $validated['nisn'],
                'jenis_kelamin' => $validated['jenis_kelamin'],
                'nomor_whatsapp_orang_tua' => $validated['nomor_whatsapp_orang_tua'],
                'riwayat_alergi' => $validated['riwayat_alergi'] ?? null,
                'updated_at' => now(),
            ]);
        });

        return redirect()->route('wali-kelas.siswa.index')->with('status', 'Data siswa berhasil diperbarui.');
    }

    public function destroyStudent(int $student): RedirectResponse
    {
        [, $class] = $this->currentTeacher();
        $studentRecord = DB::table('siswa')->where('id', $student)->where('kelas', $class)->first();
        abort_unless($studentRecord, 404);

        $hasMedicalHistory = DB::table('rekam_medis')->where('id_siswa', $studentRecord->id)->exists();
        $hasEmergencyHistory = DB::table('sinyal_darurat')->where('id_siswa', $studentRecord->id)->exists();
        $hasReportedEmergency = DB::table('sinyal_darurat')->where('id_pelapor', $studentRecord->id_user)->exists();

        if ($hasMedicalHistory || $hasEmergencyHistory || $hasReportedEmergency) {
            return redirect()->route('wali-kelas.siswa.index')
                ->with('error', 'Siswa tidak dapat dihapus karena sudah memiliki riwayat UKS atau laporan SOS.');
        }

        DB::transaction(function () use ($studentRecord): void {
            DB::table('siswa')->where('id', $studentRecord->id)->delete();
            User::where('id', $studentRecord->id_user)->where('role', 'siswa')->delete();
        });

        return redirect()->route('wali-kelas.siswa.index')->with('status', 'Data siswa berhasil dihapus.');
    }

    public function downloadRecap(): StreamedResponse|RedirectResponse
    {
        [, $class] = $this->currentTeacher();
        $records = $this->classRecords($class)
            ->orderByDesc('rekam_medis.waktu_pemeriksaan')
            ->get();

        return response()->streamDownload(function () use ($records): void {
            $output = fopen('php://output', 'w');
            fwrite($output, "\xEF\xBB\xBF");
            fputcsv($output, ['Tanggal', 'Nama Siswa', 'NISN', 'Keluhan', 'Tindakan', 'Status']);

            foreach ($records as $record) {
                fputcsv($output, [
                    $record->waktu_pemeriksaan,
                    $this->safeCsvValue($record->nama_siswa),
                    $this->safeCsvValue($record->nisn),
                    $this->safeCsvValue($record->keluhan),
                    $this->safeCsvValue($record->tindakan),
                    $record->status,
                ]);
            }

            fclose($output);
        }, 'rekap-uks-' . Str::slug($class) . '-' . now()->format('Y-m-d') . '.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function currentTeacher(): array
    {
        $user = Auth::user();
        abort_unless($user && $user->role === 'wali_kelas', 403);

        $class = DB::table('wali_kelas')->where('id_user', $user->id)->value('kelas_dampu');
        abort_unless($class, 404);

        return [$user, $class];
    }

    private function classRecords(string $class)
    {
        return DB::table('rekam_medis')
            ->join('siswa', 'siswa.id', '=', 'rekam_medis.id_siswa')
            ->join('users', 'users.id', '=', 'siswa.id_user')
            ->where('siswa.kelas', $class)
            ->select(
                'rekam_medis.id',
                'rekam_medis.waktu_pemeriksaan',
                'rekam_medis.keluhan',
                'rekam_medis.tindakan',
                'rekam_medis.status',
                'siswa.nisn',
                'users.name as nama_siswa',
            );
    }

    private function safeCsvValue(?string $value): string
    {
        $value = (string) $value;

        return preg_match('/^[=+\-@]/', $value) ? "'" . $value : $value;
    }
}