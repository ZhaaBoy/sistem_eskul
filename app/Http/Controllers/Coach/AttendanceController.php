<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Coach;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    public function index(Request $request): View
    {
        $coach = $this->currentCoach($request);
        $extracurricularIds = $coach?->extracurriculars->pluck('id') ?? collect();

        $attendances = Attendance::with(['student', 'extracurricular', 'schedule'])
            ->whereIn('extracurricular_id', $extracurricularIds)
            ->when($request->filled('date'), fn ($query) => $query->whereDate('attendance_date', $request->date))
            ->when($request->filled('extracurricular_id'), fn ($query) => $query->where('extracurricular_id', $request->extracurricular_id))
            ->when($request->filled('student'), fn ($query) => $query->whereHas('student', fn ($query) => $query->where('name', 'like', '%'.$request->student.'%')))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->status))
            ->latest('attendance_date')
            ->paginate(10)
            ->withQueryString();

        return view('coach.attendances.index', compact('coach', 'attendances'));
    }

    public function approve(Request $request, Attendance $attendance): RedirectResponse
    {
        $coach = $this->currentCoach($request);

        $this->authorizeAttendance($coach, $attendance);

        $attendance->update([
            'status' => Attendance::APPROVED,
            'approved_at' => now(),
            'rejected_at' => null,
            'rejection_reason' => null,
        ]);

        return back()->with('success', 'Absensi berhasil disetujui.');
    }

    public function reject(Request $request, Attendance $attendance): RedirectResponse
    {
        $coach = $this->currentCoach($request);

        $this->authorizeAttendance($coach, $attendance);

        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:1000'],
        ]);

        $attendance->update([
            'status' => Attendance::REJECTED,
            'rejected_at' => now(),
            'rejection_reason' => $validated['reason'],
        ]);

        return back()->with('success', 'Absensi berhasil ditolak.');
    }

    private function currentCoach(Request $request): ?Coach
    {
        return Coach::with('extracurriculars')->where('user_id', $request->user()->id)->first();
    }

    private function authorizeAttendance(?Coach $coach, Attendance $attendance): void
    {
        abort_if(! $coach, 403, 'Profil pembina belum terhubung dengan akun ini.');

        $ownsExtracurricular = $coach->extracurriculars->contains('id', $attendance->extracurricular_id);

        abort_if(! $ownsExtracurricular, 403, 'Anda hanya bisa mengelola absensi eskul yang dibina.');
    }
}
