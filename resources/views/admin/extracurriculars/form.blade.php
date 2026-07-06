@extends('layouts.app')

@section('title', $extracurricular->exists ? 'Edit Eskul' : 'Tambah Eskul')

@section('content')
<div class="card">
    <div class="card-header"><h5>{{ $extracurricular->exists ? 'Edit Eskul' : 'Tambah Eskul' }}</h5></div>
    <div class="card-body">
        <form method="POST" action="{{ $route }}">
            @csrf
            @if ($method !== 'POST')
                @method($method)
            @endif
            <div class="row">
                <div class="col-md-6"><div class="form-group"><label>Nama Eskul</label><input type="text" name="name" value="{{ old('name', $extracurricular->name) }}" class="form-control" required></div></div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Pembina</label>
                        <select name="coach_id" class="form-control" required>
                            <option value="">Pilih pembina</option>
                            @foreach ($coaches as $coach)
                                <option value="{{ $coach->id }}" @selected((string) old('coach_id', $extracurricular->coach_id) === (string) $coach->id)>{{ $coach->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4"><div class="form-group"><label>Kuota</label><input type="number" min="1" name="quota" value="{{ old('quota', $extracurricular->quota) }}" class="form-control" placeholder="Kosongkan jika tanpa batas"></div></div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control" required>
                            <option value="aktif" @selected(old('status', $extracurricular->status) === 'aktif')>Aktif</option>
                            <option value="nonaktif" @selected(old('status', $extracurricular->status) === 'nonaktif')>Nonaktif</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-12"><div class="form-group"><label>Deskripsi</label><textarea name="description" class="form-control" rows="4">{{ old('description', $extracurricular->description) }}</textarea></div></div>
            </div>
            <div class="text-right">
                <a href="{{ route('admin.extracurriculars.index') }}" class="btn btn-light">Batal</a>
                <button class="btn btn-primary" type="submit"><i class="feather icon-save"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
