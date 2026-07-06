@extends('layouts.app')

@section('title', $account->exists ? 'Edit Akun' : 'Tambah Akun')

@section('content')
<div class="card">
    <div class="card-header"><h5>{{ $account->exists ? 'Edit Akun' : 'Tambah Akun' }}</h5></div>
    <div class="card-body">
        <form method="POST" action="{{ $route }}">
            @csrf
            @if ($method !== 'POST')
                @method($method)
            @endif
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Nama</label>
                        <input type="text" name="name" value="{{ old('name', $account->name) }}" class="form-control" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" value="{{ old('email', $account->email) }}" class="form-control" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Password {{ $account->exists ? '(kosongkan jika tidak diubah)' : '' }}</label>
                        <input type="password" name="password" class="form-control" {{ $account->exists ? '' : 'required' }}>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" class="form-control" {{ $account->exists ? '' : 'required' }}>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Role</label>
                        <select name="role" class="form-control" required>
                            @foreach (['admin' => 'Admin', 'pembina' => 'Pembina', 'siswa' => 'Siswa', 'orang_tua' => 'Orang Tua'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('role', $account->role) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control" required>
                            <option value="aktif" @selected(old('status', $account->status) === 'aktif')>Aktif</option>
                            <option value="nonaktif" @selected(old('status', $account->status) === 'nonaktif')>Nonaktif</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="text-right">
                <a href="{{ route('admin.accounts.index') }}" class="btn btn-light">Batal</a>
                <button class="btn btn-primary" type="submit"><i class="feather icon-save"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
