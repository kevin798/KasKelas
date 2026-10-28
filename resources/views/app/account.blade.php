@extends('layouts.app')
@section('title', 'Pengaturan akun')
@section('content')
<div class="welcome"><p>Kelola informasi profil dan keamanan akun Anda.</p></div>

<div class="account-stack">
    <article class="panel">
        <div class="panel-head">
            <div>
                <div class="panel-title">Informasi akun</div>
                <div class="panel-sub">Perubahan akan langsung berlaku setelah disimpan.</div>
            </div>
        </div>

        <form method="POST" action="{{ route('account.update') }}">
            @csrf
            @method('PUT')

            <section class="account-form-section">
                <div class="account-section-head">
                    <h2>Profil</h2>
                    <p>Nama dan email yang digunakan pada akun ini.</p>
                </div>
                <div class="form-grid">
                    <div class="form-field">
                        <label for="name">Nama lengkap</label>
                        <input class="field" id="name" name="name" value="{{ old('name', auth()->user()->name) }}" autocomplete="name" required>
                    </div>
                    <div class="form-field">
                        <label for="email">Alamat email</label>
                        <input class="field" id="email" type="email" name="email" value="{{ old('email', auth()->user()->email) }}" autocomplete="email" required>
                    </div>
                </div>
            </section>

            <section class="account-form-section">
                <div class="account-section-head">
                    <h2>Ubah kata sandi</h2>
                    <p>Kosongkan jika Anda tidak ingin mengganti kata sandi.</p>
                </div>
                <div class="form-grid">
                    <div class="form-field">
                        <label for="password">Kata sandi baru</label>
                        <input class="field" id="password" type="password" name="password" minlength="10" autocomplete="new-password" placeholder="Minimal 10 karakter">
                    </div>
                    <div class="form-field">
                        <label for="password_confirmation">Ulangi kata sandi baru</label>
                        <input class="field" id="password_confirmation" type="password" name="password_confirmation" minlength="10" autocomplete="new-password" placeholder="Masukkan kembali kata sandi">
                    </div>
                </div>
            </section>

            <div class="account-actions">
                <span class="panel-sub">Gunakan kata sandi yang sulit ditebak.</span>
                <button class="button" type="submit">Simpan perubahan</button>
            </div>
        </form>
    </article>

    <div class="account-utility-grid">
        <article class="panel">
            <div class="panel-title">Sesi akun</div>
            <p class="account-panel-copy">Keluar dari KasKelas pada perangkat ini.</p>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="button danger" type="submit">Keluar dari akun</button>
            </form>
        </article>

        <article class="panel">
            <div class="panel-title">Butuh bantuan?</div>
            <p class="account-panel-copy">Kirim pertanyaan atau laporkan ketidaksesuaian kepada bendahara kelas.</p>
            <form class="account-help-form" method="POST" action="{{ route('inquiries.store') }}">
                @csrf
                <div class="form-field">
                    <label for="message">Pesan</label>
                    <textarea class="field" id="message" name="message" rows="3" placeholder="Tuliskan pesan Anda" required minlength="5" maxlength="2000"></textarea>
                </div>
                <div class="form-actions"><button class="button secondary" type="submit">Kirim pesan</button></div>
            </form>
        </article>
    </div>
</div>
@endsection
