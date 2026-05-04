@extends('app')

@section('title', 'Backup & Restore')
@section('page-title', 'Backup & Restore Database')

@section('content')
<div class="row">
    <div class="col-8">
        <div class="card">
            <div class="card-header" style="flex-wrap:wrap; gap:16px;">
                <span class="card-title">💾 Daftar Backup Tersedia</span>
                <form action="{{ route('backup.create') }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn btn-primary" onclick="this.disabled=true; this.innerHTML='Membuat Backup...'; this.form.submit();">+ Buat Backup Sekarang</button>
                </form>
            </div>
            <div class="card-body" style="padding: 0;">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th width="50">No</th>
                                <th>Nama File Backup</th>
                                <th>Ukuran</th>
                                <th>Tanggal Dibuat</th>
                                <th class="text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($files as $i => $file)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>
                                    <div style="font-weight:600; color:var(--text-dark);">🗄️ {{ $file['name'] }}</div>
                                </td>
                                <td><span class="badge badge-secondary">{{ number_format($file['size'] / 1024, 2) }} KB</span></td>
                                <td style="font-size:13px;">{{ \Carbon\Carbon::createFromTimestamp($file['modified'])->format('d M Y, H:i:s') }}</td>
                                <td class="text-right">
                                    <div style="display:inline-flex; gap:4px;">
                                        <a href="{{ route('backup.download', $file['name']) }}" class="btn btn-outline-success btn-xs" title="Unduh">⬇️</a>
                                        <form action="{{ route('backup.destroy', $file['name']) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus file backup ini?');" style="display:inline;">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-xs" title="Hapus">🗑️</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="table-empty">
                                    <span class="empty-icon">💾</span>
                                    Belum ada file backup database.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-4">
        <div class="card">
            <div class="card-header">
                <span class="card-title">🔄 Restore Database</span>
            </div>
            <div class="card-body">
                <div class="alert alert-warning" style="margin-bottom:20px;">
                    <strong>Peringatan!</strong><br>
                    Melakukan restore akan menimpa seluruh data arsip, agenda, dan pengguna saat ini dengan data dari file backup. Pastikan Anda mengerti risikonya.
                </div>
                
                <form action="{{ route('backup.restore') }}" method="POST" enctype="multipart/form-data" onsubmit="return confirm('PERINGATAN KRITIS: Seluruh data saat ini akan dihapus dan diganti dengan file backup. Anda yakin ingin melanjutkan?');">
                    @csrf
                    <div class="form-group">
                        <label for="backup_file" class="form-label">Upload File Backup (.sql / .sqlite)</label>
                        <div style="border: 2px dashed var(--border-color); padding: 20px; text-align: center; border-radius: 8px; background: #fafafa;">
                            <input type="file" name="backup_file" id="backup_file" class="form-control" required accept=".sql,.sqlite,.db" style="width: 100%;">
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-danger" style="width: 100%; font-weight:600; padding:10px;">Mulai Proses Restore</button>
                </form>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <span class="card-title">ℹ️ Info Serverless</span>
            </div>
            <div class="card-body">
                <p style="font-size:12px; color:var(--text-soft); line-height:1.6; margin:0;">
                    Karena sistem ini dideploy di lingkungan <strong>Vercel</strong> (Serverless / Read-Only), proses restore database mungkin memerlukan pengaturan koneksi eksternal jika menggunakan MySQL. Fitur Backup tetap berjalan normal untuk mengamankan data.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
