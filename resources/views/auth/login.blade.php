<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Arsip Digital Desa Cumibakar</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>

<div class="auth-page">
    {{-- ========== LEFT PANEL ========== --}}
    <div class="auth-left">
        {{-- Decorative circles --}}
        <div class="auth-left-decor">
            <span></span>
            <span></span>
            <span></span>
        </div>

        <div class="auth-left-content">
            <span class="auth-left-icon">🏛️</span>
            <h1>Selamat Datang<br>di Arsip Desa! <span class="wave">👋</span></h1>
            <p>Kelola arsip surat masuk, surat keluar, dan dokumen penting desa secara digital. Cepat, aman, dan mudah diakses kapan saja.</p>
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

        <h2 class="auth-title">Masuk ke Sistem</h2>
        <p class="auth-sub">Silakan masuk dengan akun Anda untuk<br>mengakses sistem arsip digital desa.</p>

        {{-- Error & Success Messages --}}
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

        @if(session('success'))
        <div class="alert alert-success alert-dismissible">
            <span>✅ {{ session('success') }}</span>
            <button onclick="this.parentElement.remove()" class="alert-close">&times;</button>
        </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}" class="auth-form">
            @csrf

            <div class="form-group">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required autofocus placeholder="Alamat email Anda">
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-control" required placeholder="Password">
            </div>

            <div class="auth-remember">
                <input type="checkbox" name="remember" id="remember">
                <label for="remember">Ingat saya di perangkat ini</label>
            </div>

            <button type="submit" class="auth-btn auth-btn-primary">Masuk Sekarang</button>
        </form>

        <div class="auth-footer-text">
            Belum punya akun? <a href="{{ route('register') }}" style="color: var(--hijau); font-weight: 600; text-decoration: none;">Daftar sekarang</a>
            <br><br>
            Lupa password? Hubungi <strong>Super Admin</strong> untuk reset.
        </div>
    </div>
</div>

</body>
</html>
