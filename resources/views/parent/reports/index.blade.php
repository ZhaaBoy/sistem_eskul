@extends('layouts.app')

@section('title', 'Laporan Anak')

@section('content')
@if (! $parent)
    <div class="alert alert-warning">Profil orang tua belum ditautkan oleh admin.</div>
@else
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5>Laporan Anak</h5>
            <button class="btn btn-secondary btn-sm no-print" type="button" onclick="window.print()"><i class="feather icon-printer"></i> Cetak</button>
        </div>
        <div class="card-body">
            <h6>Data Anak</h6>
            @forelse ($parent->children as $child)
                <div class="border-bottom py-2">
                    <strong>{{ $child->name }}</strong>
                    <div>{{ $child->class }} - {{ $child->major }}</div>
                </div>
            @empty
                <x-empty-state message="Belum ada anak terhubung." />
            @endforelse
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h5>Keanggotaan Eskul</h5></div>
        <div class="card-body table-border-style">
            @if ($memberships->isEmpty())
                <x-empty-state message="Belum ada keanggotaan aktif." />
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Anak</th>
                                <th>Eskul</th>
                                <th>Pembina</th>
                                <th>Jadwal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($memberships as $member)
                                <tr>
                                    <td>{{ $member->student?->name }}</td>
                                    <td>{{ $member->extracurricular?->name }}</td>
                                    <td>{{ $member->extracurricular?->coach?->name }}</td>
                                    <td>
                                        @foreach ($member->extracurricular?->schedules ?? [] as $schedule)
                                            <div>{{ $schedule->day }} {{ substr($schedule->start_time, 0, 5) }} - {{ substr($schedule->end_time, 0, 5) }}</div>
                                        @endforeach
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header"><h5>Rekap Absensi</h5></div>
                <div class="card-body">
                    @forelse ($attendances->groupBy('student.name') as $studentName => $items)
                        <div class="border-bottom py-2">
                            <strong>{{ $studentName }}</strong>
                            <div>Disetujui: {{ $items->where('status', 'disetujui')->count() }}</div>
                            <div>Menunggu: {{ $items->where('status', 'menunggu_approval')->count() }}</div>
                            <div>Ditolak: {{ $items->where('status', 'ditolak')->count() }}</div>
                        </div>
                    @empty
                        <x-empty-state message="Belum ada absensi." />
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header"><h5>Penilaian</h5></div>
                <div class="card-body">
                    @forelse ($assessments as $assessment)
                        <div class="border-bottom py-2">
                            <strong>{{ $assessment->student?->name }} - {{ $assessment->extracurricular?->name }}</strong>
                            <div>{{ $assessment->semester }} {{ $assessment->period }}: {{ $assessment->score }} ({{ $assessment->predicate }})</div>
                            <div class="text-muted">{{ $assessment->notes }}</div>
                        </div>
                    @empty
                        <x-empty-state message="Belum ada penilaian." />
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endif
@endsection
