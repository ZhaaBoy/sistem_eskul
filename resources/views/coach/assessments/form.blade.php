@extends('layouts.app')

@section('title', $assessment->exists ? 'Edit Penilaian' : 'Input Penilaian')

@section('content')
<div class="card">
    <div class="card-header"><h5>{{ $assessment->exists ? 'Edit Penilaian' : 'Input Penilaian' }}</h5></div>
    <div class="card-body">
        <form method="POST" action="{{ $route }}">
            @csrf
            @if ($method !== 'POST')
                @method($method)
            @endif
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Anggota Eskul</label>
                        <select name="member_picker" class="form-control" onchange="if(this.value){const data=this.value.split('|');document.getElementById('student_id').value=data[0];document.getElementById('extracurricular_id').value=data[1];}">
                            <option value="">Pilih anggota</option>
                            @foreach ($members as $member)
                                <option value="{{ $member->student_id }}|{{ $member->extracurricular_id }}" @selected((string) old('student_id', $assessment->student_id) === (string) $member->student_id && (string) old('extracurricular_id', $assessment->extracurricular_id) === (string) $member->extracurricular_id)>
                                    {{ $member->student?->name }} - {{ $member->extracurricular?->name }}
                                </option>
                            @endforeach
                        </select>
                        <input type="hidden" id="student_id" name="student_id" value="{{ old('student_id', $assessment->student_id) }}">
                        <input type="hidden" id="extracurricular_id" name="extracurricular_id" value="{{ old('extracurricular_id', $assessment->extracurricular_id) }}">
                    </div>
                </div>
                <div class="col-md-3"><div class="form-group"><label>Periode</label><input type="text" name="period" value="{{ old('period', $assessment->period) }}" class="form-control" placeholder="2025/2026"></div></div>
                <div class="col-md-3"><div class="form-group"><label>Semester</label><input type="text" name="semester" value="{{ old('semester', $assessment->semester) }}" class="form-control" placeholder="Ganjil/Genap" required></div></div>
                <div class="col-md-3"><div class="form-group"><label>Nilai Angka</label><input type="number" step="0.01" min="0" max="100" name="score" value="{{ old('score', $assessment->score) }}" class="form-control" required></div></div>
                <div class="col-md-3"><div class="form-group"><label>Predikat</label><input type="text" name="predicate" value="{{ old('predicate', $assessment->predicate) }}" class="form-control" placeholder="Otomatis jika kosong"></div></div>
                <div class="col-md-12"><div class="form-group"><label>Catatan Pembina</label><textarea name="notes" class="form-control" rows="4">{{ old('notes', $assessment->notes) }}</textarea></div></div>
            </div>
            <div class="text-right">
                <a href="{{ route('coach.assessments.index') }}" class="btn btn-light">Batal</a>
                <button class="btn btn-primary" type="submit"><i class="feather icon-save"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
