@extends('app')

@section('title', 'Manajemen Pengguna')
@section('page-title', 'Manajemen Pengguna')

@section('content')
<div class="card">
    <div class="card-header" style="flex-wrap:wrap; gap:16px;">
        <span class="card-title">👥 Daftar Pengguna Sistem</span>
        <a href="{{ route('pengguna.create') }}" class="btn btn-primary">+ Tambah Pengguna Baru</a>
    </div>

    <div class="card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th width="50">No</th>
                        <th>Profil Pengguna</th>
                        <th>Peran (Role)</th>
                        <th>Jabatan & NIP</th>
                        <th>Kontak</th>
                        <th>Aktivitas</th>
                        <th width="100">Status</th>
                        <th class="text-right" width="160">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $i => $user)
                    <tr>
                        <td style="color:var(--text-soft);">{{ $users->firstItem() + $i }}</td>
                        <td>
                            <div style="display:flex; align-items:center; gap:12px;">
                                <div class="user-avatar" style="width:36px; height:36px; font-size:14px; flex-shrink:0;">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>
                                <div>
                                    <div style="font-weight:600; color:var(--text-dark);">{{ $user->name }}</div>
                                    <div style="font-size:12px; color:var(--text-soft);">Bergabung: {{ $user->created_at->format('M Y') }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="role-badge role-{{ $user->role }}">{{ $user->role_label }}</span>
                        </td>
                        <td>
                            <div style="font-weight:500;">{{ $user->jabatan ?: '-' }}</div>
                            <div style="font-size:12px; color:var(--text-soft);">NIP: {{ $user->nip ?: '-' }}</div>
                        </td>
                        <td>
                            <div style="font-size:13px; margin-bottom:2px;">✉️ {{ $user->email }}</div>
                            <div style="font-size:12px; color:var(--text-soft);">📞 {{ $user->no_hp ?: '-' }}</div>
                        </td>
                        <td style="font-size:12px; color:var(--text-soft);">
                            <div>Dokumen: <strong>{{ $user->dokumens_count }}</strong></div>
                            <div>Log Aksi: <strong>{{ $user->activity_logs_count }}</strong></div>
                        </td>
                        <td>
                            @if($user->is_active)
                                <span class="badge badge-success">Aktif</span>
                            @else
                                <span class="badge badge-secondary">Nonaktif</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <div style="display:inline-flex; gap:4px;">
                                @if(auth()->id() !== $user->id)
                                <form action="{{ route('pengguna.toggle-active', $user) }}" method="POST" style="display:inline;">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn {{ $user->is_active ? 'btn-outline-secondary' : 'btn-outline-success' }} btn-xs" title="{{ $user->is_active ? 'Nonaktifkan User' : 'Aktifkan User' }}">
                                        {{ $user->is_active ? '⏸️' : '▶️' }}
                                    </button>
                                </form>
                                @endif
                                <a href="{{ route('pengguna.edit', $user) }}" class="btn btn-outline-warning btn-xs" title="Edit">✏️</a>
                                @if(auth()->id() !== $user->id)
                                <form action="{{ route('pengguna.destroy', $user) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus pengguna ini permanen? Semua data yang ditautkan ke pengguna ini akan menjadi NULL/System.');" style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-xs" title="Hapus">🗑️</button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="table-empty">
                            <span class="empty-icon">👥</span>
                            Tidak ada pengguna ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    @if($users->hasPages())
    <div class="card-body" style="border-top:1px solid var(--border-color);">
        {{ $users->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection
