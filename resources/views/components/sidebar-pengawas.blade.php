{{-- Pengawas Sidebar --}}
<div class="sidebar-section-label">Pengawasan</div>

<a href="{{ route('pengawas.dashboard') }}" class="sidebar-link {{ request()->routeIs('pengawas.dashboard') ? 'active' : '' }}">
  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
  Dashboard Pengawasan
</a>

<a href="{{ route('pengawas.audit-finance.index') }}" class="sidebar-link {{ request()->routeIs('pengawas.audit-finance.*') ? 'active' : '' }}">
  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
  Audit Keuangan
</a>

<a href="{{ route('pengawas.audit-trail.index') }}" class="sidebar-link {{ request()->routeIs('pengawas.audit-trail.*') ? 'active' : '' }}">
  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
  Log Audit Trail
</a>
