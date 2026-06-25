{{-- Member Sidebar --}}
<div class="sidebar-section-label">Layanan Mandiri</div>

<a href="{{ route('member.dashboard') }}" class="sidebar-link {{ request()->routeIs('member.dashboard') ? 'active' : '' }}">
  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
  Dashboard Saya
</a>

<a href="{{ route('member.deposits.index') }}" class="sidebar-link {{ request()->routeIs('member.deposits.*') ? 'active' : '' }}">
  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
  Tabungan Saya
</a>

<a href="{{ route('member.loans.index') }}" class="sidebar-link {{ request()->routeIs('member.loans.*') ? 'active' : '' }}">
  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
  Fasilitas Kredit
</a>

<a href="{{ route('member.shu.index') }}" class="sidebar-link {{ request()->routeIs('member.shu.*') ? 'active' : '' }}">
  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
  Bonus SHU
</a>
