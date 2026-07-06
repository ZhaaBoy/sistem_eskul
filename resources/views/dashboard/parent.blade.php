@extends('layouts.app')

@section('title', 'Dashboard Orang Tua')

@section('content')
@if (! $parent)
    <div class="alert alert-warning">Profil orang tua belum ditautkan oleh admin. Silakan hubungi admin sekolah.</div>
@else
    <div class="row">
        <div class="col-md-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="mb-3">Jumlah Anak</h6>
                    <h3 class="mb-0">{{ $parent->children->count() }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="mb-3">Pendaftaran Anak</h6>
                    <h3 class="mb-0">{{ $parent->children->flatMap->registrations->count() }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="mb-3">Absensi Anak</h6>
                    <h3 class="mb-0">{{ $parent->children->flatMap->attendances->count() }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="mb-3">Nilai Anak</h6>
                    <h3 class="mb-0">{{ $parent->children->flatMap->assessments->count() }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h5>Ringkasan Anak</h5></div>
        <div class="card-body table-border-style">
            @if ($parent->children->isEmpty())
                <x-empty-state message="Belum ada data anak yang terhubung." />
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Kelas</th>
                                <th>Eskul Aktif</th>
                                <th>Pendaftaran Terakhir</th>
                                <th>Nilai Terbaru</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($parent->children as $child)
                                @php($latestRegistration = $child->registrations->sortByDesc('created_at')->first())
                                @php($latestAssessment = $child->assessments->sortByDesc('created_at')->first())
                                <tr>
                                    <td>{{ $child->name }}</td>
                                    <td>{{ $child->class }}</td>
                                    <td>{{ $child->members->where('status', 'aktif')->count() }}</td>
                                    <td>
                                        @if ($latestRegistration)
                                            {{ $latestRegistration->extracurricular?->name }}
                                            <x-status-badge :status="$latestRegistration->status" />
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ $latestAssessment ? $latestAssessment->score.' / '.$latestAssessment->predicate : '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endif
@endsection
