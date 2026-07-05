@extends('layouts.app')
@section('title', 'Penarikan Sukarela')
@section('page_title', 'Penarikan Simpanan Sukarela')

@section('content')
<div class="page-header">
  <div class="page-header-text">
    <h1 class="page-title">Penarikan Sukarela</h1>
    <p class="page-subtitle">Review dan setujui permintaan penarikan simpanan sukarela anggota</p>
  </div>
</div>

{{-- Month Navigation --}}
<div class="flex justify-between items-center" style="margin-bottom:16px;">
  <form method="GET" class="flex gap-2 items-center">
    <span style="font-size:15px; font-weight:600; color:var(--gray-800);">Periode:</span>
    <select name="month" class="form-control" style="width:auto; padding:4px 32px 4px 12px; font-size:14px; min-height: 34px;" onchange="this.form.submit()">
      @foreach([
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
      ] as $m => $name)
        <option value="{{ $m }}" {{ $currentPeriod->month == $m ? 'selected' : '' }}>{{ $name }}</option>
      @endforeach
    </select>
    <select name="year" class="form-control" style="width:auto; padding:4px 32px 4px 12px; font-size:14px; min-height: 34px;" onchange="this.form.submit()">
      @for($y = date('Y'); $y >= (Auth::user()->organization->created_at ? Auth::user()->organization->created_at->year : date('Y')); $y--)
        <option value="{{ $y }}" {{ $currentPeriod->year == $y ? 'selected' : '' }}>{{ $y }}</option>
      @endfor
    </select>
    <noscript><button type="submit" class="btn btn-secondary btn-sm">Filter</button></noscript>
  </form>
  <div class="flex gap-2">
    <a href="{{ request()->fullUrlWithQuery(['month' => $prevMonth->month, 'year' => $prevMonth->year]) }}" class="btn btn-secondary">
      &laquo; Bulan Sebelumnya
    </a>
    @if(!($currentPeriod->month == now()->month && $currentPeriod->year == now()->year))
    <a href="{{ request()->fullUrlWithQuery(['month' => $nextMonth->month, 'year' => $nextMonth->year]) }}" class="btn btn-secondary">
      Bulan Selanjutnya &raquo;
    </a>
    @endif
  </div>
</div>

<div class="card">
  <div class="table-wrapper" style="border:none;">
    <table>
      <thead>
        <tr>
          <th>Tanggal Pengajuan</th>
          <th>Anggota</th>
          <th>Jumlah</th>
          <th>Keterangan</th>
          <th>Status</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($withdrawals as $w)
        <tr>
          <td style="font-size:12px;color:var(--gray-400);">{{ $w->created_at->format('d/m/Y H:i') }}</td>
          <td>
            <div class="font-semibold">{{ $w->user?->name ?? '-' }}</div>
            @php $balance = $w->user?->getTotalSimpananByType('sukarela') ?? 0; @endphp
            <div class="text-sm" style="color:var(--gray-400);">Saldo: Rp {{ number_format($balance, 0, ',', '.') }}</div>
          </td>
          <td class="money text-danger font-bold">Rp {{ number_format($w->amount, 0, ',', '.') }}</td>
          <td style="font-size:13px;max-width:200px;">{{ $w->notes ?? '-' }}</td>
          <td>{!! $w->getStatusBadge() !!}</td>
          <td>
            @if($w->status === 'pending')
            <div class="flex gap-2">
              <form method="POST" action="{{ route('pengurus.deposits.approve', $w) }}">
                @csrf
                <button class="btn btn-success btn-sm" data-confirm="Setujui penarikan Rp {{ number_format($w->amount,0,',','.') }}?">
                  ✓ Setujui
                </button>
              </form>
              <form method="POST" action="{{ route('pengurus.deposits.reject', $w) }}">
                @csrf
                <button class="btn btn-danger btn-sm" data-confirm="Tolak permintaan penarikan ini?">
                  ✕ Tolak
                </button>
              </form>
            </div>
            @else
            <span class="text-muted text-sm">Sudah diproses</span>
            @endif
          </td>
        </tr>
        @empty
        <tr><td colspan="6" class="text-center text-muted" style="padding:40px;">Tidak ada permintaan penarikan</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="card-footer">{{ $withdrawals->links() }}</div>
</div>
@endsection
