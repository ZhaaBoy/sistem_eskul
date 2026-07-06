@extends('layouts.app')

@section('title', 'Validasi Pendaftaran')

@section('content')
@if (! $parent)
    <div class="alert alert-warning">Profil orang tua belum ditautkan oleh admin.</div>
@else
    <div class="card">
        <div class="card-header"><h5>Pendaftaran Eskul Anak</h5></div>
        <div class="card-body table-border-style">
            @if ($registrations->isEmpty())
                <x-empty-state message="Belum ada pendaftaran anak." />
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Anak</th>
                                <th>Eskul</th>
                                <th>Pembina</th>
                                <th>Status</th>
                                <th>Alasan</th>
                                <th class="text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($registrations as $registration)
                                <tr>
                                    <td>{{ $registration->student?->name }}</td>
                                    <td>{{ $registration->extracurricular?->name }}</td>
                                    <td>{{ $registration->extracurricular?->coach?->name }}</td>
                                    <td><x-status-badge :status="$registration->status" /></td>
                                    <td>{{ $registration->parent_rejection_reason ?? $registration->coach_rejection_reason ?? '-' }}</td>
                                    <td>
                                        @if ($registration->status === \App\Models\ExtracurricularRegistration::WAITING_PARENT)
                                            <div class="action-row justify-content-end">
                                                <form method="POST" action="{{ route('parent.registrations.approve', $registration) }}">
                                                    @csrf
                                                    <button class="btn btn-success btn-sm" type="submit"><i class="feather icon-check"></i> Approve</button>
                                                </form>
                                                <button class="btn btn-danger btn-sm" type="button" data-toggle="collapse" data-target="#reject-parent-{{ $registration->id }}"><i class="feather icon-x"></i> Reject</button>
                                            </div>
                                            <div class="collapse mt-2" id="reject-parent-{{ $registration->id }}">
                                                <form method="POST" action="{{ route('parent.registrations.reject', $registration) }}" class="border rounded p-2">
                                                    @csrf
                                                    <div class="form-group mb-2">
                                                        <textarea name="reason" class="form-control" rows="2" placeholder="Alasan penolakan" required></textarea>
                                                    </div>
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
