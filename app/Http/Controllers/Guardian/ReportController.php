<?php

namespace App\Http\Controllers\Guardian;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Assessment;
use App\Models\ExtracurricularMember;
use App\Models\StudentParent;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $parent = StudentParent::with('children')->where('user_id', $request->user()->id)->first();
        $childIds = $parent?->children->pluck('id') ?? collect();

        $memberships = ExtracurricularMember::with(['student', 'extracurricular.coach', 'extracurricular.schedules'])
            ->whereIn('student_id', $childIds)
            ->where('status', 'aktif')
            ->get();

        $attendances = Attendance::with(['student', 'extracurricular', 'schedule'])
            ->whereIn('student_id', $childIds)
            ->latest('attendance_date')
            ->get();

        $assessments = Assessment::with(['student', 'extracurricular', 'coach'])
            ->whereIn('student_id', $childIds)
            ->latest()
            ->get();

        return view('parent.reports.index', compact('parent', 'memberships', 'attendances', 'assessments'));
    }
}
