@extends('layouts.app')
@section('title', 'Ajukan Pinjaman')
@section('page_title', 'Fasilitas Kredit')

@section('content')
<div class="page-header">
  <div class="page-header-text">
    <h1 class="page-title">Fasilitas Kredit</h1>
    <p class="page-subtitle">Simulasikan dan ajukan pinjaman online Anda</p>
  </div>
</div>

@if($hasLoan)
<div class="alert alert-warning">⚠️ Anda masih memiliki pinjaman aktif. Lunasi terlebih dahulu sebelum mengajukan pinjaman baru.</div>
@endif

<div class="grid grid-2" style="align-items:start;" x-data="{
  amount: '',
  tenor: 12,
  result: null,
  loading: false,
  async simulate() {
    if (!this.amount || this.amount < 100000) return;
    this.loading = true;
    this.result = null;
    try {
      const r = await fetch('/member/loans/calculate?amount='+this.amount+'&tenor='+this.tenor, {headers:{'X-Requested-With':'XMLHttpRequest','Accept':'application/json'}});
      this.result = await r.json();
    } catch(e) { console.error(e); }
    finally { this.loading = false; }
  },
  fmt(v) { return 'Rp ' + Math.round(v||0).toLocaleString('id-ID'); }
}">

  {{-- Application Form --}}
  <div class="card">
    <div class="card-header">
      <div style="display: flex; align-items: center; gap: 12px;">
        <div class="stat-card-icon blue" style="width: 36px; height: 36px; border-radius: 10px;">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
        </div>
        <h3 style="margin: 0;">Formulir Pengajuan Pinjaman</h3>
      </div></div>
    <div class="card-body">
      @if(!$hasLoan)
      <form method="POST" action="{{ route('member.loans.store') }}">
        @csrf
        <div class="form-group">
          <label class="form-label" for="amount">Jumlah Pinjaman (Rp) <span class="req">*</span></label>
          <input type="number" id="amount" name="amount" x-model="amount" @input="simulate()"
            class="form-control {{ $errors->has('amount') ? 'is-invalid' : '' }}"
            value="{{ old('amount') }}"
            min="100000" max="{{ $org->loan_max_plafon }}" step="100000"
            placeholder="5000000" required {{ $hasLoan ? 'disabled' : '' }}>
          <div class="form-hint">Maks: Rp {{ number_format($org->loan_max_plafon, 0, ',', '.') }}</div>
          @error('amount')<div class="form-error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
          <label class="form-label" for="tenor">Tenor / Jangka Waktu <span class="req">*</span></label>
          <select id="tenor" name="tenor" x-model="tenor" @change="simulate()" class="form-control" required {{ $hasLoan ? 'disabled' : '' }}>
            @for($t = 1; $t <= $org->loan_max_tenor; $t++)
              <option value="{{ $t }}" {{ old('tenor', 12) == $t ? 'selected' : '' }}>{{ $t }} bulan</option>
            @endfor
          </select>
        </div>

        <div class="form-group">
          <label class="form-label" for="purpose">Tujuan Pinjaman <span class="req">*</span></label>
          <textarea id="purpose" name="purpose" class="form-control {{ $errors->has('purpose') ? 'is-invalid' : '' }}"
            placeholder="Jelaskan tujuan penggunaan dana pinjaman..." required {{ $hasLoan ? 'disabled' : '' }}>{{ old('purpose') }}</textarea>
          @error('purpose')<div class="form-error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group" style="background:var(--gray-50);border-radius:8px;padding:12px;font-size:13px;color:var(--gray-600);">
          <strong>Info:</strong> Suku bunga {{ $org->loan_interest_rate }}%/bulan ({{ ucfirst($org->loan_interest_method) }}).
          Batas angsuran maks 30% dari gaji pokok Anda.
        </div>

        <button type="submit" class="btn btn-primary btn-block" {{ $hasLoan ? 'disabled' : '' }}>
          Kirim Pengajuan Pinjaman
        </button>
      </form>
      @else
      <div class="empty-state">
        <div class="empty-state-icon" style="background:#fef2f2;color:#ef4444;width:64px;height:64px;display:flex;align-items:center;justify-content:center;border-radius:50%;margin:0 auto 16px;">
          <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
        </div>
        <div class="empty-state-text">Pinjaman aktif masih berjalan</div>
      </div>
      @endif
    </div>
  </div>

  {{-- Calculator --}}
  <div class="card">
    <div class="card-header">
      <div style="display: flex; align-items: center; gap: 12px;">
        <div class="stat-card-icon blue" style="width: 36px; height: 36px; border-radius: 10px;">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="2" width="16" height="20" rx="2" ry="2"></rect><line x1="8" y1="6" x2="16" y2="6"></line><line x1="16" y1="14" x2="16" y2="14"></line><line x1="16" y1="18" x2="16" y2="18"></line><line x1="12" y1="14" x2="12" y2="14"></line><line x1="12" y1="18" x2="12" y2="18"></line><line x1="8" y1="14" x2="8" y2="14"></line><line x1="8" y1="18" x2="8" y2="18"></line></svg>
        </div>
        <h3 style="margin: 0;">Kalkulator Simulasi</h3>
      </div></div>
    <div class="card-body">
      <template x-if="loading">
        <div style="text-align:center;padding:24px;color:var(--gray-400);">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="animation:spin 1s linear infinite;"><circle cx="12" cy="12" r="10"></circle><path d="M12 2a10 10 0 0 1 10 10"></path></svg>
          <div style="margin-top:8px;">Menghitung...</div>
        </div>
      </template>

      <template x-if="result && !loading">
        <div>
          {{-- Eligibility --}}
          <div :class="result.eligible ? 'alert-success' : 'alert-danger'" class="alert" style="margin-bottom:16px;">
            <span x-text="result.eligible ? '✅ Layak Kredit' : '❌ Tidak Layak'"></span>
            <span x-text="result.reason" style="font-size:12px;display:block;margin-top:4px;"></span>
          </div>

          {{-- Simulation Results --}}
          <div style="display:grid;gap:12px;">
            <div style="display:flex;justify-content:space-between;padding:12px;background:var(--gray-50);border-radius:8px;">
              <span style="font-size:13px;color:var(--gray-500);">Angsuran / Bulan</span>
              <span class="money font-bold" style="font-size:1.1rem;" x-text="fmt(result.monthly_installment)"></span>
            </div>
            <div style="display:flex;justify-content:space-between;padding:12px;background:var(--gray-50);border-radius:8px;">
              <span style="font-size:13px;color:var(--gray-500);">Total Bunga</span>
              <span class="money text-warning font-bold" x-text="fmt(result.total_interest)"></span>
            </div>
            <div style="display:flex;justify-content:space-between;padding:12px;background:var(--gray-50);border-radius:8px;">
              <span style="font-size:13px;color:var(--gray-500);">Total Pengembalian</span>
              <span class="money font-bold" x-text="fmt(result.total_repayment)"></span>
            </div>
            <div style="display:flex;justify-content:space-between;padding:12px;background:var(--gray-50);border-radius:8px;">
              <span style="font-size:13px;color:var(--gray-500);">Skor Kredit (% Gaji)</span>
              <span :class="result.eligible ? 'badge-success' : 'badge-danger'" class="badge" x-text="result.credit_score + '%'"></span>
            </div>
            <div style="background:var(--brand-50);border:1px solid var(--brand-200);border-radius:8px;padding:12px;font-size:12px;color:var(--brand-700);">
              💡 Batas angsuran maks: <strong x-text="fmt(result.max_allowed)"></strong> (30% gaji)
            </div>
          </div>
        </div>
      </template>

      <template x-if="!result && !loading">
        <div class="empty-state">
          <div class="empty-state-icon" style="background:#eff6ff;color:#3b82f6;width:64px;height:64px;display:flex;align-items:center;justify-content:center;border-radius:50%;margin:0 auto 16px;">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
          </div>
          <div class="empty-state-text">Masukkan jumlah & tenor untuk simulasi otomatis</div>
        </div>
      </template>
    </div>
  </div>
</div>

@push('scripts')
<style>
@keyframes spin { to { transform: rotate(360deg); } }
</style>
@endpush
@endsection
