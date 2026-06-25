@extends('layouts.app')
@section('title', 'Neraca - Pengawas')
@section('page_title', 'Neraca Keuangan (Pengawas)')

@section('content')
<div class="page-header">
  <h1 class="page-title">Neraca Keuangan</h1>
  <a href="{{ route('pengawas.audit-finance.index') }}" class="btn btn-secondary">← Kembali</a>
</div>
@php
  $totalAssets      = $assets->sum(fn($a) => $a->getBalance());
  $totalLiabilities = $liabilities->sum(fn($a) => $a->getBalance());
  $totalEquities    = $equities->sum(fn($a) => $a->getBalance());
@endphp
@include('pengurus.reports.neraca')
@endsection
