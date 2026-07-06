@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
<div class="row">
    @foreach ([
        ['label' => 'Total Siswa', 'value' => $stats['total_siswa'], 'icon' => 'icon-user', 'color' => 'primary'],
        ['label' => 'Total Pembina', 'value' => $stats['total_pembina'], 'icon' => 'icon-award', 'color' => 'success'],
        ['label' => 'Total Orang Tua', 'value' => $stats['total_orang_tua'], 'icon' => 'icon-users', 'color' => 'info'],
        ['label' => 'Total Eskul', 'value' => $stats['total_eskul'], 'icon' => 'icon-layers', 'color' => 'warning'],
        ['label' => 'Pendaftaran Menunggu', 'value' => $stats['pendaftaran_menunggu'], 'icon' => 'icon-clock', 'color' => 'danger'],
        ['label' => 'Anggota Aktif', 'value' => $stats['anggota_aktif'], 'icon' => 'icon-check-circle', 'color' => 'success'],
        ['label' => 'Absensi Menunggu', 'value' => $stats['absensi_menunggu'], 'icon' => 'icon-check-square', 'color' => 'warning'],
    ] as $card)
        <div class="col-md-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="mb-3">{{ $card['label'] }}</h6>
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h3 class="mb-0">{{ $card['value'] }}</h3>
                        </div>
                        <div class="col-4 text-right">
                            <i class="feather {{ $card['icon'] }} text-{{ $card['color'] }} f-28"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h5>Pendaftaran Terbaru</h5>
            </div>
            <div class="card-body table-border-style">
                @if ($latestRegistrations->isEmpty())
                    <x-empty-state message="Belum ada pendaftaran eskul." />
                @else
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Siswa</th>
                                    <th>Eskul</th>
                                    <th>Pembina</th>
                                    <th>Status</th>
                                    <th>Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($latestRegistrations as $registration)
                                    <tr>
                                        <td>{{ $registration->student?->name }}</td>
                                        <td>{{ $registration->extracurricular?->name }}</td>
                                        <td>{{ $registration->extracurricular?->coach?->name }}</td>
                                        <td><x-status-badge :status="$registration->status" /></td>
                                        <td>{{ $registration->created_at?->format('d M Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
