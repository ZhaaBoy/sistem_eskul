<div class="mb-3">
    <span class="badge badge-light">Periode: {{ $selectedPeriod ?: 'Semua' }}</span>
    <span class="badge badge-light">Semester: {{ $selectedSemester ?: 'Semua' }}</span>
</div>

<h6>Data Anggota</h6>
@if ($members->isEmpty())
    <x-empty-state message="Belum ada anggota aktif." />
@else
    <div class="table-responsive mb-4">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Siswa</th>
                    <th>Kelas</th>
                    <th>Eskul</th>
                    <th>Status</th>
                    <th>Jadwal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($members as $member)
                    <tr>
                        <td>{{ $member->student?->name }}</td>
                        <td>{{ $member->student?->class }}</td>
                        <td>{{ $member->extracurricular?->name }}</td>
                        <td><x-status-badge :status="$member->status" /></td>
                        <td>
                            @foreach ($member->extracurricular?->schedules ?? [] as $schedule)
                                <div>{{ $schedule->day }} {{ substr($schedule->start_time, 0, 5) }} - {{ substr($schedule->end_time, 0, 5) }}</div>
                            @endforeach
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif

<div class="row">
    <div class="col-lg-6">
        <h6>Rekap Absensi</h6>
        @forelse ($attendances->groupBy('extracurricular.name') as $eskulName => $items)
            <div class="border rounded p-2 mb-2">
                <strong>{{ $eskulName }}</strong>
                <div>Disetujui: {{ $items->where('status', 'disetujui')->count() }}</div>
                <div>Menunggu: {{ $items->where('status', 'menunggu_approval')->count() }}</div>
                <div>Ditolak: {{ $items->where('status', 'ditolak')->count() }}</div>
            </div>
        @empty
            <x-empty-state message="Belum ada absensi." />
        @endforelse
    </div>
    <div class="col-lg-6">
        <h6>Penilaian dan Catatan</h6>
        @forelse ($assessments as $assessment)
            <div class="border rounded p-2 mb-2">
                <strong>{{ $assessment->student?->name }} - {{ $assessment->extracurricular?->name }}</strong>
                <div>{{ $assessment->semester }} {{ $assessment->period }}: {{ $assessment->score }} ({{ $assessment->predicate }})</div>
                <div class="text-muted">{{ $assessment->notes }}</div>
            </div>
        @empty
            <x-empty-state message="Belum ada penilaian." />
        @endforelse
    </div>
</div>
