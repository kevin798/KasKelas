@extends('layouts.app')
@section('title', 'Ringkasan')
@section('content')
<div class="welcome">
    <p>Ringkasan kas {{ $class?->name ?? 'kelas Anda' }}.</p>
    @if(auth()->user()->isClassAdministrator())
        <a class="button" href="{{ route('transactions') }}#tambah">Catat transaksi</a>
    @endif
</div>

@php($balance = ($class?->initial_balance ?? 0) + $income - $expense)
<section class="stats">
    <article class="stat">
        <div class="stat-top">Saldo kas saat ini <span class="stat-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 10h18"/></svg></span></div>
        <div class="stat-value">Rp {{ number_format($balance, 0, ',', '.') }}</div>
        <div class="stat-note">Saldo awal + pemasukan − pengeluaran</div>
    </article>
    <article class="stat">
        <div class="stat-top">Total pemasukan <span class="stat-icon green">↙</span></div>
        <div class="stat-value positive">Rp {{ number_format($income, 0, ',', '.') }}</div>
        <div class="stat-note">Transaksi pemasukan tercatat</div>
    </article>
    <article class="stat">
        <div class="stat-top">Total pengeluaran <span class="stat-icon red">↗</span></div>
        <div class="stat-value negative">Rp {{ number_format($expense, 0, ',', '.') }}</div>
        <div class="stat-note">Transaksi pengeluaran tercatat</div>
    </article>
    <article class="stat">
        <div class="stat-top">Pembayaran iuran bulan ini <span class="stat-icon amber">◎</span></div>
        <div class="stat-value">{{ $paid }}<span class="muted stat-denominator"> / {{ $students }}</span></div>
        <div class="stat-note">Anggota dengan iuran terverifikasi bulan ini</div>
    </article>
</section>

@if(!auth()->user()->isSuperAdministrator())
    <section class="stats">
        <article class="stat">
            <div class="stat-top">Pembayaran saya <span class="stat-icon green">✓</span></div>
            <div class="stat-value">{{ $myPaidInvoices }}<span class="muted stat-denominator"> kali</span></div>
            <div class="stat-note">Jumlah tagihan yang sudah dibayar dan diverifikasi</div>
        </article>
        <article class="stat">
            <div class="stat-top">Tagihan saya belum lunas <span class="stat-icon red">!</span></div>
            <div class="stat-value">{{ $myUnpaidInvoices }}<span class="muted stat-denominator"> tagihan</span></div>
            <div class="stat-note">Tagihan yang belum memiliki pembayaran terverifikasi</div>
        </article>
    </section>
@endif

@if(auth()->user()->isClassAdministrator())
    <section class="section">
        <div class="section-head">
            <div>
                <h2>Anggota belum membayar kas minggu ini</h2>
                <div class="panel-sub">{{ $weekStart->translatedFormat('d M') }} – {{ $weekEnd->translatedFormat('d M Y') }} · pembayaran dihitung setelah dicatat bendahara</div>
            </div>
            <span class="badge red">{{ $unpaidStudents->count() }} anggota</span>
        </div>
        <article class="panel">
            <div class="activity-list">
                @forelse($unpaidStudents as $student)
                    <div class="activity-row">
                        <div class="avatar">{{ strtoupper(substr($student->name, 0, 1)) }}</div>
                        <div class="activity-info"><strong>{{ $student->name }}</strong><small>{{ $student->email }}</small></div>
                        <span class="badge red">Belum bayar</span>
                    </div>
                @empty
                    <div class="empty"><b>Semua anggota sudah membayar</b>Tidak ada pembayaran kas yang tertunda untuk minggu ini.</div>
                @endforelse
            </div>
        </article>
    </section>
@endif

