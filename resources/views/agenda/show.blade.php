@extends('app')

@section('title', 'Detail Agenda')
@section('page-title', 'Detail Agenda')

@section('content')
<div class="row">
    <div class="col-8">
        <div class="card">
            <div class="card-header">
                <span class="card-title">📋 Informasi Catatan Agenda</span>
                <div>
                    @if(auth()->user()->isSuperAdmin())
                    <a href="{{ route('agenda.edit', $agenda) }}" class="btn btn-warning btn-xs">✏️ Edit</a>
                    @endif
                    <a href="{{ route('agenda.index') }}" class="btn btn-outline-secondary btn-xs">Kembali</a>
                </div>
            </div>

            <div class="card-body">
                <table class="table" style="background: transparent;">
                    <tbody>
                        <tr>
                            <td width="30%" style="font-weight:600; color:var(--text-soft); border:none; padding-top:0;">Nomor Agenda</td>
                            <td style="font-weight:700; font-size:18px; color:var(--hijau); border:none; padding-top:0;">{{ $agenda->no_agenda }}</td>
                        </tr>
                        <tr>
                            <td style="font-weight:600; color:var(--text-soft);">Perihal</td>
                            <td style="font-weight:600; font-size:15px;">{{ $agenda->perihal }}</td>
                        </tr>
                        <tr>
                            <td style="font-weight:600; color:var(--text-soft);">Jenis Agenda</td>
                            <td>
                                @if($agenda->jenis == 'masuk')
                                    <span class="badge badge-info">Surat Masuk</span>
                                @else
                                    <span class="badge badge-warning">Surat Keluar</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td style="font-weight:600; color:var(--text-soft);">Nomor Surat</td>
                            <td>{{ $agenda->no_surat ?: '-' }}</td>
                        </tr>
                        <tr>
                            <td style="font-weight:600; color:var(--text-soft);">Tanggal Surat</td>
                            <td>{{ \Carbon\Carbon::parse($agenda->tanggal_surat)->translatedFormat('d F Y') }}</td>
                        </tr>
                        <tr>
                            <td style="font-weight:600; color:var(--text-soft);">Tanggal Agenda</td>
                            <td>{{ \Carbon\Carbon::parse($agenda->tanggal_agenda)->translatedFormat('d F Y') }}</td>
                        </tr>
                        @if($agenda->jenis == 'masuk')
                        <tr>
                            <td style="font-weight:600; color:var(--text-soft);">Asal Surat</td>
                            <td>{{ $agenda->asal ?: '-' }}</td>
                        </tr>
                        @else
                        <tr>
                            <td style="font-weight:600; color:var(--text-soft);">Tujuan Surat</td>
                            <td>{{ $agenda->tujuan ?: '-' }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td style="font-weight:600; color:var(--text-soft);">Keterangan</td>
                            <td>{{ $agenda->keterangan ?: '-' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-4">
        {{-- Linked Document Card --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">📁 Tautan Dokumen Arsip</span>
            </div>
            <div class="card-body">
                @if($agenda->dokumen_id && $agenda->dokumen)
                    <div style="text-align: center; padding: 10px 0;">
                        <div style="font-size: 36px; margin-bottom: 10px;">{{ $agenda->dokumen->file_icon }}</div>
                        <div style="font-weight: 600; margin-bottom: 4px;">{{ \Illuminate\Support\Str::limit($agenda->dokumen->perihal, 40) }}</div>
                        <div style="font-size: 12px; color: var(--text-soft); margin-bottom: 16px;">{{ $agenda->dokumen->jenis_label }}</div>
                        <a href="{{ route('dokumen.show', $agenda->dokumen) }}" class="btn btn-outline-primary btn-sm" style="width: 100%;">Lihat Dokumen Arsip</a>
                    </div>
                @else
                    <div style="text-align: center; padding: 20px 0; color: var(--text-soft);">
                        <div style="font-size: 24px; margin-bottom: 10px;">📎</div>
                        <p>Agenda ini tidak ditautkan<br>ke file arsip dokumen mana pun.</p>
                        @if(auth()->user()->isSuperAdmin())
                        <a href="{{ route('agenda.edit', $agenda) }}" class="btn btn-outline-primary btn-xs mt-2">Tautkan Sekarang</a>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        {{-- Meta Data Card --}}
        <div class="card mt-4">
            <div class="card-header">
                <span class="card-title">⚙️ Meta Data</span>
            </div>
            <div class="card-body">
                <div style="margin-bottom: 12px;">
                    <div style="font-size: 11px; color: var(--text-soft);">Waktu Dicatat</div>
                    <div style="font-weight: 500; font-size:13px;">{{ $agenda->created_at->format('d M Y, H:i') }}</div>
                </div>
                <div style="margin-bottom: 12px;">
                    <div style="font-size: 11px; color: var(--text-soft);">Dicatat Oleh</div>
                    <div style="font-weight: 500; font-size:13px; display:flex; align-items:center; gap:8px;">
                        <span class="user-avatar" style="width:24px; height:24px; font-size:10px;">{{ strtoupper(substr($agenda->creator->name ?? '?', 0, 2)) }}</span>
                        {{ $agenda->creator->name ?? 'Unknown' }}
                    </div>
                </div>
                <div style="margin-bottom: 12px;">
                    <div style="font-size: 11px; color: var(--text-soft);">Terakhir Diperbarui</div>
                    <div style="font-weight: 500; font-size:13px;">{{ $agenda->updated_at->format('d M Y, H:i') }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
