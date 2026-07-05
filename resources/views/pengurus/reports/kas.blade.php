@extends('layouts.app')
@section('title', 'Buku Kas')
@section('page_title', 'Buku Kas')

@section('content')
<div class="page-header">
  <div class="page-header-text">
    <h1 class="page-title">Buku Kas</h1>
    <p class="page-subtitle">Arus kas {{ \Carbon\Carbon::parse($from)->format('d M Y') }} — {{ \Carbon\Carbon::parse($to)->format('d M Y') }}</p>
  </div>
  <a href="{{ route('pengurus.reports.index') }}" class="btn btn-secondary">← Kembali</a>
</div>

{{-- Month Navigation --}}
<div class="flex justify-between items-center" style="margin-bottom:20px;">
  <form method="GET" class="flex gap-2 items-center">
    <span style="font-size:15px; font-weight:600; color:var(--gray-800);">Periode:</span>
    <select name="month" class="form-control" style="width:auto; padding:4px 32px 4px 12px; font-size:14px; min-height: 34px;" onchange="this.form.submit()">
      @foreach([
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
      ] as $m => $name)
        <option value="{{ $m }}" {{ $currentPeriod->month == $m ? 'selected' : '' }}>{{ $name }}</option>
      @endforeach
    </select>
    <select name="year" class="form-control" style="width:auto; padding:4px 32px 4px 12px; font-size:14px; min-height: 34px;" onchange="this.form.submit()">
      @for($y = date('Y'); $y >= (Auth::user()->organization->created_at ? Auth::user()->organization->created_at->year : date('Y')); $y--)
        <option value="{{ $y }}" {{ $currentPeriod->year == $y ? 'selected' : '' }}>{{ $y }}</option>
      @endfor
    </select>
    <noscript><button type="submit" class="btn btn-secondary btn-sm">Filter</button></noscript>
  </form>
  <div class="flex gap-2">
    <a href="{{ request()->fullUrlWithQuery(['month' => $prevMonth->month, 'year' => $prevMonth->year]) }}" class="btn btn-secondary">
      &laquo; Bulan Sebelumnya
    </a>
    @if(!($currentPeriod->month == now()->month && $currentPeriod->year == now()->year))
    <a href="{{ request()->fullUrlWithQuery(['month' => $nextMonth->month, 'year' => $nextMonth->year]) }}" class="btn btn-secondary">
      Bulan Selanjutnya &raquo;
    </a>
    @endif
  </div>
</div>

{{-- Summary --}}
<div class="grid grid-3" style="margin-bottom:20px;">
  <div class="stat-card">
    <div class="stat-card-icon green">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline><polyline points="17 6 23 6 23 12"></polyline></svg>
    </div>
    <div class="stat-card-info">
      <div class="stat-card-label">Total Kredit (Masuk)</div>
      <div class="stat-card-value money" style="font-size:1.1rem;color:var(--success);">
        Rp {{ number_format($totalCredit, 0, ',', '.') }}
      </div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-card-icon red">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 18 13.5 8.5 8.5 13.5 1 6"></polyline><polyline points="17 18 23 18 23 12"></polyline></svg>
    </div>
    <div class="stat-card-info">
      <div class="stat-card-label">Total Debit (Keluar)</div>
      <div class="stat-card-value money" style="font-size:1.1rem;color:var(--danger);">
        Rp {{ number_format($totalDebit, 0, ',', '.') }}
      </div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-card-icon {{ $totalCredit - $totalDebit >= 0 ? 'green' : 'red' }}">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="6" width="20" height="12" rx="2" ry="2"></rect><circle cx="12" cy="12" r="2"></circle><path d="M6 12h.01M18 12h.01"></path></svg>
    </div>
    <div class="stat-card-info">
      <div class="stat-card-label">Selisih (Net)</div>
      <div class="stat-card-value money" style="font-size:1.1rem;color:{{ $totalCredit - $totalDebit >= 0 ? 'var(--success)' : 'var(--danger)' }};">
        Rp {{ number_format($totalCredit - $totalDebit, 0, ',', '.') }}
      </div>
    </div>
  </div>
</div>

<div class="card">
  <div class="table-wrapper" style="border:none;">
    <table>
      <thead>
        <tr><th>Tanggal</th><th>Keterangan</th><th>Sumber</th><th>Debit</th><th>Kredit</th><th>Detail</th></tr>
      </thead>
      <tbody>
        @forelse($journals as $j)
        <tr>
          <td style="font-size:12px;color:var(--gray-400);">{{ $j->date->format('d/m/Y') }}</td>
          <td class="font-semibold">{{ $j->description }}</td>
          <td><span class="badge badge-secondary">{{ $j->source_type }}</span></td>
          <td class="money text-danger">
            @php $d = $j->lines->sum('debit'); @endphp
            {{ $d > 0 ? 'Rp '.number_format($d,0,',','.') : '—' }}
          </td>
          <td class="money text-success">
            @php $c = $j->lines->sum('credit'); @endphp
            {{ $c > 0 ? 'Rp '.number_format($c,0,',','.') : '—' }}
          </td>
          <td style="font-size:12px;color:var(--gray-400);">{{ $j->creator?->name }}</td>
        </tr>
        @empty
        <tr><td colspan="6" class="text-center text-muted" style="padding:40px;">Tidak ada transaksi dalam periode ini</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
