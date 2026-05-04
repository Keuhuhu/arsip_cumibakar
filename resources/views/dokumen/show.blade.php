@extends('app')

@section('title', 'Detail Dokumen')
@section('page-title', 'Detail Dokumen')

@section('content')
<div class="row">
    <div class="col-8">
        <div class="card">
            <div class="card-header">
                <span class="card-title">📄 Informasi Dokumen</span>
                <div>
                    <a href="{{ route('dokumen.download', $dokumen) }}" class="btn btn-success btn-xs">⬇️ Unduh File</a>
                    @if(auth()->user()->isSuperAdmin())
                    <a href="{{ route('dokumen.edit', $dokumen) }}" class="btn btn-warning btn-xs">✏️ Edit</a>
                    @endif
                    <a href="{{ route('dokumen.index') }}" class="btn btn-outline-secondary btn-xs">Kembali</a>
                </div>
            </div>

            <div class="card-body">
                <table class="table" style="background: transparent;">
                    <tbody>
                        <tr>
                            <td width="30%" style="font-weight:600; color:var(--text-soft); border:none; padding-top:0;">Perihal / Judul</td>
                            <td style="font-weight:600; font-size:16px; border:none; padding-top:0;">{{ $dokumen->perihal }}</td>
                        </tr>
                        <tr>
                            <td style="font-weight:600; color:var(--text-soft);">Nomor Surat</td>
                            <td>{{ $dokumen->no_surat ?: '-' }}</td>
                        </tr>
                        <tr>
                            <td style="font-weight:600; color:var(--text-soft);">Jenis Dokumen</td>
                            <td><span class="badge badge-info">{{ $dokumen->jenis_label }}</span></td>
                        </tr>
                        <tr>
                            <td style="font-weight:600; color:var(--text-soft);">Kategori</td>
                            <td><span class="badge badge-secondary">{{ $dokumen->kategori->nama }}</span></td>
                        </tr>
                        <tr>
                            <td style="font-weight:600; color:var(--text-soft);">Tanggal Surat</td>
                            <td>{{ \Carbon\Carbon::parse($dokumen->tanggal_surat)->translatedFormat('d F Y') }}</td>
                        </tr>
                        @if($dokumen->asal_surat)
                        <tr>
                            <td style="font-weight:600; color:var(--text-soft);">Asal Surat</td>
                            <td>{{ $dokumen->asal_surat }}</td>
                        </tr>
                        @endif
                        @if($dokumen->tujuan_surat)
                        <tr>
                            <td style="font-weight:600; color:var(--text-soft);">Tujuan Surat</td>
                            <td>{{ $dokumen->tujuan_surat }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td style="font-weight:600; color:var(--text-soft);">Keterangan</td>
                            <td>{{ $dokumen->keterangan ?: '-' }}</td>
                        </tr>
                        @if(!empty($dokumen->tags))
                        <tr>
                            <td style="font-weight:600; color:var(--text-soft);">Tags</td>
                            <td>
                                @foreach($dokumen->tags as $tag)
                                    <span class="badge" style="background:#e5e7eb; color:#4b5563; margin-right:4px;">#{{ $tag }}</span>
                                @endforeach
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="card mt-4">
            <div class="card-header">
                <span class="card-title">👁️ Preview File</span>
            </div>
            <div class="card-body" style="padding: 0; background: #e5e7eb; min-height: 400px; display:flex; align-items:center; justify-content:center;">
                @if(in_array($dokumen->file_type, ['image/jpeg', 'image/png', 'image/gif', 'image/webp']))
                    <img src="{{ route('dokumen.preview', $dokumen) }}" alt="Preview {{ $dokumen->perihal }}" style="max-width:100%; max-height:600px; object-fit:contain; display:block; margin:0 auto;">
                @elseif($dokumen->file_type === 'application/pdf')
                    <iframe src="{{ route('dokumen.preview', $dokumen) }}" width="100%" height="600" style="border:none;"></iframe>
                @else
                    <div style="text-align:center; padding: 40px;">
                        <div style="font-size: 48px; margin-bottom:16px;">{{ $dokumen->file_icon }}</div>
                        <p style="color:var(--text-soft); margin-bottom:16px;">Format file ({{ $dokumen->file_type }}) tidak dapat dipreview langsung di browser.</p>
                        <a href="{{ route('dokumen.download', $dokumen) }}" class="btn btn-primary">Unduh File untuk Melihat</a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-4">
        {{-- Status Card --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">📌 Status Dokumen</span>
            </div>
            <div class="card-body">
                <div style="margin-bottom: 20px; text-align:center;">
                    <div style="font-size: 11px; color: var(--text-soft); text-transform:uppercase; letter-spacing:1px; margin-bottom:8px;">Status Saat Ini</div>
                    <span class="badge {{ $dokumen->status_badge }}" style="font-size: 16px; padding: 8px 16px;">{{ $dokumen->status_label }}</span>
                </div>

                @if(auth()->user()->isSuperAdmin())
                <form action="{{ route('dokumen.update-status', $dokumen) }}" method="POST" style="border-top: 1px solid var(--border-color); padding-top: 16px;">
                    @csrf
                    @method('PATCH')
                    <div class="form-group">
                        <label for="status" class="form-label" style="font-size:12px;">Ubah Status:</label>
                        <select name="status" id="status" class="form-control" onchange="this.form.submit()">
                            <option value="aktif" {{ $dokumen->status == 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="tindak_lanjut" {{ $dokumen->status == 'tindak_lanjut' ? 'selected' : '' }}>Tindak Lanjut</option>
                            <option value="selesai" {{ $dokumen->status == 'selesai' ? 'selected' : '' }}>Selesai</option>
                            <option value="disetujui" {{ $dokumen->status == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                        </select>
                    </div>
                </form>
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
                    <div style="font-size: 11px; color: var(--text-soft);">Nama File</div>
                    <div style="font-weight: 500; font-size:13px; word-break: break-all;">{{ $dokumen->file_name }}</div>
                </div>
                <div style="margin-bottom: 12px;">
                    <div style="font-size: 11px; color: var(--text-soft);">Ukuran File</div>
                    <div style="font-weight: 500; font-size:13px;">{{ number_format($dokumen->file_size / 1024, 2) }} KB</div>
                </div>
                <div style="margin-bottom: 12px;">
                    <div style="font-size: 11px; color: var(--text-soft);">Waktu Diunggah</div>
                    <div style="font-weight: 500; font-size:13px;">{{ $dokumen->created_at->format('d M Y, H:i') }}</div>
                </div>
                <div style="margin-bottom: 12px;">
                    <div style="font-size: 11px; color: var(--text-soft);">Diunggah Oleh</div>
                    <div style="font-weight: 500; font-size:13px; display:flex; align-items:center; gap:8px;">
                        <span class="user-avatar" style="width:24px; height:24px; font-size:10px;">{{ strtoupper(substr($dokumen->uploader->name ?? '?', 0, 2)) }}</span>
                        {{ $dokumen->uploader->name ?? 'Unknown' }}
                    </div>
                </div>
                @if($dokumen->approved_by)
                <div style="margin-top: 16px; padding-top: 16px; border-top: 1px solid var(--border-color);">
                    <div style="font-size: 11px; color: var(--text-soft);">Disetujui Oleh</div>
                    <div style="font-weight: 500; font-size:13px; display:flex; align-items:center; gap:8px; margin-top:4px;">
                        <span class="user-avatar" style="width:24px; height:24px; font-size:10px; background:var(--kuning);">{{ strtoupper(substr($dokumen->approver->name ?? '?', 0, 2)) }}</span>
                        {{ $dokumen->approver->name ?? 'Unknown' }}
                    </div>
                    <div style="font-size:11px; color:var(--text-soft); margin-top:4px;">Pada: {{ $dokumen->approved_at?->format('d M Y, H:i') }}</div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
