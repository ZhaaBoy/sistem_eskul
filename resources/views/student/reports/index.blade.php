@extends('layouts.app')

@section('title', 'Laporan Saya')

@section('content')
@if (! $student)
    <div class="alert alert-warning">Profil siswa belum ditautkan oleh admin.</div>
@else
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5>Ringkasan Laporan</h5>
            <button class="btn btn-secondary btn-sm no-print" onclick="window.print()" type="button"><i class="feather icon-printer"></i> Cetak</button>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-4"><strong>Nama</strong><div>{{ $student->name }}</div></div>
                <div class="col-md-4"><strong>Kelas</strong><div>{{ $student->class }}</div></div>
                <div class="col-md-4"><strong>Jurusan</strong><div>{{ $student->major }}</div></div>
            </div>

            <h6>Keanggotaan Eskul</h6>
            @forelse ($memberships as $member)
                <div class="border-bottom py-2">
                    <strong>{{ $member->extracurricular?->name }}</strong>
                    <div>Pembina: {{ $member->extracurricular?->coach?->name }}</div>
                    <div>Mulai: {{ $member->joined_at?->format('d M Y') }}</div>
                </div>
            @empty
                <x-empty-state message="Belum ada keanggotaan aktif." />
            @endforelse
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header"><h5>Rekap Absensi</h5></div>
                <div class="card-body">
                    @forelse ($attendances->groupBy('extracurricular.name') as $eskulName => $items)
                        <div class="border-bottom py-2">
                            <strong>{{ $eskulName }}</strong>
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
                            <strong>{{ $assessment->extracurricular?->name }}</strong>
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
