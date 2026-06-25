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
    ['title'=>'Buku Kas','desc'=>'Ringkasan arus kas masuk & keluar','icon'=>'💵','url'=>route('pengurus.reports.kas'),'color'=>'green','badge'=>null],
    ['title'=>'Neraca (Balance Sheet)','desc'=>'Laporan posisi keuangan aset & kewajiban','icon'=>'⚖️','url'=>route('pengurus.reports.neraca'),'color'=>'blue','badge'=>null],
    ['title'=>'Laba/Rugi','desc'=>'Laporan pendapatan dan beban tahunan','icon'=>'📈','url'=>route('pengurus.reports.laba-rugi'),'color'=>'indigo','badge'=>null],
    ['title'=>'Rekap Simpanan','desc'=>'Saldo simpanan semua anggota','icon'=>'💰','url'=>route('pengurus.reports.simpanan'),'color'=>'amber','badge'=>null],
    ['title'=>'Distribusi SHU','desc'=>'Hitung dan bagikan SHU kepada anggota','icon'=>'🌟','url'=>route('pengurus.reports.shu'),'color'=>'violet','badge'=>'Tahunan'],
  ] as $item)
  <a href="{{ $item['url'] }}" style="text-decoration:none;">
    <div class="stat-card" style="flex-direction:column;gap:12px;cursor:pointer;transition:all .2s;">
      <div style="display:flex;align-items:flex-start;justify-content:space-between;width:100%;">
        <div class="stat-card-icon {{ $item['color'] }}" style="font-size:24px;">{{ $item['icon'] }}</div>
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
