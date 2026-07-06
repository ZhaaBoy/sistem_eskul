@extends('layouts.app')

@section('title', 'Buat Laporan')

@section('content')
@if (! $coach)
    <div class="alert alert-warning">Profil pembina belum ditautkan oleh admin.</div>
@else
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5>Laporan Eskul Dibina</h5>
            <a href="{{ route('coach.reports.print', request()->query()) }}" class="btn btn-secondary btn-sm" target="_blank"><i class="feather icon-printer"></i> Cetak</a>
        </div>
        <div class="card-body">
            <form method="GET" class="row mb-3">
                <div class="col-md-5 mb-2">
                    <select name="period" class="form-control">
                        <option value="">Semua periode</option>
                        @foreach ($availablePeriods as $period)
                            <option value="{{ $period }}" @selected(request('period') === $period)>{{ $period }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-5 mb-2">
                    <select name="semester" class="form-control">
                        <option value="">Semua semester</option>
                        @foreach ($availableSemesters->merge(['Ganjil', 'Genap'])->unique() as $semester)
                            <option value="{{ $semester }}" @selected(request('semester') === $semester)>{{ $semester }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 mb-2"><button class="btn btn-secondary btn-block" type="submit"><i class="feather icon-search"></i> Filter</button></div>
            </form>

            @include('coach.reports.partials.report-body')
        </div>
    </div>
@endif
@endsection
