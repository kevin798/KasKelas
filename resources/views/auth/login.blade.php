<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk · KasKelas</title>
    @include('layouts.theme-bootstrap')
    @include('layouts.design-system')
</head>
<body class="guest-body">
<div class="guest-theme-toggle">@include('layouts.theme-toggle')</div>
<main class="login">
    <div class="brand"><span class="brand-mark"><svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5"><path d="M4 12h16M12 4v16"/></svg></span><span>Kas<span class="brand-name-accent">Kelas</span></span></div>
    <div class="eyebrow">Transparan bersama</div><h1>Selamat datang</h1><p class="intro">Masuk untuk melihat kas kelas dengan jelas.</p>
    @if($errors->any())<div class="error">{{ $errors->first() }}</div>@endif
    <form method="POST" action="{{ route('login.store') }}">@csrf
        <div class="field"><label for="email">Email</label><input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="username" required autofocus placeholder="nama@sekolah.id"></div>
        <div class="field"><label for="password">Kata sandi</label><input id="password" name="password" type="password" autocomplete="current-password" required placeholder="Masukkan kata sandi"></div>
        <label class="remember-field"><input type="checkbox" name="remember"> Tetap masuk</label><button class="button">Masuk ke KasKelas</button>
    </form>
    <div class="foot">Saldo dan laporan hanya memuat data yang benar-benar dicatat di aplikasi.</div>
    <a class="setup-link" href="{{ route('register') }}">Belum punya akun? Daftar sebagai Super Admin atau User</a>
</main>
@include('layouts.theme-script')
</body>
</html>
