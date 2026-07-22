<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentParent;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function index(Request $request): View
    {
        $students = Student::with(['user', 'parent'])
            ->when($request->filled('q'), function ($query) use ($request) {
                $query->where(function ($query) use ($request) {
                    $query->where('name', 'like', '%'.$request->q.'%')
                        ->orWhere('nis', 'like', '%'.$request->q.'%')
                        ->orWhere('nisn', 'like', '%'.$request->q.'%');
                });
            })
            ->when($request->filled('class'), fn ($query) => $query->where('class', $request->class))
            ->when($request->filled('major'), fn ($query) => $query->where('major', $request->major))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.students.index', compact('students'));
    }

    public function create(): View
    {
        return $this->form(new Student(['status' => 'aktif']));
    }

    public function store(Request $request): RedirectResponse
    {
        Student::create($this->validatedData($request));

        return redirect()->route('admin.students.index')->with('success', 'Data siswa berhasil dibuat.');
    }

    public function edit(Student $student): View
    {
        return $this->form($student);
    }

    public function update(Request $request, Student $student): RedirectResponse
    {
        $student->update($this->validatedData($request, $student));

        return redirect()->route('admin.students.index')->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function destroy(Student $student): RedirectResponse
    {
        $student->delete();

        return redirect()->route('admin.students.index')->with('success', 'Data siswa berhasil dihapus.');
    }

    private function form(Student $student): View
    {
        $userOptions = User::where('role', 'siswa')
            ->where(function ($query) use ($student) {
                $query->whereDoesntHave('student');

                if ($student->user_id) {
                    $query->orWhere('id', $student->user_id);
                }
            })
            ->orderBy('name')
            ->get();

        $parents = StudentParent::orderBy('name')->get();

        return view('admin.students.form', [
            'student' => $student,
            'userOptions' => $userOptions,
            'parents' => $parents,
            'route' => $student->exists ? route('admin.students.update', $student) : route('admin.students.store'),
            'method' => $student->exists ? 'PUT' : 'POST',
        ]);
    }

    private function validatedData(Request $request, ?Student $student = null): array
    {
        return $request->validate([
            'user_id' => ['nullable', 'exists:users,id', Rule::unique('students', 'user_id')->ignore($student?->id)],
            'parent_id' => ['nullable', 'exists:parents,id'],
            'nis' => ['nullable', 'string', 'max:50', Rule::unique('students', 'nis')->ignore($student?->id)],
            'nisn' => ['nullable', 'string', 'max:50', Rule::unique('students', 'nisn')->ignore($student?->id)],
            'name' => ['required', 'string', 'max:255'],
            'class' => ['nullable', 'string', 'max:50'],
            'major' => ['nullable', 'string', 'max:100'],
            'gender' => ['nullable', Rule::in(['Laki-laki', 'Perempuan'])],
            'birth_date' => ['nullable', 'date'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['aktif', 'nonaktif'])],
        ]);
    }
}
