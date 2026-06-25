@extends('layouts.app')
@section('title', 'Bonus SHU Saya')
@section('page_title', 'Bonus SHU')

@section('content')
<div class="page-header">
  <div class="page-header-text">
    <h1 class="page-title">Bonus SHU Saya</h1>
    <p class="page-subtitle">Riwayat Sisa Hasil Usaha yang Anda terima setiap tahun</p>
  </div>
</div>

{{-- Total SHU Card --}}
<div class="card" style="background:linear-gradient(135deg,var(--brand-600),var(--accent-violet));margin-bottom:24px;max-width:400px;">
  <div class="card-body" style="text-align:center;">
    <div style="font-size:13px;font-weight:700;color:rgba(255,255,255,.7);text-transform:uppercase;letter-spacing:.1em;">Total SHU Diterima</div>
    <div class="money" style="font-size:2.5rem;font-weight:900;color:white;margin:8px 0;">
      Rp {{ number_format($totalShu, 0, ',', '.') }}
    </div>
    <div style="font-size:12px;color:rgba(255,255,255,.6);">Dari {{ $details->count() }} periode distribusi</div>
  </div>
</div>

@if($details->count())
<div class="card">
  <div class="table-wrapper" style="border:none;">
    <table>
      <thead>
        <tr>
          <th>Tahun</th>
          <th>Jasa Modal</th>
          <th>Jasa Pinjaman</th>
          <th>Total SHU</th>
          <th>Dikreditkan</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        @foreach($details as $detail)
        <tr>
          <td class="font-bold" style="font-size:16px;">{{ $detail->distribution?->year ?? '—' }}</td>
          <td class="money">Rp {{ number_format($detail->jasa_modal, 0, ',', '.') }}</td>
          <td class="money">Rp {{ number_format($detail->jasa_pinjaman, 0, ',', '.') }}</td>
          <td class="money font-bold" style="color:var(--brand-700);">Rp {{ number_format($detail->total_shu, 0, ',', '.') }}</td>
          <td style="font-size:12px;color:var(--gray-400);">
            @if($detail->deposited_at) {{ \Carbon\Carbon::parse($detail->deposited_at)->format('d M Y') }} @else —  @endif
          </td>
          <td>
            @if($detail->deposited_at)
              <span class="badge badge-success">Sudah dikreditkan ke simpanan sukarela</span>
            @else
              <span class="badge badge-warning">Menunggu distribusi</span>
            @endif
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

<div class="card" style="margin-top:20px;">
  <div class="card-body">
    <h4 style="margin-bottom:12px;">Cara Perhitungan SHU</h4>
    <div class="grid grid-2" style="gap:12px;">
      <div style="padding:14px;background:var(--brand-50);border-radius:8px;border:1px solid var(--brand-200);">
        <div style="font-weight:700;color:var(--brand-700);margin-bottom:6px;">💰 Jasa Modal (60% bagian anggota)</div>
        <div style="font-size:13px;color:var(--gray-600);">Proporsional terhadap total simpanan Anda dibanding total simpanan seluruh anggota pada tahun tersebut.</div>
      </div>
      <div style="padding:14px;background:#ecfdf5;border-radius:8px;border:1px solid #a7f3d0;">
        <div style="font-weight:700;color:#065f46;margin-bottom:6px;">🏦 Jasa Pinjaman (40% bagian anggota)</div>
        <div style="font-size:13px;color:var(--gray-600);">Proporsional terhadap bunga pinjaman yang Anda bayar dibanding total bunga yang dibayar anggota lain.</div>
      </div>
    </div>
  </div>
</div>
@else
<div class="empty-state">
  <span class="empty-state-icon">⭐</span>
  <div class="empty-state-text">Belum ada distribusi SHU</div>
  <p class="text-muted" style="margin-top:8px;font-size:13px;">SHU akan dibagikan setelah penutupan buku tahunan koperasi</p>
</div>
@endif
@endsection
