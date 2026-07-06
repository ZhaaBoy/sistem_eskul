<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Models\Coach;
use App\Models\ExtracurricularSchedule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ScheduleController extends Controller
{
    public function index(Request $request): View
    {
        $coach = $this->currentCoach($request);
        $extracurricularIds = $coach?->extracurriculars->pluck('id') ?? collect();

        if ($request->filled('extracurricular_id') && ! $extracurricularIds->contains((int) $request->extracurricular_id)) {
            abort(403, 'Anda hanya bisa melihat jadwal eskul yang dibina.');
        }

        $schedules = ExtracurricularSchedule::with('extracurricular.coach')
            ->whereIn('extracurricular_id', $extracurricularIds)
            ->when($request->filled('q'), function ($query) use ($request) {
                $query->where(function ($query) use ($request) {
                    $query->where('day', 'like', '%'.$request->q.'%')
                        ->orWhere('location', 'like', '%'.$request->q.'%');
                });
            })
            ->when($request->filled('extracurricular_id'), fn ($query) => $query->where('extracurricular_id', $request->extracurricular_id))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('coach.schedules.index', compact('coach', 'schedules'));
    }

    public function create(Request $request): View
    {
        return $this->form($request, new ExtracurricularSchedule(['status' => 'aktif']));
    }

    public function store(Request $request): RedirectResponse
    {
        $coach = $this->currentCoach($request);

        ExtracurricularSchedule::create($this->validatedData($request, $coach));

        return redirect()->route('coach.schedules.index')->with('success', 'Jadwal eskul berhasil dibuat.');
    }

    public function edit(Request $request, ExtracurricularSchedule $schedule): View
    {
        $coach = $this->currentCoach($request);

        $this->authorizeSchedule($coach, $schedule);

        return $this->form($request, $schedule);
    }

    public function update(Request $request, ExtracurricularSchedule $schedule): RedirectResponse
    {
        $coach = $this->currentCoach($request);

        $this->authorizeSchedule($coach, $schedule);

        $schedule->update($this->validatedData($request, $coach));

        return redirect()->route('coach.schedules.index')->with('success', 'Jadwal eskul berhasil diperbarui.');
    }

    public function destroy(Request $request, ExtracurricularSchedule $schedule): RedirectResponse
    {
        $coach = $this->currentCoach($request);

        $this->authorizeSchedule($coach, $schedule);

        $schedule->delete();

        return redirect()->route('coach.schedules.index')->with('success', 'Jadwal eskul berhasil dihapus.');
    }

    private function form(Request $request, ExtracurricularSchedule $schedule): View
    {
        $coach = $this->currentCoach($request);

        return view('coach.schedules.form', [
            'coach' => $coach,
            'schedule' => $schedule,
            'extracurriculars' => $coach ? $coach->extracurriculars()->orderBy('name')->get() : collect(),
            'route' => $schedule->exists ? route('coach.schedules.update', $schedule) : route('coach.schedules.store'),
            'method' => $schedule->exists ? 'PUT' : 'POST',
        ]);
    }

    private function validatedData(Request $request, ?Coach $coach): array
    {
        abort_if(! $coach, 403, 'Profil pembina belum terhubung dengan akun ini.');

        $extracurricularIds = $coach->extracurriculars()->pluck('id')->all();

        return $request->validate([
            'extracurricular_id' => ['required', Rule::in($extracurricularIds)],
            'day' => ['required', Rule::in(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'])],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'location' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['aktif', 'nonaktif'])],
        ]);
    }

    private function currentCoach(Request $request): ?Coach
    {
        return Coach::with('extracurriculars')->where('user_id', $request->user()->id)->first();
    }

    private function authorizeSchedule(?Coach $coach, ExtracurricularSchedule $schedule): void
    {
        abort_if(! $coach, 403, 'Profil pembina belum terhubung dengan akun ini.');

        $ownsExtracurricular = $coach->extracurriculars->contains('id', $schedule->extracurricular_id);

        abort_if(! $ownsExtracurricular, 403, 'Anda hanya bisa mengelola jadwal eskul yang dibina.');
    }
}
