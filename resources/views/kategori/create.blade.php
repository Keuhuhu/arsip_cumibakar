@extends('app')

@section('title', 'Tambah Kategori')
@section('page-title', 'Tambah Kategori')

@section('content')
<div class="card" style="max-width:600px; margin:0 auto;">
    <div class="card-header">
        <span class="card-title">🏷️ Form Tambah Kategori Baru</span>
        <a href="{{ route('kategori.index') }}" class="btn btn-outline-secondary btn-xs">Kembali</a>
    </div>

    <div class="card-body">
        <form action="{{ route('kategori.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="nama" class="form-label">Nama Kategori <span class="text-danger">*</span></label>
                <input type="text" name="nama" id="nama" class="form-control" value="{{ old('nama') }}" required placeholder="Contoh: SK Kades, Pembangunan, dll">
            </div>

            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        <label for="kode" class="form-label">Kode Singkat <span class="text-danger">*</span></label>
                        <input type="text" name="kode" id="kode" class="form-control" value="{{ old('kode') }}" required placeholder="Contoh: SK, PEM, BPD">
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label for="urutan" class="form-label">Urutan Tampil</label>
                        <input type="number" name="urutan" id="urutan" class="form-control" value="{{ old('urutan') }}" placeholder="Otomatis diletakkan terakhir">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        <label for="icon" class="form-label">Ikon (Emoji)</label>
                        <input type="text" name="icon" id="icon" class="form-control" value="{{ old('icon', '📁') }}" placeholder="Contoh: 📄">
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label for="warna" class="form-label">Warna Label (Hex)</label>
                        <input type="color" name="warna" id="warna" class="form-control" value="{{ old('warna', '#1d8954') }}" style="height:38px; padding:2px;">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="deskripsi" class="form-label">Deskripsi / Keterangan Kategori</label>
                <textarea name="deskripsi" id="deskripsi" class="form-control" rows="3">{{ old('deskripsi') }}</textarea>
            </div>

            <div style="margin-top: 24px; text-align: right;">
                <button type="submit" class="btn btn-primary" style="padding: 10px 24px; font-weight: 600;">Simpan Kategori</button>
            </div>
        </form>
    </div>
</div>
@endsection
