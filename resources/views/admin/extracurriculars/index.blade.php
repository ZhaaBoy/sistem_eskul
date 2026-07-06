@extends('layouts.app')

@section('title', 'Data Eskul')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5>Data Ekstrakurikuler</h5>
        <a href="{{ route('admin.extracurriculars.create') }}" class="btn btn-primary btn-sm"><i class="feather icon-plus"></i> Tambah Eskul</a>
    </div>
    <div class="card-body">
        <form method="GET" class="row mb-3">
            <div class="col-md-4 mb-2"><input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari nama eskul"></div>
            <div class="col-md-3 mb-2">
                <select name="coach_id" class="form-control">
                    <option value="">Semua pembina</option>
                    @foreach ($coaches as $coach)
                        <option value="{{ $coach->id }}" @selected((string) request('coach_id') === (string) $coach->id)>{{ $coach->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 mb-2">
                <select name="status" class="form-control">
                    <option value="">Status</option>
                    <option value="aktif" @selected(request('status') === 'aktif')>Aktif</option>
                    <option value="nonaktif" @selected(request('status') === 'nonaktif')>Nonaktif</option>
                </select>
            </div>
            <div class="col-md-2 mb-2"><button class="btn btn-secondary btn-block" type="submit"><i class="feather icon-search"></i> Filter</button></div>
        </form>

        @if ($extracurriculars->isEmpty())
            <x-empty-state message="Belum ada eskul." />
        @else
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nama Eskul</th>
                            <th>Pembina</th>
                            <th>Kuota</th>
                            <th>Anggota</th>
                            <th>Status</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($extracurriculars as $extracurricular)
                            <tr>
                                <td>
                                    <strong>{{ $extracurricular->name }}</strong>
                                    <div class="text-muted">{{ str($extracurricular->description)->limit(80) }}</div>
                                </td>
                                <td>{{ $extracurricular->coach?->name }}</td>
                                <td>{{ $extracurricular->quota ?? 'Tanpa batas' }}</td>
                                <td>{{ $extracurricular->members->where('status', 'aktif')->count() }}</td>
                                <td><x-status-badge :status="$extracurricular->status" /></td>
                                <td>
                                    <div class="action-row justify-content-end">
                                        <a href="{{ route('admin.extracurriculars.edit', $extracurricular) }}" class="btn btn-info btn-sm"><i class="feather icon-edit"></i></a>
                                        <form method="POST" action="{{ route('admin.extracurriculars.destroy', $extracurricular) }}" onsubmit="return confirm('Hapus data eskul ini?')">
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
            {{ $extracurriculars->links() }}
        @endif
    </div>
</div>
@endsection
