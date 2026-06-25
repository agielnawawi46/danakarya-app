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
    ['title'=>'Buku Besar','desc'=>'Rekening akuntansi & saldo','icon'=>'📖','url'=>route('pengawas.audit-finance.ledger'),'color'=>'indigo'],
    ['title'=>'Neraca','desc'=>'Posisi keuangan aset & kewajiban','icon'=>'⚖️','url'=>route('pengawas.audit-finance.neraca'),'color'=>'blue'],
    ['title'=>'Laba / Rugi','desc'=>'Pendapatan dan beban tahunan','icon'=>'📈','url'=>route('pengawas.audit-finance.laba-rugi'),'color'=>'green'],
  ] as $item)
  <a href="{{ $item['url'] }}" style="text-decoration:none;">
    <div class="stat-card" style="flex-direction:column;gap:12px;cursor:pointer;">
      <div class="stat-card-icon {{ $item['color'] }}" style="font-size:24px;">{{ $item['icon'] }}</div>
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
