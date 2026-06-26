@extends('layouts.app')
@section('title', 'Tambah Anggota')
@section('page_title', 'Tambah Anggota')

@section('content')
<div class="page-header">
  <div class="page-header-text">
    <h1 class="page-title">Tambah Anggota Baru</h1>
    <p class="page-subtitle">Daftarkan anggota, pengurus, atau pengawas koperasi</p>
  </div>
  <a href="{{ route('admin.members.index') }}" class="btn btn-secondary">← Kembali</a>
</div>

<div class="card">
  <div class="card-header">
      <div style="display: flex; align-items: center; gap: 12px;">
        <div class="stat-card-icon blue" style="width: 36px; height: 36px; border-radius: 10px;">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect></svg>
        </div>
        <h3 style="margin: 0;">Data Anggota</h3>
      </div></div>
  <div class="card-body">
    <form method="POST" action="{{ route('admin.members.store') }}">
      @csrf

      <div class="form-row">
        <div class="form-group">
          <label class="form-label" for="name">Nama Lengkap <span class="req">*</span></label>
          <input type="text" id="name" name="name" value="{{ old('name') }}"
            class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
            placeholder="Nama Karyawan" required>
          @error('name')<div class="form-error">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
          <label class="form-label" for="email">Email <span class="req">*</span></label>
          <input type="email" id="email" name="email" value="{{ old('email') }}"
            class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
            placeholder="email@perusahaan.com" required>
          @error('email')<div class="form-error">{{ $message }}</div>@enderror
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label class="form-label" for="password">Password <span class="req">*</span></label>
          <input type="password" id="password" name="password"
            class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}"
            placeholder="Min. 8 karakter" required>
          @error('password')<div class="form-error">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
          <label class="form-label" for="role">Role <span class="req">*</span></label>
          <select id="role" name="role" class="form-control" required>
            <option value="">-- Pilih Role --</option>
            <option value="anggota"  {{ old('role') === 'anggota'  ? 'selected' : '' }}>Anggota</option>
            <option value="pengurus" {{ old('role') === 'pengurus' ? 'selected' : '' }}>Pengurus</option>
            <option value="pengawas" {{ old('role') === 'pengawas' ? 'selected' : '' }}>Pengawas</option>
          </select>
          @error('role')<div class="form-error">{{ $message }}</div>@enderror
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label class="form-label" for="employee_id">NIK Karyawan</label>
          <input type="text" id="employee_id" name="employee_id" value="{{ old('employee_id') }}"
            class="form-control" placeholder="EMP-001">
        </div>
        <div class="form-group">
          <label class="form-label" for="department">Departemen</label>
          <input type="text" id="department" name="department" value="{{ old('department') }}"
            class="form-control" placeholder="Marketing, Finance, IT, ...">
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label class="form-label" for="salary">Gaji Pokok (Rp)</label>
          <input type="number" id="salary" name="salary" value="{{ old('salary') }}"
            class="form-control" placeholder="5000000" step="100000" min="0">
          <div class="form-hint">Digunakan untuk kalkulasi kelayakan pinjaman (30% gaji)</div>
        </div>
        <div class="form-group">
          <label class="form-label" for="join_date">Tanggal Bergabung</label>
          <input type="date" id="join_date" name="join_date" value="{{ old('join_date', date('Y-m-d')) }}"
            class="form-control">
        </div>
      </div>

      <div class="form-group">
        <label class="form-label" for="phone">No. Telepon</label>
        <input type="text" id="phone" name="phone" value="{{ old('phone') }}"
          class="form-control" placeholder="08123456789">
      </div>

      <div style="background:var(--brand-50);border:1px solid var(--brand-200);border-radius:8px;padding:12px 14px;margin-bottom:20px;font-size:13px;color:var(--brand-700);">
        ℹ️ Jika role <strong>Anggota</strong> dipilih, tagihan simpanan pokok akan otomatis dibuat (status pending menunggu pembayaran).
      </div>

      <div class="flex justify-end gap-2">
        <a href="{{ route('admin.members.index') }}" class="btn btn-secondary">Batal</a>
        <button type="submit" class="btn btn-primary">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
          Simpan Anggota
        </button>
      </div>
    </form>
  </div>
</div>
@endsection
