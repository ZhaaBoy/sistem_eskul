@extends('layouts.app')

@section('title', $schedule->exists ? 'Edit Jadwal' : 'Tambah Jadwal')

@section('content')
<div class="card">
    <div class="card-header"><h5>{{ $schedule->exists ? 'Edit Jadwal' : 'Tambah Jadwal' }}</h5></div>
    <div class="card-body">
        <form method="POST" action="{{ $route }}">
            @csrf
            @if ($method !== 'POST')
                @method($method)
            @endif
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Eskul</label>
                        <select name="extracurricular_id" class="form-control" required>
                            <option value="">Pilih eskul</option>
                            @foreach ($extracurriculars as $extracurricular)
                                <option value="{{ $extracurricular->id }}" @selected((string) old('extracurricular_id', $schedule->extracurricular_id) === (string) $extracurricular->id)>{{ $extracurricular->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Hari</label>
                        <select name="day" class="form-control" required>
                            @foreach (['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'] as $day)
                                <option value="{{ $day }}" @selected(old('day', $schedule->day) === $day)>{{ $day }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control" required>
                            <option value="aktif" @selected(old('status', $schedule->status) === 'aktif')>Aktif</option>
                            <option value="nonaktif" @selected(old('status', $schedule->status) === 'nonaktif')>Nonaktif</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3"><div class="form-group"><label>Jam Mulai</label><input type="time" name="start_time" value="{{ old('start_time', $schedule->start_time ? substr($schedule->start_time, 0, 5) : '') }}" class="form-control" required></div></div>
                <div class="col-md-3"><div class="form-group"><label>Jam Selesai</label><input type="time" name="end_time" value="{{ old('end_time', $schedule->end_time ? substr($schedule->end_time, 0, 5) : '') }}" class="form-control" required></div></div>
                <div class="col-md-6"><div class="form-group"><label>Lokasi</label><input type="text" name="location" value="{{ old('location', $schedule->location) }}" class="form-control"></div></div>
                <div class="col-md-12"><div class="form-group"><label>Keterangan</label><textarea name="description" class="form-control" rows="3">{{ old('description', $schedule->description) }}</textarea></div></div>
            </div>
            <div class="text-right">
                <a href="{{ route('admin.schedules.index') }}" class="btn btn-light">Batal</a>
                <button class="btn btn-primary" type="submit"><i class="feather icon-save"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
