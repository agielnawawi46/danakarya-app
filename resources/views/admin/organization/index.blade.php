@extends('layouts.app')
@section('title', 'Profil Koperasi')
@section('page_title', 'Profil Koperasi')

@section('content')
<div class="page-header">
  <div class="page-header-text">
    <h1 class="page-title">Profil Koperasi</h1>
    <p class="page-subtitle">Kelola identitas dan informasi resmi koperasi</p>
  </div>
  <div class="page-header-actions">
    <button type="button" class="btn btn-primary" id="toggleFormBtn" onclick="toggleForm()">
      <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
      <span>Pengaturan Profil</span>
    </button>
  </div>
</div>

<div class="grid" id="mainGrid" style="align-items:stretch;">
  {{-- Preview --}}
  <div class="card">
    <div class="card-header">
      <div style="display: flex; align-items: center; gap: 12px;">
        <div class="stat-card-icon blue" style="width: 36px; height: 36px; border-radius: 10px;">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect></svg>
        </div>
        <h3 style="margin: 0;">Ringkasan Profil Koperasi</h3>
      </div></div>
    <div class="card-body">
      @if($org->logo)
      <div style="text-align:center;margin-bottom:20px;">
        <img src="{{ asset('storage/'.$org->logo) }}" alt="Logo" style="height:80px;border-radius:8px;object-fit:contain;">
      </div>
      @endif
      
      @foreach([
        ['label'=>'Nama Koperasi','value'=>$org->name],
        ['label'=>'Nama Badan Hukum','value'=>$org->legal_name ?? '-'],
        ['label'=>'No. Badan Hukum','value'=>$org->legal_number ?? '-'],
        ['label'=>'Telepon','value'=>$org->phone ?? '-'],
        ['label'=>'Email','value'=>$org->email ?? '-'],
        ['label'=>'Alamat','value'=>$org->address ?? '-'],
        ['label'=>'Terdaftar Pada','value'=>$org->created_at ? $org->created_at->format('d M Y') : '-'],
        ['label'=>'Status','value'=>$org->is_active ? 'Aktif' : 'Tidak Aktif'],
      ] as $item)
      <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--gray-100);">
        <span style="font-size:13px;color:var(--gray-500);">{{ $item['label'] }}</span>
        <span style="font-size:13px;font-weight:700;color:var(--gray-900);text-align:right;max-width:200px;word-break:break-word;">{{ $item['value'] }}</span>
      </div>
      @endforeach
    </div>
  </div>

  {{-- Form --}}
  <div class="card" id="formCard" style="display: none;">
    <div class="card-header">
      <div style="display: flex; align-items: center; gap: 12px;">
        <div class="stat-card-icon violet" style="width: 36px; height: 36px; border-radius: 10px;">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
        </div>
        <h3 style="margin: 0;">Pengaturan Profil</h3>
      </div></div>
    <div class="card-body">
      <form method="POST" action="{{ route('admin.organization.update') }}" enctype="multipart/form-data">
        @csrf
        <div class="form-row">
          <div class="form-group">
            <label class="form-label" for="name">Nama Koperasi <span class="req">*</span></label>
            <input type="text" id="name" name="name" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" value="{{ old('name', $org->name) }}" required>
          </div>
          <div class="form-group">
            <label class="form-label" for="legal_name">Nama Badan Hukum</label>
            <input type="text" id="legal_name" name="legal_name" class="form-control" value="{{ old('legal_name', $org->legal_name) }}">
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label class="form-label" for="legal_number">No. Badan Hukum</label>
            <input type="text" id="legal_number" name="legal_number" class="form-control" value="{{ old('legal_number', $org->legal_number) }}">
          </div>
          <div class="form-group">
            <label class="form-label" for="phone">Telepon</label>
            <input type="text" id="phone" name="phone" class="form-control" value="{{ old('phone', $org->phone) }}">
          </div>
        </div>
        <div class="form-group">
          <label class="form-label" for="email">Email</label>
          <input type="email" id="email" name="email" class="form-control" value="{{ old('email', $org->email) }}">
        </div>
        <div class="form-group">
          <label class="form-label" for="address">Alamat</label>
          <textarea id="address" name="address" class="form-control">{{ old('address', $org->address) }}</textarea>
        </div>
        <div class="form-group">
          <label class="form-label" for="logo">Logo Baru (Opsional)</label>
          <input type="file" id="logo" name="logo" class="form-control" accept="image/*">
        </div>
        
        <div style="display: flex; gap: 10px; margin-top: 24px;">
          <button type="button" class="btn btn-secondary" style="flex: 1; justify-content: center;" onclick="toggleForm()">Batal</button>
          <button type="submit" class="btn btn-primary" style="flex: 1; justify-content: center;">Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
function toggleForm() {
    var formCard = document.getElementById('formCard');
    var btnText = document.querySelector('#toggleFormBtn span');
    var mainGrid = document.getElementById('mainGrid');
    
    if (formCard.style.display === 'none') {
        formCard.style.display = 'block';
        mainGrid.classList.add('grid-2');
        btnText.innerText = 'Tutup';
    } else {
        formCard.style.display = 'none';
        mainGrid.classList.remove('grid-2');
        btnText.innerText = 'Pengaturan Profil';
    }
}
</script>
@endsection
