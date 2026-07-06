@extends('layouts.app')

@section('title', 'Jadwal Eskul')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5>Jadwal Ekstrakurikuler</h5>
        <a href="{{ route('admin.schedules.create') }}" class="btn btn-primary btn-sm"><i class="feather icon-plus"></i> Tambah Jadwal</a>
    </div>
    <div class="card-body">
        <form method="GET" class="row mb-3">
            <div class="col-md-4 mb-2"><input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari hari/lokasi"></div>
            <div class="col-md-3 mb-2">
                <select name="extracurricular_id" class="form-control">
                    <option value="">Semua eskul</option>
                    @foreach ($extracurriculars as $extracurricular)
                        <option value="{{ $extracurricular->id }}" @selected((string) request('extracurricular_id') === (string) $extracurricular->id)>{{ $extracurricular->name }}</option>
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

        @if ($schedules->isEmpty())
            <x-empty-state message="Belum ada jadwal." />
        @else
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Eskul</th>
                            <th>Hari</th>
                            <th>Jam</th>
                            <th>Lokasi</th>
                            <th>Keterangan</th>
                            <th>Status</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($schedules as $schedule)
                            <tr>
                                <td>{{ $schedule->extracurricular?->name }}</td>
                                <td>{{ $schedule->day }}</td>
                                <td>{{ substr($schedule->start_time, 0, 5) }} - {{ substr($schedule->end_time, 0, 5) }}</td>
                                <td>{{ $schedule->location ?? '-' }}</td>
                                <td>{{ str($schedule->description)->limit(60) }}</td>
                                <td><x-status-badge :status="$schedule->status" /></td>
                                <td>
                                    <div class="action-row justify-content-end">
                                        <a href="{{ route('admin.schedules.edit', $schedule) }}" class="btn btn-info btn-sm"><i class="feather icon-edit"></i></a>
                                        <form method="POST" action="{{ route('admin.schedules.destroy', $schedule) }}" onsubmit="return confirm('Hapus jadwal ini?')">
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
            {{ $schedules->links() }}
        @endif
    </div>
</div>
@endsection
