@extends('layouts.app')
@section('title', 'Dashboard Pengurus')
@section('page_title', 'Dashboard Kasir')

@section('content')
<div class="page-header">
  <div class="page-header-text">
    <h1 class="page-title">Dashboard Kasir</h1>
    <p class="page-subtitle">Operasional harian koperasi — {{ now()->format('l, d F Y') }}</p>
  </div>
  <a href="{{ route('pengurus.deposits.create') }}" class="btn btn-primary">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
    Input Setoran
  </a>
</div>

<div class="grid grid-4" style="margin-bottom:24px;">
  <div class="stat-card">
    <div class="stat-card-icon amber"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg></div>
    <div class="stat-card-info">
      <div class="stat-card-label">Pinjaman Pending</div>
      <div class="stat-card-value" style="color:var(--warning);">{{ $stats['pending_loans'] }}</div>
      <div class="stat-card-change">Menunggu review</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-card-icon blue"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path></svg></div>
    <div class="stat-card-info">
      <div class="stat-card-label">Pinjaman Aktif</div>
      <div class="stat-card-value">{{ $stats['active_loans'] }}</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-card-icon red"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path></svg></div>
    <div class="stat-card-info">
      <div class="stat-card-label">Angsuran Telat</div>
      <div class="stat-card-value" style="color:var(--danger);">{{ $stats['overdue_schedules'] }}</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-card-icon green"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg></div>
    <div class="stat-card-info">
      <div class="stat-card-label">Total Kas Simpanan</div>
      <div class="stat-card-value money" style="font-size:1.1rem;">Rp {{ number_format($stats['total_kas'], 0, ',', '.') }}</div>
    </div>
  </div>
</div>

<div class="grid grid-2">
  <div class="card">
    <div class="card-header"><h3>⏳ Antrian Pinjaman</h3>
      <a href="{{ route('pengurus.loans.index') }}" class="btn btn-secondary btn-sm">Semua</a>
    </div>
    <div class="table-wrapper" style="border:none;border-radius:0;">
      <table>
        <thead><tr><th>Anggota</th><th>Jumlah</th><th>Skor</th><th>Aksi</th></tr></thead>
        <tbody>
          @forelse($pendingLoans as $loan)
          <tr>
            <td><div class="font-semibold">{{ $loan->user?->name }}</div></td>
            <td class="money">Rp {{ number_format($loan->amount, 0, ',', '.') }}</td>
            <td>
              @if($loan->credit_score)
                <span class="badge {{ $loan->credit_score <= 30 ? 'badge-success' : 'badge-danger' }}">
                  {{ $loan->credit_score }}%
                </span>
              @else <span class="text-muted">-</span>
              @endif
            </td>
            <td>
              <a href="{{ route('pengurus.loans.show', $loan) }}" class="btn btn-primary btn-sm">Review</a>
            </td>
          </tr>
          @empty
          <tr><td colspan="4" class="text-center text-muted" style="padding:20px;">Tidak ada pengajuan baru 🎉</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <div class="card">
    <div class="card-header"><h3>⚠️ Angsuran Jatuh Tempo</h3></div>
    <div class="table-wrapper" style="border:none;border-radius:0;">
      <table>
        <thead><tr><th>Anggota</th><th>Angsuran ke-</th><th>Jatuh Tempo</th><th>Total</th></tr></thead>
        <tbody>
          @forelse($overdueScheds as $sch)
          <tr>
            <td>{{ $sch->user?->name }}</td>
            <td>{{ $sch->installment_number }}</td>
            <td style="color:var(--danger);font-weight:600;">{{ $sch->due_date->format('d/m/Y') }}</td>
            <td class="money">Rp {{ number_format($sch->total_amount, 0, ',', '.') }}</td>
          </tr>
          @empty
          <tr><td colspan="4" class="text-center text-muted" style="padding:20px;">Tidak ada angsuran telat 🎉</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
