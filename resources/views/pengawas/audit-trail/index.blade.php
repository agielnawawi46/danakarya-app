@extends('layouts.app')
@section('title', 'Log Audit Trail')
@section('page_title', 'Log Audit Trail')

@section('content')
<div class="page-header" style="justify-content: space-between;">
  <div class="page-header-text">
    <h1 class="page-title">Log Audit Trail</h1>
    <p class="page-subtitle">Rekam jejak semua aktivitas kritis pengurus koperasi</p>
  </div>
  <a href="{{ route('pengawas.audit-trail.export', request()->all()) }}" class="btn btn-primary">Export PDF</a>
</div>

{{-- Filters --}}
<form class="flex flex-wrap gap-2" style="margin-bottom:16px;">
  <select name="action" class="form-control" style="max-width:200px;">
    <option value="">Semua Aksi</option>
    @foreach($actions as $action)
      <option value="{{ $action }}" {{ request('action') === $action ? 'selected' : '' }}>{{ $action }}</option>
    @endforeach
  </select>
  <input type="date" name="from" class="form-control" style="max-width:160px;" value="{{ request('from') }}" placeholder="Dari">
  <input type="date" name="to"   class="form-control" style="max-width:160px;" value="{{ request('to') }}"   placeholder="Sampai">
  <button class="btn btn-secondary">Filter</button>
  @if(request()->hasAny(['action','from','to','user_id']))
    <a href="{{ route('pengawas.audit-trail.index') }}" class="btn btn-secondary">Reset</a>
  @endif
</form>

<div class="card">
  <div class="table-wrapper" style="border:none;">
    <table>
      <thead>
        <tr>
          <th>Waktu</th>
          <th>Pengguna</th>
          <th>Aksi</th>
          <th>Model</th>
          <th>Deskripsi</th>
          <th>IP</th>
        </tr>
      </thead>
      <tbody>
        @forelse($logs as $log)
        <tr>
          <td style="font-size:11px;color:var(--gray-400);white-space:nowrap;">
            {{ $log->created_at->format('d/m/Y') }}<br>
            <span style="font-size:10px;">{{ $log->created_at->format('H:i:s') }}</span>
          </td>
          <td>
            <div class="font-semibold" style="font-size:13px;">{{ $log->user?->name ?? '—' }}</div>
          </td>
          <td>
            <code style="font-size:11px;background:var(--gray-100);padding:3px 7px;border-radius:4px;color:var(--brand-700);">{{ $log->action }}</code>
          </td>
          <td style="font-size:12px;color:var(--gray-400);">
            {{ $log->model }}<br>
            @if($log->model_id)<span style="font-family:monospace;font-size:10px;">#{{ $log->model_id }}</span>@endif
          </td>
          <td style="font-size:13px;max-width:250px;word-break:break-word;">{{ $log->description }}</td>
          <td style="font-size:11px;color:var(--gray-400);font-family:monospace;">{{ $log->ip_address }}</td>
        </tr>
        @empty
        <tr><td colspan="6" class="text-center text-muted" style="padding:40px;">Tidak ada log dalam periode ini</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="card-footer">{{ $logs->withQueryString()->links() }}</div>
</div>
@endsection
