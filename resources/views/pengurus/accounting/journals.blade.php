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

{{-- Date Filter --}}
<form class="flex gap-2" style="margin-bottom:16px;">
  <input type="date" name="from" class="form-control" style="max-width:160px;" value="{{ request('from', now()->startOfMonth()->toDateString()) }}">
  <span style="display:flex;align-items:center;color:var(--gray-400);">s/d</span>
  <input type="date" name="to" class="form-control" style="max-width:160px;" value="{{ request('to', now()->toDateString()) }}">
  <button class="btn btn-secondary">Filter</button>
</form>

<div class="card">
  <div class="table-wrapper" style="border:none;">
    <table>
      <thead>
        <tr><th>Tanggal</th><th>Keterangan</th><th>Sumber</th><th>Total Debit</th><th>Dibuat oleh</th><th>Aksi</th></tr>
      </thead>
      <tbody>
        @forelse($journals as $journal)
        <tr x-data="{open:false}">
          <td style="font-size:12px;color:var(--gray-400);">{{ $journal->date->format('d/m/Y') }}</td>
          <td class="font-semibold">{{ $journal->description }}</td>
          <td><span class="badge badge-secondary">{{ $journal->source_type }}</span></td>
          <td class="money font-bold">Rp {{ number_format($journal->lines->sum('debit'), 0, ',', '.') }}</td>
          <td style="font-size:12px;color:var(--gray-400);">{{ $journal->creator?->name ?? '-' }}</td>
          <td>
            <button @click="open=!open" class="btn btn-secondary btn-sm">
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
        @empty
        <tr><td colspan="6" class="text-center text-muted" style="padding:40px;">Belum ada jurnal dalam periode ini</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="card-footer">{{ $journals->withQueryString()->links() }}</div>
</div>
@endsection
