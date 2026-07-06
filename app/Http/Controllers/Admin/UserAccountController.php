<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserAccountController extends Controller
{
    public function index(Request $request): View
    {
        $accounts = User::query()
            ->when($request->filled('q'), function ($query) use ($request) {
                $query->where(function ($query) use ($request) {
                    $query->where('name', 'like', '%'.$request->q.'%')
                        ->orWhere('email', 'like', '%'.$request->q.'%');
                });
            })
            ->when($request->filled('role'), fn ($query) => $query->where('role', $request->role))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.accounts.index', compact('accounts'));
    }

    public function create(): View
    {
        return view('admin.accounts.form', [
            'account' => new User(['role' => 'siswa', 'status' => 'aktif']),
            'route' => route('admin.accounts.store'),
            'method' => 'POST',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        User::create($this->validatedData($request));

        return redirect()->route('admin.accounts.index')->with('success', 'Akun berhasil dibuat.');
    }

    public function edit(User $account): View
    {
        return view('admin.accounts.form', [
            'account' => $account,
            'route' => route('admin.accounts.update', $account),
            'method' => 'PUT',
        ]);
    }

    public function update(Request $request, User $account): RedirectResponse
    {
        $data = $this->validatedData($request, $account);

        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }

        $account->update($data);

        return redirect()->route('admin.accounts.index')->with('success', 'Akun berhasil diperbarui.');
    }

    public function toggleStatus(User $account): RedirectResponse
    {
        $account->update([
            'status' => $account->status === 'aktif' ? 'nonaktif' : 'aktif',
        ]);

        return back()->with('success', 'Status akun berhasil diubah.');
    }

    public function resetPassword(Request $request, User $account): RedirectResponse
    {
        $validated = $request->validate([
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $account->update([
            'password' => $validated['new_password'],
        ]);

        return back()->with('success', 'Password akun berhasil direset.');
    }

    private function validatedData(Request $request, ?User $account = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($account?->id)],
            'password' => [$account ? 'nullable' : 'required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::in(['admin', 'pembina', 'siswa', 'orang_tua'])],
            'status' => ['required', Rule::in(['aktif', 'nonaktif'])],
        ]);
    }
}
