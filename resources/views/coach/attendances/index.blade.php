@extends('layouts.app')

@section('title', 'Kelola Absensi')

@section('content')
@if (! $coach)
    <div class="alert alert-warning">Profil pembina belum ditautkan oleh admin.</div>
@else
    <div class="card">
        <div class="card-header"><h5>Absensi Siswa</h5></div>
        <div class="card-body">
            <form method="GET" class="row mb-3">
                <div class="col-md-2 mb-2"><input type="date" name="date" value="{{ request('date') }}" class="form-control"></div>
                <div class="col-md-3 mb-2">
                    <select name="extracurricular_id" class="form-control">
                        <option value="">Semua eskul</option>
                        @foreach ($coach->extracurriculars as $extracurricular)
                            <option value="{{ $extracurricular->id }}" @selected((string) request('extracurricular_id') === (string) $extracurricular->id)>{{ $extracurricular->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-2"><input type="text" name="student" value="{{ request('student') }}" class="form-control" placeholder="Nama siswa"></div>
                <div class="col-md-2 mb-2">
                    <select name="status" class="form-control">
                        <option value="">Status</option>
                        <option value="menunggu_approval" @selected(request('status') === 'menunggu_approval')>Menunggu</option>
                        <option value="disetujui" @selected(request('status') === 'disetujui')>Disetujui</option>
                        <option value="ditolak" @selected(request('status') === 'ditolak')>Ditolak</option>
                    </select>
                </div>
                <div class="col-md-2 mb-2"><button class="btn btn-secondary btn-block" type="submit"><i class="feather icon-search"></i> Filter</button></div>
            </form>

            @if ($attendances->isEmpty())
                <x-empty-state message="Belum ada absensi." />
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Siswa</th>
                                <th>Eskul</th>
                                <th>Jadwal</th>
                                <th>Status</th>
                                <th>Keterangan</th>
                                <th class="text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($attendances as $attendance)
                                <tr>
                                    <td>{{ $attendance->attendance_date?->format('d M Y') }}</td>
                                    <td>{{ $attendance->student?->name }}</td>
                                    <td>{{ $attendance->extracurricular?->name }}</td>
                                    <td>{{ $attendance->schedule?->day }} {{ substr($attendance->schedule?->start_time, 0, 5) }}</td>
                                    <td><x-status-badge :status="$attendance->status" /></td>
                                    <td>{{ $attendance->rejection_reason ?? '-' }}</td>
                                    <td>
                                        @if ($attendance->status === \App\Models\Attendance::WAITING)
                                            <div class="action-row justify-content-end">
                                                <form method="POST" action="{{ route('coach.attendances.approve', $attendance) }}">
                                                    @csrf
                                                    <button class="btn btn-success btn-sm" type="submit"><i class="feather icon-check"></i></button>
                                                </form>
                                                <button class="btn btn-danger btn-sm" type="button" data-toggle="collapse" data-target="#reject-attendance-{{ $attendance->id }}"><i class="feather icon-x"></i></button>
                                            </div>
                                            <div class="collapse mt-2" id="reject-attendance-{{ $attendance->id }}">
                                                <form method="POST" action="{{ route('coach.attendances.reject', $attendance) }}" class="border rounded p-2">
                                                    @csrf
                                                    <div class="form-group mb-2"><textarea name="reason" class="form-control" rows="2" placeholder="Keterangan penolakan" required></textarea></div>
                                                    <button class="btn btn-danger btn-sm" type="submit">Tolak Absensi</button>
                                                </form>
                                            </div>
                                        @else
                                            <span class="text-muted">Sudah diproses</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $attendances->links() }}
            @endif
        </div>
    </div>
@endif
@endsection
