@extends('layouts.app')

@section('title', 'Data Orang Tua')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5>Data Orang Tua</h5>
        <a href="{{ route('admin.parents.create') }}" class="btn btn-primary btn-sm"><i class="feather icon-plus"></i> Tambah Orang Tua</a>
    </div>
    <div class="card-body">
        <form method="GET" class="row mb-3">
            <div class="col-md-10 mb-2"><input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari nama/no HP/email"></div>
            <div class="col-md-2 mb-2"><button class="btn btn-secondary btn-block" type="submit"><i class="feather icon-search"></i> Filter</button></div>
        </form>

        @if ($parents->isEmpty())
            <x-empty-state message="Belum ada data orang tua." />
        @else
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Hubungan</th>
                            <th>No HP</th>
                            <th>Email</th>
                            <th>Akun</th>
                            <th>Anak</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($parents as $parent)
                            <tr>
                                <td>{{ $parent->name }}</td>
                                <td>{{ $parent->relationship }}</td>
                                <td>{{ $parent->phone ?? '-' }}</td>
                                <td>{{ $parent->email ?? '-' }}</td>
                                <td>{{ $parent->user?->email ?? '-' }}</td>
                                <td>{{ $parent->children->pluck('name')->join(', ') ?: '-' }}</td>
                                <td>
                                    <div class="action-row justify-content-end">
                                        <a href="{{ route('admin.parents.edit', $parent) }}" class="btn btn-info btn-sm"><i class="feather icon-edit"></i></a>
                                        <form method="POST" action="{{ route('admin.parents.destroy', $parent) }}" onsubmit="return confirm('Hapus data orang tua ini?')">
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
            {{ $parents->links() }}
        @endif
    </div>
</div>
@endsection
