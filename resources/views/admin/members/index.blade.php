@extends('layouts.app')
@section('title', 'Manajemen Anggota')
@section('page_title', 'Manajemen Anggota')

@section('content')
<div class="page-header">
  <div class="page-header-text">
    <h1 class="page-title">Manajemen Anggota</h1>
    <p class="page-subtitle">Kelola data anggota, pengurus, dan pengawas koperasi</p>
  </div>
  <div class="flex gap-2">
    <a href="{{ route('admin.members.template') }}" class="btn btn-secondary btn-sm">
      ⬇ Template Import
    </a>
    <a href="{{ route('admin.members.create') }}" class="btn btn-primary btn-sm">
      + Tambah Anggota
    </a>
  </div>
</div>

{{-- Import Form --}}
<div class="card" style="margin-bottom:20px;">
  <div class="card-header"><h3>📥 Import Massal dari CSV</h3></div>
  <div class="card-body">
    <form method="POST" action="{{ route('admin.members.import') }}" enctype="multipart/form-data" class="flex items-center gap-2">
      @csrf
      <input type="file" name="file" class="form-control" accept=".csv,.txt" style="max-width:300px;">
      <button type="submit" class="btn btn-primary btn-sm">Upload & Import</button>
    </form>
  </div>
</div>

{{-- Search --}}
<form class="flex gap-2" style="margin-bottom:16px;">
  <input name="search" class="form-control" style="max-width:300px;" placeholder="Cari nama / email / NIK..." value="{{ request('search') }}">
  <select name="role" class="form-control" style="max-width:150px;">
    <option value="">Semua Role</option>
    @foreach($roles as $role)
      <option value="{{ $role }}" {{ request('role') == $role ? 'selected' : '' }}>{{ ucfirst($role) }}</option>
    @endforeach
  </select>
  <button class="btn btn-secondary">Filter</button>
  @if(request('search') || request('role'))
    <a href="{{ route('admin.members.index') }}" class="btn btn-secondary">Reset</a>
  @endif
</form>

<div class="card">
  <div class="table-wrapper" style="border:none;">
    <table>
      <thead>
        <tr>
          <th>NIK</th>
          <th>Nama & Email</th>
          <th>Role</th>
          <th>Departemen</th>
          <th>Gaji Pokok</th>
          <th>Status</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($members as $m)
        <tr>
          <td style="font-size:12px;color:var(--gray-400);">{{ $m->employee_id ?? '-' }}</td>
          <td>
            <div class="font-semibold">{{ $m->name }}</div>
            <div class="text-sm text-muted">{{ $m->email }}</div>
          </td>
          <td>
            @foreach($m->getRoleNames() as $role)
              <span class="badge badge-primary role-{{ $role }}">{{ ucfirst($role) }}</span>
            @endforeach
          </td>
          <td>{{ $m->department ?? '-' }}</td>
          <td class="money">
            @if($m->salary) Rp {{ number_format($m->salary, 0, ',', '.') }} @else <span class="text-muted">-</span> @endif
          </td>
          <td>{!! $m->getStatusBadge() !!}</td>
          <td>
            <a href="{{ route('admin.members.edit', $m) }}" class="btn btn-secondary btn-sm">Edit</a>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="7" style="text-align:center;padding:40px;color:var(--gray-400);">
            Belum ada anggota. <a href="{{ route('admin.members.create') }}" style="color:var(--brand-600);">Tambahkan sekarang →</a>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="card-footer">
    {{ $members->withQueryString()->links() }}
  </div>
</div>
@endsection
