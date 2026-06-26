@extends('layouts.app')
@section('title', 'Kredit Pinjaman')
@section('page_title', 'Kredit Pinjaman')

@section('content')
<div class="page-header">
  <div class="page-header-text">
    <h1 class="page-title">Kredit Pinjaman</h1>
    <p class="page-subtitle">Review dan kelola pengajuan serta angsuran pinjaman anggota</p>
  </div>
</div>

{{-- Filter --}}
<form class="flex gap-2" style="margin-bottom:16px;">
  <div x-data="{
      search: '',
      open: false,
      selectedId: '{{ request('search') }}',
      selectedName: '{{ request('search') ? addslashes($members->firstWhere('id', request('search'))?->name ?? '') : '' }}',
      members: [
          @foreach($members as $m)
          { id: '{{ $m->id }}', name: '{{ addslashes($m->name) }}', detail: '{{ addslashes($m->employee_id ?? $m->email) }}' },
          @endforeach
      ],
      get filteredMembers() {
          if (this.search === '') return [];
          return this.members.filter(m => m.name.toLowerCase().includes(this.search.toLowerCase()) || m.detail.toLowerCase().includes(this.search.toLowerCase()));
      }
  }" style="position:relative; width:280px;" @click.away="open = false">
    
    <input type="hidden" name="search" x-model="selectedId">
    
    {{-- Trigger Button --}}
    <div @click="open = !open" class="form-control" style="display:flex;justify-content:space-between;align-items:center;cursor:pointer;background:white;">
      <span x-text="selectedId ? selectedName : 'Cari nama anggota...'" :style="!selectedId && 'color:var(--gray-400)'"></span>
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"></polyline></svg>
    </div>

    {{-- Dropdown Panel --}}
    <div x-show="open" style="position:absolute;top:100%;left:0;right:0;background:white;border:1px solid var(--gray-200);border-radius:8px;margin-top:4px;box-shadow:0 10px 15px -3px rgba(0,0,0,0.1);z-index:50;max-height:280px;display:flex;flex-direction:column;overflow:hidden;" x-cloak>
      <div style="padding:8px;border-bottom:1px solid var(--gray-100);background:var(--gray-50);">
        <input type="text" x-model="search" class="form-control" placeholder="Ketik nama atau NIK..." style="padding:8px 12px;font-size:13px;" x-ref="searchInput" x-init="$watch('open', val => { if(val) setTimeout(() => $refs.searchInput.focus(), 50) })">
      </div>
      <div style="overflow-y:auto;flex:1;">
        {{-- State: Belum mengetik --}}
        <div x-show="search === ''" style="padding:20px;text-align:center;color:var(--gray-400);font-size:13px;">
          Ketik untuk mencari anggota
        </div>
        {{-- State: Tidak ditemukan --}}
        <div x-show="search !== '' && filteredMembers.length === 0" style="padding:20px;text-align:center;color:var(--gray-400);font-size:13px;">
          Anggota tidak ditemukan
        </div>
        {{-- List Anggota --}}
        <template x-for="m in filteredMembers" :key="m.id">
          <div @click="selectedId = m.id; selectedName = m.name; open = false; search = ''" 
               style="padding:10px 12px;cursor:pointer;border-bottom:1px solid var(--gray-50);transition:all .1s;"
               onmouseover="this.style.background='var(--brand-50)'" 
               onmouseout="this.style.background='white'">
            <div style="font-weight:600;font-size:13px;color:var(--gray-900);" x-text="m.name"></div>
            <div style="font-size:11px;color:var(--gray-500);margin-top:2px;" x-text="m.detail"></div>
          </div>
        </template>
      </div>
    </div>
  </div>
  <select name="status" class="form-control" style="max-width:150px;">
    <option value="">Semua Status</option>
    @foreach(['pending'=>'Menunggu','approved'=>'Disetujui','active'=>'Aktif','rejected'=>'Ditolak','paid_off'=>'Lunas'] as $val => $label)
      <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>{{ $label }}</option>
    @endforeach
  </select>
  <button class="btn btn-secondary">Filter</button>
  @if(request()->hasAny(['search','status']))
    <a href="{{ route('pengurus.loans.index') }}" class="btn btn-secondary">Reset</a>
  @endif
</form>

<div class="card">
  <div class="table-wrapper" style="border:none;">
    <table>
      <thead>
        <tr>
          <th>Tanggal</th>
          <th>Anggota</th>
          <th>Jumlah</th>
          <th>Tenor</th>
          <th>Skor Kredit</th>
          <th>Status</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($loans as $loan)
        <tr>
          <td style="font-size:12px;color:var(--gray-400);">{{ $loan->created_at->format('d/m/Y') }}</td>
          <td>
            <div class="font-semibold">{{ $loan->user?->name ?? '-' }}</div>
            <div class="text-sm text-muted">{{ $loan->user?->department }}</div>
          </td>
          <td class="money font-bold">Rp {{ number_format($loan->amount, 0, ',', '.') }}</td>
          <td>{{ $loan->tenor_months }} bln</td>
          <td>
            @if($loan->credit_score !== null)
              <span class="badge {{ $loan->credit_score <= 30 ? 'badge-success' : 'badge-danger' }}">
                {{ $loan->credit_score }}%
              </span>
            @else
              <span class="text-muted">—</span>
            @endif
          </td>
          <td>{!! $loan->getStatusBadge() !!}</td>
          <td>
            <a href="{{ route('pengurus.loans.show', $loan) }}" class="btn btn-secondary btn-sm">
              {{ $loan->status === 'pending' ? 'Review' : 'Detail' }}
            </a>
          </td>
        </tr>
        @empty
        <tr><td colspan="7" class="text-center text-muted" style="padding:40px;">Tidak ada data pinjaman</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="card-footer">{{ $loans->withQueryString()->links() }}</div>
</div>
@endsection
