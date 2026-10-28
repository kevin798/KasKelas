<?php

use App\Http\Controllers\KasKelasController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => auth()->check() ? redirect()->route('dashboard') : redirect()->route('login'));
Route::get('/login', [KasKelasController::class, 'login'])->name('login')->middleware('guest');
Route::post('/login', [KasKelasController::class, 'authenticate'])->name('login.store')->middleware(['guest', 'throttle:5,1']);
Route::get('/register', [KasKelasController::class, 'register'])->name('register')->middleware('guest');
Route::post('/register', [KasKelasController::class, 'createAccount'])->name('register.store')->middleware(['guest', 'throttle:5,1']);
Route::get('/setup', fn () => redirect()->route('register'));
Route::get('/register/super-admin/{token}', [KasKelasController::class, 'showSuperAdminRegistration'])->name('super-admin.register')->middleware('guest');
Route::post('/register/super-admin/{token}', [KasKelasController::class, 'registerSuperAdmin'])->name('super-admin.register.store')->middleware(['guest', 'throttle:5,1']);

Route::middleware(['auth', 'active.user'])->group(function (): void {
    Route::post('/logout', [KasKelasController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [KasKelasController::class, 'dashboard'])->name('dashboard');
    Route::get('/transaksi', [KasKelasController::class, 'transactions'])->name('transactions');
    Route::get('/laporan', [KasKelasController::class, 'report'])->name('reports');
    Route::get('/laporan/unduh', [KasKelasController::class, 'export'])->name('reports.export');
    Route::get('/bukti/{kind}/{id}', [KasKelasController::class, 'receipt'])->name('receipts.show');
    Route::get('/akun', [KasKelasController::class, 'account'])->name('account');
    Route::put('/akun', [KasKelasController::class, 'updateAccount'])->name('account.update');
    Route::post('/pertanyaan', [KasKelasController::class, 'storeInquiry'])->name('inquiries.store');

    Route::middleware('role:admin,super_admin')->group(function (): void {
        Route::post('/transaksi', [KasKelasController::class, 'storeTransaction'])->name('transactions.store');
        Route::post('/transaksi/{transaction}/batalkan', [KasKelasController::class, 'cancelTransaction'])->name('transactions.cancel');
        Route::put('/transaksi/{transaction}', [KasKelasController::class, 'updateTransaction'])->name('transactions.update');
        Route::get('/pembayaran', [KasKelasController::class, 'payments'])->name('payments');
        Route::post('/tagihan', [KasKelasController::class, 'generateInvoices'])->name('invoices.store');
        Route::post('/pembayaran/tagihan/{invoice}/catat', [KasKelasController::class, 'recordPayment'])->name('payments.record');
        Route::get('/masukan', [KasKelasController::class, 'inquiries'])->name('inquiries');
        Route::get('/aktivitas', [KasKelasController::class, 'activity'])->name('activity');
    });

    Route::middleware('role:super_admin')->group(function (): void {
        Route::get('/admin-kelas', [KasKelasController::class, 'classAdmins'])->name('class-admins');
        Route::patch('/admin-kelas/{user}/peran', [KasKelasController::class, 'setMemberRole'])->name('class-admins.role');
        Route::post('/kelas/kode', [KasKelasController::class, 'regenerateClassCode'])->name('class-code.regenerate');
        Route::post('/admin-kelas/undang-super-admin', [KasKelasController::class, 'inviteSuperAdmin'])->name('class-admins.invite-super-admin');
        Route::put('/admin-kelas/{user}', [KasKelasController::class, 'updateClassAdmin'])->name('class-admins.update');
        Route::patch('/admin-kelas/{user}/status', [KasKelasController::class, 'toggleClassAdmin'])->name('class-admins.toggle');
    });

    Route::middleware('role:student,admin')->group(function (): void {
        Route::get('/iuran-saya', [KasKelasController::class, 'myDues'])->name('my-dues');
    });
});
