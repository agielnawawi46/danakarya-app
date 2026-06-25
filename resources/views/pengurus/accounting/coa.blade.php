@extends('layouts.app')
@section('title', 'Bagan Akun (COA)')
@section('page_title', 'Chart of Accounts')

@section('content')
<div class="page-header">
  <div class="page-header-text">
    <h1 class="page-title">Bagan Akun (COA)</h1>
    <p class="page-subtitle">Daftar rekening akuntansi koperasi</p>
  </div>
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
        <thead>
          <tr><th>Kode</th><th>Nama Akun</th><th>Tipe Normal</th><th>Saldo</th></tr>
        </thead>
        <tbody>
          @foreach($grouped[$type]->sortBy('code') as $account)
          <tr>
            <td style="font-family:monospace;font-weight:600;color:var(--brand-600);">{{ $account->code }}</td>
            <td class="font-semibold">{{ $account->name }}</td>
            <td>
              <span class="badge badge-secondary">
                {{ $account->normal_balance === 'debit' ? 'Debit' : 'Kredit' }}
              </span>
            </td>
            <td class="money font-bold">Rp {{ number_format($account->getBalance(), 0, ',', '.') }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
  @endif
@endforeach
@endsection
