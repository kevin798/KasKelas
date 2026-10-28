<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar Super Admin · KasKelas</title>
    @include('layouts.theme-bootstrap')
    @include('layouts.design-system')
</head>
<body class="guest-body">
<div class="guest-theme-toggle">@include('layouts.theme-toggle')</div>
<main class="card">
    <div class="brand"><span class="brand-mark"><svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5"><path d="M4 12h16M12 4v16"/></svg></span><span>Kas<span class="brand-name-accent">Kelas</span></span></div>
    <div class="eyebrow">Pendaftaran melalui undangan</div>
    <h1>Daftar sebagai super admin</h1>
    <p class="muted">Undangan ini memberi akses pengelolaan pada kelas berikut.</p>
    <div class="class-box"><small>Kelas</small><strong>{{ $invitation->kasClass?->name ?? 'KasKelas' }}</strong></div>
    @if($errors->any())<div class="error">{{ $errors->first() }}</div>@endif
    <form method="POST" action="{{ route('super-admin.register.store', $token) }}">
        @csrf
        <div class="field"><label for="name">Nama lengkap</label><input id="name" name="name" value="{{ old('name') }}" maxlength="120" required autofocus></div>
        <div class="field"><label>Email undangan</label><input type="email" value="{{ $invitation->email }}" readonly></div>
        <div class="field"><label for="password">Kata sandi (min. 10 karakter)</label><input id="password" name="password" type="password" minlength="10" autocomplete="new-password" required></div>
        <div class="field"><label for="password_confirmation">Ulangi kata sandi</label><input id="password_confirmation" name="password_confirmation" type="password" minlength="10" autocomplete="new-password" required></div>
        <button class="button">Buat akun super admin</button>
    </form>
    <div class="foot">Tautan pendaftaran hanya dapat digunakan sekali. Setelah mendaftar, akun akan langsung masuk sebagai super admin kelas ini.</div>
</main>
@include('layouts.theme-script')
</body>
</html>
