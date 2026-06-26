@extends('layouts.app')
@section('title', 'Laporan Keuangan')
@section('page_title', 'Laporan Keuangan')

@section('content')
<div class="page-header">
  <div class="page-header-text">
    <h1 class="page-title">Pusat Laporan Keuangan</h1>
    <p class="page-subtitle">Laporan operasional, keuangan, dan distribusi SHU koperasi</p>
  </div>
</div>

<div class="grid grid-3">
  @foreach([
    ['title'=>'Buku Kas','desc'=>'Ringkasan arus kas masuk & keluar','icon'=>'<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="6" width="20" height="12" rx="2"></rect><circle cx="12" cy="12" r="2"></circle><path d="M6 12h.01M18 12h.01"></path></svg>','url'=>route('pengurus.reports.kas'),'color'=>'green','badge'=>null],
    ['title'=>'Neraca (Balance Sheet)','desc'=>'Laporan posisi keuangan aset & kewajiban','icon'=>'<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect></svg>','url'=>route('pengurus.reports.neraca'),'color'=>'blue','badge'=>null],
    ['title'=>'Laba/Rugi','desc'=>'Laporan pendapatan dan beban tahunan','icon'=>'<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline><polyline points="17 6 23 6 23 12"></polyline></svg>','url'=>route('pengurus.reports.laba-rugi'),'color'=>'indigo','badge'=>null],
    ['title'=>'Rekap Simpanan','desc'=>'Saldo simpanan semua anggota','icon'=>'<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><ellipse cx="12" cy="5" rx="9" ry="3"></ellipse><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path></svg>','url'=>route('pengurus.reports.simpanan'),'color'=>'amber','badge'=>null],
    ['title'=>'Distribusi SHU','desc'=>'Hitung dan bagikan SHU kepada anggota','icon'=>'<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>','url'=>route('pengurus.reports.shu'),'color'=>'violet','badge'=>'Tahunan'],
  ] as $item)
  <a href="{{ $item['url'] }}" style="text-decoration:none;">
    <div class="stat-card" style="flex-direction:column;gap:12px;cursor:pointer;transition:all .2s;">
      <div style="display:flex;align-items:flex-start;justify-content:space-between;width:100%;">
        <div class="stat-card-icon {{ $item['color'] }}" style="width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">{!! $item['icon'] !!}</div>
        @if($item['badge'])
          <span class="badge badge-primary">{{ $item['badge'] }}</span>
        @endif
      </div>
      <div>
        <div style="font-size:15px;font-weight:700;color:var(--gray-900);">{{ $item['title'] }}</div>
        <div style="font-size:13px;color:var(--gray-400);margin-top:4px;">{{ $item['desc'] }}</div>
      </div>
      <div style="font-size:12px;color:var(--brand-600);font-weight:600;">Lihat Laporan →</div>
    </div>
  </a>
  @endforeach
</div>
@endsection
