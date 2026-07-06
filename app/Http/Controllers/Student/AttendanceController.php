<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\ExtracurricularMember;
use App\Models\ExtracurricularSchedule;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    public function index(Request $request): View
    {
        $student = $this->currentStudent($request);

        $memberships = $student
            ? ExtracurricularMember::with(['extracurricular.schedules' => fn ($query) => $query->active()])
                ->where('student_id', $student->id)
                ->where('status', 'aktif')
                ->get()
            : collect();

        $attendances = $student
            ? Attendance::with(['extracurricular', 'schedule'])
                ->where('student_id', $student->id)
                ->latest('attendance_date')
                ->paginate(10)
                ->withQueryString()
            : collect();

        $todayAttendances = $student
            ? Attendance::where('student_id', $student->id)
                ->where('attendance_date', now()->toDateString())
                ->get()
                ->keyBy('schedule_id')
            : collect();

        return view('student.attendances.index', compact('student', 'memberships', 'attendances', 'todayAttendances'));
    }

    public function store(Request $request, ExtracurricularSchedule $schedule): RedirectResponse
    {
        $student = $this->currentStudent($request);

        abort_if(! $student, 403, 'Profil siswa belum terhubung dengan akun ini.');

        $member = ExtracurricularMember::where('student_id', $student->id)
            ->where('extracurricular_id', $schedule->extracurricular_id)
            ->where('status', 'aktif')
            ->first();

        abort_if(! $member, 403, 'Absensi hanya bisa dilakukan oleh anggota eskul aktif.');

        $exists = Attendance::where('student_id', $student->id)
            ->where('schedule_id', $schedule->id)
            ->where('attendance_date', now()->toDateString())
            ->exists();

        if ($exists) {
            return back()->with('error', 'Anda sudah mengirim absensi untuk jadwal ini hari ini.');
        }

        Attendance::create([
            'student_id' => $student->id,
            'extracurricular_id' => $schedule->extracurricular_id,
            'schedule_id' => $schedule->id,
            'attendance_date' => now()->toDateString(),
            'status' => Attendance::WAITING,
            'submitted_at' => now(),
        ]);

        return back()->with('success', 'Absensi terkirim dan menunggu approval pembina.');
    }

    private function currentStudent(Request $request): ?Student
    {
        return Student::where('user_id', $request->user()->id)->first();
    }
}
