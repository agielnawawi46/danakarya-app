@extends('layouts.auth')

@section('title', 'Daftar Koperasi Baru — Dana Karya')

@section('auth_content')
<div class="auth-logo">DK</div>
<h2 class="auth-title">Daftar Koperasi Baru</h2>
<p class="auth-subtitle">Buat akun Admin untuk koperasi Anda</p>

@if($errors->any())
  <div class="alert alert-danger">
    @foreach($errors->all() as $e) {{ $e }}<br> @endforeach
  </div>
@endif

<form method="POST" action="{{ route('register') }}">
  @csrf
  <div class="form-group">
    <label class="form-label" for="name">Nama Lengkap <span class="req">*</span></label>
    <input type="text" id="name" name="name" value="{{ old('name') }}"
      class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
      placeholder="Nama Administrator Koperasi" required autofocus>
  </div>

  <div class="form-group">
    <label class="form-label" for="email">Email <span class="req">*</span></label>
    <input type="email" id="email" name="email" value="{{ old('email') }}"
      class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
      placeholder="admin@perusahaan.com" required>
  </div>

  <div class="form-group">
    <label class="form-label" for="password">Password <span class="req">*</span></label>
    <input type="password" id="password" name="password"
      class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}"
      placeholder="Minimal 8 karakter" required>
  </div>

  <div class="form-group">
    <label class="form-label" for="password_confirmation">Konfirmasi Password <span class="req">*</span></label>
    <input type="password" id="password_confirmation" name="password_confirmation"
      class="form-control" placeholder="Ulangi password" required>
  </div>

  <div style="background:var(--brand-50);border:1px solid var(--brand-200);border-radius:8px;padding:12px 14px;margin-bottom:20px;font-size:12px;color:var(--brand-700);">
    ℹ️ Setelah registrasi, Anda perlu melengkapi profil koperasi dan aturan keuangan.
  </div>

  <button type="submit" class="btn btn-primary btn-block btn-lg">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="20" y1="8" x2="20" y2="14"></line><line x1="23" y1="11" x2="17" y2="11"></line></svg>
    Daftar Sekarang
  </button>

  <div style="text-align:center;margin-top:20px;font-size:13px;color:var(--gray-500);">
    Sudah punya akun?
    <a href="{{ route('login') }}" style="color:var(--brand-600);font-weight:600;text-decoration:none;">Masuk →</a>
  </div>
</form>
@endsection