<section class="grid-main">
    <article class="panel">
        <div class="panel-head">
            <div><div class="panel-title">Arus kas bulanan</div><div class="panel-sub">Enam bulan terakhir · Rupiah</div></div>
            <div class="chart-legend"><span><i class="legend-dot"></i>Pemasukan</span><span><i class="legend-dot expense"></i>Pengeluaran</span></div>
        </div>
        @php($maxAmount = max(1, $monthly->max(fn ($month) => max($month['income'], $month['expense']))))
        <div class="chart">
            @foreach($monthly as $month)
                <div class="month">
                    <div class="bar" title="Pemasukan Rp {{ number_format($month['income'], 0, ',', '.') }}" style="height:{{ max($month['income'] ? 3 : 1, round($month['income'] / $maxAmount * 88)) }}%"></div>
                    <div class="bar expense" title="Pengeluaran Rp {{ number_format($month['expense'], 0, ',', '.') }}" style="height:{{ max($month['expense'] ? 3 : 1, round($month['expense'] / $maxAmount * 88)) }}%"></div>
                </div>
            @endforeach
        </div>
        <div class="chart-labels">@foreach($monthly as $month)<span>{{ $month['label'] }}</span>@endforeach</div>
    </article>

    <article class="panel">
        <div class="panel-head"><div><div class="panel-title">Progres iuran</div><div class="panel-sub">Pembayaran terverifikasi</div></div><span class="stat-icon green">✓</span></div>
        <div class="progress-summary"><strong class="amount-large">{{ $students ? round($paid / $students * 100) : 0 }}%</strong><span class="muted">sudah membayar</span></div>
        <div class="progress"><span style="width:{{ $students ? min(100, round($paid / $students * 100)) : 0 }}%"></span></div>
        <div class="progress-foot"><span>{{ $paid }} anggota membayar</span><span>{{ max(0, $students - $paid) }} belum terverifikasi</span></div>
        <div class="progress-details">
            <div class="panel-title">Catatan transparansi</div>
            <p class="panel-sub">Ringkasan hanya menghitung transaksi tersimpan dan tidak dibatalkan. Saldo awal kas: <b>Rp {{ number_format($class?->initial_balance ?? 0, 0, ',', '.') }}</b>.</p>
        </div>
    </article>
</section>

<section class="section">
    <div class="section-head"><h2>Transaksi terbaru</h2><a class="link" href="{{ route('transactions') }}">Lihat semua →</a></div>
    <article class="panel">
        @forelse($transactions->take(5) as $transaction)
            <div class="activity-row">
                <div class="activity-symbol {{ $transaction->type === 'expense' ? 'expense' : '' }}">{{ $transaction->type === 'income' ? '↙' : '↗' }}</div>
                <div class="activity-info"><strong>{{ $transaction->description }}</strong><small>{{ $transaction->category }} · {{ $transaction->occurred_on->translatedFormat('d M Y') }}</small></div>
                <div class="activity-amount {{ $transaction->type === 'income' ? 'positive' : 'negative' }}">{{ $transaction->type === 'income' ? '+' : '−' }} Rp {{ number_format($transaction->amount, 0, ',', '.') }}</div>
            </div>
        @empty
            <div class="empty"><b>Belum ada transaksi</b>Transaksi akan tercatat di sini setelah dicatat bendahara.</div>
        @endforelse
    </article>
</section>

@if($expense > $income && $expense > 0)
    <section class="section">
        <div class="section-head"><h2>Perlu diperhatikan</h2></div>
        <div class="attention"><span aria-hidden="true">ⓘ</span><span>Total pengeluaran lebih besar dari pemasukan tercatat. Periksa transaksi untuk memastikan saldo sesuai.</span></div>
    </section>
@endif

<section class="section">
    <div class="section-head"><h2>Ada yang ingin ditanyakan?</h2></div>
    <article class="panel">
        <form method="POST" action="{{ route('inquiries.store') }}" class="toolbar inquiry-form">
            @csrf
            <textarea name="message" rows="2" placeholder="Laporkan ketidaksesuaian atau tanyakan sesuatu kepada bendahara…" required maxlength="2000"></textarea>
            <button class="button secondary">Kirim pertanyaan</button>
        </form>
    </article>
</section>
@endsection
