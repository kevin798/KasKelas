@extends('layouts.app')
@section('title', 'Masukan anggota')
@section('content')
<div class="welcome">
    <p>Baca pertanyaan dan laporan yang dikirim anggota kelas.</p>
</div>

<section class="section">
    <div class="section-head">
        <h2>Daftar masukan</h2>
        <span class="badge">{{ $inquiries->total() }} pesan</span>
    </div>
    <article class="panel">
        <div class="activity-list">
            @forelse($inquiries as $inquiry)
                <div class="activity-row inquiry-row">
                    <div class="avatar">{{ strtoupper(substr($inquiry->user?->name ?? '?', 0, 1)) }}</div>
                    <div class="activity-info">
                        <strong>{{ $inquiry->user?->name ?? 'Akun dihapus' }}</strong>
                        @if($inquiry->user)
                            <small>{{ $inquiry->user->email }}</small>
                        @endif
                        <div class="panel-sub inquiry-message">{{ $inquiry->message }}</div>
                        @if($inquiry->admin_reply)
                            <div class="panel-sub inquiry-message"><strong>Balasan admin:</strong> {{ $inquiry->admin_reply }}</div>
                        @endif
                    </div>
                    <span class="badge {{ $inquiry->status === 'open' ? 'red' : '' }}">
                        {{ $inquiry->status === 'open' ? 'Belum ditindaklanjuti' : 'Terjawab' }}
                    </span>
                    <small class="muted inquiry-date">{{ $inquiry->created_at->translatedFormat('d M Y, H:i') }}</small>
                </div>
            @empty
                <div class="empty">
                    <b>Belum ada masukan</b>
                    Pesan anggota kelas akan tampil di sini.
                </div>
            @endforelse
        </div>
        <div class="pagination">{{ $inquiries->links() }}</div>
    </article>
</section>
@endsection
