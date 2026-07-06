<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Coach;
use App\Models\Extracurricular;
use App\Models\ExtracurricularMember;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $members = ExtracurricularMember::with([
            'student.parent',
            'extracurricular.coach',
            'extracurricular.schedules',
            'student.attendances',
            'student.assessments',
        ])
            ->where('status', 'aktif')
            ->when($request->filled('student_id'), fn ($query) => $query->where('student_id', $request->student_id))
            ->when($request->filled('extracurricular_id'), fn ($query) => $query->where('extracurricular_id', $request->extracurricular_id))
            ->when($request->filled('coach_id'), fn ($query) => $query->whereHas('extracurricular', fn ($query) => $query->where('coach_id', $request->coach_id)))
            ->when($request->filled('class'), fn ($query) => $query->whereHas('student', fn ($query) => $query->where('class', $request->class)))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $filters = [
            'students' => Student::orderBy('name')->get(),
            'extracurriculars' => Extracurricular::orderBy('name')->get(),
            'coaches' => Coach::orderBy('name')->get(),
        ];

        $attendanceSummary = [
            'menunggu' => Attendance::where('status', Attendance::WAITING)->count(),
            'disetujui' => Attendance::where('status', Attendance::APPROVED)->count(),
            'ditolak' => Attendance::where('status', Attendance::REJECTED)->count(),
        ];

        return view('admin.reports.index', compact('members', 'filters', 'attendanceSummary'));
    }
}
