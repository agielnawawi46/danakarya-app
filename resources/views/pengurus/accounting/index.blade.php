@extends('layouts.app')
@section('title', 'Akuntansi')
@section('page_title', 'Akuntansi')

@section('content')
<div class="page-header">
  <div class="page-header-text">
    <h1 class="page-title">Modul Akuntansi</h1>
    <p class="page-subtitle">Kelola bagan akun dan buku jurnal double-entry koperasi</p>
  </div>
</div>

<div class="grid grid-3">
  @foreach([
    ['title'=>'Bagan Akun (COA)','desc'=>'Lihat chart of accounts koperasi','icon'=>'📊','url'=>route('pengurus.accounting.coa'),'color'=>'indigo'],
    ['title'=>'Buku Jurnal','desc'=>'Daftar semua jurnal transaksi','icon'=>'📒','url'=>route('pengurus.accounting.journals'),'color'=>'blue'],
    ['title'=>'Buat Jurnal Manual','desc'=>'Input jurnal akuntansi manualxs','icon'=>'✏️','url'=>route('pengurus.accounting.journals.create'),'color'=>'green'],
  ] as $item)
  <a href="{{ $item['url'] }}" style="text-decoration:none;">
    <div class="stat-card" style="flex-direction:column;gap:12px;cursor:pointer;">
      <div class="stat-card-icon {{ $item['color'] }}" style="font-size:24px;">{{ $item['icon'] }}</div>
      <div>
        <div style="font-size:15px;font-weight:700;color:var(--gray-900);">{{ $item['title'] }}</div>
        <div style="font-size:13px;color:var(--gray-400);margin-top:4px;">{{ $item['desc'] }}</div>
      </div>
    </div>
  </a>
  @endforeach
</div>
@endsection
