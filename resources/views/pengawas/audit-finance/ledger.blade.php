@extends('layouts.app')
@section('title', 'Buku Besar')
@section('page_title', 'Buku Besar (Ledger)')

@section('content')
<div class="page-header">
  <h1 class="page-title">Buku Besar</h1>
  <a href="{{ route('pengawas.audit-finance.index') }}" class="btn btn-secondary">← Kembali</a>
</div>

@php
  $typeLabels = ['asset'=>'Aset','liability'=>'Kewajiban','equity'=>'Ekuitas','income'=>'Pendapatan','expense'=>'Beban'];
  $typeColors = ['asset'=>'badge-info','liability'=>'badge-warning','equity'=>'badge-primary','income'=>'badge-success','expense'=>'badge-danger'];
  $grouped = $accounts->groupBy('type');
@endphp

@foreach($typeLabels as $type => $label)
  @if(isset($grouped[$type]) && $grouped[$type]->count())
  <div class="card" style="margin-bottom:20px;">
    <div class="card-header">
      <h3>{{ $label }}</h3>
      <span class="badge {{ $typeColors[$type] }}">{{ $grouped[$type]->count() }} akun</span>
    </div>
    <div class="table-wrapper" style="border:none;">
      <table>
        <thead><tr><th>Kode</th><th>Nama Akun</th><th>Total Debit</th><th>Total Kredit</th><th>Saldo</th></tr></thead>
        <tbody>
          @foreach($grouped[$type]->sortBy('code') as $acc)
          <tr>
            <td style="font-family:monospace;font-weight:600;color:var(--brand-600);font-size:12px;">{{ $acc->code }}</td>
            <td class="font-semibold">{{ $acc->name }}</td>
            <td class="money text-danger">Rp {{ number_format($acc->journalLines->sum('debit'),0,',','.') }}</td>
            <td class="money text-success">Rp {{ number_format($acc->journalLines->sum('credit'),0,',','.') }}</td>
            <td class="money font-bold">Rp {{ number_format($acc->getBalance(),0,',','.') }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
  @endif
@endforeach
@endsection
