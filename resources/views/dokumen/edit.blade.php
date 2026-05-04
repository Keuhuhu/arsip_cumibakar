@extends('app')

@section('title', 'Edit Dokumen')
@section('page-title', 'Edit Dokumen')

@section('content')
<div class="card" style="max-width:800px; margin:0 auto;">
    <div class="card-header">
        <span class="card-title">✏️ Edit Arsip Dokumen: {{ $dokumen->perihal }}</span>
        <a href="{{ route('dokumen.index') }}" class="btn btn-outline-secondary btn-xs">Kembali</a>
    </div>

    <div class="card-body">
        <form action="{{ route('dokumen.update', $dokumen) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        <label for="jenis" class="form-label">Jenis Dokumen <span class="text-danger">*</span></label>
                        <select name="jenis" id="jenis" class="form-control" required>
                            <option value="masuk" {{ old('jenis', $dokumen->jenis) == 'masuk' ? 'selected' : '' }}>Surat Masuk</option>
                            <option value="keluar" {{ old('jenis', $dokumen->jenis) == 'keluar' ? 'selected' : '' }}>Surat Keluar</option>
                            <option value="sk" {{ old('jenis', $dokumen->jenis) == 'sk' ? 'selected' : '' }}>SK Kades</option>
                            <option value="laporan" {{ old('jenis', $dokumen->jenis) == 'laporan' ? 'selected' : '' }}>Laporan</option>
                            <option value="kependudukan" {{ old('jenis', $dokumen->jenis) == 'kependudukan' ? 'selected' : '' }}>Kependudukan</option>
                        </select>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label for="kategori_id" class="form-label">Kategori Arsip <span class="text-danger">*</span></label>
                        <select name="kategori_id" id="kategori_id" class="form-control" required>
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($kategoris as $kat)
                                <option value="{{ $kat->id }}" {{ old('kategori_id', $dokumen->kategori_id) == $kat->id ? 'selected' : '' }}>
                                    {{ $kat->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="perihal" class="form-label">Perihal / Judul Dokumen <span class="text-danger">*</span></label>
                <input type="text" name="perihal" id="perihal" class="form-control" value="{{ old('perihal', $dokumen->perihal) }}" required>
            </div>

            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        <label for="no_surat" class="form-label">Nomor Surat</label>
                        <input type="text" name="no_surat" id="no_surat" class="form-control" value="{{ old('no_surat', $dokumen->no_surat) }}">
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label for="tanggal_surat" class="form-label">Tanggal Surat <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_surat" id="tanggal_surat" class="form-control" value="{{ old('tanggal_surat', $dokumen->tanggal_surat?->format('Y-m-d')) }}" required>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        <label for="asal_surat" class="form-label">Asal Surat</label>
                        <input type="text" name="asal_surat" id="asal_surat" class="form-control" value="{{ old('asal_surat', $dokumen->asal_surat) }}">
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label for="tujuan_surat" class="form-label">Tujuan Surat</label>
                        <input type="text" name="tujuan_surat" id="tujuan_surat" class="form-control" value="{{ old('tujuan_surat', $dokumen->tujuan_surat) }}">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="file" class="form-label">Ganti File Dokumen (Biarkan kosong jika tidak ingin mengubah file)</label>
                <div class="upload-area" style="border: 2px dashed var(--border-color); padding: 20px; text-align: center; border-radius: 8px; background: #fafafa;">
                    <div style="font-size: 12px; color: var(--text-soft); margin-bottom: 15px;">File saat ini: <strong>{{ $dokumen->file_name }}</strong></div>
                    <input type="file" name="file" id="file" class="form-control" style="width: 100%; max-width: 300px; margin: 0 auto; cursor: pointer;">
                </div>
            </div>

            <div class="form-group">
                <label for="keterangan" class="form-label">Keterangan / Catatan Tambahan</label>
                <textarea name="keterangan" id="keterangan" class="form-control" rows="3">{{ old('keterangan', $dokumen->keterangan) }}</textarea>
            </div>

            <div class="form-group">
                <label for="tags" class="form-label">Tags (Pisahkan dengan koma)</label>
                <input type="text" name="tags" id="tags" class="form-control" value="{{ old('tags', implode(', ', $dokumen->tags ?? [])) }}">
            </div>

            <div style="margin-top: 24px; text-align: right;">
                <button type="submit" class="btn btn-warning" style="padding: 10px 24px; font-weight: 600;">Update Dokumen</button>
            </div>
        </form>
    </div>
</div>
@endsection
