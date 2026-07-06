@extends('layouts.app')

@section('title', 'Input Penilaian')

@section('content')
@if (! $coach)
    <div class="alert alert-warning">Profil pembina belum ditautkan oleh admin.</div>
@else
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5>Penilaian Siswa</h5>
            <a href="{{ route('coach.assessments.create') }}" class="btn btn-primary btn-sm"><i class="feather icon-plus"></i> Input Nilai</a>
        </div>
        <div class="card-body">
            <form method="GET" class="row mb-3">
                <div class="col-md-3 mb-2">
                    <select name="extracurricular_id" class="form-control">
                        <option value="">Semua eskul</option>
                        @foreach ($coach->extracurriculars as $extracurricular)
                            <option value="{{ $extracurricular->id }}" @selected((string) request('extracurricular_id') === (string) $extracurricular->id)>{{ $extracurricular->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-2"><input type="text" name="student" value="{{ request('student') }}" class="form-control" placeholder="Nama siswa"></div>
                <div class="col-md-3 mb-2"><input type="text" name="period" value="{{ request('period') }}" class="form-control" placeholder="Periode"></div>
                <div class="col-md-3 mb-2"><button class="btn btn-secondary btn-block" type="submit"><i class="feather icon-search"></i> Filter</button></div>
            </form>

            @if ($assessments->isEmpty())
                <x-empty-state message="Belum ada penilaian." />
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Siswa</th>
                                <th>Eskul</th>
                                <th>Periode</th>
                                <th>Semester</th>
                                <th>Nilai</th>
                                <th>Predikat</th>
                                <th>Catatan</th>
                                <th class="text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($assessments as $assessment)
                                <tr>
                                    <td>{{ $assessment->student?->name }}</td>
                                    <td>{{ $assessment->extracurricular?->name }}</td>
                                    <td>{{ $assessment->period ?? '-' }}</td>
                                    <td>{{ $assessment->semester }}</td>
                                    <td>{{ $assessment->score }}</td>
                                    <td><span class="badge badge-primary">{{ $assessment->predicate }}</span></td>
                                    <td>{{ str($assessment->notes)->limit(60) }}</td>
                                    <td>
                                        <div class="action-row justify-content-end">
                                            <a href="{{ route('coach.assessments.edit', $assessment) }}" class="btn btn-info btn-sm"><i class="feather icon-edit"></i></a>
                                            <form method="POST" action="{{ route('coach.assessments.destroy', $assessment) }}" onsubmit="return confirm('Hapus penilaian ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-danger btn-sm" type="submit"><i class="feather icon-trash-2"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $assessments->links() }}
            @endif
        </div>
    </div>
@endif
@endsection
