<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\Attendance;
use App\Models\Coach;
use App\Models\Extracurricular;
use App\Models\ExtracurricularMember;
use App\Models\ExtracurricularRegistration;
use App\Models\Student;
use App\Models\StudentParent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): RedirectResponse
    {
        return match ($request->user()->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'siswa' => redirect()->route('student.dashboard'),
            'orang_tua' => redirect()->route('parent.dashboard'),
            'pembina' => redirect()->route('coach.dashboard'),
            default => abort(403),
        };
    }

    public function admin(): View
    {
        $stats = [
            'total_siswa' => Student::count(),
            'total_pembina' => Coach::count(),
            'total_orang_tua' => StudentParent::count(),
            'total_eskul' => Extracurricular::count(),
            'pendaftaran_menunggu' => ExtracurricularRegistration::whereIn('status', [
                ExtracurricularRegistration::WAITING_PARENT,
                ExtracurricularRegistration::WAITING_COACH,
            ])->count(),
            'anggota_aktif' => ExtracurricularMember::where('status', 'aktif')->count(),
            'absensi_menunggu' => Attendance::where('status', Attendance::WAITING)->count(),
        ];

        $latestRegistrations = ExtracurricularRegistration::with(['student', 'extracurricular.coach'])
            ->latest()
            ->take(6)
            ->get();

        return view('dashboard.admin', compact('stats', 'latestRegistrations'));
    }

    public function student(Request $request): View
    {
        $student = Student::with([
            'registrations.extracurricular.coach',
            'members.extracurricular.schedules',
            'attendances.extracurricular',
            'assessments.extracurricular',
        ])->where('user_id', $request->user()->id)->first();

        return view('dashboard.student', compact('student'));
    }

    public function parent(Request $request): View
    {
        $parent = StudentParent::with([
            'children.registrations.extracurricular.coach',
            'children.members.extracurricular.schedules',
            'children.attendances.extracurricular',
            'children.assessments.extracurricular',
        ])->where('user_id', $request->user()->id)->first();

        return view('dashboard.parent', compact('parent'));
    }

    public function coach(Request $request): View
    {
        $coach = Coach::with('extracurriculars.schedules')
            ->where('user_id', $request->user()->id)
            ->first();

        $extracurricularIds = $coach?->extracurriculars->pluck('id') ?? collect();

        $stats = [
            'total_eskul' => $extracurricularIds->count(),
            'total_anggota' => ExtracurricularMember::whereIn('extracurricular_id', $extracurricularIds)->where('status', 'aktif')->count(),
            'pendaftaran_menunggu' => ExtracurricularRegistration::whereIn('extracurricular_id', $extracurricularIds)
                ->where('status', ExtracurricularRegistration::WAITING_COACH)
                ->count(),
            'absensi_menunggu' => Attendance::whereIn('extracurricular_id', $extracurricularIds)
                ->where('status', Attendance::WAITING)
                ->count(),
            'penilaian' => Assessment::whereIn('extracurricular_id', $extracurricularIds)->count(),
        ];

        return view('dashboard.coach', compact('coach', 'stats'));
    }
}
