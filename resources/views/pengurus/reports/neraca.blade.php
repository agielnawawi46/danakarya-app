@extends('layouts.app')
@section('title', 'Neraca Keuangan')
@section('page_title', 'Neraca (Balance Sheet)')

@section('content')
<div class="page-header">
  <div class="page-header-text">
    <h1 class="page-title">Neraca Keuangan</h1>
    <p class="page-subtitle">Posisi keuangan per {{ now()->format('d F Y') }}</p>
  </div>
  <a href="{{ route('pengurus.reports.index') }}" class="btn btn-secondary">← Kembali</a>
</div>

<div class="grid grid-2">
  <div style="display:flex;flex-direction:column;gap:20px;height:100%;">
    {{-- ASET --}}
    <div class="card" style="flex:1;display:flex;flex-direction:column;">
      <div class="card-header">
        <h3>Aset</h3>
        <span class="money font-bold" style="color:var(--brand-600);">Rp {{ number_format($totalAssets, 0, ',', '.') }}</span>
      </div>
      <div class="table-wrapper" style="border:none;flex:1;">
        <table>
          <thead><tr><th>Kode</th><th>Nama Akun</th><th>Saldo</th></tr></thead>
          <tbody>
            @foreach($assets->sortBy('code') as $acc)
            <tr>
              <td style="font-family:monospace;font-size:12px;color:var(--brand-600);">{{ $acc->code }}</td>
              <td>{{ $acc->name }}</td>
              <td class="money font-bold">Rp {{ number_format($acc->getBalance(), 0, ',', '.') }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>

    {{-- TOTAL ASET CARD --}}
    <div class="card" style="background:linear-gradient(135deg,var(--brand-50),var(--brand-100));border-color:var(--brand-200);margin-top:auto;">
      <div class="card-body">
        <div style="display:flex;justify-content:space-between;align-items:center;">
          <span style="font-size:15px;font-weight:700;color:var(--gray-700);">Total Aset</span>
          <span class="money" style="font-size:1.25rem;font-weight:900;color:var(--brand-700);">
            Rp {{ number_format($totalAssets, 0, ',', '.') }}
          </span>
        </div>
        <div style="font-size:11px;color:transparent;user-select:none;margin-top:8px;">.</div> {{-- Spacer to match right card height --}}
      </div>
    </div>
  </div>

  {{-- KEWAJIBAN + EKUITAS --}}
  <div style="display:flex;flex-direction:column;gap:20px;height:100%;">
    <div style="flex:1;display:flex;flex-direction:column;gap:20px;">
    <div class="card">
      <div class="card-header">
        <h3>Kewajiban</h3>
        <span class="money font-bold" style="color:var(--danger);">Rp {{ number_format($totalLiabilities, 0, ',', '.') }}</span>
      </div>
      <div class="table-wrapper" style="border:none;">
        <table>
          <thead><tr><th>Kode</th><th>Nama Akun</th><th>Saldo</th></tr></thead>
          <tbody>
            @forelse($liabilities->sortBy('code') as $acc)
            <tr>
              <td style="font-family:monospace;font-size:12px;color:var(--danger);">{{ $acc->code }}</td>
              <td>{{ $acc->name }}</td>
              <td class="money font-bold">Rp {{ number_format($acc->getBalance(), 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr><td colspan="3" class="text-muted text-center" style="padding:16px;">Tidak ada</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <div class="card">
      <div class="card-header">
        <h3>Ekuitas</h3>
        <span class="money font-bold" style="color:var(--success);">Rp {{ number_format($totalEquities, 0, ',', '.') }}</span>
      </div>
      <div class="table-wrapper" style="border:none;">
        <table>
          <thead><tr><th>Kode</th><th>Nama Akun</th><th>Saldo</th></tr></thead>
          <tbody>
            @forelse($equities->sortBy('code') as $acc)
            <tr>
              <td style="font-family:monospace;font-size:12px;color:var(--success);">{{ $acc->code }}</td>
              <td>{{ $acc->name }}</td>
              <td class="money font-bold">Rp {{ number_format($acc->getBalance(), 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr><td colspan="3" class="text-muted text-center" style="padding:16px;">Tidak ada</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
    </div>

    <div class="card" style="background:linear-gradient(135deg,var(--brand-50),var(--brand-100));border-color:var(--brand-200);margin-top:auto;">
      <div class="card-body">
        <div style="display:flex;justify-content:space-between;align-items:center;">
          <span style="font-size:15px;font-weight:700;color:var(--gray-700);">Total Kewajiban + Ekuitas</span>
          <span class="money" style="font-size:1.25rem;font-weight:900;color:var(--brand-700);">
            Rp {{ number_format($totalLiabilities + $totalEquities, 0, ',', '.') }}
          </span>
        </div>
        @php $balanced = abs($totalAssets - ($totalLiabilities + $totalEquities)) < 1; @endphp
        <div class="badge {{ $balanced ? 'badge-success' : 'badge-danger' }}" style="margin-top:8px;">
          {{ $balanced ? '✅ Neraca Seimbang' : '⚠️ Neraca Tidak Seimbang' }}
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
