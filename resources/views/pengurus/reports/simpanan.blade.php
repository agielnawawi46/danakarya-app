@extends('layouts.app')
@section('title', 'Rekap Simpanan Anggota')
@section('page_title', 'Rekap Simpanan')

@section('content')
<div class="page-header">
  <div class="page-header-text">
    <h1 class="page-title">Rekap Simpanan Anggota</h1>
    <p class="page-subtitle">Saldo simpanan pokok, wajib, dan sukarela per anggota</p>
  </div>
  <a href="{{ route('pengurus.reports.index') }}" class="btn btn-secondary">← Kembali</a>
</div>

{{-- Totals --}}
<div class="grid grid-4" style="margin-bottom:20px;">
  @foreach([
    ['Simpanan Pokok','pokok','indigo'],
    ['Simpanan Wajib','wajib','blue'],
    ['Simpanan Sukarela','sukarela','green'],
    ['Grand Total','grand','violet'],
  ] as [$label, $key, $color])
  <div class="stat-card">
    <div class="stat-card-icon {{ $color }}"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg></div>
    <div class="stat-card-info">
      <div class="stat-card-label">{{ $label }}</div>
      <div class="stat-card-value money" style="font-size:1rem;">Rp {{ number_format($totals[$key], 0, ',', '.') }}</div>
    </div>
  </div>
  @endforeach
</div>

<div class="card">
  <div class="table-wrapper" style="border:none;">
    <table>
      <thead>
        <tr>
          <th>NIK</th>
          <th>Nama Anggota</th>
          <th>Dept</th>
          <th class="text-right">Pokok</th>
          <th class="text-right">Wajib</th>
          <th class="text-right">Sukarela</th>
          <th class="text-right">Total</th>
        </tr>
      </thead>
      <tbody>
        @forelse($members as $m)
        <tr>
          <td style="font-size:12px;color:var(--gray-400);">{{ $m->employee_id ?? '-' }}</td>
          <td>
            <div class="font-semibold">{{ $m->name }}</div>
          </td>
          <td>{{ $m->department ?? '-' }}</td>
          <td class="money text-right">Rp {{ number_format($m->simpanan_pokok, 0, ',', '.') }}</td>
          <td class="money text-right">Rp {{ number_format($m->simpanan_wajib, 0, ',', '.') }}</td>
          <td class="money text-right">Rp {{ number_format($m->simpanan_sukarela, 0, ',', '.') }}</td>
          <td class="money font-bold text-right" style="color:var(--brand-700);">Rp {{ number_format($m->total, 0, ',', '.') }}</td>
        </tr>
        @empty
        <tr><td colspan="7" class="text-center text-muted" style="padding:40px;">Belum ada data anggota</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<div class="card" style="background:var(--gray-50);margin-top:20px;">
  <div class="card-body">
    <div style="display:flex;justify-content:space-between;align-items:center;">
      <span style="font-size:15px;font-weight:700;color:var(--gray-700);">Total Keseluruhan</span>
      <div style="display:flex;gap:24px;text-align:right;">
        <div>
          <div style="font-size:11px;color:var(--gray-500);text-transform:uppercase;font-weight:700;">Pokok</div>
          <div class="money font-bold" style="color:var(--gray-900);">Rp {{ number_format($totals['pokok'],0,',','.') }}</div>
        </div>
        <div>
          <div style="font-size:11px;color:var(--gray-500);text-transform:uppercase;font-weight:700;">Wajib</div>
          <div class="money font-bold" style="color:var(--gray-900);">Rp {{ number_format($totals['wajib'],0,',','.') }}</div>
        </div>
        <div>
          <div style="font-size:11px;color:var(--gray-500);text-transform:uppercase;font-weight:700;">Sukarela</div>
          <div class="money font-bold" style="color:var(--gray-900);">Rp {{ number_format($totals['sukarela'],0,',','.') }}</div>
        </div>
        <div style="border-left:2px solid var(--gray-200);padding-left:24px;">
          <div style="font-size:11px;color:var(--brand-600);text-transform:uppercase;font-weight:800;">Grand Total</div>
          <div class="money" style="font-size:1.25rem;font-weight:900;color:var(--brand-700);">Rp {{ number_format($totals['grand'],0,',','.') }}</div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
