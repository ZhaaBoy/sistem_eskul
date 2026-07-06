@extends('layouts.app')

@section('title', 'Laporan')

@section('content')
<div class="row">
    @foreach ($attendanceSummary as $status => $count)
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="mb-2">Absensi {{ ucwords(str_replace('_', ' ', $status)) }}</h6>
                    <h3 class="mb-0">{{ $count }}</h3>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5>Laporan Keanggotaan Eskul</h5>
        <button class="btn btn-secondary btn-sm no-print" type="button" onclick="window.print()"><i class="feather icon-printer"></i> Cetak</button>
    </div>
    <div class="card-body">
        <form method="GET" class="row mb-3 no-print">
            <div class="col-md-3 mb-2">
                <select name="student_id" class="form-control">
                    <option value="">Semua siswa</option>
                    @foreach ($filters['students'] as $student)
                        <option value="{{ $student->id }}" @selected((string) request('student_id') === (string) $student->id)>{{ $student->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 mb-2">
                <select name="extracurricular_id" class="form-control">
                    <option value="">Semua eskul</option>
                    @foreach ($filters['extracurriculars'] as $extracurricular)
                        <option value="{{ $extracurricular->id }}" @selected((string) request('extracurricular_id') === (string) $extracurricular->id)>{{ $extracurricular->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 mb-2">
                <select name="coach_id" class="form-control">
                    <option value="">Semua pembina</option>
                    @foreach ($filters['coaches'] as $coach)
                        <option value="{{ $coach->id }}" @selected((string) request('coach_id') === (string) $coach->id)>{{ $coach->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 mb-2"><input type="text" name="class" value="{{ request('class') }}" class="form-control" placeholder="Kelas"></div>
            <div class="col-md-2 mb-2"><button class="btn btn-secondary btn-block" type="submit"><i class="feather icon-search"></i> Filter</button></div>
        </form>

        @if ($members->isEmpty())
            <x-empty-state message="Belum ada anggota eskul aktif." />
        @else
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Siswa</th>
                            <th>Kelas</th>
                            <th>Eskul</th>
                            <th>Pembina</th>
                            <th>Jadwal</th>
                            <th>Absensi</th>
                            <th>Penilaian</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($members as $member)
                            @php
                                $studentAttendances = $member->student->attendances->where('extracurricular_id', $member->extracurricular_id);
                                $studentAssessments = $member->student->assessments->where('extracurricular_id', $member->extracurricular_id);
                            @endphp
                            <tr>
                                <td>{{ $member->student?->name }}</td>
                                <td>{{ $member->student?->class }}</td>
                                <td>{{ $member->extracurricular?->name }}</td>
                                <td>{{ $member->extracurricular?->coach?->name }}</td>
                                <td>
                                    @foreach ($member->extracurricular?->schedules ?? [] as $schedule)
                                        <div>{{ $schedule->day }} {{ substr($schedule->start_time, 0, 5) }} - {{ substr($schedule->end_time, 0, 5) }}</div>
                                    @endforeach
                                </td>
                                <td>
                                    <div>Disetujui: {{ $studentAttendances->where('status', 'disetujui')->count() }}</div>
                                    <div>Menunggu: {{ $studentAttendances->where('status', 'menunggu_approval')->count() }}</div>
                                    <div>Ditolak: {{ $studentAttendances->where('status', 'ditolak')->count() }}</div>
                                </td>
                                <td>
                                    @forelse ($studentAssessments as $assessment)
                                        <div>{{ $assessment->semester }} {{ $assessment->period }}: {{ $assessment->score }} ({{ $assessment->predicate }})</div>
                                    @empty
                                        -
                                    @endforelse
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $members->links() }}
        @endif
    </div>
</div>
@endsection
