@extends('layouts.app')
@section('title', 'Laba/Rugi - Pengawas')
@section('page_title', 'Laporan Laba/Rugi (Pengawas)')

@section('content')
<div class="page-header">
  <div class="page-header-text">
    <h1 class="page-title">Laporan Laba / Rugi</h1>
    <p class="page-subtitle">Periode tahun {{ $year }}</p>
  </div>
  <div class="flex gap-2">
    <form class="flex gap-2">
      <select name="year" class="form-control" style="max-width:100px;">
        @for($y = now()->year; $y >= 2020; $y--)
          <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
        @endfor
      </select>
      <button class="btn btn-secondary">Tampilkan</button>
    </form>
    <a href="{{ route('pengawas.audit-finance.laba-rugi.export', request()->all()) }}" class="btn btn-primary">Export PDF</a>
    <a href="{{ route('pengawas.audit-finance.index') }}" class="btn btn-secondary">← Kembali</a>
  </div>
</div>

<div class="grid grid-2">
  <div style="display:flex;flex-direction:column;gap:20px;height:100%;">
    <div class="card" style="flex:1;display:flex;flex-direction:column;">
      <div class="card-header">
      <div style="display: flex; align-items: center; gap: 12px;">
        <div class="stat-card-icon green" style="width: 36px; height: 36px; border-radius: 10px;">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline><polyline points="17 6 23 6 23 12"></polyline></svg>
        </div>
        <h3 style="margin: 0;">Pendapatan</h3>
      </div><span class="money font-bold text-success">Rp {{ number_format($totalIncome, 0, ',', '.') }}</span></div>
      <div class="table-wrapper" style="border:none;flex:1;"><table><thead><tr><th>Kode</th><th>Nama Akun</th><th>Jumlah</th></tr></thead>
        <tbody>@forelse($incomeAccounts as $acc)<tr>
          <td style="font-family:monospace;font-size:12px;color:var(--success);">{{ $acc->code }}</td>
          <td>{{ $acc->name }}</td>
          <td class="money text-success font-bold">Rp {{ number_format($acc->balance, 0, ',', '.') }}</td>
        </tr>@empty<tr><td colspan="3" class="text-muted text-center" style="padding:16px;">Tidak ada</td></tr>@endforelse</tbody>
      </table></div>
    </div>
    <div class="card" style="background:var(--gray-50);margin-top:auto;">
      <div class="card-body">
        <div style="display:flex;justify-content:space-between;align-items:center;">
          <span style="font-size:15px;font-weight:700;color:var(--gray-700);">Total Pendapatan</span>
          <span class="money" style="font-size:1.25rem;font-weight:900;color:var(--success);">Rp {{ number_format($totalIncome, 0, ',', '.') }}</span>
        </div>
      </div>
    </div>
  </div>
  <div style="display:flex;flex-direction:column;gap:20px;height:100%;">
    <div class="card" style="flex:1;display:flex;flex-direction:column;">
      <div class="card-header">
      <div style="display: flex; align-items: center; gap: 12px;">
        <div class="stat-card-icon red" style="width: 36px; height: 36px; border-radius: 10px;">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 18 13.5 8.5 8.5 13.5 1 6"></polyline><polyline points="17 18 23 18 23 12"></polyline></svg>
        </div>
        <h3 style="margin: 0;">Beban</h3>
      </div><span class="money font-bold text-danger">Rp {{ number_format($totalExpense, 0, ',', '.') }}</span></div>
      <div class="table-wrapper" style="border:none;flex:1;"><table><thead><tr><th>Kode</th><th>Nama Akun</th><th>Jumlah</th></tr></thead>
        <tbody>@forelse($expenseAccounts as $acc)<tr>
          <td style="font-family:monospace;font-size:12px;color:var(--danger);">{{ $acc->code }}</td>
          <td>{{ $acc->name }}</td>
          <td class="money text-danger font-bold">Rp {{ number_format($acc->balance, 0, ',', '.') }}</td>
        </tr>@empty<tr><td colspan="3" class="text-muted text-center" style="padding:16px;">Tidak ada</td></tr>@endforelse</tbody>
      </table></div>
    </div>
    <div class="card" style="background:var(--gray-50);margin-top:auto;">
      <div class="card-body">
        <div style="display:flex;justify-content:space-between;align-items:center;">
          <span style="font-size:15px;font-weight:700;color:var(--gray-700);">Total Beban</span>
          <span class="money" style="font-size:1.25rem;font-weight:900;color:var(--danger);">Rp {{ number_format($totalExpense, 0, ',', '.') }}</span>
        </div>
      </div>
    </div>
  </div>
</div>
@php $netProfit = $totalIncome - $totalExpense; @endphp
<div class="card" style="margin-top:20px;background:linear-gradient(135deg,{{ $netProfit >= 0 ? 'var(--success),#34d399' : 'var(--danger),#f87171' }});">
  <div class="card-body" style="display:flex;justify-content:space-between;align-items:center;">
    <div style="font-size:14px;font-weight:700;color:rgba(255,255,255,.9);">{{ $netProfit >= 0 ? 'Surplus / Laba Bersih' : 'Defisit / Rugi Bersih' }} — Tahun {{ $year }}</div>
    <div class="money" style="font-size:2rem;font-weight:900;color:white;">Rp {{ number_format(abs($netProfit), 0, ',', '.') }}</div>
  </div>
</div>
@endsection
