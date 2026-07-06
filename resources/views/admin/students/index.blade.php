@extends('layouts.app')

@section('title', 'Data Siswa')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5>Data Siswa</h5>
        <a href="{{ route('admin.students.create') }}" class="btn btn-primary btn-sm"><i class="feather icon-plus"></i> Tambah Siswa</a>
    </div>
    <div class="card-body">
        <form method="GET" class="row mb-3">
            <div class="col-md-4 mb-2"><input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari nama/NIS/NISN"></div>
            <div class="col-md-2 mb-2"><input type="text" name="class" value="{{ request('class') }}" class="form-control" placeholder="Kelas"></div>
            <div class="col-md-2 mb-2"><input type="text" name="major" value="{{ request('major') }}" class="form-control" placeholder="Jurusan"></div>
            <div class="col-md-2 mb-2">
                <select name="status" class="form-control">
                    <option value="">Status</option>
                    <option value="aktif" @selected(request('status') === 'aktif')>Aktif</option>
                    <option value="nonaktif" @selected(request('status') === 'nonaktif')>Nonaktif</option>
                </select>
            </div>
            <div class="col-md-2 mb-2"><button class="btn btn-secondary btn-block" type="submit"><i class="feather icon-search"></i> Filter</button></div>
        </form>

        @if ($students->isEmpty())
            <x-empty-state message="Belum ada siswa." />
        @else
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>NIS/NISN</th>
                            <th>Kelas</th>
                            <th>Jurusan</th>
                            <th>Orang Tua</th>
                            <th>Akun</th>
                            <th>Status</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($students as $student)
                            <tr>
                                <td>{{ $student->name }}</td>
                                <td>{{ $student->nis ?? '-' }} / {{ $student->nisn ?? '-' }}</td>
                                <td>{{ $student->class }}</td>
                                <td>{{ $student->major }}</td>
                                <td>{{ $student->parent?->name ?? '-' }}</td>
                                <td>{{ $student->user?->email ?? '-' }}</td>
                                <td><x-status-badge :status="$student->status" /></td>
                                <td>
                                    <div class="action-row justify-content-end">
                                        <a href="{{ route('admin.students.edit', $student) }}" class="btn btn-info btn-sm"><i class="feather icon-edit"></i></a>
                                        <form method="POST" action="{{ route('admin.students.destroy', $student) }}" onsubmit="return confirm('Hapus data siswa ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger btn-sm" type="submit"><i class="feather icon-trash-2"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $students->links() }}
        @endif
    </div>
</div>
@endsection
