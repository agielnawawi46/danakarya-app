@extends('layouts.app')
@section('title', 'Buku Jurnal')
@section('page_title', 'Buku Jurnal')

@section('content')
<div class="page-header">
  <div class="page-header-text">
    <h1 class="page-title">Buku Jurnal</h1>
    <p class="page-subtitle">Semua jurnal transaksi akuntansi double-entry</p>
  </div>
  <a href="{{ route('pengurus.accounting.journals.create') }}" class="btn btn-primary">
    + Jurnal Manual
  </a>
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
        <tr><th>Tanggal</th><th>Keterangan</th><th>Sumber</th><th>Total Debit</th><th>Dibuat oleh</th><th>Aksi</th></tr>
      </thead>
      @forelse($journals as $journal)
        <tbody x-data="{open:false}">
          <tr>
            <td style="font-size:12px;color:var(--gray-400);">{{ $journal->date->format('d/m/Y') }}</td>
            <td class="font-semibold">{{ $journal->description }}</td>
            <td><span class="badge badge-secondary">{{ $journal->source_type }}</span></td>
            <td class="money font-bold">Rp {{ number_format($journal->lines->sum('debit'), 0, ',', '.') }}</td>
            <td style="font-size:12px;color:var(--gray-400);">{{ $journal->creator?->name ?? '-' }}</td>
            <td>
              <button type="button" @click="open=!open" class="btn btn-secondary btn-sm" x-text="open ? 'Tutup' : 'Detail'">
                Detail
              </button>
            </td>
          </tr>
          {{-- Journal Lines Sub-row --}}
          <tr x-show="open" style="display:none;" x-cloak>
            <td colspan="6" style="padding:0;">
              <div style="background:var(--gray-50);padding:12px 16px;border-top:1px solid var(--gray-200);">
                <table style="width:100%;">
                  <thead><tr style="background:none;">
                    <th style="font-size:11px;">Kode</th>
                    <th style="font-size:11px;">Nama Akun</th>
                    <th style="font-size:11px;">Keterangan</th>
                    <th style="font-size:11px;">Debit</th>
                    <th style="font-size:11px;">Kredit</th>
                  </tr></thead>
                  <tbody>
                    @foreach($journal->lines as $line)
                    <tr style="background:none;">
                      <td style="font-family:monospace;font-size:12px;color:var(--brand-600);">{{ $line->account?->code }}</td>
                      <td style="font-size:13px;">{{ $line->account?->name }}</td>
                      <td style="font-size:12px;color:var(--gray-400);">{{ $line->description }}</td>
                      <td class="money" style="color:var(--gray-700);">{{ $line->debit > 0 ? 'Rp '.number_format($line->debit,0,',','.') : '—' }}</td>
                      <td class="money" style="color:var(--gray-700);">{{ $line->credit > 0 ? 'Rp '.number_format($line->credit,0,',','.') : '—' }}</td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </td>
          </tr>
        </tbody>
        @empty
        <tbody>
          <tr><td colspan="6" class="text-center text-muted" style="padding:40px;">Belum ada jurnal dalam periode ini</td></tr>
        </tbody>
        @endforelse
    </table>
  </div>
  <div class="card-footer">{{ $journals->withQueryString()->links() }}</div>
</div>
@endsection
