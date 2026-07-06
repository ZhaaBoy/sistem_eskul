@extends('layouts.app')

@section('title', $coach->exists ? 'Edit Pembina' : 'Tambah Pembina')

@section('content')
<div class="card">
    <div class="card-header"><h5>{{ $coach->exists ? 'Edit Pembina' : 'Tambah Pembina' }}</h5></div>
    <div class="card-body">
        <form method="POST" action="{{ $route }}">
            @csrf
            @if ($method !== 'POST')
                @method($method)
            @endif
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Akun Pembina</label>
                        <select name="user_id" class="form-control">
                            <option value="">Belum ditautkan</option>
                            @foreach ($userOptions as $user)
                                <option value="{{ $user->id }}" @selected((string) old('user_id', $coach->user_id) === (string) $user->id)>{{ $user->name }} - {{ $user->email }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6"><div class="form-group"><label>Nama</label><input type="text" name="name" value="{{ old('name', $coach->name) }}" class="form-control" required></div></div>
                <div class="col-md-4"><div class="form-group"><label>NIP</label><input type="text" name="nip" value="{{ old('nip', $coach->nip) }}" class="form-control"></div></div>
                <div class="col-md-4"><div class="form-group"><label>No HP</label><input type="text" name="phone" value="{{ old('phone', $coach->phone) }}" class="form-control"></div></div>
                <div class="col-md-4"><div class="form-group"><label>Email</label><input type="email" name="email" value="{{ old('email', $coach->email) }}" class="form-control"></div></div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control" required>
                            <option value="aktif" @selected(old('status', $coach->status) === 'aktif')>Aktif</option>
                            <option value="nonaktif" @selected(old('status', $coach->status) === 'nonaktif')>Nonaktif</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="text-right">
                <a href="{{ route('admin.coaches.index') }}" class="btn btn-light">Batal</a>
                <button class="btn btn-primary" type="submit"><i class="feather icon-save"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
