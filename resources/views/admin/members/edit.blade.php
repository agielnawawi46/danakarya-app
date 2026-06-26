@extends('layouts.app')
@section('title', 'Edit Anggota')
@section('page_title', 'Edit Anggota')

@section('content')
<div class="page-header">
  <div class="page-header-text">
    <h1 class="page-title">Edit: {{ $user->name }}</h1>
    <p class="page-subtitle">{{ $user->email }}</p>
  </div>
  <a href="{{ route('admin.members.index') }}" class="btn btn-secondary">← Kembali</a>
</div>

<div class="grid grid-2" style="align-items:stretch;">
  {{-- Edit Form --}}
  <div class="card">
    <div class="card-header">
      <div style="display: flex; align-items: center; gap: 12px;">
        <div class="stat-card-icon amber" style="width: 36px; height: 36px; border-radius: 10px;">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
        </div>
        <h3 style="margin: 0;">Data Anggota</h3>
      </div></div>
    <div class="card-body">
      <form method="POST" action="{{ route('admin.members.update', $user) }}">
        @csrf @method('PUT')

        <div class="form-group">
          <label class="form-label">Nama Lengkap <span class="req">*</span></label>
          <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control" required>
        </div>
        <div class="form-group">
          <label class="form-label">Email <span class="req">*</span></label>
          <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control" required>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">NIK Karyawan</label>
            <input type="text" name="employee_id" value="{{ old('employee_id', $user->employee_id) }}" class="form-control">
          </div>
          <div class="form-group">
            <label class="form-label">Departemen</label>
            <input type="text" name="department" value="{{ old('department', $user->department) }}" class="form-control">
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Gaji Pokok (Rp)</label>
            <input type="number" name="salary" value="{{ old('salary', $user->salary) }}" class="form-control" step="100000" min="0">
          </div>
          <div class="form-group">
            <label class="form-label">No. Telepon</label>
            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="form-control">
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Status Anggota</label>
            <select name="status" class="form-control">
              <option value="active"    {{ $user->status === 'active'    ? 'selected' : '' }}>✅ Aktif</option>
              <option value="inactive"  {{ $user->status === 'inactive'  ? 'selected' : '' }}>⚫ Tidak Aktif</option>
              <option value="suspended" {{ $user->status === 'suspended' ? 'selected' : '' }}>🔴 Ditangguhkan</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Reset Password</label>
            <input type="text" name="password" class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}" placeholder="Kosongkan jika tidak diubah">
            @error('password')<div class="form-error">{{ $message }}</div>@enderror
          </div>
        </div>

        <div class="flex justify-end gap-2">
          <a href="{{ route('admin.members.index') }}" class="btn btn-secondary">Batal</a>
          <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>

  {{-- Member Info Card --}}
  <div class="card">
    <div class="card-header">
      <div style="display: flex; align-items: center; gap: 12px;">
        <div class="stat-card-icon indigo" style="width: 36px; height: 36px; border-radius: 10px;">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
        </div>
        <h3 style="margin: 0;">Info Anggota</h3>
      </div></div>
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

      @php
        $simpananPokok    = $user->getTotalSimpananByType('pokok');
        $simpananWajib    = $user->getTotalSimpananByType('wajib');
        $simpananSukarela = $user->getTotalSimpananByType('sukarela');
      @endphp

      <div style="display:grid;gap:12px;margin-bottom:24px;">
        @foreach([
          ['label'=>'Simpanan Pokok',   'val'=>$simpananPokok,    'color'=>'var(--brand-600)'],
          ['label'=>'Simpanan Wajib',   'val'=>$simpananWajib,    'color'=>'var(--accent-blue)'],
          ['label'=>'Simpanan Sukarela','val'=>$simpananSukarela, 'color'=>'var(--success)'],
        ] as $s)
        <div style="display:flex;justify-content:space-between;align-items:center;padding:14px 16px;background:var(--gray-50);border-radius:10px;border:1px solid var(--gray-100);">
          <span style="font-size:14px;color:var(--gray-600);font-weight:500;">{{ $s['label'] }}</span>
          <span class="money" style="font-size:14px;font-weight:700;color:{{ $s['color'] }};">Rp {{ number_format($s['val'],0,',','.') }}</span>
        </div>
        @endforeach
        <div style="display:flex;justify-content:space-between;align-items:center;padding:16px;background:var(--gray-900);border-radius:10px;margin-top:4px;">
          <span style="font-size:14px;color:white;font-weight:600;">Total Simpanan</span>
          <span class="money" style="font-size:16px;font-weight:800;color:white;">Rp {{ number_format($simpananPokok + $simpananWajib + $simpananSukarela,0,',','.') }}</span>
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
    </div>
  </div>
</div>
@endsection
