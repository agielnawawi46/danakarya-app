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

<div class="grid grid-2" style="align-items:start;">
  {{-- Edit Form --}}
  <div class="card">
    <div class="card-header"><h3>✏️ Data Anggota</h3></div>
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
        <div class="form-group">
          <label class="form-label">Status Anggota</label>
          <select name="status" class="form-control">
            <option value="active"    {{ $user->status === 'active'    ? 'selected' : '' }}>✅ Aktif</option>
            <option value="inactive"  {{ $user->status === 'inactive'  ? 'selected' : '' }}>⚫ Tidak Aktif</option>
            <option value="suspended" {{ $user->status === 'suspended' ? 'selected' : '' }}>🔴 Ditangguhkan</option>
          </select>
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
    <div class="card-header"><h3>👤 Info Anggota</h3></div>
    <div class="card-body">
      <div style="display:flex;align-items:center;gap:14px;margin-bottom:20px;">
        <div style="width:56px;height:56px;background:linear-gradient(135deg,var(--brand-500),var(--accent-violet));border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:22px;font-weight:900;color:white;">
          {{ strtoupper(substr($user->name, 0, 2)) }}
        </div>
        <div>
          <div style="font-size:16px;font-weight:700;color:var(--gray-900);">{{ $user->name }}</div>
          <div style="font-size:13px;color:var(--gray-400);">{{ $user->email }}</div>
          @foreach($user->getRoleNames() as $role)
            <span class="badge badge-primary" style="margin-top:4px;">{{ ucfirst($role) }}</span>
          @endforeach
        </div>
      </div>

      @php
        $simpananPokok    = $user->getTotalSimpananByType('pokok');
        $simpananWajib    = $user->getTotalSimpananByType('wajib');
        $simpananSukarela = $user->getTotalSimpananByType('sukarela');
      @endphp

      <div style="display:grid;gap:8px;">
        @foreach([
          ['label'=>'Simpanan Pokok',   'val'=>$simpananPokok,    'color'=>'var(--brand-600)'],
          ['label'=>'Simpanan Wajib',   'val'=>$simpananWajib,    'color'=>'var(--accent-blue)'],
          ['label'=>'Simpanan Sukarela','val'=>$simpananSukarela, 'color'=>'var(--success)'],
          ['label'=>'Total Simpanan',   'val'=>$simpananPokok + $simpananWajib + $simpananSukarela,'color'=>'var(--gray-900)'],
        ] as $s)
        <div style="display:flex;justify-content:space-between;padding:10px 12px;background:var(--gray-50);border-radius:8px;">
          <span style="font-size:13px;color:var(--gray-500);">{{ $s['label'] }}</span>
          <span class="money" style="font-weight:700;color:{{ $s['color'] }};">Rp {{ number_format($s['val'],0,',','.') }}</span>
        </div>
        @endforeach
      </div>

      @if($user->join_date)
      <div style="margin-top:16px;font-size:13px;color:var(--gray-400);">
        Bergabung: <strong>{{ $user->join_date->format('d F Y') }}</strong>
        ({{ $user->join_date->diffForHumans() }})
      </div>
      @endif
    </div>
  </div>
</div>
@endsection
