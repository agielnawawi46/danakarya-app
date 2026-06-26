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

<div class="grid grid-2" style="align-items:stretch;">
  @foreach($typeLabels as $type => $label)
    @if(isset($grouped[$type]) && $grouped[$type]->count())
    <div class="card">
      <div class="card-header">
        <div style="display: flex; align-items: center; gap: 12px;">
          <div class="stat-card-icon {{ str_replace('badge-', '', $typeColors[$type]) }}" style="width: 32px; height: 32px; border-radius: 8px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path></svg>
          </div>
          <h3 style="margin: 0;">{{ $label }}</h3>
        </div>
        <span class="badge {{ $typeColors[$type] }}">{{ $grouped[$type]->count() }} akun</span>
      </div>
      <div class="table-wrapper" style="border:none;">
        <table>
          <thead>
            <tr><th>Kode</th><th>Akun</th><th style="text-align:right;">Saldo Terkini</th></tr>
          </thead>
          <tbody>
            @foreach($grouped[$type]->sortBy('code') as $account)
            <tr>
              <td style="font-family:monospace;font-weight:700;color:var(--brand-600);font-size:12px;">{{ $account->code }}</td>
              <td>
                <div style="font-size:13px;font-weight:600;color:var(--gray-900);">{{ $account->name }}</div>
                <div style="font-size:11px;color:var(--gray-400);margin-top:2px;">Normal: {{ ucfirst($account->normal_balance) }}</div>
              </td>
              <td class="money font-bold" style="font-size:13px;text-align:right;">Rp {{ number_format($account->getBalance(), 0, ',', '.') }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
    @endif
  @endforeach
</div>
@endsection
