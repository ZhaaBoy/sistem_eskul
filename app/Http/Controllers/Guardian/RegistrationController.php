<?php

namespace App\Http\Controllers\Guardian;

use App\Http\Controllers\Controller;
use App\Models\ExtracurricularRegistration;
use App\Models\StudentParent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RegistrationController extends Controller
{
    public function index(Request $request): View
    {
        $parent = $this->currentParent($request);

        $registrations = $parent
            ? ExtracurricularRegistration::with(['student', 'extracurricular.coach'])
                ->whereHas('student', fn ($query) => $query->where('parent_id', $parent->id))
                ->latest()
                ->paginate(10)
                ->withQueryString()
            : collect();

        return view('parent.registrations.index', compact('parent', 'registrations'));
    }

    public function approve(Request $request, ExtracurricularRegistration $registration): RedirectResponse
    {
        $parent = $this->currentParent($request);

        $this->authorizeRegistration($parent, $registration);

        abort_if($registration->status !== ExtracurricularRegistration::WAITING_PARENT, 422, 'Pendaftaran tidak sedang menunggu validasi orang tua.');

        $registration->update([
            'status' => ExtracurricularRegistration::WAITING_COACH,
            'parent_approved_at' => now(),
            'parent_rejected_at' => null,
            'parent_rejection_reason' => null,
        ]);

        return back()->with('success', 'Pendaftaran disetujui dan diteruskan ke pembina.');
    }

    public function reject(Request $request, ExtracurricularRegistration $registration): RedirectResponse
    {
        $parent = $this->currentParent($request);

        $this->authorizeRegistration($parent, $registration);

        abort_if($registration->status !== ExtracurricularRegistration::WAITING_PARENT, 422, 'Pendaftaran tidak sedang menunggu validasi orang tua.');

        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:1000'],
        ]);

        $registration->update([
            'status' => ExtracurricularRegistration::REJECTED_PARENT,
            'parent_rejected_at' => now(),
            'parent_rejection_reason' => $validated['reason'],
        ]);

        return back()->with('success', 'Pendaftaran ditolak dengan alasan yang tersimpan.');
    }

    private function currentParent(Request $request): ?StudentParent
    {
        return StudentParent::where('user_id', $request->user()->id)->first();
    }

    private function authorizeRegistration(?StudentParent $parent, ExtracurricularRegistration $registration): void
    {
        abort_if(! $parent, 403, 'Profil orang tua belum terhubung dengan akun ini.');

        $registration->loadMissing('student');

        abort_if($registration->student->parent_id !== $parent->id, 403, 'Anda hanya bisa memvalidasi pendaftaran anak sendiri.');
    }
}
