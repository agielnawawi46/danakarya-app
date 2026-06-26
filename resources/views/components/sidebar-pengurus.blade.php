{{-- Pengurus Sidebar --}}
<div class="sidebar-section-label">Operasional</div>

<a href="{{ route('pengurus.dashboard') }}" class="sidebar-link {{ request()->routeIs('pengurus.dashboard') ? 'active' : '' }}">
  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
  Dashboard Kasir
</a>

<a href="{{ route('pengurus.deposits.index') }}" class="sidebar-link {{ request()->routeIs('pengurus.deposits.*') && !request()->routeIs('pengurus.deposits.withdrawals') ? 'active' : '' }}">
  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
  Loket Simpanan
</a>

<a href="{{ route('pengurus.deposits.withdrawals') }}" class="sidebar-link {{ request()->routeIs('pengurus.deposits.withdrawals') ? 'active' : '' }}">
  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="17 1 21 5 17 9"></polyline><path d="M3 11V9a4 4 0 0 1 4-4h14"></path><polyline points="7 23 3 19 7 15"></polyline><path d="M21 13v2a4 4 0 0 1-4 4H3"></path></svg>
  Penarikan Sukarela
</a>

<a href="{{ route('pengurus.loans.index') }}" class="sidebar-link {{ request()->routeIs('pengurus.loans.*') ? 'active' : '' }}">
  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
  Kredit Pinjaman
</a>

<div class="sidebar-section-label">Akuntansi</div>

<a href="{{ route('pengurus.accounting.coa') }}" class="sidebar-link {{ request()->routeIs('pengurus.accounting.coa') ? 'active' : '' }}">
  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path></svg>
  Bagan Akun (COA)
</a>

<a href="{{ route('pengurus.accounting.journals') }}" class="sidebar-link {{ request()->routeIs('pengurus.accounting.journals*') ? 'active' : '' }}">
  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><book x="3" y="3" width="18" height="18" rx="2"></book><path d="M3 9h18M3 15h18M9 3v18"></path></svg>
  Buku Jurnal
</a>

<div class="sidebar-section-label">Laporan</div>

<a href="{{ route('pengurus.reports.index') }}" class="sidebar-link {{ request()->routeIs('pengurus.reports.*') ? 'active' : '' }}">
  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
  Laporan Keuangan
</a>
