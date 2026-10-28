@extends('layouts.app')
@section('title','Pembayaran anggota')
@section('content')
<div class="welcome"><p>Kelola tagihan dan catat kas yang diterima untuk {{ $class?->name ?? 'kelas ini' }}.</p></div>
<section class="{{ auth()->user()->isSuperAdministrator() ? 'two-column section' : 'section' }}">
    <article class="panel"><div class="panel-head"><div><div class="panel-title">Buat tagihan iuran</div><div class="panel-sub">Satu tagihan untuk setiap anggota kelas, termasuk admin.</div></div></div><form method="POST" action="{{ route('invoices.store') }}">@csrf<div class="form-field" style="margin-bottom:12px"><label>Periode</label><input class="field" type="month" name="period" value="{{ now()->format('Y-m') }}" required></div><div class="form-field" style="margin-bottom:12px"><label>Nominal (Rp)</label><input class="field" type="number" min="1" name="amount" value="{{ $class?->monthly_dues }}" required></div><div class="form-field"><label>Batas pembayaran (opsional)</label><input class="field" type="date" name="due_on"></div><button class="button" style="margin-top:14px">Buat tagihan</button></form></article>
    @if(auth()->user()->isSuperAdministrator())<article class="panel"><div class="panel-head"><div><div class="panel-title">Pendaftaran siswa</div><div class="panel-sub">Bagikan kode kelas agar siswa dapat mendaftar sendiri.</div></div></div><a class="button secondary" href="{{ route('class-admins') }}">Kelola kode kelas</a></article>@endif
</section>
<section class="section"><div class="section-head"><h2>Tagihan dan status pembayaran</h2></div><article class="panel"><div class="table-wrap"><table><thead><tr><th>Anggota</th><th>Periode</th><th>Tagihan</th><th>Status</th><th>Tindakan bendahara</th></tr></thead><tbody>
@forelse($invoices as $invoice)
    @php($payment=$invoice->payments->first())
    <tr>
        <td>{{ $invoice->student->name }}<div class="muted">{{ $invoice->student->email }}</div><span class="badge {{ $invoice->student->role==='admin'?'yellow':'' }}">{{ $invoice->student->role==='admin'?'Admin':'Siswa' }}</span></td>
        <td>{{ \Carbon\Carbon::createFromFormat('Y-m',$invoice->period)->translatedFormat('F Y') }}</td>
        <td>Rp {{ number_format($invoice->amount,0,',','.') }}</td>
        <td>@if($payment?->status==='verified')<span class="badge">Sudah membayar</span><div class="muted">{{ $payment->paid_at?->translatedFormat('d M Y') }}</div>@else<span class="badge red">Belum membayar</span>@endif</td>
        <td>@if($payment?->status==='verified')<span class="muted">Sudah dicatat bendahara</span>@else<form method="POST" action="{{ route('payments.record',$invoice) }}" onsubmit="return confirm('Konfirmasi kas sudah diterima dari {{ $invoice->student->name }}?')">@csrf<button class="button green small">Catat pembayaran</button></form>@endif</td>
    </tr>
@empty
    <tr><td colspan="5"><div class="empty"><b>Belum ada tagihan</b>Buat tagihan periode untuk mulai memantau pembayaran anggota.</div></td></tr>
@endforelse
</tbody></table></div>@if(method_exists($invoices,'links'))<div class="pagination">{{ $invoices->links() }}</div>@endif</article></section>
<section class="section"><div class="section-head"><h2>Ringkasan pembayaran anggota</h2></div><article class="panel"><div class="activity-list">@forelse($members as $member)<div class="activity-row"><div class="avatar">{{ strtoupper(substr($member->name,0,1)) }}</div><div class="activity-info"><strong>{{ $member->name }}</strong><small>{{ $member->email }}</small></div><span class="badge {{ $member->role==='admin'?'yellow':'' }}">{{ $member->role==='admin'?'Admin':'Siswa' }}</span><span class="badge">{{ $member->paid_dues_count }} kali membayar</span><span class="badge {{ $member->unpaid_dues_count ? 'red' : '' }}">{{ $member->unpaid_dues_count }} tagihan belum lunas</span></div>@empty<div class="empty">Belum ada anggota terdaftar.</div>@endforelse</div></article></section>
@endsection
