<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coach;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CoachController extends Controller
{
    public function index(Request $request): View
    {
        $coaches = Coach::with(['user', 'extracurriculars'])
            ->when($request->filled('q'), function ($query) use ($request) {
                $query->where(function ($query) use ($request) {
                    $query->where('name', 'like', '%'.$request->q.'%')
                        ->orWhere('nip', 'like', '%'.$request->q.'%')
                        ->orWhere('phone', 'like', '%'.$request->q.'%');
                });
            })
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.coaches.index', compact('coaches'));
    }

    public function create(): View
    {
        return $this->form(new Coach(['status' => 'aktif']));
    }

    public function store(Request $request): RedirectResponse
    {
        Coach::create($this->validatedData($request));

        return redirect()->route('admin.coaches.index')->with('success', 'Data pembina berhasil dibuat.');
    }

    public function edit(Coach $coach): View
    {
        return $this->form($coach);
    }

    public function update(Request $request, Coach $coach): RedirectResponse
    {
        $coach->update($this->validatedData($request, $coach));

        return redirect()->route('admin.coaches.index')->with('success', 'Data pembina berhasil diperbarui.');
    }

    public function destroy(Coach $coach): RedirectResponse
    {
        $coach->delete();

        return redirect()->route('admin.coaches.index')->with('success', 'Data pembina berhasil dihapus.');
    }

    private function form(Coach $coach): View
    {
        $userOptions = User::where('role', 'pembina')
            ->where(function ($query) use ($coach) {
                $query->whereDoesntHave('coach');

                if ($coach->user_id) {
                    $query->orWhereKey($coach->user_id);
                }
            })
            ->orderBy('name')
            ->get();

        return view('admin.coaches.form', [
            'coach' => $coach,
            'userOptions' => $userOptions,
            'route' => $coach->exists ? route('admin.coaches.update', $coach) : route('admin.coaches.store'),
            'method' => $coach->exists ? 'PUT' : 'POST',
        ]);
    }

    private function validatedData(Request $request, ?Coach $coach = null): array
    {
        return $request->validate([
            'user_id' => ['nullable', 'exists:users,id', Rule::unique('coaches', 'user_id')->ignore($coach?->id)],
            'name' => ['required', 'string', 'max:255'],
            'nip' => ['nullable', 'string', 'max:50', Rule::unique('coaches', 'nip')->ignore($coach?->id)],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'status' => ['required', Rule::in(['aktif', 'nonaktif'])],
        ]);
    }
}
