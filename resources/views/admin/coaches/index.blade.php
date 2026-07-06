@extends('layouts.app')

@section('title', 'Data Pembina')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5>Data Pembina</h5>
        <a href="{{ route('admin.coaches.create') }}" class="btn btn-primary btn-sm"><i class="feather icon-plus"></i> Tambah Pembina</a>
    </div>
    <div class="card-body">
        <form method="GET" class="row mb-3">
            <div class="col-md-8 mb-2"><input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari nama/NIP/no HP"></div>
            <div class="col-md-2 mb-2">
                <select name="status" class="form-control">
                    <option value="">Status</option>
                    <option value="aktif" @selected(request('status') === 'aktif')>Aktif</option>
                    <option value="nonaktif" @selected(request('status') === 'nonaktif')>Nonaktif</option>
                </select>
            </div>
            <div class="col-md-2 mb-2"><button class="btn btn-secondary btn-block" type="submit"><i class="feather icon-search"></i> Filter</button></div>
        </form>

        @if ($coaches->isEmpty())
            <x-empty-state message="Belum ada pembina." />
        @else
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>NIP</th>
                            <th>No HP</th>
                            <th>Email</th>
                            <th>Akun</th>
                            <th>Eskul</th>
                            <th>Status</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($coaches as $coach)
                            <tr>
                                <td>{{ $coach->name }}</td>
                                <td>{{ $coach->nip ?? '-' }}</td>
                                <td>{{ $coach->phone ?? '-' }}</td>
                                <td>{{ $coach->email ?? '-' }}</td>
                                <td>{{ $coach->user?->email ?? '-' }}</td>
                                <td>{{ $coach->extracurriculars->pluck('name')->join(', ') ?: '-' }}</td>
                                <td><x-status-badge :status="$coach->status" /></td>
                                <td>
                                    <div class="action-row justify-content-end">
                                        <a href="{{ route('admin.coaches.edit', $coach) }}" class="btn btn-info btn-sm"><i class="feather icon-edit"></i></a>
                                        <form method="POST" action="{{ route('admin.coaches.destroy', $coach) }}" onsubmit="return confirm('Hapus data pembina ini? Pastikan tidak sedang menjadi pembina eskul.')">
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
            {{ $coaches->links() }}
        @endif
    </div>
</div>
@endsection
