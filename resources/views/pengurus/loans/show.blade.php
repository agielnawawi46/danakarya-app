@extends('layouts.app')
@section('title', 'Review Pinjaman')
@section('page_title', 'Review Pengajuan Pinjaman')

@section('content')
<div class="page-header">
  <div class="page-header-text">
    <h1 class="page-title">Detail Pinjaman</h1>
    <p class="page-subtitle">{{ $member->name }} — {{ $member->department }}</p>
  </div>
  <div class="flex gap-2">
    <a href="{{ route('pengurus.loans.index') }}" class="btn btn-secondary">← Kembali</a>
    @if($loan->status === 'pending')
    <form method="POST" action="{{ route('pengurus.loans.reject', $loan) }}" style="display:inline;">
      @csrf
      <input type="hidden" name="reason" id="reject-reason" value="">
      <button type="button" class="btn btn-danger" onclick="
        const r = prompt('Alasan penolakan (min 10 karakter):');
        if(r && r.length >= 10) { document.getElementById('reject-reason').value=r; this.closest('form').submit(); }
        else if(r !== null) { alert('Alasan terlalu pendek!'); }
      ">✕ Tolak</button>
    </form>
    @if($creditInfo['eligible'])
    <form method="POST" action="{{ route('pengurus.loans.approve', $loan) }}" style="display:inline;">
      @csrf
      <button class="btn btn-success" data-confirm="Setujui pinjaman Rp {{ number_format($loan->amount,0,',','.') }} untuk {{ $member->name }}?">
        ✓ Setujui & Cairkan
      </button>
    </form>
    @endif
    @endif
  </div>
</div>

<div class="grid grid-2" style="align-items:start;">
  {{-- Loan Detail --}}
  <div style="display:flex;flex-direction:column;gap:20px;">
    {{-- Credit Score Panel --}}
    @php
      $eligible = $creditInfo['eligible'];
      $scoreColor = $eligible ? 'var(--success)' : 'var(--danger)';
      $scoreBg = $eligible ? '#ecfdf5' : '#fef2f2';
    @endphp
    <div class="card" style="border-color:{{ $eligible ? '#10b981' : '#ef4444' }};">
      <div class="card-body" style="background:{{ $scoreBg }};border-radius:inherit;">
        <div style="display:flex;align-items:center;gap:16px;">
          <div style="width:72px;height:72px;border-radius:50%;background:white;border:3px solid {{ $scoreColor }};display:flex;flex-direction:column;align-items:center;justify-content:center;flex-shrink:0;">
            <span style="font-size:18px;font-weight:900;color:{{ $scoreColor }};">{{ $creditInfo['score'] }}%</span>
            <span style="font-size:9px;color:var(--gray-400);font-weight:600;text-transform:uppercase;">Skor</span>
          </div>
          <div>
            <div style="font-size:16px;font-weight:800;color:{{ $scoreColor }};">
              {{ $eligible ? '✅ Layak Kredit' : '❌ Tidak Layak' }}
            </div>
            <div style="font-size:13px;color:var(--gray-600);margin-top:4px;">{{ $creditInfo['reason'] }}</div>
            <div style="font-size:12px;color:var(--gray-400);margin-top:4px;">
              Maks angsuran: <strong>Rp {{ number_format($creditInfo['max_allowed'],0,',','.') }}</strong>
              (30% × Rp {{ number_format($creditInfo['salary'],0,',','.') }})
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- Loan Info --}}
    <div class="card">
      <div class="card-header"><h3>📋 Data Pinjaman</h3></div>
      <div class="card-body">
        @foreach([
          ['Jumlah Pinjaman','Rp '.number_format($loan->amount,0,',','.')],
          ['Bunga','$loan->interest_rate % / bulan ('.ucfirst($loan->interest_method).')'],
          ['Tenor',$loan->tenor_months.' bulan'],
          ['Angsuran/Bulan','Rp '.number_format($creditInfo['monthly_installment'] ?? $loan->getMonthlyInstallmentFlat(),0,',','.')],
          ['Tujuan',$loan->purpose],
          ['Tanggal Pengajuan',$loan->created_at->format('d F Y')],
        ] as [$label,$val])
        <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--gray-100);">
          <span style="font-size:13px;color:var(--gray-400);">{{ $label }}</span>
          <span style="font-size:13px;font-weight:600;color:var(--gray-900);text-align:right;max-width:60%;">{{ $val }}</span>
        </div>
        @endforeach
        @if($loan->status !== 'pending')
        <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--gray-100);">
          <span style="font-size:13px;color:var(--gray-400);">Status</span>
          <span>{!! $loan->getStatusBadge() !!}</span>
        </div>
        @if($loan->approved_at)
        <div style="display:flex;justify-content:space-between;padding:10px 0;">
          <span style="font-size:13px;color:var(--gray-400);">Diproses oleh</span>
          <span style="font-size:13px;font-weight:600;">{{ $loan->approvedBy?->name }}</span>
        </div>
        @endif
        @endif
      </div>
    </div>
  </div>

  {{-- Member Info + Schedule Preview --}}
  <div style="display:flex;flex-direction:column;gap:20px;">
    <div class="card">
      <div class="card-header"><h3>👤 Profil Anggota</h3></div>
      <div class="card-body">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;">
          <div class="user-avatar" style="width:48px;height:48px;font-size:18px;">{{ strtoupper(substr($member->name,0,2)) }}</div>
          <div>
            <div class="font-bold">{{ $member->name }}</div>
            <div class="text-sm text-muted">{{ $member->email }}</div>
          </div>
        </div>
        @foreach([
          ['Departemen',$member->department ?? '-'],
          ['NIK',$member->employee_id ?? '-'],
          ['Gaji Pokok','Rp '.number_format($member->salary??0,0,',','.')],
          ['Total Simpanan','Rp '.number_format($member->getTotalSimpanan(),0,',','.')],
        ] as [$label,$val])
        <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--gray-100);">
          <span style="font-size:13px;color:var(--gray-400);">{{ $label }}</span>
          <span style="font-size:13px;font-weight:600;">{{ $val }}</span>
        </div>
        @endforeach
      </div>
    </div>

    {{-- Installment Schedule (if approved) --}}
    @if($loan->schedules->count())
    <div class="card">
      <div class="card-header"><h3>📅 Jadwal Angsuran</h3></div>
      <div class="table-wrapper" style="border:none;border-radius:0;max-height:320px;overflow-y:auto;">
        <table>
          <thead><tr><th>#</th><th>Jatuh Tempo</th><th>Total</th><th>Status</th><th>Aksi</th></tr></thead>
          <tbody>
            @foreach($loan->schedules as $sch)
            <tr style="{{ $sch->status === 'paid' ? 'opacity:.6;' : '' }}">
              <td>{{ $sch->installment_number }}</td>
              <td style="font-size:12px;">{{ $sch->due_date->format('d/m/Y') }}</td>
              <td class="money">Rp {{ number_format($sch->total_amount,0,',','.') }}</td>
              <td>{!! $sch->getStatusBadge() !!}</td>
              <td>
                @if($sch->status === 'pending')
                <form method="POST" action="{{ route('pengurus.loans.pay-installment', $sch) }}">
                  @csrf
                  <button class="btn btn-success btn-sm" data-confirm="Tandai angsuran ke-{{ $sch->installment_number }} sebagai lunas?">Lunas</button>
                </form>
                @endif
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
    @endif
  </div>
</div>
@endsection
