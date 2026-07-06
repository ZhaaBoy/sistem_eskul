<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Assessment;
use App\Models\ExtracurricularMember;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $student = Student::where('user_id', $request->user()->id)->first();

        $memberships = $student
            ? ExtracurricularMember::with(['extracurricular.coach', 'extracurricular.schedules'])
                ->where('student_id', $student->id)
                ->where('status', 'aktif')
                ->get()
            : collect();

        $attendances = $student
            ? Attendance::with(['extracurricular', 'schedule'])
                ->where('student_id', $student->id)
                ->latest('attendance_date')
                ->get()
            : collect();

        $assessments = $student
            ? Assessment::with(['extracurricular', 'coach'])
                ->where('student_id', $student->id)
                ->latest()
                ->get()
            : collect();

        return view('student.reports.index', compact('student', 'memberships', 'attendances', 'assessments'));
    }
}
