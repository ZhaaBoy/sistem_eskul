<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Models\Coach;
use App\Models\ExtracurricularMember;
use App\Models\ExtracurricularRegistration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RegistrationController extends Controller
{
    public function index(Request $request): View
    {
        $coach = $this->currentCoach($request);
        $extracurricularIds = $coach?->extracurriculars()->pluck('id') ?? collect();

        $registrations = ExtracurricularRegistration::with(['student.parent', 'extracurricular'])
            ->whereIn('extracurricular_id', $extracurricularIds)
            ->whereIn('status', [
                ExtracurricularRegistration::WAITING_COACH,
                ExtracurricularRegistration::REJECTED_COACH,
                ExtracurricularRegistration::ACCEPTED,
            ])
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('coach.registrations.index', compact('coach', 'registrations'));
    }

    public function approve(Request $request, ExtracurricularRegistration $registration): RedirectResponse
    {
        $coach = $this->currentCoach($request);

        $this->authorizeRegistration($coach, $registration);

        abort_if($registration->status !== ExtracurricularRegistration::WAITING_COACH, 422, 'Pendaftaran belum disetujui orang tua atau sudah diproses.');

        $registration->update([
            'status' => ExtracurricularRegistration::ACCEPTED,
            'coach_approved_at' => now(),
            'coach_rejected_at' => null,
            'coach_rejection_reason' => null,
        ]);

        ExtracurricularMember::updateOrCreate([
            'student_id' => $registration->student_id,
            'extracurricular_id' => $registration->extracurricular_id,
        ], [
            'registration_id' => $registration->id,
            'joined_at' => now(),
            'status' => 'aktif',
        ]);

        return back()->with('success', 'Pendaftaran disetujui. Siswa kini menjadi anggota eskul.');
    }

    public function reject(Request $request, ExtracurricularRegistration $registration): RedirectResponse
    {
        $coach = $this->currentCoach($request);

        $this->authorizeRegistration($coach, $registration);

        abort_if($registration->status !== ExtracurricularRegistration::WAITING_COACH, 422, 'Pendaftaran belum disetujui orang tua atau sudah diproses.');

        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:1000'],
        ]);

        $registration->update([
            'status' => ExtracurricularRegistration::REJECTED_COACH,
            'coach_rejected_at' => now(),
            'coach_rejection_reason' => $validated['reason'],
        ]);

        return back()->with('success', 'Pendaftaran ditolak dengan alasan yang tersimpan.');
    }

    private function currentCoach(Request $request): ?Coach
    {
        return Coach::with('extracurriculars')->where('user_id', $request->user()->id)->first();
    }

    private function authorizeRegistration(?Coach $coach, ExtracurricularRegistration $registration): void
    {
        abort_if(! $coach, 403, 'Profil pembina belum terhubung dengan akun ini.');

        $ownsExtracurricular = $coach->extracurriculars->contains('id', $registration->extracurricular_id);

        abort_if(! $ownsExtracurricular, 403, 'Anda hanya bisa memvalidasi pendaftaran pada eskul yang dibina.');
    }
}
