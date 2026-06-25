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
    ['Simpanan Pokok','pokok','indigo','💎','Dibayar sekali saat bergabung'],
    ['Simpanan Wajib','wajib','blue','📅','Potongan gaji bulanan'],
    ['Simpanan Sukarela','sukarela','green','💵','Dapat ditarik kapan saja'],
  ] as [$label, $type, $color, $icon, $desc])
  <div class="stat-card" style="flex-direction:column;gap:8px;">
    <div style="display:flex;justify-content:space-between;align-items:center;width:100%;">
      <span style="font-size:22px;">{{ $icon }}</span>
      <span class="badge badge-secondary text-sm">{{ ucfirst($type) }}</span>
    </div>
    <div>
      <div style="font-size:12px;font-weight:600;color:var(--gray-400);text-transform:uppercase;letter-spacing:.06em;">{{ $label }}</div>
      <div class="money" style="font-size:1.4rem;font-weight:900;color:var(--gray-900);margin-top:4px;">
        Rp {{ number_format($balances[$type], 0, ',', '.') }}
      </div>
      <div style="font-size:12px;color:var(--gray-400);margin-top:2px;">{{ $desc }}</div>
    </div>
  </div>
  @endforeach
</div>

{{-- Transaction History --}}
<div class="card">
  <div class="card-header">
    <h3>📋 Riwayat Transaksi</h3>
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
