@extends('app')

@section('title', 'Laporan & Ekspor')
@section('page-title', 'Laporan & Ekspor Arsip')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <span class="card-title">⚙️ Filter Data Laporan</span>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('laporan.index') }}" style="display:flex; flex-wrap:wrap; gap:16px; align-items:flex-end;">
                    <div class="form-group" style="margin-bottom:0; min-width:120px;">
                        <label for="tahun" class="form-label">Tahun</label>
                        <select name="tahun" id="tahun" class="form-control">
                            @for($i = now()->year; $i >= now()->year - 5; $i--)
                                <option value="{{ $i }}" {{ request('tahun', $tahun) == $i ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                    </div>

                    <div class="form-group" style="margin-bottom:0; min-width:160px;">
                        <label for="bulan" class="form-label">Bulan</label>
                        <select name="bulan" id="bulan" class="form-control">
                            <option value="">-- Sepanjang Tahun --</option>
                            @foreach(['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'] as $idx => $bln)
                                <option value="{{ $idx + 1 }}" {{ request('bulan', $bulan) == ($idx + 1) ? 'selected' : '' }}>{{ $bln }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group" style="margin-bottom:0; min-width:160px;">
                        <label for="jenis" class="form-label">Jenis Dokumen</label>
                        <select name="jenis" id="jenis" class="form-control">
                            <option value="">-- Semua Jenis --</option>
                            <option value="masuk" {{ request('jenis', $jenis) == 'masuk' ? 'selected' : '' }}>Surat Masuk</option>
                            <option value="keluar" {{ request('jenis', $jenis) == 'keluar' ? 'selected' : '' }}>Surat Keluar</option>
                            <option value="sk" {{ request('jenis', $jenis) == 'sk' ? 'selected' : '' }}>SK Kades</option>
                            <option value="laporan" {{ request('jenis', $jenis) == 'laporan' ? 'selected' : '' }}>Laporan</option>
                            <option value="kependudukan" {{ request('jenis', $jenis) == 'kependudukan' ? 'selected' : '' }}>Kependudukan</option>
                        </select>
                    </div>

                    <div style="display:flex; gap:8px;">
                        <button type="submit" class="btn btn-primary" style="height:38px;">Tampilkan Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header" style="flex-wrap:wrap; gap:16px;">
                <span class="card-title">📈 Hasil Laporan ({{ $dokumens->total() }} Arsip Ditemukan)</span>
                <div>
                    <a href="{{ route('laporan.export-excel', request()->all()) }}" class="btn btn-outline-success btn-sm">📊 Ekspor Excel/CSV</a>
                    <a href="{{ route('laporan.export-pdf', request()->all()) }}" target="_blank" class="btn btn-outline-danger btn-sm">📄 Cetak PDF</a>
                </div>
            </div>
            <div class="card-body" style="padding: 0;">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th width="50">No</th>
                                <th>No. Urut</th>
                                <th>No. Surat</th>
                                <th>Perihal Dokumen</th>
                                <th>Tanggal Surat</th>
                                <th>Kategori</th>
                                <th>Jenis</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dokumens as $i => $dok)
                            <tr>
                                <td style="color:var(--text-soft)">{{ $dokumens->firstItem() + $i }}</td>
                                <td style="font-weight:600; color:var(--hijau);">{{ str_pad($dok->no_urut, 4, '0', STR_PAD_LEFT) }}</td>
                                <td>{{ $dok->no_surat ?: '-' }}</td>
                                <td>
                                    <div style="font-weight:600; color:var(--text-dark);">{{ \Illuminate\Support\Str::limit($dok->perihal, 50) }}</div>
                                    <div style="font-size:12px; color:var(--text-soft);">Oleh: {{ $dok->uploader->name ?? '-' }}</div>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($dok->tanggal_surat)->format('d/m/Y') }}</td>
                                <td><span class="badge badge-secondary">{{ $dok->kategori->nama ?? '-' }}</span></td>
                                <td><span class="badge badge-info">{{ $dok->jenis_label }}</span></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="table-empty">
                                    <span class="empty-icon">📈</span>
                                    Data laporan tidak ditemukan untuk filter tersebut.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($dokumens->hasPages())
            <div class="card-body" style="border-top:1px solid var(--border-color);">
                {{ $dokumens->appends(request()->all())->links('pagination::bootstrap-5') }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
