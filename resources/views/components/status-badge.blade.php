@props(['status'])

@php
    $classes = [
        'aktif' => 'badge-success',
        'nonaktif' => 'badge-secondary',
        'menunggu_validasi_orang_tua' => 'badge-warning',
        'menunggu_validasi_pembina' => 'badge-info',
        'ditolak_orang_tua' => 'badge-danger',
        'ditolak_pembina' => 'badge-danger',
        'diterima' => 'badge-success',
        'menunggu_approval' => 'badge-warning',
        'disetujui' => 'badge-success',
        'ditolak' => 'badge-danger',
    ];
    $label = ucwords(str_replace('_', ' ', (string) $status));
@endphp

<span class="badge {{ $classes[$status] ?? 'badge-light' }}">{{ $label }}</span>
