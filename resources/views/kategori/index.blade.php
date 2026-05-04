@extends('app')

@section('title', 'Kategori Arsip')
@section('page-title', 'Kategori Arsip')

@section('content')
<div class="card">
    <div class="card-header" style="flex-wrap:wrap; gap:16px;">
        <span class="card-title">🏷️ Daftar Kategori</span>
        <a href="{{ route('kategori.create') }}" class="btn btn-primary">+ Tambah Kategori Baru</a>
    </div>

    <div class="card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th width="80">Urutan</th>
                        <th>Ikon & Kode</th>
                        <th>Nama Kategori</th>
                        <th>Deskripsi</th>
                        <th>Jml Dokumen</th>
                        <th width="120">Status</th>
                        <th class="text-right" width="160">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kategoris as $kategori)
                    <tr>
                        <td style="color:var(--text-soft); font-weight:600; text-align:center;">{{ $kategori->urutan }}</td>
                        <td>
                            <div style="display:inline-flex; align-items:center; justify-content:center; width:32px; height:32px; border-radius:6px; background:{{ $kategori->warna ?? 'var(--hijau)' }}22; color:{{ $kategori->warna ?? 'var(--hijau)' }}; font-size:16px; margin-right:8px;">
                                {{ $kategori->icon ?? '📁' }}
                            </div>
                            <span style="font-weight:600; color:var(--text-soft); font-size:12px;">{{ $kategori->kode }}</span>
                        </td>
                        <td style="font-weight:600; color:var(--text-dark);">
                            {{ $kategori->nama }}
                        </td>
                        <td style="font-size:13px;">{{ \Illuminate\Support\Str::limit($kategori->deskripsi, 50) ?: '-' }}</td>
                        <td style="text-align:center;">
                            <span class="badge badge-info">{{ $kategori->dokumens_count }}</span>
                        </td>
                        <td>
                            @if($kategori->is_active)
                                <span class="badge badge-success">Aktif</span>
                            @else
                                <span class="badge badge-secondary">Nonaktif</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <div style="display:inline-flex; gap:4px;">
                                <a href="{{ route('kategori.show', $kategori) }}" class="btn btn-outline-primary btn-xs" title="Lihat">👁️</a>
                                <a href="{{ route('kategori.edit', $kategori) }}" class="btn btn-outline-warning btn-xs" title="Edit">✏️</a>
                                <form action="{{ route('kategori.destroy', $kategori) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus kategori ini?');" style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-xs" title="Hapus" {{ $kategori->dokumens_count > 0 ? 'disabled' : '' }}>🗑️</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="table-empty">
                            <span class="empty-icon">🏷️</span>
                            Belum ada kategori arsip yang ditambahkan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    @if($kategoris->hasPages())
    <div class="card-body" style="border-top:1px solid var(--border-color);">
        {{ $kategoris->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection
