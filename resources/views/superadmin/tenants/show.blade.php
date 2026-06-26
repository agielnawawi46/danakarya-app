@extends('layouts.app')
@section('title', 'Detail Koperasi')
@section('page_title', 'Detail Koperasi')

@section('content')
<div class="page-header">
  <div class="page-header-text">
    <h1 class="page-title">{{ $tenant->name }}</h1>
    <p class="page-subtitle">Detail informasi dan statistik koperasi mitra</p>
  </div>
  <div class="flex gap-2">
    <form method="POST" action="{{ route('superadmin.tenants.toggle-active', $tenant) }}">
      @csrf
      <button class="btn {{ $tenant->is_active ? 'btn-warning' : 'btn-success' }}"
        data-confirm="{{ $tenant->is_active ? 'Tangguhkan koperasi ini?' : 'Aktifkan kembali?' }}">
        {{ $tenant->is_active ? '⏸ Suspend' : '▶ Aktifkan' }}
      </button>
    </form>
    <a href="{{ route('superadmin.tenants.index') }}" class="btn btn-secondary">← Kembali</a>
  </div>
</div>

<div class="grid" style="align-items:start;">
  <div class="card">
    <div class="card-header">
      <div style="display: flex; align-items: center; gap: 12px;">
        <div class="stat-card-icon indigo" style="width: 36px; height: 36px; border-radius: 10px;">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18"></path><path d="M5 21V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16"></path><path d="M9 21v-4a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v4"></path><path d="M9 7h6"></path><path d="M9 11h6"></path></svg>
        </div>
        <h3 style="margin: 0;">Profil Koperasi</h3>
      </div></div>
    <div class="card-body">
      @foreach([
        ['Nama Resmi', $tenant->legal_name ?? $tenant->name],
        ['No. Badan Hukum', $tenant->legal_number ?? '—'],
        ['Email', $tenant->email ?? '—'],
        ['Telepon', $tenant->phone ?? '—'],
        ['Alamat', $tenant->address ?? '—'],
        ['Status', $tenant->is_active ? '✅ Aktif' : '🔴 Suspended'],
        ['Konfigurasi', $tenant->is_configured ? '✅ Lengkap' : '⚠️ Belum dikonfigurasi'],
        ['Terdaftar', $tenant->created_at->format('d F Y')],
      ] as [$label, $val])
      <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--gray-100);">
        <span style="font-size:13px;color:var(--gray-400);">{{ $label }}</span>
        <span style="font-size:13px;font-weight:600;text-align:right;max-width:60%;">{{ $val }}</span>
      </div>
      @endforeach
    </div>
  </div>


</div>
@endsection
