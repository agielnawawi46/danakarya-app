@extends('layouts.app')
@section('title', 'Bonus SHU Saya')
@section('page_title', 'Bonus SHU')

@section('content')
<div class="page-header">
  <div class="page-header-text">
    <h1 class="page-title">Bonus SHU Saya</h1>
    <p class="page-subtitle">Riwayat Sisa Hasil Usaha yang Anda terima setiap tahun</p>
  </div>
</div>

{{-- Total SHU Card --}}
<div class="card" style="background:linear-gradient(135deg, var(--brand-600), var(--brand-800)); border:none; position:relative; overflow:hidden; margin-bottom:24px;">
  <div style="position:absolute; right:-20px; top:-40px; width:150px; height:150px; background:rgba(255,255,255,0.05); border-radius:50%;"></div>
  <div style="position:absolute; right:40px; bottom:-30px; width:100px; height:100px; background:rgba(255,255,255,0.05); border-radius:50%;"></div>
  
  <div class="card-body" style="padding:28px 24px; position:relative; z-index:1; display:flex; align-items:center; justify-content:space-between;">
    <div>
      <div style="font-size:12px; font-weight:700; color:rgba(255,255,255,0.7); text-transform:uppercase; letter-spacing:0.1em; margin-bottom:8px;">Total SHU Diterima</div>
      <div class="money" style="font-size:2.5rem; font-weight:900; color:white; line-height:1; letter-spacing:-0.02em;">Rp {{ number_format($totalShu, 0, ',', '.') }}</div>
      <div style="font-size:12px; color:rgba(255,255,255,0.8); margin-top:12px; display:flex; align-items:center; gap:6px;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20"></path><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
        Dari {{ $details->count() }} periode distribusi
      </div>
    </div>
    <div style="width:64px;height:64px;background:rgba(255,255,255,0.15);border-radius:16px;display:flex;align-items:center;justify-content:center;backdrop-filter:blur(4px);">
      <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><circle cx="12" cy="8" r="7"></circle><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline></svg>
    </div>
  </div>
</div>

@if($details->count())
<div class="card" style="margin-bottom:24px;">
  <div class="card-header">
    <div style="display: flex; align-items: center; gap: 12px;">
      <div class="stat-card-icon blue" style="width: 36px; height: 36px; border-radius: 10px;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>
      </div>
      <h3 style="margin: 0;">Riwayat Distribusi SHU</h3>
    </div>
  </div>
  <div class="table-wrapper" style="border:none;">
    <table>
      <thead>
        <tr>
          <th>Tahun</th>
          <th>Jasa Modal</th>
          <th>Jasa Pinjaman</th>
          <th>Total SHU</th>
          <th>Dikreditkan</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        @foreach($details as $detail)
        <tr>
          <td class="font-bold" style="font-size:16px;">{{ $detail->distribution?->year ?? '—' }}</td>
          <td class="money">Rp {{ number_format($detail->jasa_modal, 0, ',', '.') }}</td>
          <td class="money">Rp {{ number_format($detail->jasa_pinjaman, 0, ',', '.') }}</td>
          <td class="money font-bold" style="color:var(--brand-700);">Rp {{ number_format($detail->total_shu, 0, ',', '.') }}</td>
          <td style="font-size:12px;color:var(--gray-400);">
            @if($detail->deposited_at) {{ \Carbon\Carbon::parse($detail->deposited_at)->format('d M Y') }} @else —  @endif
          </td>
          <td>
            @if($detail->deposited_at)
              <span class="badge badge-success" style="padding:6px 10px;">✅ Sukarela</span>
            @else
              <span class="badge badge-warning" style="padding:6px 10px;">⏳ Menunggu</span>
            @endif
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

<div class="card">
  <div class="card-header">
    <div style="display: flex; align-items: center; gap: 12px;">
      <div class="stat-card-icon green" style="width: 36px; height: 36px; border-radius: 10px;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
      </div>
      <h3 style="margin: 0;">Cara Perhitungan SHU</h3>
    </div>
  </div>
  <div class="card-body">
    <div class="grid grid-2" style="gap:20px;">
      <div style="display:flex; gap:16px; align-items:flex-start; padding:16px; background:var(--brand-50); border-radius:12px; border:1px solid var(--brand-100); transition:background 0.2s;" onmouseover="this.style.background='var(--brand-100)'" onmouseout="this.style.background='var(--brand-50)'">
        <div style="width:48px; height:48px; border-radius:12px; background:var(--brand-600); color:white; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
        </div>
        <div>
          <div style="font-weight:800; color:var(--brand-800); margin-bottom:6px; font-size:14px;">Jasa Modal (60% Bagian)</div>
          <div style="font-size:13px; color:var(--brand-700); line-height:1.5;">Proporsional terhadap total simpanan Anda dibanding total simpanan seluruh anggota. Makin besar simpanan, makin besar SHU.</div>
        </div>
      </div>
      <div style="display:flex; gap:16px; align-items:flex-start; padding:16px; background:#ecfdf5; border-radius:12px; border:1px solid #d1fae5; transition:background 0.2s;" onmouseover="this.style.background='#d1fae5'" onmouseout="this.style.background='#ecfdf5'">
        <div style="width:48px; height:48px; border-radius:12px; background:#10b981; color:white; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
        </div>
        <div>
          <div style="font-weight:800; color:#064e3b; margin-bottom:6px; font-size:14px;">Jasa Pinjaman (40% Bagian)</div>
          <div style="font-size:13px; color:#065f46; line-height:1.5;">Proporsional terhadap bunga pinjaman yang Anda bayar. Partisipasi Anda dalam meminjam juga dikembalikan dalam bentuk SHU.</div>
        </div>
      </div>
    </div>
  </div>
</div>
@else
<div class="empty-state">
  <div class="empty-state-icon" style="background:#fefce8;color:#eab308;width:64px;height:64px;display:flex;align-items:center;justify-content:center;border-radius:50%;margin:0 auto 16px;">
    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="7"></circle><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline></svg>
  </div>
  <div class="empty-state-text">Belum ada distribusi SHU</div>
  <p class="text-muted" style="margin-top:8px;font-size:13px;">SHU akan dibagikan ke simpanan sukarela Anda setelah penutupan buku tahunan koperasi.</p>
</div>
@endif
@endsection
