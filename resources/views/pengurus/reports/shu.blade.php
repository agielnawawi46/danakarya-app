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
<div class="card" style="margin-bottom:20px;max-width:600px;">
  <div class="card-header"><h3>🧮 Hitung SHU Tahun {{ $year }}</h3></div>
  <div class="card-body">
    <form method="POST" action="{{ route('pengurus.reports.shu.calculate') }}">
      @csrf
      <input type="hidden" name="year" value="{{ $year }}">
      @if($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
      @endif
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Total Pendapatan (Rp) <span class="req">*</span></label>
          <input type="number" name="total_income" class="form-control" placeholder="Ambil dari Laporan L/R" min="0" step="1000" required>
          <div class="form-hint">Lihat di Laporan Laba/Rugi → Total Pendapatan</div>
        </div>
        <div class="form-group">
          <label class="form-label">Total Beban (Rp) <span class="req">*</span></label>
          <input type="number" name="total_expense" class="form-control" placeholder="Ambil dari Laporan L/R" min="0" step="1000" required>
        </div>
      </div>
      <button type="submit" class="btn btn-primary">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
        Hitung SHU
      </button>
    </form>
  </div>
</div>

{{-- Distribution Results --}}
@forelse($distributions as $dist)
<div class="card" style="margin-bottom:20px;">
  <div class="card-header">
    <h3>📊 SHU Tahun {{ $dist->year }}</h3>
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
  <span class="empty-state-icon">📊</span>
  <div class="empty-state-text">Belum ada kalkulasi SHU untuk tahun {{ $year }}</div>
  <p class="text-muted" style="margin-top:8px;font-size:13px;">Gunakan form di atas untuk menghitung SHU berdasarkan laporan Laba/Rugi</p>
</div>
@endforelse
@endsection
