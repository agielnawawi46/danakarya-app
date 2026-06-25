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

{{-- Date Filter --}}
<form class="flex gap-2 items-center" style="margin-bottom:20px;">
  <input type="date" name="from" class="form-control" style="max-width:160px;" value="{{ $from }}">
  <span style="color:var(--gray-400);">s/d</span>
  <input type="date" name="to" class="form-control" style="max-width:160px;" value="{{ $to }}">
  <button class="btn btn-secondary">Tampilkan</button>
</form>

{{-- Summary --}}
<div class="grid grid-3" style="margin-bottom:20px;">
  <div class="stat-card">
    <div class="stat-card-icon green"></div>
    <div class="stat-card-info">
      <div class="stat-card-label">Total Kredit (Masuk)</div>
      <div class="stat-card-value money" style="font-size:1.1rem;color:var(--success);">
        Rp {{ number_format($totalCredit, 0, ',', '.') }}
      </div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-card-icon red"></div>
    <div class="stat-card-info">
      <div class="stat-card-label">Total Debit (Keluar)</div>
      <div class="stat-card-value money" style="font-size:1.1rem;color:var(--danger);">
        Rp {{ number_format($totalDebit, 0, ',', '.') }}
      </div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-card-icon {{ $totalCredit - $totalDebit >= 0 ? 'green' : 'red' }}"></div>
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
