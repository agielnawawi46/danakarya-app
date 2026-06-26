@extends('layouts.app')
@section('title', 'Sinkronisasi Payroll')
@section('page_title', 'Sinkronisasi Payroll')

@section('content')
<div class="page-header">
  <div class="page-header-text">
    <h1 class="page-title">Sinkronisasi Payroll</h1>
    <p class="page-subtitle">Generate tagihan & import konfirmasi potongan gaji dari Finance</p>
  </div>
</div>

{{-- Period Selector --}}
<form class="flex gap-2 items-center" style="margin-bottom:20px;">
  <select name="month" class="form-control" style="max-width:150px;">
    @php $months=['1'=>'Januari','2'=>'Februari','3'=>'Maret','4'=>'April','5'=>'Mei','6'=>'Juni','7'=>'Juli','8'=>'Agustus','9'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember']; @endphp
    @foreach($months as $num => $name)
      <option value="{{ $num }}" {{ $month == $num ? 'selected' : '' }}>{{ $name }}</option>
    @endforeach
  </select>
  <input type="number" name="year" class="form-control" style="max-width:100px;" value="{{ $year }}" min="2020" max="2030">
  <button class="btn btn-secondary">Lihat Periode</button>
</form>

{{-- Summary --}}
<div class="grid grid-3" style="margin-bottom:20px;">
  <div class="stat-card">
    <div class="stat-card-icon blue"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle></svg></div>
    <div class="stat-card-info">
      <div class="stat-card-label">Total Karyawan</div>
      <div class="stat-card-value">{{ count($billing) }}</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-card-icon green"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg></div>
    <div class="stat-card-info">
      <div class="stat-card-label">Total Simpanan Wajib</div>
      <div class="stat-card-value money" style="font-size:1.2rem;">Rp {{ number_format($totalSimpananWajib,0,',','.') }}</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-card-icon amber"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"></rect></svg></div>
    <div class="stat-card-info">
      <div class="stat-card-label">Total Angsuran Pinjaman</div>
      <div class="stat-card-value money" style="font-size:1.2rem;">Rp {{ number_format($totalAngsuran,0,',','.') }}</div>
    </div>
  </div>
</div>

{{-- Import Form --}}
<div class="card" style="margin-bottom:20px;">
  <div class="card-header">
      <div style="display: flex; align-items: center; gap: 12px;">
        <div class="stat-card-icon blue" style="width: 36px; height: 36px; border-radius: 10px;">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="8 17 12 21 16 17"></polyline><line x1="12" y1="12" x2="12" y2="21"></line><path d="M20.88 18.09A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.29"></path></svg>
        </div>
        <h3 style="margin: 0;">Import Konfirmasi Payroll dari Finance</h3>
      </div>
    <span class="badge badge-info">CSV</span>
  </div>
  <div class="card-body">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 16px; padding-bottom: 16px; border-bottom: 1px dashed var(--gray-200);">
      <div>
        <div style="font-weight: 600; font-size: 14px;">1. Export Data Tagihan</div>
        <div class="form-hint" style="margin-top: 4px;">Download file CSV tagihan bulan ini untuk dikirim ke tim Finance.</div>
      </div>
      <a href="{{ route('admin.payroll.export', ['month'=>$month,'year'=>$year]) }}" class="btn btn-primary" style="width: 190px; justify-content: center;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
        Export CSV
      </a>
    </div>

    <form method="POST" action="{{ route('admin.payroll.import') }}" enctype="multipart/form-data" style="display:flex; justify-content:space-between; align-items:center;">
      @csrf
      <input type="hidden" name="month" value="{{ $month }}">
      <input type="hidden" name="year"  value="{{ $year }}">
      
      <div>
        <div style="font-weight: 600; font-size: 14px; margin-bottom: 4px;">2. Import Hasil Konfirmasi (Upload CSV)</div>
        <div class="form-hint" style="margin-bottom: 8px;">Upload file CSV hasil konfirmasi Finance. Kolom: employee_id, email, simpanan_wajib, angsuran</div>
        <input type="file" name="file" class="form-control" accept=".csv,.txt" style="max-width:360px;" required>
      </div>

      <button type="submit" class="btn btn-primary" style="width: 190px; justify-content: center;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 0-4-4H5a2 2 0 0 0-4 4v2"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
        Upload & Proses
      </button>
    </form>
  </div>
</div>

{{-- Billing Table --}}
<div class="card">
  <div class="card-header">
      <div style="display: flex; align-items: center; gap: 12px;">
        <div class="stat-card-icon blue" style="width: 36px; height: 36px; border-radius: 10px;">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect></svg>
        </div>
        <h3 style="margin: 0;">Tagihan Periode {{ $months[$month] }} {{ $year }}</h3>
      </div>
    <span class="badge badge-secondary">{{ count($billing) }} karyawan</span>
  </div>
  <div class="table-wrapper" style="border:none;">
    <table>
      <thead>
        <tr>
          <th>NIK</th>
          <th>Nama</th>
          <th>Departemen</th>
          <th>Simpanan Wajib</th>
          <th>Angsuran Pinjaman</th>
          <th>Total Potongan</th>
        </tr>
      </thead>
      <tbody>
        @forelse($billing as $row)
        <tr>
          <td style="font-size:12px;color:var(--gray-400);">{{ $row['employee_id'] }}</td>
          <td class="font-semibold">{{ $row['name'] }}</td>
          <td>{{ $row['department'] }}</td>
          <td class="money">Rp {{ number_format($row['simpanan_wajib'],0,',','.') }}</td>
          <td class="money {{ $row['angsuran'] > 0 ? 'text-warning' : 'text-muted' }}">
            Rp {{ number_format($row['angsuran'],0,',','.') }}
          </td>
          <td class="money font-bold">Rp {{ number_format($row['total'],0,',','.') }}</td>
        </tr>
        @empty
        <tr>
          <td colspan="6" class="text-center text-muted" style="padding:40px;">
            Tidak ada data billing untuk periode ini
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

@if(count($billing))
<div class="card" style="background:var(--brand-50);border:1px solid var(--brand-100);margin-top:16px;">
  <div class="card-body">
    <div style="display:flex;justify-content:space-between;align-items:center;">
      <span style="font-size:15px;font-weight:700;color:var(--brand-800);">Grand Total Potongan Payroll</span>
      <div style="text-align:right;">
        <div class="money" style="font-size:1.5rem;font-weight:900;color:var(--brand-700);">
          Rp {{ number_format($totalPotongan,0,',','.') }}
        </div>
        <div style="font-size:12px;color:var(--brand-600);margin-top:4px;">
          (Simpanan: Rp {{ number_format($totalSimpananWajib,0,',','.') }} + Angsuran: Rp {{ number_format($totalAngsuran,0,',','.') }})
        </div>
      </div>
    </div>
  </div>
</div>
@endif
@endsection
