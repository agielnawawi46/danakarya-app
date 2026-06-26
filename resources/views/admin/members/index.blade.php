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
    <a href="{{ route('admin.members.create') }}" class="btn btn-primary btn-sm">
      + Tambah Anggota
    </a>
  </div>
</div>

{{-- Import Form --}}
<div class="card" style="margin-bottom:20px;">
  <div class="card-header">
      <div style="display: flex; align-items: center; gap: 12px;">
        <div class="stat-card-icon blue" style="width: 36px; height: 36px; border-radius: 10px;">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="8 17 12 21 16 17"></polyline><line x1="12" y1="12" x2="12" y2="21"></line><path d="M20.88 18.09A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.29"></path></svg>
        </div>
        <h3 style="margin: 0;">Import Massal dari CSV</h3>
      </div></div>
  <div class="card-body">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 16px; padding-bottom: 16px; border-bottom: 1px dashed var(--gray-200);">
      <div>
        <div style="font-weight: 600; font-size: 14px;">1. Download Template CSV</div>
        <div class="form-hint" style="margin-top: 4px;">Gunakan template ini untuk mengisi data anggota secara massal.</div>
      </div>
      <a href="{{ route('admin.members.template') }}" class="btn btn-primary" style="width: 190px; justify-content: center;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
        Download Template
      </a>
    </div>

    <form method="POST" action="{{ route('admin.members.import') }}" enctype="multipart/form-data" style="display:flex; justify-content:space-between; align-items:center;">
      @csrf
      <div>
        <div style="font-weight: 600; font-size: 14px; margin-bottom: 4px;">2. Upload Data Anggota (CSV)</div>
        <div class="form-hint" style="margin-bottom: 8px;">Upload file CSV yang sudah diisi sesuai template.</div>
        <input type="file" name="file" class="form-control" accept=".csv,.txt" style="max-width:360px;" required>
      </div>
      
      <button type="submit" class="btn btn-primary" style="width: 190px; justify-content: center;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 0-4-4H5a2 2 0 0 0-4 4v2"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
        Upload & Import
      </button>
    </form>
  </div>
</div>

{{-- Search --}}
<form class="flex gap-2" style="margin-bottom:16px;">
  <div x-data="{
      search: '{{ addslashes(request('search')) }}',
      open: false,
      members: [
          @foreach($allMembersForAutocomplete as $m)
          { text: '{{ addslashes($m->name) }}', detail: '{{ addslashes($m->employee_id ?? $m->email) }}' },
          @endforeach
      ],
      get filteredMembers() {
          if (this.search === '') return [];
          return this.members.filter(m => m.text.toLowerCase().includes(this.search.toLowerCase()) || m.detail.toLowerCase().includes(this.search.toLowerCase())).slice(0, 10);
      }
  }" style="position:relative; width: 300px;" @click.away="open = false">
    
    <input type="text" name="search" x-model="search" @focus="open = true" @input="open = true" autocomplete="off" class="form-control" style="width:100%;" placeholder="Cari nama / email / NIK...">
    
    <div x-show="open && filteredMembers.length > 0" style="position:absolute;top:100%;left:0;right:0;background:white;border:1px solid var(--gray-200);border-radius:8px;margin-top:4px;box-shadow:0 10px 15px -3px rgba(0,0,0,0.1);z-index:50;max-height:280px;display:flex;flex-direction:column;overflow:hidden;" x-cloak>
      <div style="overflow-y:auto;flex:1;">
          <template x-for="m in filteredMembers" :key="m.text + m.detail">
            <div @click="search = m.text; open = false;" 
                 style="padding:10px 12px;cursor:pointer;border-bottom:1px solid var(--gray-50);transition:background .1s;"
                 onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background='white'">
              <div class="font-semibold" x-text="m.text" style="font-size:14px;color:var(--gray-900);"></div>
              <div style="font-size:12px;color:var(--gray-500);" x-text="m.detail"></div>
            </div>
          </template>
      </div>
    </div>
  </div>
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
