@extends('layouts.app')
@section('title', 'Loket Simpanan')
@section('page_title', 'Loket Simpanan')

@section('content')
<div class="page-header">
  <div class="page-header-text">
    <h1 class="page-title">Loket Simpanan</h1>
    <p class="page-subtitle">Kelola setoran simpanan anggota koperasi</p>
  </div>
  <a href="{{ route('pengurus.deposits.create') }}" class="btn btn-primary">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
    Input Setoran Tunai
  </a>
</div>

{{-- Filter --}}
<form class="flex gap-2" style="margin-bottom:16px; align-items: stretch;">
  
  <div x-data="{
      search: '',
      open: false,
      selectedName: '{{ request('search') }}',
      members: [
          @foreach($members as $m)
          { id: '{{ $m->id }}', name: '{{ addslashes($m->name) }}', detail: '{{ addslashes($m->employee_id ?? $m->email) }}' },
          @endforeach
      ],
      get filteredMembers() {
          if (this.search === '') return [];
          return this.members.filter(m => m.name.toLowerCase().includes(this.search.toLowerCase()) || m.detail.toLowerCase().includes(this.search.toLowerCase()));
      }
  }" style="position:relative; width: 250px;" @click.away="open = false">
    
    <input type="hidden" name="search" x-model="selectedName">
    
    {{-- Trigger --}}
    <div @click="open = !open" class="form-control" style="display:flex;justify-content:space-between;align-items:center;cursor:pointer;background:white;height:100%;">
      <span x-text="selectedName ? selectedName : 'Cari nama anggota...'" :style="!selectedName && 'color:var(--gray-400)'" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"></span>
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;"><polyline points="6 9 12 15 18 9"></polyline></svg>
    </div>

    {{-- Dropdown --}}
    <div x-show="open" style="position:absolute;top:100%;left:0;right:0;background:white;border:1px solid var(--gray-200);border-radius:8px;margin-top:4px;box-shadow:0 10px 15px -3px rgba(0,0,0,0.1);z-index:50;max-height:280px;display:flex;flex-direction:column;overflow:hidden;" x-cloak>
      <div style="padding:8px;border-bottom:1px solid var(--gray-100);background:var(--gray-50);">
        <input type="text" x-model="search" class="form-control" placeholder="Ketik nama atau NIK..." style="padding:8px 12px;font-size:13px;" x-ref="searchInput" x-init="$watch('open', val => { if(val) setTimeout(() => $refs.searchInput.focus(), 50) })" @keydown.enter.prevent="selectedName = search; open = false; $nextTick(() => document.getElementById('filter-btn').click())">
      </div>
      <div style="overflow-y:auto;flex:1;">
        
        <div x-show="search === ''" style="padding:16px;text-align:center;color:var(--gray-400);font-size:13px;">
          Mulai ketikkan nama atau NIK...
        </div>

        <div x-show="search !== ''">
          <template x-for="m in filteredMembers" :key="m.id">
            <div @click="selectedName = m.name; open = false; search = ''; $nextTick(() => document.getElementById('filter-btn').click())" 
                 style="padding:10px 12px;cursor:pointer;border-bottom:1px solid var(--gray-50);transition:background .1s;"
                 onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background='white'">
              <div class="font-semibold" x-text="m.name" style="font-size:14px;color:var(--gray-900);"></div>
              <div style="font-size:12px;color:var(--gray-500);" x-text="m.detail"></div>
            </div>
          </template>
          <div x-show="filteredMembers.length === 0" style="padding:16px;text-align:center;color:var(--gray-400);font-size:13px;">
            Anggota tidak ditemukan
          </div>
        </div>

      </div>
    </div>
  </div>

  <select name="type" class="form-control" style="max-width:150px;">
    <option value="">Semua Jenis</option>
    <option value="pokok"    {{ request('type') === 'pokok'    ? 'selected' : '' }}>Pokok</option>
    <option value="wajib"    {{ request('type') === 'wajib'    ? 'selected' : '' }}>Wajib</option>
    <option value="sukarela" {{ request('type') === 'sukarela' ? 'selected' : '' }}>Sukarela</option>
  </select>
  <select name="status" class="form-control" style="max-width:150px;">
    <option value="">Semua Status</option>
    <option value="pending"   {{ request('status') === 'pending'   ? 'selected' : '' }}>Pending</option>
    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai</option>
    <option value="rejected"  {{ request('status') === 'rejected'  ? 'selected' : '' }}>Ditolak</option>
  </select>
  <button id="filter-btn" class="btn btn-secondary">Filter</button>
  @if(request()->hasAny(['search','type','status']))
    <a href="{{ route('pengurus.deposits.index') }}" class="btn btn-secondary" style="display:flex;align-items:center;justify-content:center;">Reset</a>
  @endif
</form>

<div class="card">
  <div class="table-wrapper" style="border:none;">
    <table>
      <thead>
        <tr>
          <th>Tanggal</th>
          <th>Anggota</th>
          <th>Jenis Simpanan</th>
          <th>Jumlah</th>
          <th>Status</th>
          <th>Dicatat oleh</th>
        </tr>
      </thead>
      <tbody>
        @forelse($deposits as $deposit)
        <tr>
          <td style="font-size:12px;color:var(--gray-400);">{{ $deposit->created_at->format('d/m/Y H:i') }}</td>
          <td>
            <div class="font-semibold">{{ $deposit->user?->name ?? '-' }}</div>
            <div class="text-sm text-muted">{{ $deposit->user?->employee_id }}</div>
          </td>
          <td>
            @php
              $typeColors = ['pokok'=>'badge-primary','wajib'=>'badge-info','sukarela'=>'badge-success'];
              $typeLabels = ['pokok'=>'Simpanan Pokok','wajib'=>'Simpanan Wajib','sukarela'=>'Simpanan Sukarela'];
            @endphp
            <span class="badge {{ $typeColors[$deposit->type] ?? 'badge-secondary' }}">
              {{ $typeLabels[$deposit->type] ?? ucfirst($deposit->type) }}
            </span>
          </td>
          <td class="money text-success font-bold">Rp {{ number_format($deposit->amount, 0, ',', '.') }}</td>
          <td>{!! $deposit->getStatusBadge() !!}</td>
          <td style="font-size:12px;color:var(--gray-400);">
            {{ $deposit->processedBy?->name ?? '-' }}
          </td>
        </tr>
        @empty
        <tr><td colspan="6" class="text-center text-muted" style="padding:40px;">Belum ada data setoran</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="card-footer">{{ $deposits->withQueryString()->links() }}</div>
</div>
@endsection
