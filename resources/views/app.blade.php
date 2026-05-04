<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Arsip Digital Desa Cumibakar</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}">
    @stack('styles')
</head>
<body>

<div class="wrapper">
    {{-- Sidebar --}}
    <nav class="sidebar {{ auth()->user()->isUser() ? 'theme-user' : '' }}" id="sidebar">
        <div class="sidebar-brand">
            <div class="brand-icon">🏛️</div>
            <div class="brand-text">
                <div class="brand-name">Desa Cumibakar</div>
                <div class="brand-sub">Sistem Arsip Digital</div>
            </div>
        </div>

        <div class="sidebar-nav">
            <div class="nav-section">Menu Utama</div>
            <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <span class="nav-icon">📊</span>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('dokumen.index') }}" class="nav-item {{ request()->routeIs('dokumen.*') ? 'active' : '' }}">
                <span class="nav-icon">📁</span>
                <span>Arsip Dokumen</span>
                @php $tindakLanjut = \App\Models\Dokumen::where('status','tindak_lanjut')->count() @endphp
                @if($tindakLanjut > 0 && auth()->user()->isSuperAdmin())
                    <span class="nav-badge">{{ $tindakLanjut }}</span>
                @endif
            </a>
            <a href="{{ route('agenda.index') }}" class="nav-item {{ request()->routeIs('agenda.*') ? 'active' : '' }}">
                <span class="nav-icon">📋</span>
                <span>Buku Agenda</span>
            </a>

            @if(auth()->user()->isSuperAdmin())
            <div class="nav-section">Administrasi</div>
            <a href="{{ route('laporan.index') }}" class="nav-item {{ request()->routeIs('laporan.*') ? 'active' : '' }}">
                <span class="nav-icon">📈</span>
                <span>Laporan & Ekspor</span>
            </a>
            <a href="{{ route('log.index') }}" class="nav-item {{ request()->routeIs('log.*') ? 'active' : '' }}">
                <span class="nav-icon">🔍</span>
                <span>Log Aktivitas</span>
            </a>

            <div class="nav-section">Pengaturan Sistem</div>
            <a href="{{ route('pengguna.index') }}" class="nav-item {{ request()->routeIs('pengguna.*') ? 'active' : '' }}">
                <span class="nav-icon">👥</span>
                <span>Manajemen Pengguna</span>
            </a>
            <a href="{{ route('kategori.index') }}" class="nav-item {{ request()->routeIs('kategori.*') ? 'active' : '' }}">
                <span class="nav-icon">🏷️</span>
                <span>Kategori Arsip</span>
            </a>
            <a href="{{ route('backup.index') }}" class="nav-item {{ request()->routeIs('backup.*') ? 'active' : '' }}">
                <span class="nav-icon">💾</span>
                <span>Backup & Restore</span>
            </a>
            @endif
        </div>

        <div class="sidebar-user">
            <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</div>
            <div class="user-info">
                <div class="user-name">{{ auth()->user()->name }}</div>
                <div class="user-role">{{ auth()->user()->role_label }}</div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="logout-form">
                @csrf
                <button type="submit" class="logout-btn" title="Logout">⏻</button>
            </form>
        </div>
    </nav>

    {{-- Main Content --}}
    <div class="main-content">
        <header class="topbar">
            <button class="sidebar-toggle" onclick="toggleSidebar()">☰</button>
            <h1 class="page-title">@yield('page-title', 'Dashboard')</h1>
            <div class="topbar-actions">
                @can('upload-dokumen')
                <a href="{{ route('dokumen.create') }}" class="btn btn-primary btn-sm">
                    + Unggah Dokumen
                </a>
                @endcan
                <div class="topbar-user">
                    <span class="role-badge role-{{ auth()->user()->role }}">{{ auth()->user()->role_label }}</span>
                </div>
            </div>
        </header>

        <div class="content-area">
            {{-- Flash Messages --}}
            @if(session('success'))
            <div class="alert alert-success alert-dismissible">
                <span>✅ {{ session('success') }}</span>
                <button onclick="this.parentElement.remove()" class="alert-close">×</button>
            </div>
            @endif
            @if(session('error'))
            <div class="alert alert-danger alert-dismissible">
                <span>❌ {{ session('error') }}</span>
                <button onclick="this.parentElement.remove()" class="alert-close">×</button>
            </div>
            @endif
            @if($errors->any())
            <div class="alert alert-danger alert-dismissible">
                <strong>Terdapat kesalahan:</strong>
                <ul class="error-list">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button onclick="this.parentElement.remove()" class="alert-close">×</button>
            </div>
            @endif

            @yield('content')
        </div>
    </div>
</div>

<script src="{{ asset('js/app.js') }}"></script>
@stack('scripts')
</body>
</html>