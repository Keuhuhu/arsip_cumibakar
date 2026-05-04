@extends('app')

@section('title', 'Tambah Pengguna Baru')
@section('page-title', 'Tambah Pengguna')

@section('content')
<div class="card" style="max-width:800px; margin:0 auto;">
    <div class="card-header">
        <span class="card-title">👥 Form Tambah Akun Pengguna</span>
        <a href="{{ route('pengguna.index') }}" class="btn btn-outline-secondary btn-xs">Kembali</a>
    </div>

    <div class="card-body">
        <form action="{{ route('pengguna.store') }}" method="POST">
            @csrf

            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        <label for="name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label for="email" class="form-label">Alamat Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                        <input type="password" name="password" id="password" class="form-control" required placeholder="Min 8 karakter (huruf & angka)">
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label for="role" class="form-label">Peran (Role) Hak Akses <span class="text-danger">*</span></label>
                        <select name="role" id="role" class="form-control" required>
                            <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>User Biasa (Hanya Lihat/Unduh)</option>
                            <option value="super_admin" {{ old('role') == 'super_admin' ? 'selected' : '' }}>Super Admin (Akses Penuh)</option>
                        </select>
                    </div>
                </div>
            </div>

            <hr style="border:0; border-top:1px solid var(--border-color); margin:20px 0;">
            <h4 style="font-size:14px; margin-bottom:15px; color:var(--text-dark);">Informasi Opsional (Pegawai Desa)</h4>

            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        <label for="jabatan" class="form-label">Jabatan di Desa</label>
                        <input type="text" name="jabatan" id="jabatan" class="form-control" value="{{ old('jabatan') }}" placeholder="Contoh: Sekretaris Desa">
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label for="nip" class="form-label">NIP / NIK</label>
                        <input type="text" name="nip" id="nip" class="form-control" value="{{ old('nip') }}" placeholder="Nomor Induk Pegawai/Kependudukan">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        <label for="no_hp" class="form-label">Nomor HP / WhatsApp</label>
                        <input type="text" name="no_hp" id="no_hp" class="form-control" value="{{ old('no_hp') }}" placeholder="08xxxxxxxxxx">
                    </div>
                </div>
            </div>

            <div style="margin-top: 24px; text-align: right;">
                <button type="submit" class="btn btn-primary" style="padding: 10px 24px; font-weight: 600;">Simpan Pengguna</button>
            </div>
        </form>
    </div>
</div>
@endsection
