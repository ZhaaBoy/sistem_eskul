@extends('layouts.app')

@section('title', $student->exists ? 'Edit Siswa' : 'Tambah Siswa')

@section('content')
<div class="card">
    <div class="card-header"><h5>{{ $student->exists ? 'Edit Siswa' : 'Tambah Siswa' }}</h5></div>
    <div class="card-body">
        <form method="POST" action="{{ $route }}">
            @csrf
            @if ($method !== 'POST')
                @method($method)
            @endif
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Akun Siswa</label>
                        <select name="user_id" class="form-control">
                            <option value="">Belum ditautkan</option>
                            @foreach ($userOptions as $user)
                                <option value="{{ $user->id }}" @selected((string) old('user_id', $student->user_id) === (string) $user->id)>{{ $user->name }} - {{ $user->email }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Orang Tua/Wali</label>
                        <select name="parent_id" class="form-control">
                            <option value="">Belum ditautkan</option>
                            @foreach ($parents as $parent)
                                <option value="{{ $parent->id }}" @selected((string) old('parent_id', $student->parent_id) === (string) $parent->id)>{{ $parent->name }} ({{ $parent->relationship }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6"><div class="form-group"><label>Nama Siswa</label><input type="text" name="name" value="{{ old('name', $student->name) }}" class="form-control" required></div></div>
                <div class="col-md-3"><div class="form-group"><label>NIS</label><input type="text" name="nis" value="{{ old('nis', $student->nis) }}" class="form-control"></div></div>
                <div class="col-md-3"><div class="form-group"><label>NISN</label><input type="text" name="nisn" value="{{ old('nisn', $student->nisn) }}" class="form-control"></div></div>
                <div class="col-md-3"><div class="form-group"><label>Kelas</label><input type="text" name="class" value="{{ old('class', $student->class) }}" class="form-control"></div></div>
                <div class="col-md-3"><div class="form-group"><label>Jurusan</label><input type="text" name="major" value="{{ old('major', $student->major) }}" class="form-control"></div></div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Jenis Kelamin</label>
                        <select name="gender" class="form-control">
                            <option value="">Pilih</option>
                            <option value="Laki-laki" @selected(old('gender', $student->gender) === 'Laki-laki')>Laki-laki</option>
                            <option value="Perempuan" @selected(old('gender', $student->gender) === 'Perempuan')>Perempuan</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3"><div class="form-group"><label>Tanggal Lahir</label><input type="date" name="birth_date" value="{{ old('birth_date', optional($student->birth_date)->format('Y-m-d')) }}" class="form-control"></div></div>
                <div class="col-md-4"><div class="form-group"><label>No HP</label><input type="text" name="phone" value="{{ old('phone', $student->phone) }}" class="form-control"></div></div>
                <div class="col-md-4"><div class="form-group"><label>Email</label><input type="email" name="email" value="{{ old('email', $student->email) }}" class="form-control"></div></div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control" required>
                            <option value="aktif" @selected(old('status', $student->status) === 'aktif')>Aktif</option>
                            <option value="nonaktif" @selected(old('status', $student->status) === 'nonaktif')>Nonaktif</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-12"><div class="form-group"><label>Alamat</label><textarea name="address" class="form-control" rows="3">{{ old('address', $student->address) }}</textarea></div></div>
            </div>
            <div class="text-right">
                <a href="{{ route('admin.students.index') }}" class="btn btn-light">Batal</a>
                <button class="btn btn-primary" type="submit"><i class="feather icon-save"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
