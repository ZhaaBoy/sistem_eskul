@extends('layouts.app')

@section('title', 'Validasi Pendaftaran')

@section('content')
@if (! $coach)
    <div class="alert alert-warning">Profil pembina belum ditautkan oleh admin.</div>
@else
    <div class="card">
        <div class="card-header"><h5>Pendaftaran Eskul Dibina</h5></div>
        <div class="card-body">
            <form method="GET" class="row mb-3">
                <div class="col-md-10 mb-2">
                    <select name="status" class="form-control">
                        <option value="">Semua status</option>
                        <option value="menunggu_validasi_pembina" @selected(request('status') === 'menunggu_validasi_pembina')>Menunggu Validasi Pembina</option>
                        <option value="diterima" @selected(request('status') === 'diterima')>Diterima</option>
                        <option value="ditolak_pembina" @selected(request('status') === 'ditolak_pembina')>Ditolak Pembina</option>
                    </select>
                </div>
                <div class="col-md-2 mb-2"><button class="btn btn-secondary btn-block" type="submit"><i class="feather icon-search"></i> Filter</button></div>
            </form>

            @if ($registrations->isEmpty())
                <x-empty-state message="Belum ada pendaftaran untuk divalidasi." />
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Siswa</th>
                                <th>Orang Tua</th>
                                <th>Eskul</th>
                                <th>Status</th>
                                <th>Alasan</th>
                                <th class="text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($registrations as $registration)
                                <tr>
                                    <td>{{ $registration->student?->name }}</td>
                                    <td>{{ $registration->student?->parent?->name ?? '-' }}</td>
                                    <td>{{ $registration->extracurricular?->name }}</td>
                                    <td><x-status-badge :status="$registration->status" /></td>
                                    <td>{{ $registration->coach_rejection_reason ?? '-' }}</td>
                                    <td>
                                        @if ($registration->status === \App\Models\ExtracurricularRegistration::WAITING_COACH)
                                            <div class="action-row justify-content-end">
                                                <form method="POST" action="{{ route('coach.registrations.approve', $registration) }}">
                                                    @csrf
                                                    <button class="btn btn-success btn-sm" type="submit"><i class="feather icon-check"></i> Approve</button>
                                                </form>
                                                <button class="btn btn-danger btn-sm" type="button" data-toggle="collapse" data-target="#reject-coach-{{ $registration->id }}"><i class="feather icon-x"></i> Reject</button>
                                            </div>
                                            <div class="collapse mt-2" id="reject-coach-{{ $registration->id }}">
                                                <form method="POST" action="{{ route('coach.registrations.reject', $registration) }}" class="border rounded p-2">
                                                    @csrf
                                                    <div class="form-group mb-2"><textarea name="reason" class="form-control" rows="2" placeholder="Alasan penolakan" required></textarea></div>
                                                    <button class="btn btn-danger btn-sm" type="submit">Simpan Penolakan</button>
                                                </form>
                                            </div>
                                        @else
                                            <span class="text-muted">Sudah diproses</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $registrations->links() }}
            @endif
        </div>
    </div>
@endif
@endsection
