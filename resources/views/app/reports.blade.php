@extends('layouts.app')
@section('title', 'Laporan keuangan')
@section('content')
<div class="welcome">
    <p>Rekap diperbarui otomatis dari transaksi kas. Unduhan CSV dapat dibuka di Excel atau Google Sheets.</p>
    <a class="button secondary" href="{{ route('reports.export') }}">Unduh semua periode (CSV)</a>
</div>

<section class="stats">
    <article class="stat">
        <div class="stat-top">Saldo awal</div>
        <div class="stat-value">Rp {{ number_format($class?->initial_balance ?? 0, 0, ',', '.') }}</div>
        <div class="stat-note">Dicatat saat membuat kelas</div>
    </article>
    <article class="stat">
        <div class="stat-top">Jumlah transaksi</div>
        <div class="stat-value">{{ $transactions->count() }}</div>
        <div class="stat-note">Transaksi aktif di laporan</div>
    </article>
    <article class="stat">
        <div class="stat-top">Pemasukan</div>
        <div class="stat-value positive">Rp {{ number_format($transactions->where('type', 'income')->sum('amount'), 0, ',', '.') }}</div>
        <div class="stat-note">Total pemasukan tercatat</div>
    </article>
    <article class="stat">
        <div class="stat-top">Pengeluaran</div>
        <div class="stat-value negative">Rp {{ number_format($transactions->where('type', 'expense')->sum('amount'), 0, ',', '.') }}</div>
        <div class="stat-note">Total pengeluaran tercatat</div>
    </article>
</section>

<section class="panel section">
    <div class="panel-head">
        <div>
            <div class="panel-title">Rekap per bulan</div>
            <div class="panel-sub">Angka mengikuti transaksi aktif dan diperbarui setiap kali laporan dibuka.</div>
        </div>
    </div>
    @forelse($months as $month => $items)
        <div class="report-card">
            <div class="report-month-info">
                <strong>{{ \Carbon\Carbon::createFromFormat('Y-m', $month)->translatedFormat('F Y') }}</strong>
                <div class="panel-sub">{{ $items->count() }} transaksi · <a class="link" href="{{ route('transactions', ['month' => $month]) }}">Lihat detail</a></div>
                <a class="link report-download" href="{{ route('reports.export', ['month' => $month]) }}">Unduh rekap bulan ini (CSV)</a>
            </div>
            <div class="report-totals">
                <div class="positive">+ Rp {{ number_format($items->where('type', 'income')->sum('amount'), 0, ',', '.') }}</div>
                <div class="negative">− Rp {{ number_format($items->where('type', 'expense')->sum('amount'), 0, ',', '.') }}</div>
            </div>
        </div>
    @empty
        <div class="empty"><b>Belum ada data laporan</b>Laporan akan terbentuk setelah transaksi kas dicatat.</div>
    @endforelse
</section>
@endsection
