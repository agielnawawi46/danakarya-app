@extends('layouts.app')
@section('title', 'Kredit Pinjaman')
@section('page_title', 'Kredit Pinjaman')

@section('content')
<div class="page-header">
  <div class="page-header-text">
    <h1 class="page-title">Kredit Pinjaman</h1>
    <p class="page-subtitle">Review dan kelola pengajuan serta angsuran pinjaman anggota</p>
  </div>
</div>

{{-- Filter --}}
<form class="flex gap-2" style="margin-bottom:16px;">
  <input name="search" class="form-control" style="max-width:250px;" placeholder="Cari nama anggota..." value="{{ request('search') }}">
  <select name="status" class="form-control" style="max-width:150px;">
    <option value="">Semua Status</option>
    @foreach(['pending'=>'Menunggu','approved'=>'Disetujui','active'=>'Aktif','rejected'=>'Ditolak','paid_off'=>'Lunas'] as $val => $label)
      <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>{{ $label }}</option>
    @endforeach
  </select>
  <button class="btn btn-secondary">Filter</button>
  @if(request()->hasAny(['search','status']))
    <a href="{{ route('pengurus.loans.index') }}" class="btn btn-secondary">Reset</a>
  @endif
</form>

<div class="card">
  <div class="table-wrapper" style="border:none;">
    <table>
      <thead>
        <tr>
          <th>Tanggal</th>
          <th>Anggota</th>
          <th>Jumlah</th>
          <th>Tenor</th>
          <th>Skor Kredit</th>
          <th>Status</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($loans as $loan)
        <tr>
          <td style="font-size:12px;color:var(--gray-400);">{{ $loan->created_at->format('d/m/Y') }}</td>
          <td>
            <div class="font-semibold">{{ $loan->user?->name ?? '-' }}</div>
            <div class="text-sm text-muted">{{ $loan->user?->department }}</div>
          </td>
          <td class="money font-bold">Rp {{ number_format($loan->amount, 0, ',', '.') }}</td>
          <td>{{ $loan->tenor_months }} bln</td>
          <td>
            @if($loan->credit_score !== null)
              <span class="badge {{ $loan->credit_score <= 30 ? 'badge-success' : 'badge-danger' }}">
                {{ $loan->credit_score }}%
              </span>
            @else
              <span class="text-muted">—</span>
            @endif
          </td>
          <td>{!! $loan->getStatusBadge() !!}</td>
          <td>
            <a href="{{ route('pengurus.loans.show', $loan) }}" class="btn btn-secondary btn-sm">
              {{ $loan->status === 'pending' ? 'Review' : 'Detail' }}
            </a>
          </td>
        </tr>
        @empty
        <tr><td colspan="7" class="text-center text-muted" style="padding:40px;">Tidak ada data pinjaman</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="card-footer">{{ $loans->withQueryString()->links() }}</div>
</div>
@endsection
