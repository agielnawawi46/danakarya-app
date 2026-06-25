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

<div class="card" style="max-width:540px;">
  <div class="card-header"><h3>💵 Form Setoran</h3></div>
  <div class="card-body">
    <form method="POST" action="{{ route('pengurus.deposits.store') }}">
      @csrf

      <div class="form-group">
        <label class="form-label" for="user_id">Anggota <span class="req">*</span></label>
        <select id="user_id" name="user_id" class="form-control {{ $errors->has('user_id') ? 'is-invalid' : '' }}" required>
          <option value="">-- Pilih Anggota --</option>
          @foreach($members as $member)
            <option value="{{ $member->id }}" {{ old('user_id') == $member->id ? 'selected' : '' }}>
              {{ $member->name }} — {{ $member->employee_id ?? $member->email }}
            </option>
          @endforeach
        </select>
        @error('user_id')<div class="form-error">{{ $message }}</div>@enderror
      </div>

      <div class="form-group">
        <label class="form-label" for="type">Jenis Simpanan <span class="req">*</span></label>
        <select id="type" name="type" class="form-control {{ $errors->has('type') ? 'is-invalid' : '' }}" required>
          <option value="">-- Pilih Jenis --</option>
          <option value="pokok"    {{ old('type') === 'pokok'    ? 'selected' : '' }}>Simpanan Pokok (dibayar sekali)</option>
          <option value="wajib"    {{ old('type') === 'wajib'    ? 'selected' : '' }}>Simpanan Wajib (bulanan)</option>
          <option value="sukarela" {{ old('type') === 'sukarela' ? 'selected' : '' }}>Simpanan Sukarela</option>
        </select>
        @error('type')<div class="form-error">{{ $message }}</div>@enderror
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
