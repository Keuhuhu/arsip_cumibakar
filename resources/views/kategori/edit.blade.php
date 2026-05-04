@extends('app')

@section('title', 'Edit Kategori')
@section('page-title', 'Edit Kategori')

@section('content')
<div class="card" style="max-width:600px; margin:0 auto;">
    <div class="card-header">
        <span class="card-title">✏️ Edit Kategori: {{ $kategori->nama }}</span>
        <a href="{{ route('kategori.index') }}" class="btn btn-outline-secondary btn-xs">Kembali</a>
    </div>

    <div class="card-body">
        <form action="{{ route('kategori.update', $kategori) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="nama" class="form-label">Nama Kategori <span class="text-danger">*</span></label>
                <input type="text" name="nama" id="nama" class="form-control" value="{{ old('nama', $kategori->nama) }}" required>
            </div>

            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        <label for="kode" class="form-label">Kode Singkat <span class="text-danger">*</span></label>
                        <input type="text" name="kode" id="kode" class="form-control" value="{{ old('kode', $kategori->kode) }}" required>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label for="urutan" class="form-label">Urutan Tampil</label>
                        <input type="number" name="urutan" id="urutan" class="form-control" value="{{ old('urutan', $kategori->urutan) }}">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        <label for="icon" class="form-label">Ikon (Emoji)</label>
                        <input type="text" name="icon" id="icon" class="form-control" value="{{ old('icon', $kategori->icon) }}">
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label for="warna" class="form-label">Warna Label (Hex)</label>
                        <input type="color" name="warna" id="warna" class="form-control" value="{{ old('warna', $kategori->warna) }}" style="height:38px; padding:2px;">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="is_active" class="form-label">Status Kategori</label>
                <select name="is_active" id="is_active" class="form-control">
                    <option value="1" {{ old('is_active', $kategori->is_active) ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ old('is_active', $kategori->is_active) ? '' : 'selected' }}>Nonaktif</option>
                </select>
            </div>

            <div class="form-group">
                <label for="deskripsi" class="form-label">Deskripsi / Keterangan Kategori</label>
                <textarea name="deskripsi" id="deskripsi" class="form-control" rows="3">{{ old('deskripsi', $kategori->deskripsi) }}</textarea>
            </div>

            <div style="margin-top: 24px; text-align: right;">
                <button type="submit" class="btn btn-warning" style="padding: 10px 24px; font-weight: 600;">Update Kategori</button>
            </div>
        </form>
    </div>
</div>
@endsection
