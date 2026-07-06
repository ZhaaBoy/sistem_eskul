@extends('layouts.app')

@section('title', 'Daftar Eskul')

@section('content')
@if (! $student)
    <div class="alert alert-warning">Profil siswa belum ditautkan oleh admin. Anda belum bisa mendaftar eskul.</div>
@endif

<div class="card">
    <div class="card-header"><h5>Ekstrakurikuler Aktif</h5></div>
    <div class="card-body">
        <form method="GET" class="row mb-3">
            <div class="col-md-10 mb-2"><input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari eskul"></div>
            <div class="col-md-2 mb-2"><button class="btn btn-secondary btn-block" type="submit"><i class="feather icon-search"></i> Filter</button></div>
        </form>

        @if ($extracurriculars->isEmpty())
            <x-empty-state message="Belum ada eskul aktif." />
        @else
            <div class="row">
                @foreach ($extracurriculars as $extracurricular)
                    @php($status = $registrations[$extracurricular->id] ?? null)
                    <div class="col-md-6 col-xl-4">
                        <div class="card border">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <h5>{{ $extracurricular->name }}</h5>
                                    @if ($status)
                                        <x-status-badge :status="$status" />
                                    @else
                                        <span class="badge badge-light">Belum daftar</span>
                                    @endif
                                </div>
                                <p class="text-muted mb-2">{{ str($extracurricular->description)->limit(120) }}</p>
                                <div class="mb-2"><i class="feather icon-award"></i> {{ $extracurricular->coach?->name }}</div>
                                <div class="mb-3"><i class="feather icon-users"></i> {{ $extracurricular->members->where('status', 'aktif')->count() }} / {{ $extracurricular->quota ?? 'Tanpa batas' }}</div>
                                <div class="action-row">
                                    <a href="{{ route('student.extracurriculars.show', $extracurricular) }}" class="btn btn-info btn-sm"><i class="feather icon-eye"></i> Detail</a>
                                    @if ($student && ! $status)
                                        <form method="POST" action="{{ route('student.extracurriculars.register', $extracurricular) }}">
                                            @csrf
                                            <button class="btn btn-primary btn-sm" type="submit"><i class="feather icon-send"></i> Daftar</button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            {{ $extracurriculars->links() }}
        @endif
    </div>
</div>
@endsection
