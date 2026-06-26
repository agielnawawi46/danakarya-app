@extends('layouts.app')

@section('title', 'Dashboard Admin')
@section('page_title', 'Dashboard Admin')

@section('content')
<div class="page-header">
  <div class="page-header-text">
    <h1 class="page-title">Selamat Datang, {{ auth()->user()->name }}</h1>
    <p class="page-subtitle">{{ $org->name }} &mdash; Ringkasan operasional koperasi Anda</p>
  </div>
  <div class="flex gap-2">
    <a href="{{ route('admin.payroll.export') }}" class="btn btn-secondary btn-sm">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
      Export Billing
    </a>
    <a href="{{ route('admin.members.create') }}" class="btn btn-primary btn-sm">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
      Tambah Anggota
    </a>
  </div>
</div>

{{-- Stats Grid --}}
<div class="grid grid-4 mb-5" style="margin-bottom:24px;">
  <div class="stat-card">
    <div class="stat-card-icon indigo">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle></svg>
    </div>
    <div class="stat-card-info">
      <div class="stat-card-label">Total Anggota</div>
      <div class="stat-card-value" data-counter data-target="{{ $stats['total_members'] }}">{{ $stats['total_members'] }}</div>
      <div class="stat-card-change">Anggota aktif</div>
    </div>
  </div>

  <div class="stat-card">
    <div class="stat-card-icon green">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
    </div>
    <div class="stat-card-info">
      <div class="stat-card-label">Total Simpanan</div>
      <div class="stat-card-value money" data-counter data-rupiah data-target="{{ $stats['total_simpanan'] }}">Rp {{ number_format($stats['total_simpanan'], 0, ',', '.') }}</div>
      <div class="stat-card-change">Semua jenis simpanan</div>
    </div>
  </div>

  <div class="stat-card">
    <div class="stat-card-icon blue">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line></svg>
    </div>
    <div class="stat-card-info">
      <div class="stat-card-label">Total Pinjaman Aktif</div>
      <div class="stat-card-value money">Rp {{ number_format($stats['total_pinjaman'], 0, ',', '.') }}</div>
      <div class="stat-card-change">Portfolio kredit berjalan</div>
    </div>
  </div>

  <div class="stat-card">
    <div class="stat-card-icon amber">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
    </div>
    <div class="stat-card-info">
      <div class="stat-card-label">Menunggu Review</div>
      <div class="stat-card-value" style="color:var(--warning);">{{ $stats['pending_loans'] }}</div>
      <div class="stat-card-change">Pengajuan pinjaman baru</div>
    </div>
  </div>
</div>

<div class="grid grid-2">
  {{-- Recent Loans --}}
  <div class="card">
    <div class="card-header">
      <div style="display: flex; align-items: center; gap: 12px;">
        <div class="stat-card-icon blue" style="width: 36px; height: 36px; border-radius: 10px;">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line></svg>
        </div>
        <h3 style="margin: 0;">Pinjaman Terbaru</h3>
      </div>
    </div>
    <div class="table-wrapper" style="border-radius:0;border:none;">
      <table>
        <thead>
          <tr>
            <th>Anggota</th>
            <th>Jumlah</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          @forelse($recentLoans as $loan)
          <tr>
            <td>
              <div class="font-semibold">{{ $loan->user?->name ?? '-' }}</div>
              <div class="text-sm text-muted">{{ $loan->user?->department ?? '' }}</div>
            </td>
            <td class="money">Rp {{ number_format($loan->amount, 0, ',', '.') }}</td>
            <td>{!! $loan->getStatusBadge() !!}</td>
          </tr>
          @empty
          <tr><td colspan="3" class="text-center text-muted" style="padding:24px;">Belum ada pinjaman</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- Recent Deposits --}}
  <div class="card">
    <div class="card-header">
      <div style="display: flex; align-items: center; gap: 12px;">
        <div class="stat-card-icon green" style="width: 36px; height: 36px; border-radius: 10px;">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
        </div>
        <h3 style="margin: 0;">Transaksi Terbaru</h3>
      </div>
    </div>
    <div class="table-wrapper" style="border-radius:0;border:none;">
      <table>
        <thead>
          <tr>
            <th>Anggota</th>
            <th>Jenis</th>
            <th>Jumlah</th>
          </tr>
        </thead>
        <tbody>
          @forelse($recentDeposits as $deposit)
          <tr>
            <td>{{ $deposit->user?->name ?? '-' }}</td>
            <td><span class="badge badge-primary">{{ $deposit->getTypeLabel() }}</span></td>
            <td class="money {{ $deposit->transaction_type === 'debit' ? 'text-danger' : 'text-success' }}">
              {{ $deposit->transaction_type === 'debit' ? '-' : '+' }}Rp {{ number_format($deposit->amount, 0, ',', '.') }}
            </td>
          </tr>
          @empty
          <tr><td colspan="3" class="text-center text-muted" style="padding:24px;">Belum ada transaksi</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
