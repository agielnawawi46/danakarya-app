@extends('layouts.app')
@section('title', 'Dashboard Pengawas')
@section('page_title', 'Dashboard Pengawasan')

@section('content')
<div class="page-header">
  <div class="page-header-text">
    <h1 class="page-title">Pengawasan Koperasi</h1>
    <p class="page-subtitle">Pantauan kesehatan keuangan dan kepatuhan koperasi</p>
  </div>
</div>

<div class="grid grid-4" style="margin-bottom:24px;">
  <div class="stat-card">
    <div class="stat-card-icon green"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg></div>
    <div class="stat-card-info">
      <div class="stat-card-label">Total Simpanan</div>
      <div class="stat-card-value money" style="font-size:1rem;">Rp {{ number_format($totalSimpanan, 0, ',', '.') }}</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-card-icon blue"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path></svg></div>
    <div class="stat-card-info">
      <div class="stat-card-label">Portfolio Pinjaman Aktif</div>
      <div class="stat-card-value money" style="font-size:1rem;">Rp {{ number_format($totalLoans, 0, ',', '.') }}</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-card-icon amber"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg></div>
    <div class="stat-card-info">
      <div class="stat-card-label">Pendapatan Bunga YTD</div>
      <div class="stat-card-value money" style="font-size:1rem;">Rp {{ number_format($totalBunga, 0, ',', '.') }}</div>
    </div>
  </div>
  <div class="stat-card" style="{{ $nplRatio > 5 ? 'border-color:var(--danger);' : '' }}">
    <div class="stat-card-icon {{ $nplRatio > 5 ? 'red' : 'green' }}"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path></svg></div>
    <div class="stat-card-info">
      <div class="stat-card-label">Rasio NPL</div>
      <div class="stat-card-value" style="color:{{ $nplRatio > 5 ? 'var(--danger)' : 'var(--success)' }};">{{ number_format($nplRatio, 2) }}%</div>
      <div class="stat-card-change">{{ $nplRatio > 5 ? '⚠️ Di atas ambang 5%' : '✅ Normal (< 5%)' }}</div>
    </div>
  </div>
</div>

{{-- Monthly Income Trend --}}
<div class="card">
  <div class="card-header">
    <div style="display: flex; align-items: center; gap: 12px;">
      <div class="stat-card-icon green" style="width: 36px; height: 36px; border-radius: 10px;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline><polyline points="17 6 23 6 23 12"></polyline></svg>
      </div>
      <h3 style="margin: 0;">Tren Pendapatan 6 Bulan Terakhir</h3>
    </div>
  </div>
  <div class="card-body">
    @if($totalLoans > 0 && $overdueAmount > 0)
    <div class="alert alert-warning" style="margin-bottom:16px;">
      ⚠️ Terdapat angsuran macet senilai <strong>Rp {{ number_format($overdueAmount,0,',','.') }}</strong> dari portfolio pinjaman.
    </div>
    @endif

    @php $maxVal = max(array_column($months, 'income')) ?: 1; @endphp
    <div style="display:flex;align-items:flex-end;gap:12px;height:180px;padding:0 8px;">
      @foreach($months as $m)
      @php $h = $maxVal > 0 ? ($m['income'] / $maxVal * 100) : 0; @endphp
      <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:6px;">
        <div style="font-size:11px;color:var(--gray-400);font-weight:600;">
          Rp {{ number_format($m['income']/1000000,1) }}jt
        </div>
        <div style="width:100%;border-radius:6px 6px 0 0;min-height:4px;
          background:linear-gradient(180deg,var(--brand-500),var(--accent-violet));
          height:{{ $h }}%;
          transition:height .5s ease;">
        </div>
        <div style="font-size:11px;color:var(--gray-500);">{{ $m['label'] }}</div>
      </div>
      @endforeach
    </div>
  </div>
</div>

<div style="margin-top:20px;display:grid;grid-template-columns:1fr 1fr;gap:12px;">
  <a href="{{ route('pengawas.audit-finance.index') }}" class="btn btn-secondary" style="justify-content:center;padding:14px;display:flex;align-items:center;">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:8px;"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
    Audit Keuangan
  </a>
  <a href="{{ route('pengawas.audit-trail.index') }}" class="btn btn-secondary" style="justify-content:center;padding:14px;display:flex;align-items:center;">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:8px;"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
    Log Audit Trail
  </a>
</div>
@endsection
