@extends('layouts.app')
@section('title', 'Dashboard Saya')
@section('page_title', 'Dashboard Anggota')

@section('content')
<div class="page-header">
  <div class="page-header-text">
    <h1 class="page-title">Halo, {{ explode(' ', auth()->user()->name)[0] }} 👋</h1>
    <p class="page-subtitle">{{ auth()->user()->department ?? 'Anggota Koperasi' }} — {{ auth()->user()->organization?->name }}</p>
  </div>
  <a href="{{ route('member.loans.apply') }}" class="btn btn-primary">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
    Ajukan Pinjaman
  </a>
</div>

{{-- Savings Summary Cards --}}
<div class="grid grid-4" style="margin-bottom:24px;">
  <div class="stat-card">
    <div class="stat-card-icon indigo"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle></svg></div>
    <div class="stat-card-info">
      <div class="stat-card-label">Simpanan Pokok</div>
      <div class="stat-card-value money" style="font-size:1.2rem;">Rp {{ number_format($simpananPokok, 0, ',', '.') }}</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-card-icon blue"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"></rect></svg></div>
    <div class="stat-card-info">
      <div class="stat-card-label">Simpanan Wajib</div>
      <div class="stat-card-value money" style="font-size:1.2rem;">Rp {{ number_format($simpananWajib, 0, ',', '.') }}</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-card-icon green"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg></div>
    <div class="stat-card-info">
      <div class="stat-card-label">Simpanan Sukarela</div>
      <div class="stat-card-value money" style="font-size:1.2rem;">Rp {{ number_format($simpananSukarela, 0, ',', '.') }}</div>
    </div>
  </div>
  <div class="stat-card" style="background:linear-gradient(135deg,#4f46e5,#7c3aed);border-color:transparent;">
    <div class="stat-card-icon" style="background:rgba(255,255,255,.15);color:white;"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg></div>
    <div class="stat-card-info">
      <div class="stat-card-label" style="color:rgba(255,255,255,.7);">Total Tabungan</div>
      <div class="stat-card-value money" style="font-size:1.2rem;color:white;">Rp {{ number_format($totalSimpanan, 0, ',', '.') }}</div>
    </div>
  </div>
</div>

<div class="grid grid-2">
  {{-- Active Loan --}}
  <div class="card">
    <div class="card-header">
      <div style="display: flex; align-items: center; gap: 12px;">
        <div class="stat-card-icon blue" style="width: 36px; height: 36px; border-radius: 10px;">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path></svg>
        </div>
        <h3 style="margin: 0;">Pinjaman Aktif</h3>
      </div>
    </div>
    <div class="card-body">
      @if($activeLoan)
        <div style="display:grid;gap:12px;">
          <div style="display:flex;justify-content:space-between;align-items:center;padding:12px;background:var(--gray-50);border-radius:8px;">
            <span style="font-size:13px;color:var(--gray-500);">Jumlah Pinjaman</span>
            <span class="money font-bold">Rp {{ number_format($activeLoan->amount, 0, ',', '.') }}</span>
          </div>
          <div style="display:flex;justify-content:space-between;align-items:center;padding:12px;background:var(--gray-50);border-radius:8px;">
            <span style="font-size:13px;color:var(--gray-500);">Sisa Pokok</span>
            <span class="money font-bold" style="color:var(--danger);">Rp {{ number_format($activeLoan->getRemainingPrincipal(), 0, ',', '.') }}</span>
          </div>
          <div style="display:flex;justify-content:space-between;align-items:center;padding:12px;background:var(--gray-50);border-radius:8px;">
            <span style="font-size:13px;color:var(--gray-500);">Tenor</span>
            <span class="font-bold">{{ $activeLoan->schedules->where('status','paid')->count() }} / {{ $activeLoan->tenor_months }} bulan</span>
          </div>
          @if($nextInstallment)
          <div style="background:linear-gradient(135deg,#fffbeb,#fef3c7);border:1px solid #f59e0b;border-radius:8px;padding:14px;">
            <div style="font-size:12px;color:#92400e;font-weight:600;">Angsuran Berikutnya</div>
            <div class="money" style="font-size:1.25rem;font-weight:900;color:#78350f;">Rp {{ number_format($nextInstallment->total_amount, 0, ',', '.') }}</div>
            <div style="font-size:12px;color:#92400e;margin-top:2px;">Jatuh tempo: {{ $nextInstallment->due_date->format('d F Y') }}</div>
          </div>
          @endif
          <a href="{{ route('member.loans.card', $activeLoan) }}" class="btn btn-secondary btn-block">Lihat Kartu Piutang →</a>
        </div>
      @else
        <div class="empty-state">
          <span class="empty-state-icon">💳</span>
          <div class="empty-state-text">Tidak ada pinjaman aktif</div>
          <a href="{{ route('member.loans.apply') }}" class="btn btn-primary" style="margin-top:16px;">Ajukan Pinjaman</a>
        </div>
      @endif
    </div>
  </div>

  {{-- Recent Transactions --}}
  <div class="card">
    <div class="card-header">
      <div style="display: flex; align-items: center; gap: 12px;">
        <div class="stat-card-icon green" style="width: 36px; height: 36px; border-radius: 10px;">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
        </div>
        <h3 style="margin: 0;">Riwayat Transaksi</h3>
      </div>
      <a href="{{ route('member.deposits.index') }}" class="btn btn-secondary btn-sm">Semua</a>
    </div>
    <div class="table-wrapper" style="border:none;border-radius:0;">
      <table>
        <thead><tr><th>Tanggal</th><th>Jenis</th><th>Jumlah</th></tr></thead>
        <tbody>
          @forelse($recentTransactions as $t)
          <tr>
            <td style="font-size:12px;color:var(--gray-400);">{{ $t->created_at->format('d/m/Y') }}</td>
            <td><span class="badge badge-primary">{{ $t->getTypeLabel() }}</span></td>
            <td class="money {{ $t->transaction_type === 'debit' ? 'text-danger' : 'text-success' }}">
              {{ $t->transaction_type === 'debit' ? '-' : '+' }}Rp {{ number_format($t->amount, 0, ',', '.') }}
            </td>
          </tr>
          @empty
          <tr><td colspan="3" class="text-center text-muted" style="padding:20px;">Belum ada transaksi</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
