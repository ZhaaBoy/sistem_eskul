@extends('layouts.app')

@section('title', 'Kelola Akun')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5>Akun Pengguna</h5>
        <a href="{{ route('admin.accounts.create') }}" class="btn btn-primary btn-sm"><i class="feather icon-plus"></i> Tambah Akun</a>
    </div>
    <div class="card-body">
        <form method="GET" class="row mb-3">
            <div class="col-md-4 mb-2">
                <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari nama/email">
            </div>
            <div class="col-md-3 mb-2">
                <select name="role" class="form-control">
                    <option value="">Semua role</option>
                    @foreach (['admin' => 'Admin', 'pembina' => 'Pembina', 'siswa' => 'Siswa', 'orang_tua' => 'Orang Tua'] as $value => $label)
                        <option value="{{ $value }}" @selected(request('role') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 mb-2">
                <select name="status" class="form-control">
                    <option value="">Semua status</option>
                    <option value="aktif" @selected(request('status') === 'aktif')>Aktif</option>
                    <option value="nonaktif" @selected(request('status') === 'nonaktif')>Nonaktif</option>
                </select>
            </div>
            <div class="col-md-2 mb-2">
                <button class="btn btn-secondary btn-block" type="submit"><i class="feather icon-search"></i> Filter</button>
            </div>
        </form>

        @if ($accounts->isEmpty())
            <x-empty-state message="Belum ada akun." />
        @else
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($accounts as $account)
                            <tr>
                                <td>{{ $account->name }}</td>
                                <td>{{ $account->email }}</td>
                                <td>{{ strtoupper(str_replace('_', ' ', $account->role)) }}</td>
                                <td><x-status-badge :status="$account->status" /></td>
                                <td>
                                    <div class="action-row justify-content-end">
                                        <a href="{{ route('admin.accounts.edit', $account) }}" class="btn btn-info btn-sm"><i class="feather icon-edit"></i></a>
                                        <form method="POST" action="{{ route('admin.accounts.toggle-status', $account) }}">
                                            @csrf
                                            <button class="btn btn-warning btn-sm" type="submit"><i class="feather icon-power"></i></button>
                                        </form>
                                        <button class="btn btn-secondary btn-sm" type="button" data-toggle="collapse" data-target="#reset-{{ $account->id }}"><i class="feather icon-key"></i></button>
                                    </div>
                                    <div class="collapse mt-2" id="reset-{{ $account->id }}">
                                        <form method="POST" action="{{ route('admin.accounts.reset-password', $account) }}" class="border rounded p-2">
                                            @csrf
                                            <div class="form-row">
                                                <div class="col-md-5 mb-2">
                                                    <input type="password" name="new_password" class="form-control form-control-sm" placeholder="Password baru" required>
                                                </div>
                                                <div class="col-md-5 mb-2">
                                                    <input type="password" name="new_password_confirmation" class="form-control form-control-sm" placeholder="Konfirmasi" required>
                                                </div>
                                                <div class="col-md-2 mb-2">
                                                    <button class="btn btn-primary btn-sm btn-block" type="submit">Reset</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $accounts->links() }}
        @endif
    </div>
</div>
@endsection
