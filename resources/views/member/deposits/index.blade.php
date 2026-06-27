@extends('layouts.app')
@section('title', 'Tabungan Saya')
@section('page_title', 'Tabungan Saya')

@section('content')
<div class="page-header">
  <div class="page-header-text">
    <h1 class="page-title">Tabungan Saya</h1>
    <p class="page-subtitle">Rincian seluruh simpanan Anda di koperasi</p>
  </div>
  <a href="{{ route('member.deposits.withdraw') }}" class="btn btn-secondary">
    ↑ Ajukan Penarikan
  </a>
</div>

{{-- Balance Cards --}}
<div class="grid grid-3" style="margin-bottom:24px;">
  @foreach([
    ['Simpanan Pokok','pokok','indigo','<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle></svg>','Dibayar sekali saat bergabung'],
    ['Simpanan Wajib','wajib','blue','<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"></rect></svg>','Potongan gaji bulanan'],
    ['Simpanan Sukarela','sukarela','green','<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>','Dapat ditarik kapan saja'],
  ] as [$label, $type, $color, $icon, $desc])
  <div class="stat-card" style="display:flex;align-items:flex-start;gap:16px;">
    <div class="stat-card-icon {{ $color }}">{!! $icon !!}</div>
    <div class="stat-card-info" style="flex:1;">
      <div style="display:flex;justify-content:space-between;align-items:flex-start;width:100%;">
        <div class="stat-card-label" style="font-size:13px;margin-bottom:4px;">{{ $label }}</div>
        <span class="badge badge-secondary" style="font-size:10px;">{{ ucfirst($type) }}</span>
      </div>
      <div class="stat-card-value money" style="font-size:1.4rem;">Rp {{ number_format($balances[$type], 0, ',', '.') }}</div>
      <div class="stat-card-change" style="font-size:11px;margin-top:6px;font-weight:600;">{{ $desc }}</div>
    </div>
  </div>
  @endforeach
</div>

{{-- Transaction History --}}
<div class="card">
  <div class="card-header">
      <div style="display: flex; align-items: center; gap: 12px;">
        <div class="stat-card-icon blue" style="width: 36px; height: 36px; border-radius: 10px;">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect></svg>
        </div>
        <h3 style="margin: 0;">Riwayat Transaksi</h3>
      </div>
    <span class="badge badge-secondary">{{ $transactions->total() }} transaksi</span>
  </div>
  <div class="table-wrapper" style="border:none;">
    <table>
      <thead>
        <tr>
          <th>Tanggal</th>
          <th>Jenis</th>
          <th>Tipe</th>
          <th>Keterangan</th>
          <th>Jumlah</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        @forelse($transactions as $t)
        <tr>
          <td style="font-size:12px;color:var(--gray-400);">{{ $t->created_at->format('d/m/Y') }}</td>
          <td><span class="badge badge-primary">{{ $t->getTypeLabel() }}</span></td>
          <td>
            @if($t->transaction_type === 'credit')
              <span class="badge badge-success">↓ Masuk</span>
            @else
              <span class="badge badge-danger">↑ Keluar</span>
            @endif
          </td>
          <td style="font-size:13px;max-width:200px;">{{ $t->notes ?? '—' }}</td>
          <td class="money font-bold {{ $t->transaction_type === 'credit' ? 'text-success' : 'text-danger' }}">
            {{ $t->transaction_type === 'credit' ? '+' : '-' }}Rp {{ number_format($t->amount, 0, ',', '.') }}
          </td>
          <td>{!! $t->getStatusBadge() !!}</td>
        </tr>
        @empty
        <tr><td colspan="6" class="text-center text-muted" style="padding:40px;">Belum ada transaksi simpanan</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="card-footer">{{ $transactions->links() }}</div>
</div>
@endsection
