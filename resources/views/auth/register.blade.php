<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun — Arsip Digital Desa Cumibakar</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

<div class="auth-page">
    {{-- ========== LEFT PANEL ========== --}}
    <div class="auth-left">
        <div class="auth-left-decor">
            <span></span>
            <span></span>
            <span></span>
        </div>

        <div class="auth-left-content">
            <span class="auth-left-icon">🏛️</span>
            <h1>Bergabunglah<br>Bersama Kami! <span class="wave">🤝</span></h1>
            <p>Daftarkan akun Anda untuk mengakses arsip dokumen desa secara digital. Cepat, mudah, dan gratis.</p>
        </div>

        <div class="auth-left-footer">
            &copy; {{ date('Y') }} Desa Cumibakar. Hak cipta dilindungi.
        </div>
    </div>

    {{-- ========== RIGHT PANEL ========== --}}
    <div class="auth-right">
        <div class="auth-brand">
            <span class="auth-brand-icon">🏛️</span>
            <span class="auth-brand-name">Desa Cumibakar</span>
        </div>

        <h2 class="auth-title">Buat Akun Baru</h2>
        <p class="auth-sub">Lengkapi formulir di bawah ini untuk bergabung<br>dengan sistem arsip digital desa kami.</p>

        {{-- Error Messages --}}
        @if($errors->any())
        <div class="alert alert-danger alert-dismissible">
            <div>
                @foreach($errors->all() as $error)
                    <span>{{ $error }}</span><br>
                @endforeach
            </div>
            <button onclick="this.parentElement.remove()" class="alert-close">&times;</button>
        </div>
        @endif

        <form method="POST" action="{{ route('register.post') }}" class="auth-form">
            @csrf

            <div class="form-group">
                <label for="name" class="form-label">Nama Lengkap</label>
                <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required autofocus placeholder="Nama lengkap Anda">
            </div>

            <div class="form-group">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required placeholder="Alamat email Anda">
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-control" required placeholder="Minimal 8 karakter (huruf & angka)">
            </div>

            <div class="form-group">
                <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required placeholder="Ulangi password Anda">
            </div>

            <button type="submit" class="auth-btn auth-btn-primary">Daftar Sekarang</button>
        </form>

        <div class="auth-footer-text">
            Sudah punya akun? <a href="{{ route('login') }}" style="color: var(--hijau); font-weight: 600; text-decoration: none;">Masuk di sini</a>
            <br><br>
            Dengan mendaftar, Anda menyetujui <strong>Ketentuan Layanan</strong> kami.
        </div>
    </div>
</div>

</body>
</html>
