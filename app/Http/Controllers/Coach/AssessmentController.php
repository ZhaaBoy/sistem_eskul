<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Coach;
use App\Models\ExtracurricularMember;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AssessmentController extends Controller
{
    public function index(Request $request): View
    {
        $coach = $this->currentCoach($request);
        $extracurricularIds = $coach?->extracurriculars->pluck('id') ?? collect();

        $assessments = Assessment::with(['student', 'extracurricular'])
            ->where('coach_id', $coach?->id)
            ->when($request->filled('extracurricular_id'), fn ($query) => $query->where('extracurricular_id', $request->extracurricular_id))
            ->when($request->filled('period'), fn ($query) => $query->where('period', $request->period))
            ->when($request->filled('student'), fn ($query) => $query->whereHas('student', fn ($query) => $query->where('name', 'like', '%'.$request->student.'%')))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('coach.assessments.index', compact('coach', 'assessments'));
    }

    public function create(Request $request): View
    {
        return $this->form($request, new Assessment());
    }

    public function store(Request $request): RedirectResponse
    {
        $coach = $this->currentCoach($request);
        $data = $this->validatedData($request, $coach);
        $data['coach_id'] = $coach->id;
        $data['predicate'] = $data['predicate'] ?: $this->predicateFor((float) $data['score']);

        Assessment::create($data);

        return redirect()->route('coach.assessments.index')->with('success', 'Penilaian berhasil disimpan.');
    }

    public function edit(Request $request, Assessment $assessment): View
    {
        $coach = $this->currentCoach($request);

        abort_if(! $coach || $assessment->coach_id !== $coach->id, 403);

        return $this->form($request, $assessment);
    }

    public function update(Request $request, Assessment $assessment): RedirectResponse
    {
        $coach = $this->currentCoach($request);

        abort_if(! $coach || $assessment->coach_id !== $coach->id, 403);

        $data = $this->validatedData($request, $coach, $assessment);
        $data['coach_id'] = $coach->id;
        $data['predicate'] = $data['predicate'] ?: $this->predicateFor((float) $data['score']);

        $assessment->update($data);

        return redirect()->route('coach.assessments.index')->with('success', 'Penilaian berhasil diperbarui.');
    }

    public function destroy(Request $request, Assessment $assessment): RedirectResponse
    {
        $coach = $this->currentCoach($request);

        abort_if(! $coach || $assessment->coach_id !== $coach->id, 403);

        $assessment->delete();

        return redirect()->route('coach.assessments.index')->with('success', 'Penilaian berhasil dihapus.');
    }

    private function form(Request $request, Assessment $assessment): View
    {
        $coach = $this->currentCoach($request);
        $extracurricularIds = $coach?->extracurriculars->pluck('id') ?? collect();

        $members = ExtracurricularMember::with(['student', 'extracurricular'])
            ->whereIn('extracurricular_id', $extracurricularIds)
            ->where('status', 'aktif')
            ->orderBy('student_id')
            ->get();

        return view('coach.assessments.form', [
            'assessment' => $assessment,
            'coach' => $coach,
            'members' => $members,
            'route' => $assessment->exists ? route('coach.assessments.update', $assessment) : route('coach.assessments.store'),
            'method' => $assessment->exists ? 'PUT' : 'POST',
        ]);
    }

    private function validatedData(Request $request, ?Coach $coach, ?Assessment $assessment = null): array
    {
        abort_if(! $coach, 403, 'Profil pembina belum terhubung dengan akun ini.');

        $extracurricularIds = $coach->extracurriculars->pluck('id')->all();

        $data = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'extracurricular_id' => ['required', Rule::in($extracurricularIds)],
            'period' => ['nullable', 'string', 'max:50'],
            'semester' => ['required', 'string', 'max:50'],
            'score' => ['required', 'numeric', 'min:0', 'max:100'],
            'predicate' => ['nullable', 'string', 'max:10'],
            'notes' => ['nullable', 'string'],
        ]);

        $isMember = ExtracurricularMember::where('student_id', $data['student_id'])
            ->where('extracurricular_id', $data['extracurricular_id'])
            ->where('status', 'aktif')
            ->exists();

        abort_if(! $isMember, 422, 'Penilaian hanya bisa diberikan kepada anggota eskul aktif.');

        $duplicate = Assessment::where('student_id', $data['student_id'])
            ->where('extracurricular_id', $data['extracurricular_id'])
            ->where('period', $data['period'])
            ->where('semester', $data['semester'])
            ->when($assessment?->id, fn ($query) => $query->whereKeyNot($assessment->id))
            ->exists();

        abort_if($duplicate, 422, 'Penilaian untuk siswa, eskul, periode, dan semester tersebut sudah ada.');

        return $data;
    }

    private function currentCoach(Request $request): ?Coach
    {
        return Coach::with('extracurriculars')->where('user_id', $request->user()->id)->first();
    }

    private function predicateFor(float $score): string
    {
        return match (true) {
            $score >= 90 => 'A',
            $score >= 80 => 'B',
            $score >= 70 => 'C',
            $score >= 60 => 'D',
            default => 'E',
        };
    }
}
