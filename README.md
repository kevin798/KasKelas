# KasKelas

Aplikasi kas kelas berbasis Laravel dengan antarmuka Bahasa Indonesia. Proyek memakai database Laravel yang sudah dikonfigurasi (SQLite secara default), sehingga tidak memerlukan kredensial Supabase. Data tidak diisi dengan transaksi contoh: saldo dan laporan bermula dari saldo awal yang dimasukkan bendahara.

## Menjalankan secara lokal

1. Pastikan PHP 8.3+, Composer, Node.js, dan npm tersedia.
2. Pasang dependensi PHP dan siapkan environment:

   ```sh
   composer install
   cp .env.example .env
   php artisan key:generate
   ```

   Pada PowerShell, gunakan `Copy-Item .env.example .env` jika file `.env` belum ada.

3. Secara default database menggunakan SQLite. Buat file database lalu jalankan migrasi:

   ```sh
   New-Item -ItemType File database/database.sqlite
   php artisan migrate
   ```

   Bila file sudah ada, lewati `New-Item`. Untuk MySQL/PostgreSQL, isi variabel `DB_*` di `.env` sebelum migrasi.

4. Pasang dan build aset frontend:

   ```sh
   npm install
   npm run build
   ```

5. Jalankan aplikasi:

   ```sh
   php artisan serve
   ```

   Buka alamat lokal yang ditampilkan Artisan. Untuk pengembangan dengan Vite, jalankan `npm run dev` pada terminal lain.

## Pendaftaran akun dan kelas

Buka `/register` dan pilih jenis akun:

- **Super Admin** membuat kelas baru, mengisi saldo awal yang benar, lalu mendapat kode kelas unik. Kode ditampilkan pada menu **Kelola admin** dan hanya super admin yang dapat membuat kode pengganti.
- **User/Siswa** harus memasukkan kode dari kelas yang ingin dimasuki. Kode tidak memberi hak admin; super admin dapat mengubah peran anggota kelas menjadi admin dari daftar anggota.

Pengguna lama tetap memakai email dan kata sandi sebelumnya. Migrasi menjadikan admin tertua per kelas sebagai super admin dan membuat kode kelas untuk kelas lama.

## Akses dan data

- Role dan pembatasan perubahan diperiksa pada route/controller server. Pendaftaran User selalu membuat role siswa. Hanya super admin yang dapat mengubah anggota di kelasnya menjadi admin atau mengelola kode kelas.
- Transaksi kelas dan bukti transaksi kelas bisa dilihat oleh anggota kelas yang sudah masuk. Bukti pembayaran iuran bersifat privat dan hanya dapat dilihat pemilik serta admin kelas.
- Pengubahan dan pembatalan transaksi memerlukan alasan. Log audit tidak memiliki fitur ubah/hapus pada antarmuka.
- Foto disimpan pada disk lokal privat (`storage/app/private`) dan disajikan melalui route yang memeriksa akses. Atur backup dan disk privat yang sesuai sebelum deployment.
- Saldo dihitung dari saldo awal ditambah pemasukan aktif dikurangi pengeluaran aktif. Pembayaran baru menjadi pemasukan kas setelah diverifikasi bendahara.

## Catatan deployment

Gunakan HTTPS, atur `APP_ENV=production` dan `APP_DEBUG=false`, simpan `APP_KEY` dengan aman, jalankan migrasi saat deployment, dan konfigurasikan penyimpanan privat yang persisten untuk bukti. Buat backup database dan bukti secara rutin. Aplikasi ini tidak mengaktifkan PostgreSQL Row Level Security karena proyek yang tersedia memakai Laravel; perlindungan akses dilakukan di server aplikasi.
Super admin pertama dibuat pada `/setup`. Dari menu **Kelola admin**, super admin dapat menambah beberapa admin untuk kelasnya, mengubah identitas atau kata sandi, dan mengaktifkan atau menonaktifkan akun. Jika aplikasi sudah memiliki akun admin dari versi sebelumnya, migrasi akan menaikkan admin tertua di setiap kelas menjadi super admin. Admin yang dinonaktifkan ditolak saat login dan pada setiap request berikutnya.

Untuk mendaftarkan super admin lain, super admin yang sudah masuk membuat undangan dari menu **Kelola admin**. Bagikan tautan yang dihasilkan hanya kepada penerima yang dituju. Tautan `/register/super-admin/{token}` terikat ke email dan kelas undangan, berlaku 24 jam, serta hanya dapat dipakai sekali.
