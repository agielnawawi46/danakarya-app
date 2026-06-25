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
  <a href="{{ route('admin.payroll.export', ['month'=>$month,'year'=>$year]) }}" class="btn btn-primary">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
    Export CSV untuk Finance
  </a>
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
    <h3>📥 Import Konfirmasi Payroll dari Finance</h3>
    <span class="badge badge-info">CSV</span>
  </div>
  <div class="card-body">
    <form method="POST" action="{{ route('admin.payroll.import') }}" enctype="multipart/form-data" class="flex items-center gap-2">
      @csrf
      <input type="hidden" name="month" value="{{ $month }}">
      <input type="hidden" name="year"  value="{{ $year }}">
      <input type="file" name="file" class="form-control" accept=".csv,.txt" style="max-width:360px;" required>
      <button type="submit" class="btn btn-primary">Upload & Proses</button>
    </form>
    <div class="form-hint" style="margin-top:8px;">Upload file CSV hasil konfirmasi Finance. Kolom: employee_id, email, simpanan_wajib, angsuran</div>
  </div>
</div>

{{-- Billing Table --}}
<div class="card">
  <div class="card-header">
    <h3>📋 Tagihan Periode {{ $months[$month] }} {{ $year }}</h3>
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
      @if(count($billing))
      <tfoot>
        <tr style="background:var(--brand-50);font-weight:700;">
          <td colspan="3" class="font-bold">TOTAL</td>
          <td class="money">Rp {{ number_format($totalSimpananWajib,0,',','.') }}</td>
          <td class="money">Rp {{ number_format($totalAngsuran,0,',','.') }}</td>
          <td class="money" style="color:var(--brand-600);">Rp {{ number_format($totalPotongan,0,',','.') }}</td>
        </tr>
      </tfoot>
      @endif
    </table>
  </div>
</div>
@endsection
