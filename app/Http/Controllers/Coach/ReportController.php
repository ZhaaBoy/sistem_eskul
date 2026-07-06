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

        $selectedPeriod = $request->filled('period') ? $request->period : null;
        $selectedSemester = $request->filled('semester') ? $request->semester : null;

        $availablePeriods = Assessment::whereIn('extracurricular_id', $extracurricularIds)
            ->whereNotNull('period')
            ->distinct()
            ->orderBy('period')
            ->pluck('period');

        $availableSemesters = Assessment::whereIn('extracurricular_id', $extracurricularIds)
            ->whereNotNull('semester')
            ->distinct()
            ->orderBy('semester')
            ->pluck('semester');

        $members = ExtracurricularMember::with(['student', 'extracurricular.coach', 'extracurricular.schedules'])
            ->whereIn('extracurricular_id', $extracurricularIds)
            ->where('status', 'aktif')
            ->get();

        $attendances = Attendance::with(['student', 'extracurricular', 'schedule'])
            ->whereIn('extracurricular_id', $extracurricularIds)
            ->latest('attendance_date')
            ->get();

        $assessments = Assessment::with(['student', 'extracurricular'])
            ->whereIn('extracurricular_id', $extracurricularIds)
            ->when($selectedPeriod, fn ($query) => $query->where('period', $selectedPeriod))
            ->when($selectedSemester, fn ($query) => $query->where('semester', $selectedSemester))
            ->latest()
            ->get();

        return view($view, compact(
            'coach',
            'members',
            'attendances',
            'assessments',
            'selectedPeriod',
            'selectedSemester',
            'availablePeriods',
            'availableSemesters'
        ));
    }
}
