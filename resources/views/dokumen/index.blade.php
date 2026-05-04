@extends('app')

@section('title', 'Arsip Dokumen')
@section('page-title', 'Arsip Dokumen')

@section('content')
<div class="card">
    <div class="card-header" style="flex-wrap:wrap; gap:16px;">
        <span class="card-title">📁 Daftar Arsip Dokumen</span>
        @if(auth()->user()->isSuperAdmin())
        <a href="{{ route('dokumen.create') }}" class="btn btn-primary">+ Unggah Dokumen</a>
        @endif
    </div>
    
    {{-- Filter Form --}}
    <div class="card-body" style="border-bottom: 1px solid var(--border-color); background: #fafafa;">
        <form method="GET" action="{{ route('dokumen.index') }}" class="filter-form" style="display:flex; flex-wrap:wrap; gap:12px; align-items:flex-end;">
            <div class="form-group" style="margin-bottom:0; flex:1; min-width:200px;">
                <label for="q" class="form-label" style="font-size:11px;">Pencarian (Ctrl+K)</label>
                <input type="text" name="q" id="q" class="form-control filter-control" value="{{ request('q') }}" placeholder="Cari perihal, no surat, tags...">
            </div>
            
            <div class="form-group" style="margin-bottom:0; min-width:140px;">
                <label for="kategori_id" class="form-label" style="font-size:11px;">Kategori</label>
                <select name="kategori_id" id="kategori_id" class="form-control">
                    <option value="">Semua Kategori</option>
                    @foreach($kategoris as $kat)
                        <option value="{{ $kat->id }}" {{ request('kategori_id') == $kat->id ? 'selected' : '' }}>{{ $kat->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group" style="margin-bottom:0; min-width:120px;">
                <label for="jenis" class="form-label" style="font-size:11px;">Jenis</label>
                <select name="jenis" id="jenis" class="form-control">
                    <option value="">Semua Jenis</option>
                    <option value="masuk" {{ request('jenis') == 'masuk' ? 'selected' : '' }}>Surat Masuk</option>
                    <option value="keluar" {{ request('jenis') == 'keluar' ? 'selected' : '' }}>Surat Keluar</option>
                    <option value="sk" {{ request('jenis') == 'sk' ? 'selected' : '' }}>SK Kades</option>
                    <option value="laporan" {{ request('jenis') == 'laporan' ? 'selected' : '' }}>Laporan</option>
                    <option value="kependudukan" {{ request('jenis') == 'kependudukan' ? 'selected' : '' }}>Kependudukan</option>
                </select>
            </div>

            <div class="form-group" style="margin-bottom:0; min-width:120px;">
                <label for="status" class="form-label" style="font-size:11px;">Status</label>
                <select name="status" id="status" class="form-control">
                    <option value="">Semua Status</option>
                    <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="tindak_lanjut" {{ request('status') == 'tindak_lanjut' ? 'selected' : '' }}>Tindak Lanjut</option>
                    <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                    <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                </select>
            </div>

            <div style="display:flex; gap:8px;">
                <button type="submit" class="btn btn-primary" style="height:38px;">Filter</button>
                @if(request()->anyFilled(['q', 'kategori_id', 'jenis', 'status', 'dari', 'sampai']))
                    <a href="{{ route('dokumen.index') }}" class="btn btn-outline-danger" style="height:38px; line-height:22px;" title="Reset Filter">✕</a>
                @endif
            </div>
        </form>
    </div>

    <div class="card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th width="50">No</th>
                        <th>Perihal Dokumen</th>
                        <th>Kategori & Jenis</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dokumens as $i => $dok)
                    <tr>
                        <td style="color:var(--text-soft)">{{ $dokumens->firstItem() + $i }}</td>
                        <td>
                            <div style="font-weight:600; color:var(--text-dark); margin-bottom:4px;">
                                {{ $dok->file_icon }} <a href="{{ route('dokumen.show', $dok) }}" style="color:inherit;">{{ $dok->perihal }}</a>
                            </div>
                            <div style="font-size:12px; color:var(--text-soft);">
                                No Surat: {{ $dok->no_surat ?: '-' }}
                            </div>
                        </td>
                        <td>
                            <div style="margin-bottom:4px;"><span class="badge badge-secondary">{{ $dok->kategori->nama }}</span></div>
                            <div><span class="badge badge-info">{{ $dok->jenis_label }}</span></div>
                        </td>
                        <td style="font-size:12px;">
                            <div style="margin-bottom:4px;">Surat: {{ \Carbon\Carbon::parse($dok->tanggal_surat)->format('d M Y') }}</div>
                            <div style="color:var(--text-soft);">Upload: {{ $dok->created_at->format('d M Y') }}</div>
                        </td>
                        <td><span class="badge {{ $dok->status_badge }}">{{ $dok->status_label }}</span></td>
                        <td class="text-right">
                            <div style="display:inline-flex; gap:4px;">
                                <a href="{{ route('dokumen.show', $dok) }}" class="btn btn-outline-primary btn-xs" title="Lihat Detail">👁️</a>
                                <a href="{{ route('dokumen.download', $dok) }}" class="btn btn-outline-success btn-xs" title="Unduh">⬇️</a>
                                @if(auth()->user()->isSuperAdmin())
                                <a href="{{ route('dokumen.edit', $dok) }}" class="btn btn-outline-warning btn-xs" title="Edit">✏️</a>
                                <form action="{{ route('dokumen.destroy', $dok) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus dokumen ini?');" style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-xs" title="Hapus">🗑️</button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="table-empty">
                            <span class="empty-icon">📁</span>
                            Tidak ada dokumen yang ditemukan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    @if($dokumens->hasPages())
    <div class="card-body" style="border-top:1px solid var(--border-color);">
        {{ $dokumens->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection
