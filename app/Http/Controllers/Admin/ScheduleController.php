<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Extracurricular;
use App\Models\ExtracurricularSchedule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ScheduleController extends Controller
{
    public function index(Request $request): View
    {
        $schedules = ExtracurricularSchedule::with('extracurricular.coach')
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

        $extracurriculars = Extracurricular::orderBy('name')->get();

        return view('admin.schedules.index', compact('schedules', 'extracurriculars'));
    }

    public function create(): View
    {
        return $this->form(new ExtracurricularSchedule(['status' => 'aktif']));
    }

    public function store(Request $request): RedirectResponse
    {
        ExtracurricularSchedule::create($this->validatedData($request));

        return redirect()->route('admin.schedules.index')->with('success', 'Jadwal eskul berhasil dibuat.');
    }

    public function edit(ExtracurricularSchedule $schedule): View
    {
        return $this->form($schedule);
    }

    public function update(Request $request, ExtracurricularSchedule $schedule): RedirectResponse
    {
        $schedule->update($this->validatedData($request));

        return redirect()->route('admin.schedules.index')->with('success', 'Jadwal eskul berhasil diperbarui.');
    }

    public function destroy(ExtracurricularSchedule $schedule): RedirectResponse
    {
        $schedule->delete();

        return redirect()->route('admin.schedules.index')->with('success', 'Jadwal eskul berhasil dihapus.');
    }

    private function form(ExtracurricularSchedule $schedule): View
    {
        return view('admin.schedules.form', [
            'schedule' => $schedule,
            'extracurriculars' => Extracurricular::active()->orderBy('name')->get(),
            'route' => $schedule->exists ? route('admin.schedules.update', $schedule) : route('admin.schedules.store'),
            'method' => $schedule->exists ? 'PUT' : 'POST',
        ]);
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'extracurricular_id' => ['required', 'exists:extracurriculars,id'],
            'day' => ['required', Rule::in(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'])],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'location' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['aktif', 'nonaktif'])],
        ]);
    }
}
