@extends('layouts.app')
@section('title', 'Fasilitas Kredit - Riwayat')
@section('page_title', 'Fasilitas Kredit')

@section('content')
<div class="page-header">
  <div class="page-header-text">
    <h1 class="page-title">Fasilitas Kredit</h1>
    <p class="page-subtitle">Riwayat dan status pengajuan pinjaman Anda</p>
  </div>
  <a href="{{ route('member.loans.apply') }}" class="btn btn-primary">+ Ajukan Pinjaman</a>
</div>

@forelse($loans as $loan)
<div class="card" style="margin-bottom:16px;">
  <div class="card-header">
    <div>
      <div class="font-bold" style="font-size:15px;">Pinjaman Rp {{ number_format($loan->amount,0,',','.') }}</div>
      <div class="text-sm text-muted">{{ $loan->tenor_months }} bulan — {{ ucfirst($loan->interest_method) }} {{ $loan->interest_rate }}%/bln</div>
    </div>
    <div class="flex gap-2 items-center">
      {!! $loan->getStatusBadge() !!}
      @if($loan->status === 'active')
        <a href="{{ route('member.loans.card', $loan) }}" class="btn btn-secondary btn-sm">Kartu Piutang</a>
      @endif
    </div>
  </div>
  <div class="card-body" style="padding:16px 24px;">
    <div class="grid grid-4">
      @php
        $paid    = $loan->schedules->where('status','paid')->count();
        $pending = $loan->schedules->where('status','pending')->count();
        $totalPaid = $loan->schedules->where('status','paid')->sum('total_amount');
      @endphp
      @foreach([
        ['Tujuan',$loan->purpose],
        ['Tanggal Pengajuan',$loan->created_at->format('d F Y')],
        ['Angsuran Lunas',$paid.' / '.$loan->tenor_months.' bulan'],
        ['Total Sudah Bayar','Rp '.number_format($totalPaid,0,',','.')],
      ] as [$label,$val])
      <div>
        <div style="font-size:11px;color:var(--gray-400);font-weight:600;text-transform:uppercase;letter-spacing:.06em;">{{ $label }}</div>
        <div style="font-size:14px;font-weight:700;color:var(--gray-800);margin-top:2px;">{{ $val }}</div>
      </div>
      @endforeach
    </div>

    @if($loan->status === 'rejected' && $loan->rejection_reason)
    <div class="alert alert-danger" style="margin-top:12px;margin-bottom:0;">
      <strong>Alasan Penolakan:</strong> {{ $loan->rejection_reason }}
    </div>
    @endif
  </div>
</div>
@empty
<div class="empty-state">
  <div class="empty-state-icon" style="background:var(--brand-50);color:var(--brand-600);width:64px;height:64px;display:flex;align-items:center;justify-content:center;border-radius:50%;margin:0 auto 16px;">
    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg>
  </div>
  <div class="empty-state-text">Belum ada riwayat pinjaman</div>
  <a href="{{ route('member.loans.apply') }}" class="btn btn-primary" style="margin-top:16px;">Ajukan Pinjaman Pertama</a>
</div>
@endforelse
@endsection
