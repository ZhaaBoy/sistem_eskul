<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StudentParent;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ParentController extends Controller
{
    public function index(Request $request): View
    {
        $parents = StudentParent::with(['user', 'children'])
            ->when($request->filled('q'), function ($query) use ($request) {
                $query->where(function ($query) use ($request) {
                    $query->where('name', 'like', '%'.$request->q.'%')
                        ->orWhere('phone', 'like', '%'.$request->q.'%')
                        ->orWhere('email', 'like', '%'.$request->q.'%');
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.parents.index', compact('parents'));
    }

    public function create(): View
    {
        return $this->form(new StudentParent(['relationship' => 'Wali']));
    }

    public function store(Request $request): RedirectResponse
    {
        StudentParent::create($this->validatedData($request));

        return redirect()->route('admin.parents.index')->with('success', 'Data orang tua berhasil dibuat.');
    }

    public function edit(StudentParent $parent): View
    {
        return $this->form($parent);
    }

    public function update(Request $request, StudentParent $parent): RedirectResponse
    {
        $parent->update($this->validatedData($request, $parent));

        return redirect()->route('admin.parents.index')->with('success', 'Data orang tua berhasil diperbarui.');
    }

    public function destroy(StudentParent $parent): RedirectResponse
    {
        $parent->delete();

        return redirect()->route('admin.parents.index')->with('success', 'Data orang tua berhasil dihapus.');
    }

    private function form(StudentParent $parent): View
    {
        $userOptions = User::where('role', 'orang_tua')
            ->where(function ($query) use ($parent) {
                $query->whereDoesntHave('studentParent');

                if ($parent->user_id) {
                    $query->orWhereKey($parent->user_id);
                }
            })
            ->orderBy('name')
            ->get();

        return view('admin.parents.form', [
            'parent' => $parent,
            'userOptions' => $userOptions,
            'route' => $parent->exists ? route('admin.parents.update', $parent) : route('admin.parents.store'),
            'method' => $parent->exists ? 'PUT' : 'POST',
        ]);
    }

    private function validatedData(Request $request, ?StudentParent $parent = null): array
    {
        return $request->validate([
            'user_id' => ['nullable', 'exists:users,id', Rule::unique('parents', 'user_id')->ignore($parent?->id)],
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string'],
            'relationship' => ['required', Rule::in(['Ayah', 'Ibu', 'Wali'])],
        ]);
    }
}
