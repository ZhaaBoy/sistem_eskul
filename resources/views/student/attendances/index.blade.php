@extends('layouts.app')

@section('title', 'Absensi')

@section('content')
@if (! $student)
    <div class="alert alert-warning">Profil siswa belum ditautkan oleh admin. Anda belum bisa melakukan absensi.</div>
@else
    <div class="card">
        <div class="card-header"><h5>Jadwal Eskul Saya</h5></div>
        <div class="card-body">
            @forelse ($memberships as $member)
                <h6>{{ $member->extracurricular?->name }}</h6>
                <div class="table-responsive mb-4">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Hari</th>
                                <th>Jam</th>
                                <th>Lokasi</th>
                                <th>Status Hari Ini</th>
                                <th class="text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($member->extracurricular?->schedules ?? [] as $schedule)
                                @php($todayAttendance = $todayAttendances[$schedule->id] ?? null)
                                <tr>
                                    <td>{{ $schedule->day }}</td>
                                    <td>{{ substr($schedule->start_time, 0, 5) }} - {{ substr($schedule->end_time, 0, 5) }}</td>
                                    <td>{{ $schedule->location }}</td>
                                    <td>
                                        @if ($todayAttendance)
                                            <x-status-badge :status="$todayAttendance->status" />
                                        @else
                                            <span class="badge badge-light">Belum absen</span>
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        @if (! $todayAttendance)
                                            <form method="POST" action="{{ route('student.attendances.store', $schedule) }}">
                                                @csrf
                                                <button class="btn btn-primary btn-sm" type="submit"><i class="feather icon-check"></i> Absen</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted">Belum ada jadwal aktif.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @empty
                <x-empty-state message="Anda belum menjadi anggota eskul aktif." />
            @endforelse
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h5>Riwayat Absensi</h5></div>
        <div class="card-body table-border-style">
            @if ($attendances->isEmpty())
                <x-empty-state message="Belum ada riwayat absensi." />
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Eskul</th>
                                <th>Jadwal</th>
                                <th>Status</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($attendances as $attendance)
                                <tr>
                                    <td>{{ $attendance->attendance_date?->format('d M Y') }}</td>
                                    <td>{{ $attendance->extracurricular?->name }}</td>
                                    <td>{{ $attendance->schedule?->day }} {{ substr($attendance->schedule?->start_time, 0, 5) }}</td>
                                    <td><x-status-badge :status="$attendance->status" /></td>
                                    <td>{{ $attendance->rejection_reason ?? '-' }}</td>
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
