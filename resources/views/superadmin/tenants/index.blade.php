@extends('layouts.app')
@section('title', 'Manajemen Koperasi')
@section('page_title', 'Manajemen Koperasi')

@section('content')
<div class="page-header">
  <div class="page-header-text">
    <h1 class="page-title">Manajemen Koperasi</h1>
    <p class="page-subtitle">Daftar seluruh koperasi mitra yang terdaftar di platform</p>
  </div>
</div>

<div class="card">
  <div class="table-wrapper" style="border:none;">
    <table>
      <thead>
        <tr>
          <th>Nama Koperasi</th>
          <th>Email</th>
          <th>Status</th>
          <th>Konfigurasi</th>
          <th>Anggota</th>
          <th>Terdaftar</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($tenants as $tenant)
        <tr>
          <td>
            <div class="font-semibold">{{ $tenant->name }}</div>
            @if($tenant->legal_number)
            <div class="text-sm text-muted">{{ $tenant->legal_number }}</div>
            @endif
          </td>
          <td>{{ $tenant->email ?? '—' }}</td>
          <td><span class="badge {{ $tenant->is_active ? 'badge-success' : 'badge-danger' }}">{{ $tenant->is_active ? 'Aktif' : 'Suspended' }}</span></td>
          <td><span class="badge {{ $tenant->is_configured ? 'badge-success' : 'badge-warning' }}">{{ $tenant->is_configured ? 'Lengkap' : 'Belum Konfigurasi' }}</span></td>
          <td>{{ $tenant->users_count }}</td>
          <td style="font-size:12px;color:var(--gray-400);">{{ $tenant->created_at->format('d/m/Y') }}</td>
          <td>
            <div class="flex gap-2">
              <a href="{{ route('superadmin.tenants.show', $tenant) }}" class="btn btn-secondary btn-sm">Detail</a>
              <form method="POST" action="{{ route('superadmin.tenants.toggle-active', $tenant) }}">
                @csrf
                <button class="btn {{ $tenant->is_active ? 'btn-warning' : 'btn-success' }} btn-sm"
                  data-confirm="{{ $tenant->is_active ? 'Tangguhkan koperasi '.$tenant->name.'?' : 'Aktifkan kembali koperasi '.$tenant->name.'?' }}">
                  {{ $tenant->is_active ? 'Suspend' : 'Aktifkan' }}
                </button>
              </form>
            </div>
          </td>
        </tr>
        @empty
        <tr><td colspan="7" class="text-center text-muted" style="padding:40px;">Belum ada koperasi terdaftar</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="card-footer">{{ $tenants->links() }}</div>
</div>
@endsection
