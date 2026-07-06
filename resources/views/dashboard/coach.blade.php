@extends('layouts.app')

@section('title', 'Dashboard Pembina')

@section('content')
@if (! $coach)
    <div class="alert alert-warning">Profil pembina belum ditautkan oleh admin. Silakan hubungi admin sekolah.</div>
@else
    <div class="row">
        @foreach ([
            ['label' => 'Eskul Dibina', 'value' => $stats['total_eskul'], 'icon' => 'icon-layers', 'color' => 'primary'],
            ['label' => 'Total Anggota', 'value' => $stats['total_anggota'], 'icon' => 'icon-users', 'color' => 'success'],
            ['label' => 'Pendaftaran Menunggu', 'value' => $stats['pendaftaran_menunggu'], 'icon' => 'icon-clock', 'color' => 'warning'],
            ['label' => 'Absensi Menunggu', 'value' => $stats['absensi_menunggu'], 'icon' => 'icon-check-square', 'color' => 'danger'],
            ['label' => 'Penilaian', 'value' => $stats['penilaian'], 'icon' => 'icon-edit', 'color' => 'info'],
        ] as $card)
            <div class="col-md-6 col-xl-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="mb-3">{{ $card['label'] }}</h6>
                        <div class="row align-items-center">
                            <div class="col-8"><h3 class="mb-0">{{ $card['value'] }}</h3></div>
                            <div class="col-4 text-right"><i class="feather {{ $card['icon'] }} text-{{ $card['color'] }} f-28"></i></div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="card">
        <div class="card-header"><h5>Eskul dan Jadwal</h5></div>
        <div class="card-body">
            @forelse ($coach->extracurriculars as $extracurricular)
                <div class="border-bottom pb-3 mb-3">
                    <div class="d-flex justify-content-between">
                        <h6>{{ $extracurricular->name }}</h6>
                        <x-status-badge :status="$extracurricular->status" />
                    </div>
                    @forelse ($extracurricular->schedules as $schedule)
                        <span class="badge badge-light mr-1">{{ $schedule->day }} {{ substr($schedule->start_time, 0, 5) }} - {{ substr($schedule->end_time, 0, 5) }}</span>
                    @empty
                        <span class="text-muted">Belum ada jadwal.</span>
                    @endforelse
                </div>
            @empty
                <x-empty-state message="Belum ada eskul yang dibina." />
            @endforelse
        </div>
    </div>
@endif
@endsection
