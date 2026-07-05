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
      <style>
        .profile-summary-container {
          display: flex;
          flex-direction: column;
          gap: 24px;
        }
        .profile-logo-wrapper {
          text-align: center;
        }
        .profile-logo-wrapper img {
          height: 80px;
          border-radius: 8px;
          object-fit: contain;
          transition: all 0.3s ease;
        }
        .profile-details-wrapper {
          flex: 1;
        }

        /* Desktop view: side by side */
        @media (min-width: 769px) {
          .profile-summary-container {
            flex-direction: row;
            align-items: flex-start;
          }
          .profile-logo-wrapper {
            flex: 0 0 240px;
            padding-right: 24px;
            border-right: 1px solid var(--gray-100);
            display: flex;
            align-items: center;
            justify-content: center;
          }
          .profile-logo-wrapper img {
            height: 160px;
            max-width: 100%;
          }
        }

        /* Mobile view: stack */
        @media (max-width: 768px) {
          .profile-summary-container {
            flex-direction: column;
          }
          .profile-logo-wrapper {
            flex: auto;
            border-right: none;
            padding-right: 0;
            border-bottom: 1px solid var(--gray-100);
            padding-bottom: 20px;
          }
          .profile-logo-wrapper img {
            height: 100px;
          }
        }
      </style>

      <div class="profile-summary-container">
        @if($tenant->logo)
        <div class="profile-logo-wrapper">
          <img src="{{ asset('storage/'.$tenant->logo) }}" alt="Logo">
        </div>
        @endif
        
        <div class="profile-details-wrapper">
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
            <span style="font-size:13px;color:var(--gray-500);">{{ $label }}</span>
            <span style="font-size:13px;font-weight:600;text-align:right;max-width:60%;">{{ $val }}</span>
          </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>


</div>
@endsection
