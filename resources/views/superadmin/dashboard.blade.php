@extends('layouts.app')
@section('title', 'Dashboard Superadmin')
@section('page_title', 'Dashboard Superadmin')

@section('content')
<div class="page-header">
  <div class="page-header-text">
    <h1 class="page-title">Dashboard Platform</h1>
    <p class="page-subtitle">Pantauan global seluruh koperasi mitra Dana Karya</p>
  </div>
</div>

<div class="grid grid-2" style="margin-bottom:24px;">
  <div class="stat-card">
    <div class="stat-card-icon indigo"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg></div>
    <div class="stat-card-info">
      <div class="stat-card-label">Total Koperasi</div>
      <div class="stat-card-value">{{ $stats['total_tenants'] }}</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-card-icon green"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="20 6 9 17 4 12"></polyline></svg></div>
    <div class="stat-card-info">
      <div class="stat-card-label">Koperasi Aktif</div>
      <div class="stat-card-value" style="color:var(--success);">{{ $stats['active_tenants'] }}</div>
    </div>
  </div>
</div>

<div class="card">
    <div class="card-header">
      <h3>🏢 Koperasi Terbaru</h3>
      <a href="{{ route('superadmin.tenants.index') }}" class="btn btn-secondary btn-sm">Kelola Semua</a>
    </div>
    <div class="table-wrapper" style="border:none;border-radius:0;">
      <table>
        <thead><tr><th>Nama Koperasi</th><th>Status</th><th>Terdaftar</th><th>Aksi</th></tr></thead>
        <tbody>
          @forelse($recentTenants as $tenant)
          <tr>
            <td>
              <div class="font-semibold">{{ $tenant->name }}</div>
              <div class="text-sm text-muted">{{ $tenant->email }}</div>
            </td>
            <td><span class="badge {{ $tenant->is_active ? 'badge-success' : 'badge-danger' }}">{{ $tenant->is_active ? 'Aktif' : 'Non-Aktif' }}</span></td>
            <td style="font-size:12px;color:var(--gray-400);">{{ $tenant->created_at->format('d/m/Y') }}</td>
            <td><a href="{{ route('superadmin.tenants.show', $tenant) }}" class="btn btn-secondary btn-sm">Detail</a></td>
          </tr>
          @empty
          <tr><td colspan="4" class="text-center text-muted" style="padding:24px;">Belum ada koperasi terdaftar</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

@endsection
