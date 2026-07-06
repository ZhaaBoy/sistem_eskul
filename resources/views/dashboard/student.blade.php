@extends('layouts.app')

@section('title', 'Dashboard Siswa')

@section('content')
@if (! $student)
    <div class="alert alert-warning">Profil siswa belum ditautkan oleh admin. Silakan hubungi admin sekolah.</div>
@else
    <div class="row">
        <div class="col-md-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="mb-3">Eskul Diikuti</h6>
                    <h3 class="mb-0">{{ $student->members->where('status', 'aktif')->count() }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="mb-3">Pendaftaran</h6>
                    <h3 class="mb-0">{{ $student->registrations->count() }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="mb-3">Absensi Terakhir</h6>
                    @php($lastAttendance = $student->attendances->sortByDesc('attendance_date')->first())
                    <h6 class="mb-0">{{ $lastAttendance?->attendance_date?->format('d M Y') ?? '-' }}</h6>
                    @if ($lastAttendance)
                        <x-status-badge :status="$lastAttendance->status" />
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="mb-3">Nilai Terbaru</h6>
                    @php($lastAssessment = $student->assessments->sortByDesc('created_at')->first())
                    <h3 class="mb-0">{{ $lastAssessment?->score ?? '-' }}</h3>
                    <span>{{ $lastAssessment?->predicate }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header"><h5>Status Pendaftaran</h5></div>
                <div class="card-body">
                    @forelse ($student->registrations as $registration)
                        <div class="d-flex justify-content-between border-bottom py-2">
                            <span>{{ $registration->extracurricular?->name }}</span>
                            <x-status-badge :status="$registration->status" />
                        </div>
                    @empty
                        <x-empty-state message="Belum ada pendaftaran." />
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header"><h5>Jadwal Eskul</h5></div>
                <div class="card-body">
                    @forelse ($student->members->where('status', 'aktif') as $member)
                        <h6>{{ $member->extracurricular?->name }}</h6>
                        @foreach ($member->extracurricular?->schedules ?? [] as $schedule)
                            <div class="d-flex justify-content-between border-bottom py-2">
                                <span>{{ $schedule->day }} - {{ substr($schedule->start_time, 0, 5) }} s/d {{ substr($schedule->end_time, 0, 5) }}</span>
                                <span>{{ $schedule->location }}</span>
                            </div>
                        @endforeach
                    @empty
                        <x-empty-state message="Belum menjadi anggota eskul aktif." />
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endif
@endsection
