@extends('layouts.app')
@section('title', 'Ajukan Penarikan')
@section('page_title', 'Ajukan Penarikan Sukarela')

@section('content')
<div class="page-header">
  <div class="page-header-text">
    <h1 class="page-title">Ajukan Penarikan Simpanan</h1>
    <p class="page-subtitle">Hanya simpanan sukarela yang dapat ditarik</p>
  </div>
  <a href="{{ route('member.deposits.index') }}" class="btn btn-secondary">← Kembali</a>
</div>

<div class="grid grid-2" style="align-items:start;max-width:900px;">
  <div class="card">
    <div class="card-header">
      <div style="display: flex; align-items: center; gap: 12px;">
        <div class="stat-card-icon green" style="width: 36px; height: 36px; border-radius: 10px;">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="6" width="20" height="12" rx="2" ry="2"></rect><circle cx="12" cy="12" r="2"></circle><path d="M6 12h.01M18 12h.01"></path></svg>
        </div>
        <h3 style="margin: 0;">Form Penarikan</h3>
      </div></div>
    <div class="card-body">
      <div style="background:var(--brand-50);border:1px solid var(--brand-200);border-radius:8px;padding:14px;margin-bottom:20px;">
        <div style="font-size:12px;color:var(--brand-700);font-weight:600;text-transform:uppercase;letter-spacing:.06em;">Saldo Simpanan Sukarela</div>
        <div class="money" style="font-size:1.75rem;font-weight:900;color:var(--brand-700);margin-top:4px;">
          Rp {{ number_format($balance, 0, ',', '.') }}
        </div>
      </div>

      @if($balance <= 0)
        <div class="alert alert-warning">⚠️ Saldo simpanan sukarela Anda kosong.</div>
      @else
      <form method="POST" action="{{ route('member.deposits.withdraw.store') }}">
        @csrf
        <div class="form-group">
          <label class="form-label" for="amount">Jumlah Penarikan (Rp) <span class="req">*</span></label>
          <input type="number" id="amount" name="amount"
            class="form-control {{ $errors->has('amount') ? 'is-invalid' : '' }}"
            value="{{ old('amount') }}"
            min="10000" max="{{ $balance }}" step="10000"
            placeholder="Minimal Rp 10.000" required>
          @error('amount')<div class="form-error">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
          <label class="form-label" for="notes">Keterangan</label>
          <textarea id="notes" name="notes" class="form-control" rows="2"
            placeholder="Tujuan penarikan (opsional)">{{ old('notes') }}</textarea>
        </div>
        <div style="background:var(--gray-50);border-radius:8px;padding:12px;margin-bottom:16px;font-size:13px;color:var(--gray-500);">
          ℹ️ Permintaan penarikan akan diproses oleh pengurus dalam 1–2 hari kerja.
        </div>
        <div class="flex justify-end gap-2">
          <a href="{{ route('member.deposits.index') }}" class="btn btn-secondary">Batal</a>
          <button type="submit" class="btn btn-primary">Ajukan Penarikan</button>
        </div>
      </form>
      @endif
    </div>
  </div>

  <div class="card">
    <div class="card-header">
      <div style="display: flex; align-items: center; gap: 12px;">
        <div class="stat-card-icon blue" style="width: 36px; height: 36px; border-radius: 10px;">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
        </div>
        <h3 style="margin: 0;">Ketentuan Penarikan</h3>
      </div></div>
    <div class="card-body">
      <ul style="list-style:none;display:flex;flex-direction:column;gap:12px;">
        @foreach([
          ['icon'=>'💎','text'=>'Simpanan Pokok tidak dapat ditarik (hanya saat keluar koperasi)'],
          ['icon'=>'📅','text'=>'Simpanan Wajib tidak dapat ditarik per saldo (hanya saat keluar koperasi)'],
          ['icon'=>'💵','text'=>'Simpanan Sukarela dapat ditarik kapan saja sesuai saldo tersedia'],
          ['icon'=>'⏱️','text'=>'Proses verifikasi 1–2 hari kerja oleh pengurus koperasi'],
          ['icon'=>'🔒','text'=>'Hanya 1 permintaan penarikan yang dapat aktif dalam satu waktu'],
        ] as $item)
        <li style="display:flex;gap:10px;align-items:flex-start;">
          <span style="font-size:18px;flex-shrink:0;">{{ $item['icon'] }}</span>
          <span style="font-size:13px;color:var(--gray-600);line-height:1.5;">{{ $item['text'] }}</span>
        </li>
        @endforeach
      </ul>
    </div>
  </div>
</div>
@endsection
