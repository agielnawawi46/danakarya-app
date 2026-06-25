@extends('layouts.app')
@section('title', 'Setup Profil Koperasi')
@section('page_title', 'Setup Profil Koperasi')

@section('content')
<div class="page-header">
  <div class="page-header-text">
    <h1 class="page-title">Lengkapi Profil Koperasi</h1>
    <p class="page-subtitle">Isi data resmi koperasi Anda untuk mengaktifkan semua fitur platform</p>
  </div>
</div>

<div class="card" style="max-width:700px;">
  <div class="card-header">
    <h3>Data Identitas Koperasi</h3>
    <span class="badge badge-warning">Wajib Dilengkapi</span>
  </div>
  <div class="card-body">
    <form method="POST" action="{{ route('admin.organization.update') }}" enctype="multipart/form-data">
      @csrf

      <div class="form-row">
        <div class="form-group">
          <label class="form-label" for="name">Nama Koperasi <span class="req">*</span></label>
          <input type="text" id="name" name="name" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
            value="{{ old('name', $org->name !== 'Koperasi Baru (Belum Dikonfigurasi)' ? $org->name : '') }}"
            placeholder="KSP Sejahtera Bersama" required>
          @error('name')<div class="form-error">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
          <label class="form-label" for="legal_name">Nama Resmi / Badan Hukum</label>
          <input type="text" id="legal_name" name="legal_name" class="form-control"
            value="{{ old('legal_name', $org->legal_name) }}"
            placeholder="Koperasi Simpan Pinjam ...">
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label class="form-label" for="legal_number">No. Badan Hukum</label>
          <input type="text" id="legal_number" name="legal_number" class="form-control"
            value="{{ old('legal_number', $org->legal_number) }}"
            placeholder="001234/BH/KOP/2020">
        </div>
        <div class="form-group">
          <label class="form-label" for="phone">No. Telepon</label>
          <input type="text" id="phone" name="phone" class="form-control"
            value="{{ old('phone', $org->phone) }}"
            placeholder="021-12345678">
        </div>
      </div>

      <div class="form-group">
        <label class="form-label" for="email">Email Koperasi</label>
        <input type="email" id="email" name="email" class="form-control"
          value="{{ old('email', $org->email) }}"
          placeholder="admin@koperasi.co.id">
      </div>

      <div class="form-group">
        <label class="form-label" for="address">Alamat Lengkap</label>
        <textarea id="address" name="address" class="form-control" rows="3"
          placeholder="Jl. Sudirman No. 1, Jakarta Pusat">{{ old('address', $org->address) }}</textarea>
      </div>

      <div class="form-group">
        <label class="form-label" for="logo">Logo Koperasi</label>
        @if($org->logo)
          <div style="margin-bottom:10px;">
            <img src="{{ asset('storage/'.$org->logo) }}" alt="Logo" style="height:60px;border-radius:8px;border:1px solid var(--gray-200);">
          </div>
        @endif
        <input type="file" id="logo" name="logo" class="form-control" accept="image/*">
        <div class="form-hint">Format: JPG, PNG, GIF. Maks 2MB.</div>
      </div>

      <div class="divider"></div>

      <div class="flex justify-end gap-2">
        <button type="submit" class="btn btn-primary">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
          Simpan & Lanjutkan
        </button>
      </div>
    </form>
  </div>
</div>
@endsection
