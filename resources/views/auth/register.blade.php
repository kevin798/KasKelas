<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar · KasKelas</title>
    @include('layouts.theme-bootstrap')
    @include('layouts.design-system')
</head>
<body class="guest-body">
<div class="guest-theme-toggle">@include('layouts.theme-toggle')</div>
<main class="card register-card">
    <div class="brand"><span class="brand-mark"><svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5"><path d="M4 12h16M12 4v16"/></svg></span><span>Kas<span class="brand-name-accent">Kelas</span></span></div>
    <div class="eyebrow">Bergabung dengan KasKelas</div><h1>Buat akun</h1><p class="intro">Pilih jenis akun. User harus memasukkan kode kelas dari super admin agar bergabung ke kelas yang benar.</p>
    @if($errors->any())<div class="error">{{ $errors->first() }}</div>@endif
    <form method="POST" action="{{ route('register.store') }}" id="registration-form">@csrf
        <div class="field"><label for="account_type">Daftar sebagai</label><select id="account_type" name="account_type" required><option value="user" @selected(old('account_type','user')==='user')>User / Siswa</option><option value="super_admin" @selected(old('account_type')==='super_admin')>Super Admin — buat kelas baru</option></select></div>
        <div class="field"><label for="name">Nama lengkap</label><input id="name" name="name" value="{{ old('name') }}" maxlength="120" autocomplete="name" required></div>
        <div class="field"><label for="email">Email</label><input id="email" name="email" type="email" value="{{ old('email') }}" maxlength="255" autocomplete="email" required></div>
        <div id="user-fields"><div class="field"><label for="class_code">Kode kelas</label><input id="class_code" name="class_code" value="{{ old('class_code') }}" maxlength="10" minlength="10" placeholder="Masukkan kode dari super admin" autocomplete="off"></div></div>
        <div id="super-admin-fields" class="hidden">
            <div class="field"><label for="class_name">Nama kelas</label><input id="class_name" name="class_name" value="{{ old('class_name') }}" maxlength="120" placeholder="Contoh: XI IPA 1"></div>
            <div class="grid"><div class="field"><label for="monthly_dues">Iuran bulanan (Rp)</label><input id="monthly_dues" name="monthly_dues" type="number" min="0" max="100000000" value="{{ old('monthly_dues','0') }}"></div><div class="field"><label for="initial_balance">Saldo awal (Rp)</label><input id="initial_balance" name="initial_balance" type="number" min="0" max="1000000000" value="{{ old('initial_balance','0') }}"></div></div>
        </div>
        <div class="grid"><div class="field"><label for="password">Kata sandi (min. 10 karakter)</label><input id="password" name="password" type="password" minlength="10" autocomplete="new-password" required></div><div class="field"><label for="password_confirmation">Ulangi kata sandi</label><input id="password_confirmation" name="password_confirmation" type="password" minlength="10" autocomplete="new-password" required></div></div>
        <button class="button">Daftar</button>
    </form>
    <div class="foot">Sudah punya akun? <a href="{{ route('login') }}">Masuk</a><br>Super Admin membuat kelas baru dan kode kelasnya. User hanya dapat bergabung memakai kode yang valid.</div>
</main>
@include('layouts.theme-script')
<script>
    const accountType = document.getElementById('account_type');
    const userFields = document.getElementById('user-fields');
    const superAdminFields = document.getElementById('super-admin-fields');
    const classCode = document.getElementById('class_code');
    const className = document.getElementById('class_name');
    function updateRegistrationFields() {
        const isSuperAdmin = accountType.value === 'super_admin';
        userFields.classList.toggle('hidden', isSuperAdmin);
        superAdminFields.classList.toggle('hidden', !isSuperAdmin);
        classCode.required = !isSuperAdmin;
        className.required = isSuperAdmin;
    }
    accountType.addEventListener('change', updateRegistrationFields);
    updateRegistrationFields();
</script>
</body>
</html>
