@extends('layouts.app')

@section('title', 'Detail Eskul')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5>{{ $extracurricular->name }}</h5>
                <x-status-badge :status="$extracurricular->status" />
            </div>
            <div class="card-body">
                <p>{{ $extracurricular->description ?: 'Belum ada deskripsi.' }}</p>
                <div class="row">
                    <div class="col-md-4"><strong>Pembina</strong><div>{{ $extracurricular->coach?->name }}</div></div>
                    <div class="col-md-4"><strong>Kuota</strong><div>{{ $extracurricular->quota ?? 'Tanpa batas' }}</div></div>
                    <div class="col-md-4"><strong>Status Pendaftaran</strong><div>@if ($registration)<x-status-badge :status="$registration->status" />@else <span class="badge badge-light">Belum daftar</span> @endif</div></div>
                </div>
                @if ($registration?->parent_rejection_reason)
                    <div class="alert alert-danger mt-3">Alasan orang tua: {{ $registration->parent_rejection_reason }}</div>
                @endif
                @if ($registration?->coach_rejection_reason)
                    <div class="alert alert-danger mt-3">Alasan pembina: {{ $registration->coach_rejection_reason }}</div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><h5>Jadwal</h5></div>
            <div class="card-body">
                @forelse ($extracurricular->schedules as $schedule)
                    <div class="border-bottom py-2">
                        <strong>{{ $schedule->day }}</strong>
                        <div>{{ substr($schedule->start_time, 0, 5) }} - {{ substr($schedule->end_time, 0, 5) }}</div>
                        <div class="text-muted">{{ $schedule->location }}</div>
                    </div>
                @empty
                    <x-empty-state message="Belum ada jadwal aktif." />
                @endforelse
            </div>
        </div>
    </div>
</div>

<div class="text-right">
    <a href="{{ route('student.extracurriculars.index') }}" class="btn btn-light">Kembali</a>
    @if ($student && ! $registration)
        <form method="POST" action="{{ route('student.extracurriculars.register', $extracurricular) }}" class="d-inline">
            @csrf
            <button class="btn btn-primary" type="submit"><i class="feather icon-send"></i> Daftar Eskul</button>
        </form>
    @endif
</div>
@endsection
