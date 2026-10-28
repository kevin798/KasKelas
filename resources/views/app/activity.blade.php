@extends('layouts.app')
@section('title','Riwayat aktivitas')
@section('content')
<div class="welcome">
    <p>Catatan perubahan dan tindakan pada data kelas.</p>
</div>
<article class="panel">
    <div class="activity-list">@forelse($logs as $log)<div class="activity-row">
            <div class="activity-symbol">↻</div>
            <div class="activity-info"><strong>{{ str_replace(['.','_'],[' · ',' '],ucfirst($log->action)) }}</strong><small>{{ $log->user?->name ?? 'Akun dihapus' }} · {{ $log->created_at->translatedFormat('d M Y, H:i') }}</small>@if($log->details)<small>{{ collect($log->details)->map(fn($value,$key)=>is_scalar($value)?ucfirst($key).': '.$value:null)->filter()->join(' · ') }}</small>@endif</div>
        </div>@empty<div class="empty"><b>Belum ada aktivitas</b>Perubahan transaksi dan pembayaran akan tersimpan di sini.</div>@endforelse</div>
    <div class="pagination">{{ $logs->links() }}</div>
</article>
@endsection