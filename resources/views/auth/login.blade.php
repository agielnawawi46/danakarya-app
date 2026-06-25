@extends('layouts.auth')

@section('title', 'Masuk ke Dana Karya')

@section('auth_content')
<div class="auth-logo">DK</div>
<h2 class="auth-title">Selamat Datang</h2>
<p class="auth-subtitle">Masuk ke akun Dana Karya Anda</p>

@if($errors->any())
  <div class="alert alert-danger" style="margin-bottom:20px;">
    @foreach($errors->all() as $e) {{ $e }}<br> @endforeach
  </div>
@endif

<form method="POST" action="{{ route('login') }}">
  @csrf
  <div class="form-group">
    <label class="form-label" for="email">Email <span class="req">*</span></label>
    <input type="email" id="email" name="email" value="{{ old('email') }}"
      class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
      placeholder="email@perusahaan.com" required autofocus>
  </div>

  <div class="form-group">
    <label class="form-label" for="password">Password <span class="req">*</span></label>
    <input type="password" id="password" name="password"
      class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}"
      placeholder="••••••••" required>
  </div>

  <div class="flex items-center justify-between mb-5" style="margin-bottom:20px;">
    <label style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--gray-600);cursor:pointer;">
      <input type="checkbox" name="remember"> Ingat Saya
    </label>
  </div>

  <button type="submit" class="btn btn-primary btn-block btn-lg">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path><polyline points="10 17 15 12 10 7"></polyline><line x1="15" y1="12" x2="3" y2="12"></line></svg>
    Masuk Sekarang
  </button>

  <div style="text-align:center;margin-top:24px;font-size:13px;color:var(--gray-500);">
    Belum punya akun?
    <a href="{{ route('register') }}" style="color:var(--brand-600);font-weight:600;text-decoration:none;">Daftar Koperasi Baru →</a>
  </div>
</form>
</div>
@endsection
