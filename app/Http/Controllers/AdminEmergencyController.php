<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminEmergencyController extends Controller
{
    private const STATUSES = ['menunggu', 'ditangani', 'selesai'];

    public function index(Request $request): View
    {
        $user = $this->currentAdmin();
        $filters = $request->validate([
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'status' => ['nullable', 'in:all,menunggu,ditangani,selesai'],
        ]);
        $dateFrom = $filters['date_from'] ?? now()->startOfMonth()->toDateString();
        $dateTo = $filters['date_to'] ?? now()->toDateString();

        $alerts = DB::table('sinyal_darurat')
            ->join('siswa', 'siswa.id', '=', 'sinyal_darurat.id_siswa')
            ->join('users as student_users', 'student_users.id', '=', 'siswa.id_user')
            ->select(
                'sinyal_darurat.id',
                'sinyal_darurat.waktu_pemicu',
                'sinyal_darurat.status_respon',
                'siswa.kelas',
                'student_users.name as nama_siswa',
            )
            ->whereDate('sinyal_darurat.waktu_pemicu', '>=', $dateFrom)
            ->whereDate('sinyal_darurat.waktu_pemicu', '<=', $dateTo);

        if (($filters['status'] ?? 'all') !== 'all') {
            $alerts->where('sinyal_darurat.status_respon', $filters['status']);
        }

        $periodAlerts = DB::table('sinyal_darurat')
            ->whereDate('waktu_pemicu', '>=', $dateFrom)
            ->whereDate('waktu_pemicu', '<=', $dateTo);

        return view('admin-monitoring-sos', [
            'user' => $user,
            'filters' => $filters,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'alerts' => $alerts->orderByDesc('sinyal_darurat.waktu_pemicu')
                ->orderByDesc('sinyal_darurat.id')
                ->paginate(15)
                ->withQueryString(),
            'totalAlerts' => (clone $periodAlerts)->count(),
            'waitingAlerts' => (clone $periodAlerts)->where('status_respon', 'menunggu')->count(),
            'handledAlerts' => (clone $periodAlerts)->whereIn('status_respon', ['ditangani', 'selesai'])->count(),
        ]);
    }

    public function updateStatus(Request $request, int $alert): RedirectResponse
    {
        $this->currentAdmin();
        $validated = $request->validate([
            'status_respon' => ['required', 'in:'.implode(',', self::STATUSES)],
        ]);

        DB::transaction(function () use ($alert, $validated): void {
            $record = DB::table('sinyal_darurat')->where('id', $alert)->lockForUpdate()->first();
            abort_unless($record, 404);

            DB::table('sinyal_darurat')
                ->where('id', $record->id)
                ->update([
                    'status_respon' => $validated['status_respon'],
                    'updated_at' => now(),
                ]);
        });

        return redirect()->route('admin.sos.index', $request->only(['date_from', 'date_to', 'status']))
            ->with('status', 'Status sinyal SOS berhasil diperbarui.');
    }

    private function currentAdmin(): User
    {
        $user = Auth::user();
        abort_unless($user instanceof User && $user->role === 'admin', 403);

        return $user;
    }
}
