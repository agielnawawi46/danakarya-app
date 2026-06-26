@extends('layouts.app')
@section('title', 'Input Setoran')
@section('page_title', 'Input Setoran Tunai')

@section('content')
<div class="page-header">
  <div class="page-header-text">
    <h1 class="page-title">Input Setoran Tunai</h1>
    <p class="page-subtitle">Catat setoran simpanan anggota di loket</p>
  </div>
  <a href="{{ route('pengurus.deposits.index') }}" class="btn btn-secondary">← Kembali</a>
</div>

<div class="card">
  <div class="card-header">
      <div style="display: flex; align-items: center; gap: 12px;">
        <div class="stat-card-icon green" style="width: 36px; height: 36px; border-radius: 10px;">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="6" width="20" height="12" rx="2" ry="2"></rect><circle cx="12" cy="12" r="2"></circle><path d="M6 12h.01M18 12h.01"></path></svg>
        </div>
        <h3 style="margin: 0;">Form Setoran</h3>
      </div></div>
  <div class="card-body">
    <form method="POST" action="{{ route('pengurus.deposits.store') }}">
      @csrf

      <div class="grid grid-2" style="column-gap: 24px;">
        <div class="form-group">
        <label class="form-label" for="user_id">Anggota <span class="req">*</span></label>
        <div x-data="{
            search: '',
            open: false,
            selectedId: '{{ old('user_id') }}',
            selectedName: '{{ old('user_id') ? addslashes($members->firstWhere('id', old('user_id'))?->name ?? '') : '' }}',
            members: [
                @foreach($members as $m)
                { id: '{{ $m->id }}', name: '{{ addslashes($m->name) }}', detail: '{{ addslashes($m->employee_id ?? $m->email) }}' },
                @endforeach
            ],
            get filteredMembers() {
                if (this.search === '') return [];
                return this.members.filter(m => m.name.toLowerCase().includes(this.search.toLowerCase()) || m.detail.toLowerCase().includes(this.search.toLowerCase()));
            }
        }" style="position:relative;" @click.away="open = false">
          
          <input type="hidden" name="user_id" x-model="selectedId" required>
          
          {{-- Trigger Button --}}
          <div @click="open = !open" class="form-control {{ $errors->has('user_id') ? 'is-invalid' : '' }}" style="display:flex;justify-content:space-between;align-items:center;cursor:pointer;background:white;">
            <span x-text="selectedId ? selectedName : '-- Cari & Pilih Anggota --'" :style="!selectedId && 'color:var(--gray-400)'"></span>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"></polyline></svg>
          </div>

          {{-- Dropdown Panel --}}
          <div x-show="open" style="position:absolute;top:100%;left:0;right:0;background:white;border:1px solid var(--gray-200);border-radius:8px;margin-top:4px;box-shadow:0 10px 15px -3px rgba(0,0,0,0.1);z-index:50;max-height:280px;display:flex;flex-direction:column;overflow:hidden;" x-cloak>
            <div style="padding:8px;border-bottom:1px solid var(--gray-100);background:var(--gray-50);">
              <input type="text" x-model="search" class="form-control" placeholder="Ketik nama atau NIK..." style="padding:8px 12px;font-size:13px;" x-ref="searchInput" x-init="$watch('open', val => { if(val) setTimeout(() => $refs.searchInput.focus(), 50) })">
            </div>
            <div style="overflow-y:auto;flex:1;">
              
              {{-- State: Belum mengetik --}}
              <div x-show="search === ''" style="padding:16px;text-align:center;color:var(--gray-400);font-size:13px;">
                Mulai ketikkan nama atau NIK untuk mencari...
              </div>

              {{-- State: Tidak ditemukan --}}
              <div x-show="search !== '' && filteredMembers.length === 0" style="padding:16px;text-align:center;color:var(--gray-400);font-size:13px;">
                Anggota tidak ditemukan
              </div>

              {{-- State: Sudah mengetik --}}
              <div x-show="search !== ''">
                <template x-for="m in filteredMembers" :key="m.id">
                  <div @click="selectedId = m.id; selectedName = m.name; open = false; search = ''" 
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
        @error('user_id')<div class="form-error">{{ $message }}</div>@enderror
      </div>

      <div class="form-group">
        <label class="form-label" for="type">Jenis Simpanan</label>
        <input type="text" class="form-control" value="Simpanan Sukarela" readonly style="background:var(--gray-50);color:var(--gray-500);cursor:not-allowed;">
        <input type="hidden" name="type" value="sukarela">
        <div style="font-size:12px;color:var(--gray-400);margin-top:4px;">
          *Simpanan Pokok & Wajib diproses otomatis via payroll. Loket tunai ini khusus melayani Simpanan Sukarela.
        </div>
      </div>

      <div class="form-group">
        <label class="form-label" for="amount">Jumlah (Rp) <span class="req">*</span></label>
        <input type="number" id="amount" name="amount" value="{{ old('amount') }}"
          class="form-control {{ $errors->has('amount') ? 'is-invalid' : '' }}"
          placeholder="100000" min="1000" step="1000" required>
        @error('amount')<div class="form-error">{{ $message }}</div>@enderror
      </div>

      <div class="form-group">
        <label class="form-label" for="notes">Keterangan</label>
        <textarea id="notes" name="notes" class="form-control" rows="2"
          placeholder="Catatan tambahan (opsional)">{{ old('notes') }}</textarea>
      </div>
      </div>

      <div style="background:var(--gray-50);border-radius:8px;padding:12px;margin-bottom:16px;font-size:13px;color:var(--gray-500);">
        ⚡ Setoran akan langsung dicatat sebagai <strong>selesai</strong> dan jurnal akuntansi dibuat otomatis.
      </div>

      <div class="flex justify-end gap-2">
        <a href="{{ route('pengurus.deposits.index') }}" class="btn btn-secondary">Batal</a>
        <button type="submit" class="btn btn-primary">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
          Catat Setoran
        </button>
      </div>
    </form>
  </div>
</div>
@endsection
