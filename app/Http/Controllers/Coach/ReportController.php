<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Attendance;
use App\Models\Coach;
use App\Models\ExtracurricularMember;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        return $this->reportView($request, 'coach.reports.index');
    }

    public function print(Request $request): View
    {
        return $this->reportView($request, 'coach.reports.print');
    }

    private function reportView(Request $request, string $view): View
    {
        $coach = Coach::with('extracurriculars.schedules')->where('user_id', $request->user()->id)->first();
        $extracurricularIds = $coach?->extracurriculars->pluck('id') ?? collect();

        $selectedExtracurricular = $request->filled('extracurricular_id')
            ? (int) $request->extracurricular_id
            : null;

        abort_if($selectedExtracurricular && ! $extracurricularIds->contains($selectedExtracurricular), 403);

        $members = ExtracurricularMember::with(['student', 'extracurricular.coach', 'extracurricular.schedules'])
            ->whereIn('extracurricular_id', $extracurricularIds)
            ->when($selectedExtracurricular, fn ($query) => $query->where('extracurricular_id', $selectedExtracurricular))
            ->where('status', 'aktif')
            ->get();

        $attendances = Attendance::with(['student', 'extracurricular', 'schedule'])
            ->whereIn('extracurricular_id', $extracurricularIds)
            ->when($selectedExtracurricular, fn ($query) => $query->where('extracurricular_id', $selectedExtracurricular))
            ->latest('attendance_date')
            ->get();

        $assessments = Assessment::with(['student', 'extracurricular'])
            ->whereIn('extracurricular_id', $extracurricularIds)
            ->when($selectedExtracurricular, fn ($query) => $query->where('extracurricular_id', $selectedExtracurricular))
            ->latest()
            ->get();

        return view($view, compact('coach', 'members', 'attendances', 'assessments', 'selectedExtracurricular'));
    }
}
