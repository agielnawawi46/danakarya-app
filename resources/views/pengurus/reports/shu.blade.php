@extends('layouts.app')
@section('title', 'Distribusi SHU')
@section('page_title', 'Distribusi SHU')

@section('content')
<div class="page-header">
  <div class="page-header-text">
    <h1 class="page-title">Distribusi SHU</h1>
    <p class="page-subtitle">Hitung dan distribusikan Sisa Hasil Usaha kepada anggota</p>
  </div>
  <div class="flex gap-2">
    <form class="flex gap-2">
      <select name="year" class="form-control" style="max-width:100px;">
        @for($y = now()->year; $y >= 2020; $y--)
          <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
        @endfor
      </select>
      <button class="btn btn-secondary">Lihat</button>
    </form>
    <a href="{{ route('pengurus.reports.index') }}" class="btn btn-secondary">← Kembali</a>
  </div>
</div>

{{-- Calculation Form --}}
<div class="card" style="margin-bottom:20px;">
  <div class="card-header">
      <div style="display: flex; align-items: center; gap: 12px;">
        <div class="stat-card-icon blue" style="width: 36px; height: 36px; border-radius: 10px;">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="2" width="16" height="20" rx="2" ry="2"></rect><line x1="8" y1="6" x2="16" y2="6"></line><line x1="16" y1="14" x2="16" y2="14"></line><line x1="16" y1="18" x2="16" y2="18"></line><line x1="12" y1="14" x2="12" y2="14"></line><line x1="12" y1="18" x2="12" y2="18"></line><line x1="8" y1="14" x2="8" y2="14"></line><line x1="8" y1="18" x2="8" y2="18"></line></svg>
        </div>
        <h3 style="margin: 0;">Hitung SHU Tahun {{ $year }}</h3>
      </div></div>
  <div class="card-body" style="padding:32px;">
    <form method="POST" action="{{ route('pengurus.reports.shu.calculate') }}">
      @csrf
      <input type="hidden" name="year" value="{{ $year }}">
      @if($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
      @endif
      
      <div style="display:flex;gap:24px;margin-bottom:32px;">
        <div style="flex:1;background:var(--gray-50);border:1px solid var(--gray-200);border-radius:16px;padding:32px;text-align:center;box-shadow:inset 0 2px 4px rgba(0,0,0,0.02);">
          <div style="font-size:15px;color:var(--gray-500);font-weight:700;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:12px;">Total Pendapatan</div>
          <div class="money" style="font-size:2.5rem;font-weight:900;color:var(--success);">Rp {{ number_format($totalIncome, 0, ',', '.') }}</div>
          <input type="hidden" name="total_income" value="{{ $totalIncome }}">
        </div>
        <div style="flex:1;background:var(--gray-50);border:1px solid var(--gray-200);border-radius:16px;padding:32px;text-align:center;box-shadow:inset 0 2px 4px rgba(0,0,0,0.02);">
          <div style="font-size:15px;color:var(--gray-500);font-weight:700;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:12px;">Total Beban</div>
          <div class="money" style="font-size:2.5rem;font-weight:900;color:var(--danger);">Rp {{ number_format($totalExpense, 0, ',', '.') }}</div>
          <input type="hidden" name="total_expense" value="{{ $totalExpense }}">
        </div>
      </div>

      <div style="text-align:center;">
        <button type="submit" class="btn btn-primary" style="padding:14px 40px;font-size:16px;border-radius:12px;box-shadow:0 4px 12px rgba(99,102,241,0.3);">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
          Hitung & Proses SHU Tahun {{ $year }}
        </button>
      </div>
    </form>
  </div>
</div>

{{-- Distribution Results --}}
@forelse($distributions as $dist)
<div class="card" style="margin-bottom:20px;">
  <div class="card-header">
      <div style="display: flex; align-items: center; gap: 12px;">
        <div class="stat-card-icon indigo" style="width: 36px; height: 36px; border-radius: 10px;">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
        </div>
        <h3 style="margin: 0;">SHU Tahun {{ $dist->year }}</h3>
      </div>
    <div class="flex gap-2 items-center">
      @if($dist->status === 'draft')
        <span class="badge badge-warning">Draft</span>
        <form method="POST" action="{{ route('pengurus.reports.shu.distribute', $dist) }}">
          @csrf
          <button class="btn btn-success btn-sm" data-confirm="Distribusikan SHU tahun {{ $dist->year }} ke semua anggota? Tindakan ini tidak dapat dibatalkan.">
            🌟 Distribusikan ke Anggota
          </button>
        </form>
      @elseif($dist->status === 'distributed')
        <span class="badge badge-success">✅ Sudah Didistribusikan</span>
      @endif
    </div>
  </div>
  <div class="card-body">
    <div class="grid grid-3" style="margin-bottom:16px;">
      @foreach([
        ['Total Laba Bersih', $dist->total_profit, 'var(--success)'],
        ['Dana Cadangan', $dist->total_dana_cadangan, 'var(--brand-600)'],
        ['Bagian Anggota', $dist->total_anggota, 'var(--accent-violet)'],
        ['Dana Pengurus', $dist->total_pengurus, 'var(--warning)'],
        ['Dana Karyawan', $dist->total_karyawan, 'var(--accent-blue)'],
        ['Dana Pendidikan', $dist->total_pendidikan, 'var(--gray-600)'],
      ] as [$label, $val, $color])
      <div style="padding:12px;background:var(--gray-50);border-radius:8px;">
        <div style="font-size:11px;color:var(--gray-400);font-weight:700;text-transform:uppercase;letter-spacing:.06em;">{{ $label }}</div>
        <div class="money" style="font-size:1rem;font-weight:900;color:{{ $color }};margin-top:4px;">Rp {{ number_format($val,0,',','.') }}</div>
      </div>
      @endforeach
    </div>

    {{-- Member Details --}}
    @if($dist->memberDetails && $dist->memberDetails->count())
    <div class="table-wrapper">
      <table>
        <thead><tr><th>Anggota</th><th>Jasa Modal</th><th>Jasa Pinjaman</th><th>Total SHU</th></tr></thead>
        <tbody>
          @foreach($dist->memberDetails->take(10) as $detail)
          <tr>
            <td class="font-semibold">{{ $detail->user?->name ?? '-' }}</td>
            <td class="money">Rp {{ number_format($detail->jasa_modal,0,',','.') }}</td>
            <td class="money">Rp {{ number_format($detail->jasa_pinjaman,0,',','.') }}</td>
            <td class="money font-bold" style="color:var(--brand-600);">Rp {{ number_format($detail->total_shu,0,',','.') }}</td>
          </tr>
          @endforeach
          @if($dist->memberDetails->count() > 10)
          <tr><td colspan="4" class="text-center text-muted" style="font-size:12px;padding:8px;">...dan {{ $dist->memberDetails->count() - 10 }} anggota lainnya</td></tr>
          @endif
        </tbody>
      </table>
    </div>
    @endif
  </div>
</div>
@empty
<div class="empty-state">
  <div class="empty-state-text">Belum ada kalkulasi SHU untuk tahun {{ $year }}</div>
  <p class="text-muted" style="margin-top:8px;font-size:13px;">Gunakan form di atas untuk menghitung SHU berdasarkan laporan Laba/Rugi</p>
</div>
@endforelse
@endsection
