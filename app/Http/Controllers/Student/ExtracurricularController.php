<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Extracurricular;
use App\Models\ExtracurricularRegistration;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExtracurricularController extends Controller
{
    public function index(Request $request): View
    {
        $student = $this->currentStudent($request);

        $registrations = $student
            ? $student->registrations()->pluck('status', 'extracurricular_id')
            : collect();

        $extracurriculars = Extracurricular::with(['coach', 'schedules', 'members'])
            ->active()
            ->when($request->filled('q'), fn ($query) => $query->where('name', 'like', '%'.$request->q.'%'))
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('student.extracurriculars.index', compact('student', 'registrations', 'extracurriculars'));
    }

    public function show(Request $request, Extracurricular $extracurricular): View
    {
        $student = $this->currentStudent($request);

        $registration = $student
            ? $student->registrations()->where('extracurricular_id', $extracurricular->id)->first()
            : null;

        $extracurricular->load(['coach', 'schedules' => fn ($query) => $query->active()->orderBy('day')]);

        return view('student.extracurriculars.show', compact('student', 'extracurricular', 'registration'));
    }

    public function register(Request $request, Extracurricular $extracurricular): RedirectResponse
    {
        $student = $this->currentStudent($request);

        abort_if(! $student, 403, 'Profil siswa belum terhubung dengan akun ini.');
        abort_if($student->status !== 'aktif', 403, 'Status siswa belum aktif.');
        abort_if($extracurricular->status !== 'aktif', 403, 'Eskul tidak aktif.');

        $alreadyRegistered = ExtracurricularRegistration::where('student_id', $student->id)
            ->where('extracurricular_id', $extracurricular->id)
            ->exists();

        if ($alreadyRegistered) {
            return back()->with('error', 'Anda sudah pernah mendaftar eskul ini.');
        }

        if ($extracurricular->quota && $extracurricular->members()->where('status', 'aktif')->count() >= $extracurricular->quota) {
            return back()->with('error', 'Kuota eskul sudah penuh.');
        }

        ExtracurricularRegistration::create([
            'student_id' => $student->id,
            'extracurricular_id' => $extracurricular->id,
            'status' => ExtracurricularRegistration::WAITING_PARENT,
        ]);

        return redirect()
            ->route('student.extracurriculars.index')
            ->with('success', 'Pendaftaran berhasil dikirim dan menunggu validasi orang tua.');
    }

    private function currentStudent(Request $request): ?Student
    {
        return Student::where('user_id', $request->user()->id)->first();
    }
}
