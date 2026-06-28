@extends('layouts.app')

@section('title', 'Profil Saya')
@section('page_title', 'Profil Saya')

@section('content')
<div class="page-header">
  <div class="page-header-text">
    <h1 class="page-title">Profil Saya</h1>
    <p class="page-subtitle">Kelola informasi pribadi dan keamanan akun Anda</p>
  </div>
</div>

@php
    $hasPasswordError = $errors->has('password') || $errors->has('current_password');
    $hasIdentityError = $errors->any() && !$hasPasswordError;
    $defaultTab = $hasPasswordError ? 'password' : ($hasIdentityError ? 'identity' : 'info');
@endphp

<div x-data="{ tab: '{{ $defaultTab }}' }" :class="tab === 'info' ? '' : 'grid grid-2'" style="align-items: stretch;">

    {{-- Left Side / Full Width: Info Card --}}
    <div class="card" style="display: flex; flex-direction: column; transition: all 0.3s ease; height: 100%;">
        <div class="card-header">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div class="stat-card-icon indigo" style="width: 36px; height: 36px; border-radius: 10px;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                </div>
                <h3 style="margin: 0;">Identitas Akun</h3>
            </div>
        </div>
        <div class="card-body" style="display: flex; flex-direction: column; flex: 1;">
            <div style="display:flex;align-items:center;gap:16px;margin-bottom:24px;">
                <div style="width:64px;height:64px;background:linear-gradient(135deg,var(--brand-500),var(--accent-violet));border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:24px;font-weight:900;color:white;box-shadow:0 4px 10px rgba(0,0,0,0.1);">
                    {{ strtoupper(substr($user->name, 0, 2)) }}
                </div>
                <div>
                    <div style="font-size:18px;font-weight:700;color:var(--gray-900);">{{ $user->name }}</div>
                    <div style="font-size:13px;color:var(--gray-500);margin-bottom:6px;">{{ $user->email }}</div>
                    @foreach($user->getRoleNames() as $role)
                        <span class="badge badge-primary">{{ ucfirst($role) }}</span>
                    @endforeach
                </div>
            </div>

            <div style="display:grid;gap:12px;margin-bottom:24px;">
                <div style="display:flex;justify-content:space-between;align-items:center;padding:14px 16px;background:var(--gray-50);border-radius:10px;border:1px solid var(--gray-100);">
                    <span style="font-size:14px;color:var(--gray-600);font-weight:500;">No. Telepon</span>
                    <span style="font-size:14px;font-weight:700;color:var(--gray-900);">{{ $user->phone ?: '-' }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center;padding:14px 16px;background:var(--gray-50);border-radius:10px;border:1px solid var(--gray-100);">
                    <span style="font-size:14px;color:var(--gray-600);font-weight:500;">ID Karyawan</span>
                    <span style="font-size:14px;font-weight:700;color:var(--gray-900);">{{ $user->employee_id ?: '-' }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center;padding:14px 16px;background:var(--gray-50);border-radius:10px;border:1px solid var(--gray-100);">
                    <span style="font-size:14px;color:var(--gray-600);font-weight:500;">Departemen</span>
                    <span style="font-size:14px;font-weight:700;color:var(--gray-900);">{{ $user->department ?: '-' }}</span>
                </div>
            </div>

            <div style="margin-top:auto;padding-top:16px;border-top:1px dashed var(--gray-200);display:flex;justify-content:space-between;align-items:center;">
                <span style="font-size:13px;color:var(--gray-500);">Terdaftar Pada</span>
                @if($user->join_date)
                    <strong style="font-size:13px;color:var(--gray-900);">{{ $user->join_date->format('d M Y') }}</strong>
                @else
                    <strong style="font-size:13px;color:var(--gray-900);">-</strong>
                @endif
            </div>

            {{-- Action Buttons to toggle views --}}
            <div style="margin-top: 24px; display: flex; gap: 12px;">
                @if(!auth()->user()->isSuperadmin())
                    <button type="button" @click="tab = 'identity'" :class="tab === 'identity' ? 'btn btn-primary' : 'btn btn-secondary'" style="flex: 1; justify-content: center;">
                        Ubah Identitas
                    </button>
                @endif
                <button type="button" @click="tab = 'password'" :class="tab === 'password' ? 'btn btn-primary' : 'btn btn-secondary'" style="flex: 1; justify-content: center;">
                    Perbarui Kata Sandi
                </button>
            </div>
        </div>
    </div>

    {{-- Right Side: Forms --}}
    <div x-show="tab !== 'info'" style="display: none; height: 100%;">
        {{-- Tab: Identity --}}
        @if(!auth()->user()->isSuperadmin())
        <div x-show="tab === 'identity'" style="height: 100%;" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
            <div class="card" style="height: 100%; display: flex; flex-direction: column;">
                <div class="card-header">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div class="stat-card-icon amber" style="width: 36px; height: 36px; border-radius: 10px;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                        </div>
                        <h3 style="margin: 0;">Ubah Identitas</h3>
                    </div>
                </div>
                <div class="card-body" style="flex: 1;">
                    <form method="POST" action="{{ route('profile.update') }}" style="height: 100%; display: flex; flex-direction: column;">
                        @csrf
                        @method('PUT')
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Nama <span class="req">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label">Email <span class="req">*</span></label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label">Nomor Telepon</label>
                            <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $user->phone) }}">
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">ID Karyawan</label>
                                <input type="text" name="employee_id" class="form-control @error('employee_id') is-invalid @enderror" value="{{ old('employee_id', $user->employee_id) }}">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Departemen</label>
                                <input type="text" name="department" class="form-control @error('department') is-invalid @enderror" value="{{ old('department', $user->department) }}">
                            </div>
                        </div>

                        <div class="flex justify-end gap-2" style="margin-top: auto; padding-top: 16px;">
                            <button type="button" @click="tab = 'info'" class="btn btn-secondary">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif

        {{-- Tab: Password --}}
        <div x-show="tab === 'password'" style="display: none; height: 100%;" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
            <div class="card" style="height: 100%; display: flex; flex-direction: column;">
                <div class="card-header">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div class="stat-card-icon" style="width: 36px; height: 36px; border-radius: 10px; background: linear-gradient(135deg, var(--danger), #991b1b); color: white;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                        </div>
                        <h3 style="margin: 0;">Perbarui Kata Sandi</h3>
                    </div>
                </div>
                <div class="card-body" style="flex: 1;">
                    <form method="POST" action="{{ route('profile.password') }}" style="height: 100%; display: flex; flex-direction: column;">
                        @csrf
                        @method('PUT')

                        <div class="form-group mb-3">
                            <label class="form-label">Kata Sandi Saat Ini <span class="req">*</span></label>
                            <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" required>
                            @error('current_password')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label">Kata Sandi Baru <span class="req">*</span></label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                            @error('password')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-4">
                            <label class="form-label">Konfirmasi Kata Sandi Baru <span class="req">*</span></label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>

                        <div class="flex justify-end gap-2" style="margin-top: auto; padding-top: 16px;">
                            <button type="button" @click="tab = 'info'" class="btn btn-secondary">Batal</button>
                            <button type="submit" class="btn btn-primary">Perbarui Kata Sandi</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
</div>
@endsection
