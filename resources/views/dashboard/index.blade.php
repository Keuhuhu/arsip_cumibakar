@extends('app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

{{-- ========== STATISTIK UTAMA ========== --}}
<div class="stat-grid">
    <div class="stat-card" style="--stat-color: var(--hijau);">
        <span class="stat-icon">📄</span>
        <div class="stat-label">Total Dokumen</div>
        <div class="stat-value">{{ $stats['total_dokumen'] }}</div>
        <div class="stat-sub">+{{ $stats['total_bulan_ini'] }} bulan ini</div>
    </div>
    <div class="stat-card" style="--stat-color: var(--biru);">
        <span class="stat-icon">📥</span>
        <div class="stat-label">Surat Masuk</div>
        <div class="stat-value">{{ $stats['surat_masuk'] }}</div>
        <div class="stat-sub">{{ $stats['agenda_masuk_bulan'] }} agenda bulan ini</div>
    </div>
    <div class="stat-card" style="--stat-color: #e9a800;">
        <span class="stat-icon">📤</span>
        <div class="stat-label">Surat Keluar</div>
        <div class="stat-value">{{ $stats['surat_keluar'] }}</div>
        <div class="stat-sub">{{ $stats['agenda_keluar_bulan'] }} agenda bulan ini</div>
    </div>
    @if(auth()->user()->isSuperAdmin())
    <div class="stat-card" style="--stat-color: var(--merah);">
        <span class="stat-icon">⏳</span>
        <div class="stat-label">Tindak Lanjut</div>
        <div class="stat-value">{{ $stats['tindak_lanjut'] }}</div>
        <div class="stat-sub">Menunggu ditindaklanjuti</div>
    </div>
    @endif
</div>

{{-- ========== CHART + KATEGORI ========== --}}
<div class="row">
    {{-- Grafik Bulanan --}}
    <div class="col-6">
        <div class="card">
            <div class="card-header">
                <span class="card-title">📊 Statistik 12 Bulan Terakhir</span>
            </div>
            <div class="card-body">
                <div class="chart-wrap">
                    <canvas id="chartBulanan"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Per Kategori --}}
    <div class="col-6">
        <div class="card">
            <div class="card-header">
                <span class="card-title">🏷️ Arsip per Kategori</span>
            </div>
            <div class="card-body" style="padding: 0;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Kategori</th>
                            <th class="text-right">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($perKategori as $kat)
                        <tr>
                            <td>
                                @if($kat->icon)<span style="margin-right:6px">{{ $kat->icon }}</span>@endif
                                {{ $kat->nama }}
                            </td>
                            <td class="text-right">
                                <span class="badge badge-primary">{{ $kat->dokumens_count }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="table-empty">
                                <span class="empty-icon">🏷️</span>
                                Belum ada kategori
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- ========== DOKUMEN TERBARU + AKTIVITAS ========== --}}
<div class="row">
    {{-- Tabel Dokumen Terbaru --}}
    <div class="col-6">
        <div class="card">
            <div class="card-header">
                <span class="card-title">📁 Dokumen Terbaru</span>
                <a href="{{ route('dokumen.index') }}" class="btn btn-outline-primary btn-xs">Lihat Semua →</a>
            </div>
            <div class="card-body" style="padding: 0;">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Perihal</th>
                                <th>Jenis</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dokumenTerbaru as $dok)
                            <tr>
                                <td>
                                    <a href="{{ route('dokumen.show', $dok) }}" class="truncate" title="{{ $dok->perihal }}">
                                        {{ $dok->file_icon }} {{ Str::limit($dok->perihal, 40) }}
                                    </a>
                                </td>
                                <td><span class="badge badge-info">{{ $dok->jenis_label }}</span></td>
                                <td><span class="badge {{ $dok->status_badge }}">{{ $dok->status_label }}</span></td>
                                <td style="white-space:nowrap; font-size:12px; color:var(--text-soft)">{{ $dok->created_at->format('d M Y') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="table-empty">
                                    <span class="empty-icon">📁</span>
                                    Belum ada dokumen yang diunggah
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Log Aktivitas Terbaru --}}
    <div class="col-6">
        @if(auth()->user()->isSuperAdmin())
        <div class="card">
            <div class="card-header">
                <span class="card-title">🔍 Aktivitas Terbaru</span>
                <a href="{{ route('log.index') }}" class="btn btn-outline-primary btn-xs">Lihat Semua →</a>
            </div>
            <div class="card-body">
                <ul class="log-list">
                    @forelse($logTerbaru as $log)
                    <li class="log-item">
                        <span class="log-dot {{ $log->action }}"></span>
                        <div class="log-content">
                            <div class="log-desc">
                                <span class="badge {{ $log->action_badge }}" style="margin-right:4px">{{ $log->action_label }}</span>
                                {{ $log->description }}
                            </div>
                            <div class="log-meta">
                                {{ $log->user?->name ?? 'System' }} &middot; {{ $log->created_at->diffForHumans() }}
                            </div>
                        </div>
                    </li>
                    @empty
                    <li class="log-item" style="justify-content:center; color:var(--text-soft); padding:30px 0;">
                        Belum ada aktivitas tercatat
                    </li>
                    @endforelse
                </ul>
            </div>
        </div>
        @endif

        {{-- Agenda Terbaru --}}
        @if($agendaTerbaru->count() > 0)
        <div class="card">
            <div class="card-header">
                <span class="card-title">📋 Agenda Terbaru</span>
                <a href="{{ route('agenda.index') }}" class="btn btn-outline-primary btn-xs">Lihat Semua →</a>
            </div>
            <div class="card-body" style="padding:0;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>No. Agenda</th>
                            <th>Perihal</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($agendaTerbaru as $agenda)
                        <tr>
                            <td><span class="agenda-no">{{ $agenda->no_agenda }}</span></td>
                            <td>{{ Str::limit($agenda->perihal, 35) }}</td>
                            <td style="white-space:nowrap; font-size:12px; color:var(--text-soft)">{{ $agenda->tanggal_agenda->format('d M Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('chartBulanan');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($chartLabels),
            datasets: [
                {
                    label: 'Surat Masuk',
                    data: @json($chartMasuk),
                    backgroundColor: 'rgba(26,78,138,.7)',
                    borderRadius: 4,
                    barPercentage: 0.5,
                },
                {
                    label: 'Surat Keluar',
                    data: @json($chartKeluar),
                    backgroundColor: 'rgba(233,168,0,.7)',
                    borderRadius: 4,
                    barPercentage: 0.5,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { intersect: false, mode: 'index' },
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { font: { size: 11 }, padding: 16, usePointStyle: true, pointStyleWidth: 10 }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 10 }, color: '#9ca3af', maxRotation: 45 }
                },
                y: {
                    beginAtZero: true,
                    ticks: { font: { size: 10 }, color: '#9ca3af', stepSize: 1 },
                    grid: { color: '#f3f4f6' }
                }
            }
        }
    });
});
</script>
@endpush
