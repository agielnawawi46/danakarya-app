@extends('layouts.app')
@section('title', 'Loket Simpanan')
@section('page_title', 'Loket Simpanan')

@section('content')
<div class="page-header">
  <div class="page-header-text">
    <h1 class="page-title">Loket Simpanan</h1>
    <p class="page-subtitle">Kelola setoran simpanan anggota koperasi</p>
  </div>
  <a href="{{ route('pengurus.deposits.create') }}" class="btn btn-primary">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
    Input Setoran Tunai
  </a>
</div>

{{-- Filter --}}
<form class="flex gap-2" style="margin-bottom:16px;">
  <input name="search" class="form-control" style="max-width:250px;" placeholder="Cari nama anggota..." value="{{ request('search') }}">
  <select name="type" class="form-control" style="max-width:150px;">
    <option value="">Semua Jenis</option>
    <option value="pokok"    {{ request('type') === 'pokok'    ? 'selected' : '' }}>Pokok</option>
    <option value="wajib"    {{ request('type') === 'wajib'    ? 'selected' : '' }}>Wajib</option>
    <option value="sukarela" {{ request('type') === 'sukarela' ? 'selected' : '' }}>Sukarela</option>
  </select>
  <select name="status" class="form-control" style="max-width:150px;">
    <option value="">Semua Status</option>
    <option value="pending"   {{ request('status') === 'pending'   ? 'selected' : '' }}>Pending</option>
    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai</option>
    <option value="rejected"  {{ request('status') === 'rejected'  ? 'selected' : '' }}>Ditolak</option>
  </select>
  <button class="btn btn-secondary">Filter</button>
  @if(request()->hasAny(['search','type','status']))
    <a href="{{ route('pengurus.deposits.index') }}" class="btn btn-secondary">Reset</a>
  @endif
</form>

<div class="card">
  <div class="table-wrapper" style="border:none;">
    <table>
      <thead>
        <tr>
          <th>Tanggal</th>
          <th>Anggota</th>
          <th>Jenis Simpanan</th>
          <th>Jumlah</th>
          <th>Status</th>
          <th>Dicatat oleh</th>
        </tr>
      </thead>
      <tbody>
        @forelse($deposits as $deposit)
        <tr>
          <td style="font-size:12px;color:var(--gray-400);">{{ $deposit->created_at->format('d/m/Y H:i') }}</td>
          <td>
            <div class="font-semibold">{{ $deposit->user?->name ?? '-' }}</div>
            <div class="text-sm text-muted">{{ $deposit->user?->employee_id }}</div>
          </td>
          <td>
            @php
              $typeColors = ['pokok'=>'badge-primary','wajib'=>'badge-info','sukarela'=>'badge-success'];
              $typeLabels = ['pokok'=>'Simpanan Pokok','wajib'=>'Simpanan Wajib','sukarela'=>'Simpanan Sukarela'];
            @endphp
            <span class="badge {{ $typeColors[$deposit->type] ?? 'badge-secondary' }}">
              {{ $typeLabels[$deposit->type] ?? ucfirst($deposit->type) }}
            </span>
          </td>
          <td class="money text-success font-bold">Rp {{ number_format($deposit->amount, 0, ',', '.') }}</td>
          <td>{!! $deposit->getStatusBadge() !!}</td>
          <td style="font-size:12px;color:var(--gray-400);">
            {{ $deposit->processedBy?->name ?? '-' }}
          </td>
        </tr>
        @empty
        <tr><td colspan="6" class="text-center text-muted" style="padding:40px;">Belum ada data setoran</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="card-footer">{{ $deposits->withQueryString()->links() }}</div>
</div>
@endsection
