@extends('layouts.app')

@section('title', $parent->exists ? 'Edit Orang Tua' : 'Tambah Orang Tua')

@section('content')
<div class="card">
    <div class="card-header"><h5>{{ $parent->exists ? 'Edit Orang Tua' : 'Tambah Orang Tua' }}</h5></div>
    <div class="card-body">
        <form method="POST" action="{{ $route }}">
            @csrf
            @if ($method !== 'POST')
                @method($method)
            @endif
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Akun Orang Tua</label>
                        <select name="user_id" class="form-control">
                            <option value="">Belum ditautkan</option>
                            @foreach ($userOptions as $user)
                                <option value="{{ $user->id }}" @selected((string) old('user_id', $parent->user_id) === (string) $user->id)>{{ $user->name }} - {{ $user->email }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6"><div class="form-group"><label>Nama</label><input type="text" name="name" value="{{ old('name', $parent->name) }}" class="form-control" required></div></div>
                <div class="col-md-4"><div class="form-group"><label>No HP</label><input type="text" name="phone" value="{{ old('phone', $parent->phone) }}" class="form-control"></div></div>
                <div class="col-md-4"><div class="form-group"><label>Email</label><input type="email" name="email" value="{{ old('email', $parent->email) }}" class="form-control"></div></div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Hubungan</label>
                        <select name="relationship" class="form-control" required>
                            @foreach (['Ayah', 'Ibu', 'Wali'] as $relationship)
                                <option value="{{ $relationship }}" @selected(old('relationship', $parent->relationship) === $relationship)>{{ $relationship }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-12"><div class="form-group"><label>Alamat</label><textarea name="address" class="form-control" rows="3">{{ old('address', $parent->address) }}</textarea></div></div>
            </div>
            <div class="text-right">
                <a href="{{ route('admin.parents.index') }}" class="btn btn-light">Batal</a>
                <button class="btn btn-primary" type="submit"><i class="feather icon-save"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
