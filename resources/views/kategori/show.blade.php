@extends('app')

@section('title', 'Detail Kategori')
@section('page-title', 'Detail Kategori')

@section('content')
<div class="row">
    <div class="col-4">
        <div class="card">
            <div class="card-header">
                <span class="card-title">🏷️ Info Kategori</span>
            </div>
            <div class="card-body text-center">
                <div style="font-size:48px; margin-bottom:10px;">{{ $kategori->icon ?? '📁' }}</div>
                <h3 style="margin:0 0 5px;">{{ $kategori->nama }}</h3>
                <div style="color:var(--text-soft); margin-bottom:15px;">Kode: <strong>{{ $kategori->kode }}</strong></div>
                
                @if($kategori->is_active)
                    <span class="badge badge-success" style="padding:6px 12px; font-size:12px;">Kategori Aktif</span>
                @else
                    <span class="badge badge-secondary" style="padding:6px 12px; font-size:12px;">Kategori Nonaktif</span>
                @endif
                
                <p style="margin-top:20px; font-size:13px; color:var(--text-soft);">
                    {{ $kategori->deskripsi ?: 'Tidak ada deskripsi.' }}
                </p>

                <div style="margin-top: 20px; display:flex; gap:10px; justify-content:center;">
                    <a href="{{ route('kategori.edit', $kategori) }}" class="btn btn-warning btn-sm">✏️ Edit</a>
                    <a href="{{ route('kategori.index') }}" class="btn btn-outline-secondary btn-sm">Kembali</a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-8">
        <div class="card">
            <div class="card-header">
                <span class="card-title">📄 Dokumen dalam Kategori Ini ({{ $kategori->dokumens->count() }})</span>
            </div>
            <div class="card-body" style="padding:0;">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Perihal Dokumen</th>
                                <th>Jenis</th>
                                <th>Status</th>
                                <th class="text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($kategori->dokumens as $i => $dok)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td style="font-weight:600;">{{ $dok->file_icon }} {{ \Illuminate\Support\Str::limit($dok->perihal, 40) }}</td>
                                <td><span class="badge badge-info">{{ $dok->jenis_label }}</span></td>
                                <td><span class="badge {{ $dok->status_badge }}">{{ $dok->status_label }}</span></td>
                                <td class="text-right">
                                    <a href="{{ route('dokumen.show', $dok) }}" class="btn btn-outline-primary btn-xs">Lihat</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="table-empty">Belum ada dokumen di kategori ini.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
