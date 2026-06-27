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

<div class="grid grid-2" style="align-items:stretch;gap:24px;">
  {{-- Left Column: Balance & Form --}}
  <div style="display:flex;flex-direction:column;gap:24px;">
    
    {{-- Gradient Balance Card --}}
    <div class="card" style="background:linear-gradient(135deg, var(--brand-600), var(--brand-800)); border:none; position:relative; overflow:hidden;">
      <div style="position:absolute; right:-20px; top:-40px; width:150px; height:150px; background:rgba(255,255,255,0.05); border-radius:50%;"></div>
      <div style="position:absolute; right:40px; bottom:-30px; width:100px; height:100px; background:rgba(255,255,255,0.05); border-radius:50%;"></div>
      
      <div class="card-body" style="padding:28px 24px; position:relative; z-index:1; display:flex; align-items:center; justify-content:space-between;">
        <div>
          <div style="font-size:12px; font-weight:700; color:rgba(255,255,255,0.7); text-transform:uppercase; letter-spacing:0.1em; margin-bottom:8px;">Saldo Simpanan Sukarela</div>
          <div class="money" style="font-size:2.25rem; font-weight:900; color:white; line-height:1; letter-spacing:-0.02em;">Rp {{ number_format($balance, 0, ',', '.') }}</div>
        </div>
        <div style="width:56px;height:56px;background:rgba(255,255,255,0.15);border-radius:16px;display:flex;align-items:center;justify-content:center;backdrop-filter:blur(4px);">
          <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><path d="M21 12V7H5a2 2 0 0 1 0-4h14v4"></path><path d="M3 5v14a2 2 0 0 0 2 2h16v-5"></path><path d="M18 12a2 2 0 0 0 0 4h4v-4Z"></path></svg>
        </div>
      </div>
    </div>

    {{-- Form Card --}}
    <div class="card" style="display:flex;flex-direction:column;flex:1;">
      <div class="card-header">
        <div style="display: flex; align-items: center; gap: 12px;">
          <div class="stat-card-icon green" style="width: 36px; height: 36px; border-radius: 10px;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
          </div>
          <h3 style="margin: 0;">Buat Penarikan Baru</h3>
        </div>
      </div>
      <div class="card-body" style="flex:1;display:flex;flex-direction:column;">
        @if($balance <= 0)
          <div class="alert alert-warning" style="margin-bottom:0;">⚠️ Saldo simpanan sukarela Anda kosong. Tidak dapat melakukan penarikan.</div>
        @else
        <form method="POST" action="{{ route('member.deposits.withdraw.store') }}" style="display:flex;flex-direction:column;flex:1;">
          @csrf
          <div class="form-group">
            <label class="form-label" for="amount">Jumlah Penarikan (Rp) <span class="req">*</span></label>
            <div style="position:relative;">
              <span style="position:absolute;left:14px;top:10px;color:var(--gray-500);font-weight:600;">Rp</span>
              <input type="number" id="amount" name="amount"
                class="form-control {{ $errors->has('amount') ? 'is-invalid' : '' }}"
                value="{{ old('amount') }}"
                min="10000" max="{{ $balance }}" step="10000"
                style="padding-left:42px; font-weight:bold; font-size:1.1rem; height:48px;"
                placeholder="0" required>
            </div>
            @error('amount')<div class="form-error">{{ $message }}</div>@enderror
            <div style="font-size:11px;color:var(--gray-500);margin-top:6px;">Minimal penarikan Rp 10.000</div>
          </div>
          
          <div class="form-group">
            <label class="form-label" for="notes">Keterangan (Opsional)</label>
            <textarea id="notes" name="notes" class="form-control" rows="2"
              style="resize:none;"
              placeholder="Tuliskan tujuan penarikan Anda...">{{ old('notes') }}</textarea>
          </div>
          
          <div style="display:flex;align-items:center;gap:12px;background:var(--brand-50);border:1px solid var(--brand-100);border-radius:8px;padding:12px 16px;margin-bottom:24px;margin-top:auto;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--brand-500)" stroke-width="2" style="flex-shrink:0;"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
            <div style="font-size:12px;color:var(--brand-700);line-height:1.4;">Permintaan penarikan Anda akan ditinjau dan diproses oleh pengurus dalam waktu <strong>1–2 hari kerja</strong>.</div>
          </div>
          
          <div class="flex justify-end gap-3">
            <a href="{{ route('member.deposits.index') }}" class="btn btn-secondary" style="padding:10px 20px;">Batal</a>
            <button type="submit" class="btn btn-primary" style="padding:10px 24px;">Ajukan Sekarang →</button>
          </div>
        </form>
        @endif
      </div>
    </div>
  </div>

  {{-- Right Column: Terms --}}
  <div class="card" style="display:flex;flex-direction:column;height:100%;">
    <div class="card-header">
      <div style="display: flex; align-items: center; gap: 12px;">
        <div class="stat-card-icon blue" style="width: 36px; height: 36px; border-radius: 10px;">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
        </div>
        <h3 style="margin: 0;">Ketentuan Penarikan</h3>
      </div>
    </div>
    <div class="card-body">
      <div style="display:flex;flex-direction:column;gap:16px;">
        @foreach([
          ['color'=>'var(--brand-50)','iconColor'=>'var(--brand-600)','icon'=>'<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>','title'=>'Simpanan Pokok Tertahan','desc'=>'Simpanan Pokok adalah kewajiban dasar keanggotaan dan tidak dapat ditarik selama masih berstatus anggota aktif.'],
          ['color'=>'#eff6ff','iconColor'=>'#3b82f6','icon'=>'<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>','title'=>'Simpanan Wajib Berkala','desc'=>'Simpanan Wajib rutin dibayar per bulan dan juga hanya dapat ditarik secara penuh saat anggota memutuskan keluar dari koperasi.'],
          ['color'=>'#ecfdf5','iconColor'=>'#10b981','icon'=>'<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>','title'=>'Simpanan Sukarela Bebas','desc'=>'Simpanan Sukarela adalah investasi bebas yang dapat ditarik kapanpun sesuai dengan ketersediaan saldo berjalan.'],
          ['color'=>'#fffbeb','iconColor'=>'#f59e0b','icon'=>'<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>','title'=>'Estimasi Verifikasi','desc'=>'Setiap penarikan memerlukan proses mutasi yang akan diverifikasi oleh bendahara/pengurus dalam 1-2 hari kerja.'],
          ['color'=>'#fef2f2','iconColor'=>'#ef4444','icon'=>'<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>','title'=>'Batasan Permintaan','desc'=>'Sistem hanya memperbolehkan maksimal 1 (satu) permintaan penarikan dengan status tertunda dalam waktu bersamaan.'],
        ] as $item)
        <div style="display:flex;gap:16px;align-items:flex-start;padding:12px;border-radius:12px;transition:background 0.2s;" onmouseover="this.style.background='var(--gray-50)'" onmouseout="this.style.background='transparent'">
          <div style="width:40px;height:40px;border-radius:10px;background:{{ $item['color'] }};color:{{ $item['iconColor'] }};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            {!! $item['icon'] !!}
          </div>
          <div>
            <div style="font-size:14px;font-weight:700;color:var(--gray-900);margin-bottom:4px;">{{ $item['title'] }}</div>
            <div style="font-size:12px;color:var(--gray-500);line-height:1.5;">{{ $item['desc'] }}</div>
          </div>
        </div>
        @endforeach
      </div>
    </div>
  </div>
</div>
@endsection
