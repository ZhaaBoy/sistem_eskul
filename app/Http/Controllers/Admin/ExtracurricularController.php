<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coach;
use App\Models\Extracurricular;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ExtracurricularController extends Controller
{
    public function index(Request $request): View
    {
        $extracurriculars = Extracurricular::with(['coach', 'members'])
            ->when($request->filled('q'), fn ($query) => $query->where('name', 'like', '%'.$request->q.'%'))
            ->when($request->filled('coach_id'), fn ($query) => $query->where('coach_id', $request->coach_id))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $coaches = Coach::orderBy('name')->get();

        return view('admin.extracurriculars.index', compact('extracurriculars', 'coaches'));
    }

    public function create(): View
    {
        return $this->form(new Extracurricular(['status' => 'aktif']));
    }

    public function store(Request $request): RedirectResponse
    {
        Extracurricular::create($this->validatedData($request));

        return redirect()->route('admin.extracurriculars.index')->with('success', 'Data eskul berhasil dibuat.');
    }

    public function edit(Extracurricular $extracurricular): View
    {
        return $this->form($extracurricular);
    }

    public function update(Request $request, Extracurricular $extracurricular): RedirectResponse
    {
        $extracurricular->update($this->validatedData($request));

        return redirect()->route('admin.extracurriculars.index')->with('success', 'Data eskul berhasil diperbarui.');
    }

    public function destroy(Extracurricular $extracurricular): RedirectResponse
    {
        $extracurricular->delete();

        return redirect()->route('admin.extracurriculars.index')->with('success', 'Data eskul berhasil dihapus.');
    }

    private function form(Extracurricular $extracurricular): View
    {
        return view('admin.extracurriculars.form', [
            'extracurricular' => $extracurricular,
            'coaches' => Coach::where('status', 'aktif')->orderBy('name')->get(),
            'route' => $extracurricular->exists
                ? route('admin.extracurriculars.update', $extracurricular)
                : route('admin.extracurriculars.store'),
            'method' => $extracurricular->exists ? 'PUT' : 'POST',
        ]);
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'coach_id' => ['required', 'exists:coaches,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'quota' => ['nullable', 'integer', 'min:1'],
            'status' => ['required', Rule::in(['aktif', 'nonaktif'])],
        ]);
    }
}
