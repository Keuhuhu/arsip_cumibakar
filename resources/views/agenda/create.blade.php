@extends('app')

@section('title', 'Catat Agenda Baru')
@section('page-title', 'Catat Agenda')

@section('content')
<div class="card" style="max-width:800px; margin:0 auto;">
    <div class="card-header">
        <span class="card-title">📋 Form Catat Buku Agenda</span>
        <a href="{{ route('agenda.index') }}" class="btn btn-outline-secondary btn-xs">Kembali</a>
    </div>

    <div class="card-body">
        <form action="{{ route('agenda.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="jenis" class="form-label">Jenis Agenda <span class="text-danger">*</span></label>
                <select name="jenis" id="jenis" class="form-control" required>
                    <option value="">-- Pilih Jenis --</option>
                    <option value="masuk" {{ old('jenis') == 'masuk' ? 'selected' : '' }}>Surat Masuk</option>
                    <option value="keluar" {{ old('jenis') == 'keluar' ? 'selected' : '' }}>Surat Keluar</option>
                </select>
                <small class="form-text" style="color:var(--text-soft); font-size:11px; margin-top:4px;">Nomor Agenda akan dibuat secara otomatis berdasarkan jenis yang dipilih.</small>
            </div>

            <div class="form-group">
                <label for="perihal" class="form-label">Perihal Dokumen <span class="text-danger">*</span></label>
                <input type="text" name="perihal" id="perihal" class="form-control" value="{{ old('perihal') }}" required placeholder="Masukkan perihal ringkas surat">
            </div>

            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        <label for="no_surat" class="form-label">Nomor Surat</label>
                        <input type="text" name="no_surat" id="no_surat" class="form-control" value="{{ old('no_surat') }}" placeholder="Opsional jika tidak ada">
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label for="tanggal_surat" class="form-label">Tanggal Surat <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_surat" id="tanggal_surat" class="form-control" value="{{ old('tanggal_surat') }}" required>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        <label for="tanggal_agenda" class="form-label">Tanggal Dicatat di Agenda <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_agenda" id="tanggal_agenda" class="form-control" value="{{ old('tanggal_agenda', date('Y-m-d')) }}" required>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label for="dokumen_id" class="form-label">Tautkan ke Arsip Dokumen</label>
                        <select name="dokumen_id" id="dokumen_id" class="form-control">
                            <option value="">-- Tidak Ditautkan --</option>
                            @foreach($dokumens as $dok)
                                <option value="{{ $dok->id }}" {{ old('dokumen_id') == $dok->id ? 'selected' : '' }}>
                                    [{{ $dok->no_surat ?: 'Tanpa No' }}] {{ \Illuminate\Support\Str::limit($dok->perihal, 30) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        <label for="asal" class="form-label">Asal (Untuk Surat Masuk)</label>
                        <input type="text" name="asal" id="asal" class="form-control" value="{{ old('asal') }}" placeholder="Pengirim surat">
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label for="tujuan" class="form-label">Tujuan (Untuk Surat Keluar)</label>
                        <input type="text" name="tujuan" id="tujuan" class="form-control" value="{{ old('tujuan') }}" placeholder="Penerima surat">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="keterangan" class="form-label">Keterangan / Catatan Tambahan</label>
                <textarea name="keterangan" id="keterangan" class="form-control" rows="3" placeholder="Opsional">{{ old('keterangan') }}</textarea>
            </div>

            <div style="margin-top: 24px; text-align: right;">
                <button type="submit" class="btn btn-primary" style="padding: 10px 24px; font-weight: 600;">Simpan Agenda</button>
            </div>
        </form>
    </div>
</div>
@endsection
