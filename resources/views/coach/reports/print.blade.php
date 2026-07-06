<!DOCTYPE html>
<html lang="id">
<head>
    <title>Cetak Laporan Eskul</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{ asset('template/dist/assets/css/style.css') }}">
    <style>
        body { background: #fff; padding: 24px; }
        .card { box-shadow: none; border: 1px solid #ddd; }
        @media print { .no-print { display: none !important; } }
    </style>
</head>
<body>
    <div class="d-flex justify-content-between align-items-center mb-3 no-print">
        <h4>Laporan Eskul Dibina</h4>
        <button class="btn btn-primary" onclick="window.print()" type="button"><i class="feather icon-printer"></i> Cetak</button>
    </div>
    <div class="card">
        <div class="card-body">
            <h4 class="mb-1">SMK Yappika Legok</h4>
            <p class="text-muted">Laporan kegiatan ekstrakurikuler</p>
            @include('coach.reports.partials.report-body')
        </div>
    </div>
    <script>window.addEventListener('load', function () { window.print(); });</script>
</body>
</html>
