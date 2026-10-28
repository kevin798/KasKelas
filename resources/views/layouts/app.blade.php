<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Ringkasan') · KasKelas</title>
    @include('layouts.theme-bootstrap')
    @include('layouts.design-system')
</head>
<body class="app-body">
<div class="shell">
    <button class="sidebar-backdrop" id="sidebar-backdrop" type="button" aria-label="Tutup menu"></button>
    <aside class="sidebar" id="sidebar" aria-label="Navigasi utama">
        <a class="brand" href="{{ route('dashboard') }}">
            <span class="brand-mark" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M5 12h14M12 5v14"/></svg></span>
            <span>Kas<span class="brand-name-accent">Kelas</span></span>
        </a>

        <div class="nav-label">Menu utama</div>
        <nav class="nav" aria-label="Menu utama">
            <a class="{{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="3" y="3" width="8" height="8" rx="1.5"/><rect x="13" y="3" width="8" height="5" rx="1.5"/><rect x="13" y="10" width="8" height="11" rx="1.5"/><rect x="3" y="13" width="8" height="8" rx="1.5"/></svg>Ringkasan</a>
            <a class="{{ request()->routeIs('transactions') ? 'active' : '' }}" href="{{ route('transactions') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v18m7-14H9a4 4 0 0 0 0 8h6a4 4 0 0 1 0 8H5"/></svg>Transaksi kas</a>
            @if(auth()->user()->isClassAdministrator())
                <a class="{{ request()->routeIs('payments') ? 'active' : '' }}" href="{{ route('payments') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 10h18m-4 5h1"/></svg>Pembayaran anggota</a>
                <a class="{{ request()->routeIs('inquiries') ? 'active' : '' }}" href="{{ route('inquiries') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M20 11.5a7.5 7.5 0 0 1-7.5 7.5H6l-3 2v-6.5A7.5 7.5 0 1 1 20 11.5Z"/><path d="M8 10h8M8 13h5"/></svg>Masukan anggota</a>
                @if(auth()->user()->role === 'admin')
                    <a class="{{ request()->routeIs('my-dues') ? 'active' : '' }}" href="{{ route('my-dues') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 10h18m-4 5h1"/></svg>Iuran saya</a>
                @endif
                <a class="{{ request()->routeIs('activity') ? 'active' : '' }}" href="{{ route('activity') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 2.6-6.4L3 8m0-5v5h5m4-1v5l3 2"/></svg>Riwayat aktivitas</a>
            @else
                <a class="{{ request()->routeIs('my-dues') ? 'active' : '' }}" href="{{ route('my-dues') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 10h18m-4 5h1"/></svg>Iuran saya</a>
            @endif
            <a class="{{ request()->routeIs('reports*') ? 'active' : '' }}" href="{{ route('reports') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19V5m0 14h17M8 15l3-4 3 2 5-7"/></svg>Laporan keuangan</a>
        </nav>

        @if(auth()->user()->isSuperAdministrator())
            <div class="nav-label">Administrasi</div>
            <nav class="nav" aria-label="Administrasi">
                <a class="{{ request()->routeIs('class-admins*') ? 'active' : '' }}" href="{{ route('class-admins') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2m16 0v-2a4 4 0 0 0-3-3.87M10 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm8-7.87a4 4 0 0 1 0 7.75"/></svg>Kelola kelas dan admin</a>
            </nav>
        @endif

        <div class="nav-label">Akun</div>
        <nav class="nav" aria-label="Akun">
            <a class="{{ request()->routeIs('account') ? 'active' : '' }}" href="{{ route('account') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a7.7 7.7 0 0 0 0-6l1.3-1-1.5-2.6-1.6.6a7.5 7.5 0 0 0-2.6-1.5L14.7 3h-3.1l-.3 1.5a7.5 7.5 0 0 0-2.6 1.5l-1.6-.6-1.5 2.6 1.3 1a7.7 7.7 0 0 0 0 6l-1.3 1 1.5 2.6 1.6-.6a7.5 7.5 0 0 0 2.6 1.5l.3 1.5h3.1l.3-1.5a7.5 7.5 0 0 0 2.6-1.5l1.6.6 1.5-2.6z"/></svg>Pengaturan akun</a>
        </nav>

        <div class="sidebar-foot">
            <div class="class-chip"><small>Kelas aktif</small><strong>{{ $activeClassName }}</strong></div>
            <div class="profile">
                <div class="avatar" aria-hidden="true">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                <div class="user-meta"><strong>{{ auth()->user()->name }}</strong><small>{{ auth()->user()->isSuperAdministrator() ? 'Super admin' : (auth()->user()->role === 'admin' ? 'Admin kelas' : 'Siswa') }}</small></div>
            </div>
        </div>
    </aside>

    <main class="main">
        <header class="topbar">
            <div class="topbar-leading">
                <button id="menu-toggle" class="icon-button menu-toggle" type="button" aria-label="Buka menu" aria-expanded="false" aria-controls="sidebar"><svg viewBox="0 0 24 24" width="19" height="19" fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1.8"><path d="M4 7h16M4 12h16M4 17h16"/></svg></button>
                <div><div class="crumb">KasKelas</div><h1 class="page-title">@yield('title', 'Ringkasan')</h1></div>
            </div>
            <div class="top-actions"><span class="date-pill">{{ now()->translatedFormat('l, d F Y') }}</span>@include('layouts.theme-toggle')<a class="icon-button" href="{{ route('account') }}" aria-label="Pengaturan akun"><svg viewBox="0 0 24 24" width="17" height="17" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a7.7 7.7 0 0 0 0-6l1.3-1-1.5-2.6-1.6.6a7.5 7.5 0 0 0-2.6-1.5L14.7 3h-3.1l-.3 1.5a7.5 7.5 0 0 0-2.6 1.5l-1.6-.6-1.5 2.6 1.3 1a7.7 7.7 0 0 0 0 6l-1.3 1 1.5 2.6 1.6-.6a7.5 7.5 0 0 0 2.6 1.5l.3 1.5h3.1l.3-1.5a7.5 7.5 0 0 0 2.6-1.5l1.6.6 1.5-2.6z"/></svg></a></div>
        </header>

        @if(session('success'))<div class="flash" role="status">{{ session('success') }}</div>@endif
        @if($errors->any())<div class="error" role="alert">{{ $errors->first() }}</div>@endif
        @yield('content')
    </main>
</div>
<script>
    const sidebar = document.getElementById('sidebar');
    const menuToggle = document.getElementById('menu-toggle');
    const sidebarBackdrop = document.getElementById('sidebar-backdrop');

    function closeSidebar() {
        sidebar.classList.remove('open');
        sidebarBackdrop.classList.remove('open');
        menuToggle.setAttribute('aria-expanded', 'false');
    }

    menuToggle.addEventListener('click', () => {
        const isOpen = sidebar.classList.toggle('open');
        sidebarBackdrop.classList.toggle('open', isOpen);
        menuToggle.setAttribute('aria-expanded', String(isOpen));
    });

    sidebarBackdrop.addEventListener('click', closeSidebar);
    window.addEventListener('resize', () => {
        if (window.innerWidth > 760) {
            closeSidebar();
        }
    });
</script>
@include('layouts.theme-script')
</body>
</html>
