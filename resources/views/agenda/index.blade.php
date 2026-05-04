@extends('app')

@section('title', 'Buku Agenda')
@section('page-title', 'Buku Agenda')

@section('content')
<div class="row" style="margin-bottom: 24px;">
    <div class="col-6">
        <div class="stat-card" style="--stat-color: var(--biru); margin-bottom:0;">
            <span class="stat-icon">📥</span>
            <div class="stat-label">Agenda Masuk Bulan Ini</div>
            <div class="stat-value">{{ $masukBulanIni }}</div>
        </div>
    </div>
    <div class="col-6">
        <div class="stat-card" style="--stat-color: #e9a800; margin-bottom:0;">
            <span class="stat-icon">📤</span>
            <div class="stat-label">Agenda Keluar Bulan Ini</div>
            <div class="stat-value">{{ $keluarBulanIni }}</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header" style="flex-wrap:wrap; gap:16px;">
        <span class="card-title">📋 Daftar Catatan Agenda</span>
        @if(auth()->user()->isSuperAdmin())
        <a href="{{ route('agenda.create') }}" class="btn btn-primary">+ Catat Agenda Baru</a>
        @endif
    </div>

    {{-- Filter Form --}}
    <div class="card-body" style="border-bottom: 1px solid var(--border-color); background: #fafafa;">
        <form method="GET" action="{{ route('agenda.index') }}" class="filter-form" style="display:flex; flex-wrap:wrap; gap:12px; align-items:flex-end;">
            <div class="form-group" style="margin-bottom:0; flex:1; min-width:200px;">
                <label for="q" class="form-label" style="font-size:11px;">Pencarian (Ctrl+K)</label>
                <input type="text" name="q" id="q" class="form-control filter-control" value="{{ request('q') }}" placeholder="Cari perihal, no agenda, asal/tujuan...">
            </div>
            
            <div class="form-group" style="margin-bottom:0; min-width:140px;">
                <label for="jenis" class="form-label" style="font-size:11px;">Jenis Agenda</label>
                <select name="jenis" id="jenis" class="form-control">
                    <option value="">Semua Jenis</option>
                    <option value="masuk" {{ request('jenis') == 'masuk' ? 'selected' : '' }}>Agenda Masuk</option>
                    <option value="keluar" {{ request('jenis') == 'keluar' ? 'selected' : '' }}>Agenda Keluar</option>
                </select>
            </div>

            <div class="form-group" style="margin-bottom:0; min-width:120px;">
                <label for="dari" class="form-label" style="font-size:11px;">Dari Tanggal</label>
                <input type="date" name="dari" id="dari" class="form-control" value="{{ request('dari') }}">
            </div>
            <div class="form-group" style="margin-bottom:0; min-width:120px;">
                <label for="sampai" class="form-label" style="font-size:11px;">Sampai Tanggal</label>
                <input type="date" name="sampai" id="sampai" class="form-control" value="{{ request('sampai') }}">
            </div>

            <div style="display:flex; gap:8px;">
                <button type="submit" class="btn btn-primary" style="height:38px;">Filter</button>
                @if(request()->anyFilled(['q', 'jenis', 'dari', 'sampai']))
                    <a href="{{ route('agenda.index') }}" class="btn btn-outline-danger" style="height:38px; line-height:22px;" title="Reset Filter">✕</a>
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
                        <th>No. Agenda / Surat</th>
                        <th>Perihal & Asal/Tujuan</th>
                        <th>Jenis</th>
                        <th>Tgl Agenda</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($agendas as $i => $agenda)
                    <tr>
                        <td style="color:var(--text-soft)">{{ $agendas->firstItem() + $i }}</td>
                        <td>
                            <div style="font-weight:600; color:var(--text-dark); margin-bottom:4px;">
                                <a href="{{ route('agenda.show', $agenda) }}" style="color:var(--hijau);">{{ $agenda->no_agenda }}</a>
                            </div>
                            <div style="font-size:12px; color:var(--text-soft);">
                                {{ $agenda->no_surat ?: '-' }}
                            </div>
                        </td>
                        <td>
                            <div style="font-weight:600; margin-bottom:4px; max-width:300px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="{{ $agenda->perihal }}">
                                {{ $agenda->perihal }}
                            </div>
                            <div style="font-size:12px; color:var(--text-soft);">
                                @if($agenda->jenis == 'masuk')
                                    Dari: {{ $agenda->asal ?: '-' }}
                                @else
                                    Ke: {{ $agenda->tujuan ?: '-' }}
                                @endif
                            </div>
                        </td>
                        <td>
                            @if($agenda->jenis == 'masuk')
                                <span class="badge badge-info">Masuk</span>
                            @else
                                <span class="badge badge-warning">Keluar</span>
                            @endif
                        </td>
                        <td style="font-size:13px; color:var(--text-dark);">
                            {{ \Carbon\Carbon::parse($agenda->tanggal_agenda)->format('d M Y') }}
                        </td>
                        <td class="text-right">
                            <div style="display:inline-flex; gap:4px;">
                                <a href="{{ route('agenda.show', $agenda) }}" class="btn btn-outline-primary btn-xs" title="Lihat Detail">👁️</a>
                                @if(auth()->user()->isSuperAdmin())
                                <a href="{{ route('agenda.edit', $agenda) }}" class="btn btn-outline-warning btn-xs" title="Edit">✏️</a>
                                <form action="{{ route('agenda.destroy', $agenda) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus agenda ini?');" style="display:inline;">
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
                            <span class="empty-icon">📋</span>
                            Tidak ada catatan agenda yang ditemukan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    @if($agendas->hasPages())
    <div class="card-body" style="border-top:1px solid var(--border-color);">
        {{ $agendas->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection
