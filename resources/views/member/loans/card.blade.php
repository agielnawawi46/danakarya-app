@extends('layouts.app')
@section('title', 'Kartu Piutang')
@section('page_title', 'Kartu Piutang Pribadi')

@section('content')
<div class="page-header">
  <div class="page-header-text">
    <h1 class="page-title">Kartu Piutang</h1>
    <p class="page-subtitle">Detail jadwal angsuran pinjaman Anda</p>
  </div>
  <a href="{{ route('member.loans.index') }}" class="btn btn-secondary">← Kembali</a>
</div>

{{-- Loan Summary --}}
<div class="card mb-5" style="margin-bottom:20px;">
  <div class="card-body">
    <div class="grid grid-4">
      @foreach([
        ['label'=>'Jumlah Pinjaman','value'=>'Rp '.number_format($loan->amount,0,',','.'),'color'=>'var(--gray-900)'],
        ['label'=>'Sisa Pokok','value'=>'Rp '.number_format($remaining,0,',','.'),'color'=>'var(--danger)'],
        ['label'=>'Angsuran Lunas','value'=>$paidCount.' / '.$loan->tenor_months.' bulan','color'=>'var(--success)'],
        ['label'=>'Sisa Angsuran','value'=>$pendingCount.' bulan','color'=>'var(--warning)'],
      ] as $s)
      <div style="text-align:center;padding:12px;background:var(--gray-50);border-radius:8px;">
        <div style="font-size:11px;font-weight:600;color:var(--gray-400);text-transform:uppercase;letter-spacing:.08em;">{{ $s['label'] }}</div>
        <div style="font-size:1.1rem;font-weight:900;color:{{ $s['color'] }};margin-top:4px;">{{ $s['value'] }}</div>
      </div>
      @endforeach
    </div>
  </div>
</div>

<div class="card">
  <div class="card-header">
    <div style="display: flex; align-items: center; gap: 12px;">
      <div class="stat-card-icon blue" style="width: 36px; height: 36px; border-radius: 10px;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
      </div>
      <h3 style="margin: 0;">Jadwal Angsuran Lengkap</h3>
    </div>
  </div>
  <div class="table-wrapper" style="border:none;border-radius:0;">
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Jatuh Tempo</th>
          <th>Pokok</th>
          <th>Bunga</th>
          <th>Total</th>
          <th>Sisa Pokok</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        @foreach($loan->schedules as $sch)
        <tr style="{{ $sch->status === 'paid' ? 'opacity:.6;' : '' }}">
          <td class="font-bold">{{ $sch->installment_number }}</td>
          <td>{{ $sch->due_date->format('d M Y') }}</td>
          <td class="money">Rp {{ number_format($sch->principal_amount, 0, ',', '.') }}</td>
          <td class="money text-warning">Rp {{ number_format($sch->interest_amount, 0, ',', '.') }}</td>
          <td class="money font-bold">Rp {{ number_format($sch->total_amount, 0, ',', '.') }}</td>
          <td class="money">Rp {{ number_format($sch->remaining_balance, 0, ',', '.') }}</td>
          <td>{!! $sch->getStatusBadge() !!}</td>
        </tr>
        @endforeach
      </tbody>
      <tfoot>
        <tr style="background:var(--brand-50);">
          <td colspan="2" class="font-bold">TOTAL</td>
          <td class="money font-bold">Rp {{ number_format($loan->schedules->sum('principal_amount'), 0, ',', '.') }}</td>
          <td class="money font-bold text-warning">Rp {{ number_format($loan->schedules->sum('interest_amount'), 0, ',', '.') }}</td>
          <td class="money font-bold">Rp {{ number_format($loan->schedules->sum('total_amount'), 0, ',', '.') }}</td>
          <td colspan="2"></td>
        </tr>
      </tfoot>
    </table>
  </div>
</div>
@endsection
