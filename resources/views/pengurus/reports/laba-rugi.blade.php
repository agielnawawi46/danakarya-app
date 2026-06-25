@extends('layouts.app')
@section('title', 'Laporan Laba/Rugi')
@section('page_title', 'Laporan Laba/Rugi')

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
    <a href="{{ route('pengurus.reports.index') }}" class="btn btn-secondary">← Kembali</a>
  </div>
</div>

<div class="grid grid-2">
  {{-- PENDAPATAN --}}
  <div class="card">
    <div class="card-header">
      <h3>📈 Pendapatan</h3>
      <span class="money font-bold text-success">Rp {{ number_format($totalIncome, 0, ',', '.') }}</span>
    </div>
    <div class="table-wrapper" style="border:none;">
      <table>
        <thead><tr><th>Kode</th><th>Nama Akun</th><th>Jumlah</th></tr></thead>
        <tbody>
          @forelse($incomeAccounts as $acc)
          <tr>
            <td style="font-family:monospace;font-size:12px;color:var(--success);">{{ $acc->code }}</td>
            <td>{{ $acc->name }}</td>
            <td class="money text-success font-bold">Rp {{ number_format($acc->balance, 0, ',', '.') }}</td>
          </tr>
          @empty
          <tr><td colspan="3" class="text-muted text-center" style="padding:16px;">Tidak ada data</td></tr>
          @endforelse
        </tbody>
        <tfoot>
          <tr style="background:#ecfdf5;"><td colspan="2" class="font-bold">Total Pendapatan</td>
            <td class="money font-bold text-success">Rp {{ number_format($totalIncome, 0, ',', '.') }}</td></tr>
        </tfoot>
      </table>
    </div>
  </div>

  {{-- BEBAN --}}
  <div class="card">
    <div class="card-header">
      <h3>📉 Beban</h3>
      <span class="money font-bold text-danger">Rp {{ number_format($totalExpense, 0, ',', '.') }}</span>
    </div>
    <div class="table-wrapper" style="border:none;">
      <table>
        <thead><tr><th>Kode</th><th>Nama Akun</th><th>Jumlah</th></tr></thead>
        <tbody>
          @forelse($expenseAccounts as $acc)
          <tr>
            <td style="font-family:monospace;font-size:12px;color:var(--danger);">{{ $acc->code }}</td>
            <td>{{ $acc->name }}</td>
            <td class="money text-danger font-bold">Rp {{ number_format($acc->balance, 0, ',', '.') }}</td>
          </tr>
          @empty
          <tr><td colspan="3" class="text-muted text-center" style="padding:16px;">Tidak ada data</td></tr>
          @endforelse
        </tbody>
        <tfoot>
          <tr style="background:#fef2f2;"><td colspan="2" class="font-bold">Total Beban</td>
            <td class="money font-bold text-danger">Rp {{ number_format($totalExpense, 0, ',', '.') }}</td></tr>
        </tfoot>
      </table>
    </div>
  </div>
</div>

{{-- Net Profit/Loss --}}
<div class="card" style="margin-top:20px;background:linear-gradient(135deg,{{ $netProfit >= 0 ? 'var(--success)' : 'var(--danger)' }},{{ $netProfit >= 0 ? '#34d399' : '#f87171' }});">
  <div class="card-body" style="display:flex;justify-content:space-between;align-items:center;">
    <div>
      <div style="font-size:13px;font-weight:700;color:rgba(255,255,255,.8);text-transform:uppercase;letter-spacing:.08em;">
        {{ $netProfit >= 0 ? 'Surplus / Laba Bersih' : 'Defisit / Rugi Bersih' }}
      </div>
      <div style="font-size:12px;color:rgba(255,255,255,.6);margin-top:2px;">Tahun {{ $year }}</div>
    </div>
    <div class="money" style="font-size:2rem;font-weight:900;color:white;letter-spacing:-.03em;">
      Rp {{ number_format(abs($netProfit), 0, ',', '.') }}
    </div>
  </div>
</div>
@endsection
