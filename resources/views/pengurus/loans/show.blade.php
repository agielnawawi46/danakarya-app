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

<div style="display:flex;flex-direction:column;gap:20px;">
  
  {{-- ROW 1: Score & Profil --}}
  <div class="grid grid-2" style="align-items:stretch;">
    {{-- Credit Score Panel --}}
    @php
      $eligible = $creditInfo['eligible'];
      $score = $creditInfo['score'];
      
      if ($score >= 70) {
          $scoreColor = '#10b981'; // Green
          $scoreBg = '#ecfdf5';
      } elseif ($score >= 40) {
          $scoreColor = '#f59e0b'; // Yellow
          $scoreBg = '#fffbeb';
      } else {
          $scoreColor = '#ef4444'; // Red
          $scoreBg = '#fef2f2';
      }
      
      $radius = 46;
      $circumference = 2 * pi() * $radius;
      $offset = $circumference - ($score / 100) * $circumference;
    @endphp

    <style>
      @keyframes drawScore_{{ $loan->id }} {
        from { stroke-dashoffset: {{ $circumference }}; }
        to { stroke-dashoffset: {{ $offset }}; }
      }
    </style>

    <div class="card" style="border-color:{{ $scoreColor }};display:flex;flex-direction:column;">
      <div class="card-body" style="background:{{ $scoreBg }};border-radius:inherit;flex:1;display:flex;align-items:center;justify-content:space-between;gap:20px;padding:24px;">
        
        <div style="display:flex;align-items:center;gap:20px;flex:1;">
          {{-- Circular Score Graphic (Left) --}}
          <div style="position:relative;width:100px;height:100px;flex-shrink:0;background:white;border-radius:50%;box-shadow:0 8px 16px -4px rgba(0,0,0,0.1);">
            <svg width="100" height="100" style="transform:rotate(-90deg);position:absolute;top:0;left:0;">
              <circle cx="50" cy="50" r="{{ $radius }}" fill="none" stroke="rgba(0,0,0,0.06)" stroke-width="8" />
              <circle cx="50" cy="50" r="{{ $radius }}" fill="none" stroke="{{ $scoreColor }}" stroke-width="8" 
                      stroke-dasharray="{{ $circumference }}" 
                      stroke-dashoffset="{{ $offset }}"
                      stroke-linecap="round"
                      style="animation: drawScore_{{ $loan->id }} 1.5s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;" />
            </svg>
            <div style="position:absolute;top:0;left:0;right:0;bottom:0;display:flex;flex-direction:column;align-items:center;justify-content:center;">
              <span style="font-size:22px;font-weight:900;color:{{ $scoreColor }};line-height:1;">{{ $score }}%</span>
              <span style="font-size:10px;color:var(--gray-400);font-weight:700;text-transform:uppercase;letter-spacing:1px;margin-top:4px;">Skor</span>
            </div>
          </div>

          {{-- Text Info (Middle) --}}
          <div style="flex:1;">
            <div style="font-size:16px;font-weight:900;color:{{ $scoreColor }};margin-bottom:6px;letter-spacing:-0.02em;">
              {{ $eligible ? '✅ Layak Kredit' : '❌ Tidak Layak' }}
            </div>
            <div style="font-size:12px;color:var(--gray-700);line-height:1.4;">
              @if($score == 100)
                <strong>Kapasitas limit masih utuh.</strong>
              @else
                {{ $creditInfo['reason'] }}
              @endif
            </div>
          </div>
        </div>

        {{-- Max Installment (Right) --}}
        <div style="font-size:11px;color:var(--gray-600);background:rgba(255,255,255,0.7);padding:10px 14px;border-radius:8px;border:1px solid rgba(0,0,0,0.05);text-align:right;flex-shrink:0;">
          Maks. Angsuran:<br>
          <strong style="color:var(--gray-900);font-size:14px;">Rp {{ number_format($creditInfo['max_allowed'],0,',','.') }}</strong> / bln<br>
          <span style="font-size:10px;">(30% × Gaji)</span>
        </div>

      </div>
    </div>

    {{-- Profil Anggota --}}
    <div class="card" style="display:flex;flex-direction:column;">
      <div class="card-header">
      <div style="display: flex; align-items: center; gap: 12px;">
        <div class="stat-card-icon indigo" style="width: 36px; height: 36px; border-radius: 10px;">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
        </div>
        <h3 style="margin: 0;">Profil Anggota</h3>
      </div></div>
      <div class="card-body" style="flex:1;">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:12px;">
          <div class="user-avatar" style="width:40px;height:40px;font-size:16px;">{{ strtoupper(substr($member->name,0,2)) }}</div>
          <div>
            <div class="font-bold" style="font-size:14px;">{{ $member->name }}</div>
            <div class="text-sm text-muted">{{ $member->email }}</div>
          </div>
        </div>
        @foreach([
          ['Departemen',$member->department ?? '-'],
          ['NIK',$member->employee_id ?? '-'],
          ['Gaji Pokok','Rp '.number_format($member->getSalaryDecrypted(),0,',','.')],
          ['Total Simpanan','Rp '.number_format($member->getTotalSimpanan(),0,',','.')],
        ] as [$label,$val])
        <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--gray-100);">
          <span style="font-size:12px;color:var(--gray-400);">{{ $label }}</span>
          <span style="font-size:12px;font-weight:600;">{{ $val }}</span>
        </div>
        @endforeach
      </div>
    </div>
  </div>

  {{-- ROW 2: Data Pinjaman & Jadwal --}}
  <div class="grid grid-2" style="align-items:stretch;">
    {{-- Loan Info --}}
    <div class="card" style="display:flex;flex-direction:column;">
      <div class="card-header">
      <div style="display: flex; align-items: center; gap: 12px;">
        <div class="stat-card-icon blue" style="width: 36px; height: 36px; border-radius: 10px;">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect></svg>
        </div>
        <h3 style="margin: 0;">Data Pinjaman</h3>
      </div></div>
      <div class="card-body" style="flex:1;">
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

    {{-- Installment Schedule (if approved) --}}
    @if($loan->schedules->count())
    <div class="card" style="display:flex;flex-direction:column;">
      <div class="card-header">
      <div style="display: flex; align-items: center; gap: 12px;">
        <div class="stat-card-icon amber" style="width: 36px; height: 36px; border-radius: 10px;">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
        </div>
        <h3 style="margin: 0;">Jadwal Angsuran</h3>
      </div></div>
      <div class="table-wrapper" style="border:none;border-radius:0;flex:1;overflow-y:auto;max-height:350px;">
        <table style="font-size:13px;height:100%;">
          <thead><tr><th>#</th><th>Jatuh Tempo</th><th>Total</th><th>Status</th><th>Aksi</th></tr></thead>
          <tbody>
            @foreach($loan->schedules as $sch)
            <tr style="{{ $sch->status === 'paid' ? 'opacity:.6;' : '' }}">
              <td>{{ $sch->installment_number }}</td>
              <td style="font-size:12px;">{{ $sch->due_date->format('d/m/Y') }}</td>
              <td class="money font-semibold">Rp {{ number_format($sch->total_amount,0,',','.') }}</td>
              <td>{!! $sch->getStatusBadge() !!}</td>
              <td>
                @if($sch->status === 'pending')
                <form method="POST" action="{{ route('pengurus.loans.pay-installment', $sch) }}">
                  @csrf
                  <button class="btn btn-success btn-sm" style="padding:4px 8px;font-size:11px;" data-confirm="Tandai angsuran ke-{{ $sch->installment_number }} sebagai lunas?">Lunas</button>
                </form>
                @endif
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
    @else
    <div></div>
    @endif
  </div>
</div>
@endsection
