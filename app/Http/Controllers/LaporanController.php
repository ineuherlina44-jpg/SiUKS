<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class LaporanController extends Controller
{
    private const REPORTS = [
        'patients' => 'Laporan Pasien',
        'stock' => 'Laporan Stok Obat',
        'medical' => 'Laporan Rekam Medis',
        'visits' => 'Laporan Kunjungan',
    ];

    public function index(Request $request): View
    {
        $this->authorizePmr($request);

        return view('pmr-laporan', [
            'user' => $request->user(),
            'reports' => self::REPORTS,
        ]);
    }

    public function download(Request $request, string $type): Response
    {
        $this->authorizePmr($request);
        abort_unless(array_key_exists($type, self::REPORTS), 404);

        $validated = $request->validate([
            'date_from' => ['nullable', 'date_format:Y-m-d'],
            'date_to' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:date_from'],
        ], [
            'date_to.after_or_equal' => 'Tanggal akhir harus sama dengan atau setelah tanggal awal.',
        ]);
        $dateFrom = $validated['date_from'] ?? null;
        $dateTo = $validated['date_to'] ?? null;
        [$columns, $rows] = $this->reportRows($type, $dateFrom, $dateTo);
        $period = ($dateFrom ?: 'awal data').' s.d. '.($dateTo ?: 'hari ini');
        $pdf = $this->makePdf(self::REPORTS[$type], $period, $columns, $rows);
        $filename = 'siuks-'.Str::slug(self::REPORTS[$type]).'-'.now()->format('Y-m-d').'.pdf';

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'Content-Length' => (string) strlen($pdf),
        ]);
    }

    public function adminIndex(Request $request): View
    {
        $this->authorizeAdmin($request);
        [$dateFrom, $dateTo] = $this->adminReportPeriod($request);
        $visits = $this->adminVisitQuery($dateFrom, $dateTo);

        $monthLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $monthlyChart = collect(range(1, 12))->map(function (int $month) use ($dateFrom, $monthLabels): array {
            $monthStart = Carbon::parse($dateFrom)->startOfYear()->month($month)->startOfMonth();
            $monthEnd = $monthStart->copy()->endOfMonth();
            $monthlyTotals = DB::table('rekam_medis')
                ->whereBetween('waktu_pemeriksaan', [$monthStart, $monthEnd])
                ->selectRaw('COUNT(id) as total_kunjungan, COUNT(DISTINCT id_siswa) as siswa_unik')
                ->first();
            return [
                'label' => $monthLabels[$month - 1],
                'visits' => (int) $monthlyTotals->total_kunjungan,
                'students' => (int) $monthlyTotals->siswa_unik,
            ];
        });

        $complaints = (clone $visits)
            ->selectRaw("COALESCE(NULLIF(TRIM(rekam_medis.status_penyakit), ''), NULLIF(TRIM(rekam_medis.keluhan), ''), 'Tidak dicatat') as nama, COUNT(rekam_medis.id) as total")
            ->groupBy('nama')
            ->orderByDesc('total')
            ->orderBy('nama')
            ->get();
        $topComplaints = $complaints->take(5)->values();
        $otherComplaints = $complaints->skip(5)->sum('total');
        if ($otherComplaints > 0) {
            $topComplaints->push((object) ['nama' => 'Lainnya', 'total' => $otherComplaints]);
        }
        $totalVisits = (int) $visits->count();
        $complaintColors = ['#287fe0', '#24b9cc', '#ed777a', '#f3c52e', '#9b69d6', '#aab9cc'];
        $complaintSlices = $topComplaints->map(function ($complaint, int $index) use ($totalVisits, $complaintColors): array {
            return [
                'name' => $complaint->nama,
                'total' => (int) $complaint->total,
                'percentage' => $totalVisits === 0 ? 0 : (int) round(($complaint->total / $totalVisits) * 100),
                'color' => $complaintColors[$index],
            ];
        });
        $angle = 0;
        $gradientStops = [];
        foreach ($complaintSlices as $slice) {
            $nextAngle = $angle + ($totalVisits === 0 ? 0 : ($slice['total'] / $totalVisits) * 360);
            $gradientStops[] = $slice['color'].' '.$angle.'deg '.$nextAngle.'deg';
            $angle = $nextAngle;
        }
        $maxMonthlyValue = max(1, (int) $monthlyChart->max('visits'));
        $periodDays = Carbon::parse($dateFrom)->diffInDays(Carbon::parse($dateTo)) + 1;

        return view('admin-laporan', [
            'user' => $request->user(),
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'monthlyChart' => $monthlyChart,
            'maxMonthlyValue' => $maxMonthlyValue,
            'totalVisits' => $totalVisits,
            'uniqueStudents' => (int) $visits->distinct('rekam_medis.id_siswa')->count('rekam_medis.id_siswa'),
            'dailyAverage' => round($totalVisits / max(1, $periodDays), 1),
            'complaintSlices' => $complaintSlices,
            'complaintGradient' => $gradientStops === [] ? '#e8eef4' : implode(', ', $gradientStops),
            'visits' => (clone $visits)
                ->select(
                    'rekam_medis.waktu_pemeriksaan',
                    'rekam_medis.keluhan',
                    'rekam_medis.status_penyakit',
                    'siswa_users.name as nama_siswa',
                    'siswa.kelas'
                )
                ->orderByDesc('rekam_medis.waktu_pemeriksaan')
                ->paginate(15)
                ->withQueryString(),
        ]);
    }

    public function adminDownload(Request $request): Response
    {
        $this->authorizeAdmin($request);
        [$dateFrom, $dateTo] = $this->adminReportPeriod($request);
        $rows = $this->adminVisitQuery($dateFrom, $dateTo)
            ->select(
                'rekam_medis.waktu_pemeriksaan',
                'rekam_medis.keluhan',
                'rekam_medis.status_penyakit',
                'siswa_users.name as nama_siswa',
                'siswa.kelas'
            )
            ->orderByDesc('rekam_medis.waktu_pemeriksaan')
            ->get()
            ->map(fn ($visit) => [
                Carbon::parse($visit->waktu_pemeriksaan)->format('d-m-Y H:i'),
                $visit->nama_siswa,
                $visit->kelas,
                $visit->status_penyakit ?: ($visit->keluhan ?: 'Tidak dicatat'),
            ])
            ->all();
        $period = Carbon::parse($dateFrom)->format('d-m-Y').' s.d. '.Carbon::parse($dateTo)->format('d-m-Y');
        $pdf = $this->makePdf('Laporan Kunjungan UKS', $period, ['Tanggal', 'Nama siswa', 'Kelas', 'Keluhan / diagnosis'], $rows);
        $filename = 'siuks-laporan-kunjungan-'.Carbon::parse($dateFrom)->format('Ymd').'-'.Carbon::parse($dateTo)->format('Ymd').'.pdf';

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'Content-Length' => (string) strlen($pdf),
        ]);
    }

    public function adminMedicalRecords(Request $request): View
    {
        $this->authorizeAdmin($request);
        $filters = $this->medicalRecordFilters($request);

        $records = $this->adminMedicalRecordQuery($filters)
            ->orderByDesc('rekam_medis.waktu_pemeriksaan')
            ->orderByDesc('rekam_medis.id')
            ->paginate(15)
            ->withQueryString();
        $medicinesByRecord = $this->medicalRecordMedicines($records->getCollection()->pluck('id')->all());
        $records->getCollection()->transform(function ($record) use ($medicinesByRecord) {
            $record->medicines = $medicinesByRecord->get($record->id, collect());

            return $record;
        });

        return view('admin-rekam-medis', [
            'user' => $request->user(),
            'filters' => $filters,
            'records' => $records,
            'types' => DB::table('rekam_medis')
                ->whereNotNull('status_penyakit')
                ->where('status_penyakit', '<>', '')
                ->distinct()
                ->orderBy('status_penyakit')
                ->pluck('status_penyakit'),
        ]);
    }

    public function adminMedicalRecordsDownload(Request $request): Response
    {
        $this->authorizeAdmin($request);
        $filters = $this->medicalRecordFilters($request);

        $records = $this->adminMedicalRecordQuery($filters)
            ->orderByDesc('rekam_medis.waktu_pemeriksaan')
            ->orderByDesc('rekam_medis.id')
            ->get();
        $medicinesByRecord = $this->medicalRecordMedicines($records->pluck('id')->all());
        $rows = $records->map(fn ($record) => [
            Carbon::parse($record->waktu_pemeriksaan)->format('d-m-Y H:i'),
            $record->nama_siswa,
            $record->status_penyakit ?: 'Tidak dicatat',
            $record->kelas,
            $record->keluhan ?: 'Tidak dicatat',
            $medicinesByRecord->get($record->id, collect())->pluck('nama_obat')->implode(', ') ?: 'Tidak ada obat',
            $this->statusLabel($record->status),
        ])->all();

        $period = match (true) {
            isset($filters['date_from'], $filters['date_to']) => Carbon::parse($filters['date_from'])->format('d-m-Y').' s.d. '.Carbon::parse($filters['date_to'])->format('d-m-Y'),
            isset($filters['date_from']) => 'Sejak '.Carbon::parse($filters['date_from'])->format('d-m-Y'),
            isset($filters['date_to']) => 'Sampai '.Carbon::parse($filters['date_to'])->format('d-m-Y'),
            default => 'Semua tanggal',
        };
        $pdf = $this->makePdf('Riwayat Rekam Medis', $period, [
            'Tanggal',
            'Nama siswa',
            'Jenis pemeriksaan',
            'Kelas',
            'Keluhan',
            'Obat',
            'Status',
        ], $rows);
        $filename = 'siuks-rekam-medis-'.($filters['date_from'] ?? 'awal').'-'.($filters['date_to'] ?? 'akhir').'.pdf';

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'Content-Length' => (string) strlen($pdf),
        ]);
    }

    private function medicalRecordFilters(Request $request): array
    {
        return $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'type' => ['nullable', 'string', 'max:150'],
            'status' => ['nullable', 'in:dirawat,diizinkan_pulang,rujukan'],
            'date_from' => ['nullable', 'date_format:Y-m-d'],
            'date_to' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:date_from'],
        ], [
            'date_to.after_or_equal' => 'Tanggal akhir harus sama dengan atau setelah tanggal awal.',
        ]);
    }

    private function adminMedicalRecordQuery(array $filters): \Illuminate\Database\Query\Builder
    {
        $search = trim($filters['search'] ?? '');

        return DB::table('rekam_medis')
            ->join('siswa', 'siswa.id', '=', 'rekam_medis.id_siswa')
            ->join('users as siswa_users', 'siswa_users.id', '=', 'siswa.id_user')
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('siswa_users.name', 'like', '%'.$search.'%')
                        ->orWhere('siswa.nisn', 'like', '%'.$search.'%')
                        ->orWhere('rekam_medis.keluhan', 'like', '%'.$search.'%')
                        ->orWhere('rekam_medis.status_penyakit', 'like', '%'.$search.'%')
                        ->orWhere('rekam_medis.tindakan', 'like', '%'.$search.'%');
                });
            })
            ->when(! empty($filters['type']), fn ($query) => $query->where('rekam_medis.status_penyakit', $filters['type']))
            ->when(! empty($filters['status']), fn ($query) => $query->where('rekam_medis.status', $filters['status']))
            ->when(! empty($filters['date_from']), fn ($query) => $query->whereDate('rekam_medis.waktu_pemeriksaan', '>=', $filters['date_from']))
            ->when(! empty($filters['date_to']), fn ($query) => $query->whereDate('rekam_medis.waktu_pemeriksaan', '<=', $filters['date_to']))
            ->select(
                'rekam_medis.id',
                'rekam_medis.waktu_pemeriksaan',
                'rekam_medis.status_penyakit',
                'rekam_medis.keluhan',
                'rekam_medis.tindakan',
                'rekam_medis.status',
                'siswa.kelas',
                'siswa.nisn',
                'siswa_users.name as nama_siswa',
            );
    }

    private function medicalRecordMedicines(array $recordIds): \Illuminate\Support\Collection
    {
        if ($recordIds === []) {
            return collect();
        }

        return DB::table('detail_pemberian_obat')
            ->join('obat', 'obat.id', '=', 'detail_pemberian_obat.id_obat')
            ->whereIn('detail_pemberian_obat.id_rekam_medis', $recordIds)
            ->orderBy('obat.nama_obat')
            ->get([
                'detail_pemberian_obat.id_rekam_medis',
                'detail_pemberian_obat.jumlah_dosis',
                'detail_pemberian_obat.aturan_pakai',
                'obat.nama_obat',
            ])
            ->groupBy('id_rekam_medis');
    }

    private function adminReportPeriod(Request $request): array
    {
        $validated = $request->validate([
            'date_from' => ['nullable', 'date_format:Y-m-d'],
            'date_to' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:date_from'],
        ], [
            'date_to.after_or_equal' => 'Tanggal akhir harus sama dengan atau setelah tanggal awal.',
        ]);

        $dateTo = $validated['date_to'] ?? now()->toDateString();
        $dateFrom = $validated['date_from'] ?? Carbon::parse($dateTo)->startOfMonth()->toDateString();

        if (! isset($validated['date_to']) && $dateFrom > $dateTo) {
            $dateTo = $dateFrom;
        }

        return [$dateFrom, $dateTo];
    }

    private function adminVisitQuery(string $dateFrom, string $dateTo): \Illuminate\Database\Query\Builder
    {
        return DB::table('rekam_medis')
            ->join('siswa', 'siswa.id', '=', 'rekam_medis.id_siswa')
            ->join('users as siswa_users', 'siswa_users.id', '=', 'siswa.id_user')
            ->whereBetween('rekam_medis.waktu_pemeriksaan', [
                Carbon::parse($dateFrom)->startOfDay(),
                Carbon::parse($dateTo)->endOfDay(),
            ]);
    }

    private function reportRows(string $type, ?string $dateFrom, ?string $dateTo): array
    {
        if ($type === 'stock') {
            $medicines = DB::table('obat')
                ->orderBy('nama_obat')
                ->get(['kode_obat', 'nama_obat', 'kategori', 'jenis', 'stok_tersedia', 'satuan']);

            return [
                ['Kode', 'Nama obat', 'Kategori / jenis', 'Stok', 'Status'],
                $medicines->map(fn ($medicine) => [
                    $medicine->kode_obat,
                    $medicine->nama_obat,
                    $medicine->kategori.' / '.$medicine->jenis,
                    $medicine->stok_tersedia.' '.$medicine->satuan,
                    $medicine->stok_tersedia <= 5 ? 'Kritis' : 'Aman',
                ])->all(),
            ];
        }

        $records = DB::table('rekam_medis')
            ->join('siswa', 'siswa.id', '=', 'rekam_medis.id_siswa')
            ->join('users as siswa_users', 'siswa_users.id', '=', 'siswa.id_user')
            ->when($dateFrom, fn ($query) => $query->whereDate('rekam_medis.waktu_pemeriksaan', '>=', $dateFrom))
            ->when($dateTo, fn ($query) => $query->whereDate('rekam_medis.waktu_pemeriksaan', '<=', $dateTo));

        if ($type === 'patients') {
            $patients = (clone $records)
                ->select('siswa_users.name as nama_siswa', 'siswa.nisn', 'siswa.kelas')
                ->selectRaw('COUNT(rekam_medis.id) as total_kunjungan, MAX(rekam_medis.waktu_pemeriksaan) as kunjungan_terakhir')
                ->groupBy('siswa.id', 'siswa_users.name', 'siswa.nisn', 'siswa.kelas')
                ->orderBy('siswa_users.name')
                ->get();

            return [
                ['Nama siswa', 'NISN', 'Kelas', 'Kunjungan', 'Terakhir berkunjung'],
                $patients->map(fn ($patient) => [
                    $patient->nama_siswa,
                    $patient->nisn,
                    $patient->kelas,
                    $patient->total_kunjungan,
                    $patient->kunjungan_terakhir,
                ])->all(),
            ];
        }

        if ($type === 'visits') {
            $visits = (clone $records)
                ->selectRaw('DATE(rekam_medis.waktu_pemeriksaan) as tanggal, COUNT(rekam_medis.id) as total_kunjungan, COUNT(DISTINCT rekam_medis.id_siswa) as total_siswa')
                ->groupByRaw('DATE(rekam_medis.waktu_pemeriksaan)')
                ->orderByDesc('tanggal')
                ->get();

            return [
                ['Tanggal', 'Total kunjungan', 'Siswa unik'],
                $visits->map(fn ($visit) => [
                    $visit->tanggal,
                    $visit->total_kunjungan,
                    $visit->total_siswa,
                ])->all(),
            ];
        }

        $medicalRecords = (clone $records)
            ->leftJoin('petugas_pmr', 'petugas_pmr.id', '=', 'rekam_medis.id_petugas')
            ->leftJoin('users as staff_users', 'staff_users.id', '=', 'petugas_pmr.id_user')
            ->select(
                'rekam_medis.waktu_pemeriksaan',
                'siswa_users.name as nama_siswa',
                'siswa.kelas',
                'rekam_medis.status_penyakit',
                'rekam_medis.keluhan',
                'rekam_medis.tindakan',
                'rekam_medis.status',
                'staff_users.name as nama_petugas',
            )
            ->orderByDesc('rekam_medis.waktu_pemeriksaan')
            ->get();

        return [
            ['Tanggal', 'Siswa / kelas', 'Keluhan / diagnosis', 'Penanganan', 'Status'],
            $medicalRecords->map(fn ($record) => [
                $record->waktu_pemeriksaan,
                $record->nama_siswa.' / '.$record->kelas,
                ($record->status_penyakit ? $record->status_penyakit.': ' : '').($record->keluhan ?: 'Tidak dicatat'),
                $record->tindakan ?: 'Tidak dicatat',
                $this->statusLabel($record->status),
            ])->all(),
        ];
    }

    private function makePdf(string $title, string $period, array $columns, array $rows): string
    {
        $pageRows = array_chunk($rows, 42);
        if ($pageRows === []) {
            $pageRows = [[]];
        }

        $objects = [
            1 => '<< /Type /Catalog /Pages 2 0 R >>',
            3 => '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>',
            4 => '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold >>',
        ];
        $pageObjectIds = [];

        foreach ($pageRows as $pageIndex => $pageRowsChunk) {
            $pageObjectId = 5 + ($pageIndex * 2);
            $contentObjectId = $pageObjectId + 1;
            $pageObjectIds[] = $pageObjectId;
            $content = $this->pdfPageContent($title, $period, $columns, $pageRowsChunk, $pageIndex + 1, count($pageRows));
            $objects[$pageObjectId] = '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 842 595] /Resources << /Font << /F1 3 0 R /F2 4 0 R >> >> /Contents '.$contentObjectId.' 0 R >>';
            $objects[$contentObjectId] = '<< /Length '.strlen($content)." >>\nstream\n".$content."\nendstream";
        }

        $kids = implode(' ', array_map(fn (int $id) => $id.' 0 R', $pageObjectIds));
        $objects[2] = '<< /Type /Pages /Kids ['.$kids.'] /Count '.count($pageObjectIds).' >>';
        ksort($objects);

        $pdf = "%PDF-1.4\n%\xE2\xE3\xCF\xD3\n";
        $offsets = [0];
        foreach ($objects as $id => $object) {
            $offsets[$id] = strlen($pdf);
            $pdf .= $id." 0 obj\n".$object."\nendobj\n";
        }

        $xrefOffset = strlen($pdf);
        $objectCount = max(array_keys($objects)) + 1;
        $pdf .= "xref\n0 ".$objectCount."\n0000000000 65535 f \n";
        for ($id = 1; $id < $objectCount; $id++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$id] ?? 0);
        }
        $pdf .= "trailer\n<< /Size ".$objectCount." /Root 1 0 R >>\nstartxref\n".$xrefOffset."\n%%EOF";

        return $pdf;
    }

    private function pdfPageContent(string $title, string $period, array $columns, array $rows, int $page, int $totalPages): string
    {
        $content = "0.13 0.29 0.42 rg\nBT /F2 18 Tf 42 548 Td (SiUKS - ".$this->pdfText($title).") Tj ET\n";
        $content .= "0.38 0.45 0.52 rg\nBT /F1 9 Tf 42 530 Td (Periode: ".$this->pdfText($period)."    Dicetak: ".now()->format('d-m-Y H:i').") Tj ET\n";
        $columnCount = max(1, count($columns));
        $usableWidth = 758;
        $columnWidth = $usableWidth / $columnCount;
        $tableTop = 504;
        $content .= "0.91 0.95 0.98 rg\n42 ".($tableTop - 24)." ".$usableWidth." 22 re f\n";
        $content .= "0.28 0.38 0.47 rg\n";

        foreach ($columns as $index => $column) {
            $x = 47 + ($index * $columnWidth);
            $content .= "BT /F2 8 Tf ".$this->pdfNumber($x).' '.($tableTop - 16).' Td ('.$this->pdfText($column, max(8, (int) ($columnWidth / 4.6))).") Tj ET\n";
        }

        $rowTop = $tableTop - 32;
        $lineHeight = 10;
        foreach ($rows as $rowIndex => $row) {
            $y = $rowTop - ($rowIndex * $lineHeight);
            if ($y < 38) {
                break;
            }
            if ($rowIndex % 2 === 1) {
                $content .= "0.975 0.982 0.989 rg\n42 ".($y - 3)." ".$usableWidth." 10 re f\n";
            }
            $content .= "0.22 0.29 0.36 rg\n";
            foreach (array_values($row) as $columnIndex => $value) {
                $x = 47 + ($columnIndex * $columnWidth);
                $characters = max(8, (int) ($columnWidth / 4.6));
                $content .= "BT /F1 7 Tf ".$this->pdfNumber($x).' '.$this->pdfNumber($y).' Td ('.$this->pdfText((string) $value, $characters).") Tj ET\n";
            }
        }

        $content .= "0.55 0.62 0.68 rg\nBT /F1 8 Tf 42 22 Td (Halaman ".$page." dari ".$totalPages."    -    Dokumen SiUKS) Tj ET\n";

        return $content;
    }

    private function pdfText(string $value, int $limit = 110): string
    {
        $value = Str::ascii($value);
        $value = preg_replace('/[\r\n\t]+/', ' ', $value) ?? '';
        $value = mb_substr($value, 0, $limit);

        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $value);
    }

    private function pdfNumber(float|int $value): string
    {
        return rtrim(rtrim(number_format((float) $value, 2, '.', ''), '0'), '.');
    }

    private function statusLabel(string $status): string
    {
        return match ($status) {
            'dirawat' => 'Sedang dirawat',
            'diizinkan_pulang' => 'Diizinkan pulang',
            'rujukan' => 'Rujukan',
            default => ucfirst($status),
        };
    }

    private function authorizePmr(Request $request): void
    {
        abort_unless($request->user() && $request->user()->role === 'petugas_pmr', 403);
    }

    private function authorizeAdmin(Request $request): void
    {
        abort_unless($request->user() && $request->user()->role === 'admin', 403);
    }
}
// isi kode yang ada di file ini adalah controller untuk menangani laporan di aplikasi SiUKS. Controller ini memiliki beberapa metode untuk menampilkan halaman laporan, mengunduh laporan dalam format PDF, dan memfilter data rekam medis berdasarkan kriteria tertentu. Metode-metode ini juga memastikan bahwa hanya pengguna dengan peran yang sesuai (petugas PMR atau admin) yang dapat mengakses fitur-fitur tersebut.
