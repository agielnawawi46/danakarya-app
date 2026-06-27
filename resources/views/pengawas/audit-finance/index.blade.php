@extends('layouts.app')
@section('title', 'Audit Keuangan')
@section('page_title', 'Audit Keuangan')

@section('content')
<div class="page-header">
  <div class="page-header-text">
    <h1 class="page-title">Audit Keuangan</h1>
    <p class="page-subtitle">Lihat laporan keuangan — akses hanya baca</p>
  </div>
</div>

<div class="grid grid-3" style="margin-bottom:0;">
  @foreach([
    ['title'=>'Buku Besar','desc'=>'Rekening akuntansi & saldo','icon'=>'<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path></svg>','url'=>route('pengawas.audit-finance.ledger'),'color'=>'indigo'],
    ['title'=>'Neraca','desc'=>'Posisi keuangan aset & kewajiban','icon'=>'<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="9" y1="21" x2="9" y2="9"></line></svg>','url'=>route('pengawas.audit-finance.neraca'),'color'=>'blue'],
    ['title'=>'Laba / Rugi','desc'=>'Pendapatan dan beban tahunan','icon'=>'<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline><polyline points="17 6 23 6 23 12"></polyline></svg>','url'=>route('pengawas.audit-finance.laba-rugi'),'color'=>'green'],
    ['title'=>'Arus Kas','desc'=>'Pemasukan dan pengeluaran','icon'=>'<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="17 1 21 5 17 9"></polyline><path d="M3 11V9a4 4 0 0 1 4-4h14"></path><polyline points="7 23 3 19 7 15"></polyline><path d="M21 13v2a4 4 0 0 1-4 4H3"></path></svg>','url'=>route('pengawas.audit-finance.kas'),'color'=>'cyan'],
    ['title'=>'Rekap Simpanan','desc'=>'Simpanan pokok, wajib, & sukarela','icon'=>'<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><ellipse cx="12" cy="5" rx="9" ry="3"></ellipse><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path></svg>','url'=>route('pengawas.audit-finance.simpanan'),'color'=>'violet'],
    ['title'=>'Distribusi SHU','desc'=>'Laporan kalkulasi SHU tahunan','icon'=>'<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21.21 15.89A10 10 0 1 1 8 2.83"></path><path d="M22 12A10 10 0 0 0 12 2v10z"></path></svg>','url'=>route('pengawas.audit-finance.shu'),'color'=>'orange'],
  ] as $item)
  <a href="{{ $item['url'] }}" style="text-decoration:none;">
    <div class="stat-card" style="flex-direction:column;gap:12px;cursor:pointer;">
      <div class="stat-card-icon {{ $item['color'] }}">{!! $item['icon'] !!}</div>
      <div>
        <div style="font-size:15px;font-weight:700;color:var(--gray-900);">{{ $item['title'] }}</div>
        <div style="font-size:13px;color:var(--gray-400);margin-top:4px;">{{ $item['desc'] }}</div>
      </div>
      <div style="font-size:12px;color:var(--brand-600);font-weight:600;">Buka Laporan →</div>
    </div>
  </a>
  @endforeach
</div>
@endsection
