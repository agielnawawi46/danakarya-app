@extends('layouts.app')
@section('title', 'Aturan Keuangan')
@section('page_title', 'Aturan Keuangan')

@section('content')
<div class="page-header">
  <div class="page-header-text">
    <h1 class="page-title">Aturan Keuangan Koperasi</h1>
    <p class="page-subtitle">Konfigurasi nominal simpanan, bunga pinjaman, dan alokasi SHU</p>
  </div>
  <div class="page-header-actions">
    <button type="button" class="btn btn-primary" id="toggleFormBtn" onclick="toggleForm()">
      <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
      <span>Pengaturan Keuangan</span>
    </button>
  </div>
</div>

<div class="grid" id="mainGrid" style="align-items:stretch;">
  {{-- Preview --}}
  <div class="card">
    <div class="card-header">
      <div style="display: flex; align-items: center; gap: 12px;">
        <div class="stat-card-icon blue" style="width: 36px; height: 36px; border-radius: 10px;">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect></svg>
        </div>
        <h3 style="margin: 0;">Ringkasan Aturan Saat Ini</h3>
      </div></div>
    <div class="card-body">
      @foreach([
        ['label'=>'Tanggal Tagihan (Masuk)','value'=>'Tgl '.$org->deposit_date],
        ['label'=>'Tanggal Payroll (Sync)','value'=>'Tgl '.$org->payroll_date],
        ['label'=>'Simpanan Pokok','value'=>'Rp '.number_format($org->simpanan_pokok,0,',','.')],
        ['label'=>'Simpanan Wajib/Bulan','value'=>'Rp '.number_format($org->simpanan_wajib,0,',','.')],
        ['label'=>'Bunga Pinjaman','value'=>$org->loan_interest_rate.'% / bulan'],
        ['label'=>'Batas Angsuran dari Gaji','value'=>$org->max_loan_salary_pct.'%'],
        ['label'=>'Tenor Maks','value'=>$org->loan_max_tenor.' bulan'],
        ['label'=>'Plafon Maks','value'=>'Rp '.number_format($org->loan_max_plafon,0,',','.')],
        ['label'=>'Metode Bunga','value'=>ucfirst($org->loan_interest_method)],
      ] as $item)
      <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--gray-100);">
        <span style="font-size:13px;color:var(--gray-500);">{{ $item['label'] }}</span>
        <span style="font-size:13px;font-weight:700;color:var(--gray-900);">{{ $item['value'] }}</span>
      </div>
      @endforeach

      <div style="margin-top:16px;">
        <div style="font-size:12px;font-weight:700;color:var(--gray-400);text-transform:uppercase;letter-spacing:.08em;margin-bottom:8px;">Alokasi SHU</div>
        @foreach([
          ['label'=>'Dana Cadangan','key'=>'shu_dana_cadangan_pct','color'=>'#6366f1'],
          ['label'=>'Bagian Anggota','key'=>'shu_anggota_pct','color'=>'#10b981'],
          ['label'=>'Pengurus','key'=>'shu_pengurus_pct','color'=>'#f59e0b'],
          ['label'=>'Karyawan','key'=>'shu_karyawan_pct','color'=>'#3b82f6'],
          ['label'=>'Pendidikan & Sosial','key'=>'shu_pendidikan_pct','color'=>'#8b5cf6'],
        ] as $s)
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;">
          <div style="width:8px;height:8px;border-radius:50%;background:{{ $s['color'] }};flex-shrink:0;"></div>
          <span style="font-size:13px;flex:1;color:var(--gray-600);">{{ $s['label'] }}</span>
          <span style="font-size:13px;font-weight:700;">{{ $org->{$s['key']} }}%</span>
          <div style="width:80px;height:6px;background:var(--gray-100);border-radius:3px;overflow:hidden;">
            <div style="width:{{ $org->{$s['key']} }}%;height:100%;background:{{ $s['color'] }};border-radius:3px;"></div>
          </div>
        </div>
        @endforeach
      </div>
    </div>
  </div>

  {{-- Simpanan & Pinjaman --}}
  <div class="card" id="formCard" style="display: none;">
    <div class="card-header">
      <div style="display: flex; align-items: center; gap: 12px;">
        <div class="stat-card-icon green" style="width: 36px; height: 36px; border-radius: 10px;">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 1v2m0 18v2M4.22 4.22l1.42 1.42m12.72 12.72l1.42 1.42M1 12h2m18 0h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"></path></svg>
        </div>
        <h3 style="margin: 0;">Simpanan & Pinjaman</h3>
      </div></div>
    <div class="card-body">
      <form method="POST" action="{{ route('admin.rules.update') }}">
        @csrf
        @error('shu_total')<div class="alert alert-danger">{{ $message }}</div>@enderror

        <div id="step1">
        <div class="sidebar-section-label" style="font-size:10px;font-weight:700;color:var(--gray-500);letter-spacing:.12em;text-transform:uppercase;">Jadwal Sinkronisasi</div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
          <div class="form-group">
            <label class="form-label">Tanggal Masuk Tagihan <span class="req">*</span></label>
            <input type="number" name="deposit_date" class="form-control" value="{{ old('deposit_date', $org->deposit_date) }}" min="1" max="28" required>
            <div class="form-hint">Tanggal 1-28 (Simpanan Wajib)</div>
          </div>
          <div class="form-group">
            <label class="form-label">Tanggal Import Payroll <span class="req">*</span></label>
            <input type="number" name="payroll_date" class="form-control" value="{{ old('payroll_date', $org->payroll_date) }}" min="1" max="28" required>
            <div class="form-hint">Tanggal 1-28</div>
          </div>
        </div>

        <div class="sidebar-section-label" style="font-size:10px;font-weight:700;color:var(--gray-500);letter-spacing:.12em;text-transform:uppercase;margin-top:10px;">Simpanan</div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
          <div class="form-group">
            <label class="form-label">Simpanan Pokok (Rp) <span class="req">*</span></label>
            <input type="number" name="simpanan_pokok" class="form-control" value="{{ old('simpanan_pokok', $org->simpanan_pokok) }}" min="0" step="1000" required>
            <div class="form-hint">Dibayar sekali saat bergabung</div>
          </div>
          <div class="form-group">
            <label class="form-label">Simpanan Wajib / Bulan (Rp) <span class="req">*</span></label>
            <input type="number" name="simpanan_wajib" class="form-control" value="{{ old('simpanan_wajib', $org->simpanan_wajib) }}" min="0" step="1000" required>
          </div>
        </div>

        <div class="sidebar-section-label" style="font-size:10px;font-weight:700;color:var(--gray-500);letter-spacing:.12em;text-transform:uppercase;margin-top:10px;">Pinjaman (Kredit)</div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
          <div class="form-group">
            <label class="form-label">Bunga Pinjaman (% / bulan) <span class="req">*</span></label>
            <input type="number" name="loan_interest_rate" class="form-control" value="{{ old('loan_interest_rate', $org->loan_interest_rate) }}" min="0" max="100" step="0.1" required>
          </div>
          <div class="form-group">
            <label class="form-label">Tenor Maksimum (bulan) <span class="req">*</span></label>
            <input type="number" name="loan_max_tenor" class="form-control" value="{{ old('loan_max_tenor', $org->loan_max_tenor) }}" min="1" max="360" required>
          </div>
          <div class="form-group">
            <label class="form-label">Batas Angsuran / Gaji (%) <span class="req">*</span></label>
            <input type="number" name="max_loan_salary_pct" class="form-control" value="{{ old('max_loan_salary_pct', $org->max_loan_salary_pct) }}" min="1" max="100" required>
            <div class="form-hint">Contoh: 30</div>
          </div>
          <div class="form-group">
            <label class="form-label">Plafon Maksimum (Rp) <span class="req">*</span></label>
            <input type="number" name="loan_max_plafon" class="form-control" value="{{ old('loan_max_plafon', $org->loan_max_plafon) }}" min="0" step="100000" required>
          </div>
          <div class="form-group" style="grid-column: 1 / -1;">
            <label class="form-label">Metode Bunga <span class="req">*</span></label>
            <select name="loan_interest_method" class="form-control">
              <option value="flat" {{ $org->loan_interest_method === 'flat' ? 'selected' : '' }}>Flat</option>
              <option value="annuity" {{ $org->loan_interest_method === 'annuity' ? 'selected' : '' }}>Anuitas</option>
            </select>
          </div>
        </div>

        <button type="button" class="btn btn-primary btn-block" onclick="document.getElementById('step1').style.display='none'; document.getElementById('step2').style.display='block';">Lanjut ke Alokasi SHU &rarr;</button>
        </div>

        <div id="step2" style="display: none;">
        <div class="sidebar-section-label" style="font-size:10px;font-weight:700;color:var(--gray-500);letter-spacing:.12em;text-transform:uppercase;">Alokasi SHU (%)</div>
        <div style="background:var(--brand-50);border:1px solid var(--brand-200);border-radius:8px;padding:12px;margin:10px 0;font-size:12px;color:var(--brand-700);">
          Total semua persentase SHU harus = 100%
        </div>
        @foreach([
          ['key'=>'shu_dana_cadangan_pct','label'=>'Dana Cadangan','default'=>40],
          ['key'=>'shu_anggota_pct','label'=>'Bagian Anggota','default'=>40],
          ['key'=>'shu_pengurus_pct','label'=>'Dana Pengurus','default'=>5],
          ['key'=>'shu_karyawan_pct','label'=>'Dana Karyawan','default'=>5],
          ['key'=>'shu_pendidikan_pct','label'=>'Dana Pendidikan & Sosial','default'=>10],
        ] as $shu)
        <div class="form-group">
          <label class="form-label">{{ $shu['label'] }} (%)</label>
          <input type="number" name="{{ $shu['key'] }}" class="form-control" value="{{ old($shu['key'], $org->{$shu['key']}) }}" min="0" max="100" step="0.5" required>
        </div>
        @endforeach

        <div style="display: flex; gap: 10px;">
          <button type="button" class="btn btn-secondary" style="flex: 1; justify-content: center;" onclick="document.getElementById('step2').style.display='none'; document.getElementById('step1').style.display='block';">&larr; Kembali</button>
          <button type="submit" class="btn btn-primary" style="flex: 1; justify-content: center;">Simpan Aturan</button>
        </div>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
function toggleForm() {
    var formCard = document.getElementById('formCard');
    var btnText = document.querySelector('#toggleFormBtn span');
    var mainGrid = document.getElementById('mainGrid');
    
    if (formCard.style.display === 'none') {
        formCard.style.display = 'block';
        mainGrid.classList.add('grid-2');
        btnText.innerText = 'Tutup';
    } else {
        formCard.style.display = 'none';
        mainGrid.classList.remove('grid-2');
        btnText.innerText = 'Pengaturan Keuangan';
    }
}
</script>

{{-- Riwayat Perubahan Aturan Keuangan --}}
@if($auditLogs->isNotEmpty())
<div class="card" style="margin-top: 20px;">
  <div class="card-header">
    <div style="display:flex;align-items:center;gap:12px;">
      <div class="stat-card-icon purple" style="width:36px;height:36px;border-radius:10px;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
        </svg>
      </div>
      <div>
        <h3 style="margin:0;">Riwayat Perubahan Aturan Keuangan</h3>
        <p style="margin:0;font-size:12px;color:var(--gray-500);">Audit trail otomatis — setiap perubahan aturan dicatat sistem</p>
      </div>
    </div>
  </div>
  <div class="card-body" style="padding:0;">
    <table class="table" style="margin:0;">
      <thead>
        <tr>
          <th>Waktu</th>
          <th>Diubah Oleh</th>
          <th>Field yang Berubah</th>
          <th>Sebelum</th>
          <th>Sesudah</th>
        </tr>
      </thead>
      <tbody>
        @foreach($auditLogs as $log)
        <tr>
          <td style="white-space:nowrap;font-size:12px;color:var(--gray-500);">
            {{ $log->created_at->format('d M Y') }}<br>
            <span style="font-weight:600;color:var(--gray-700);">{{ $log->created_at->format('H:i') }}</span>
          </td>
          <td>
            <span style="font-weight:600;font-size:13px;">{{ $log->user?->name ?? 'Sistem' }}</span><br>
            <span style="font-size:11px;color:var(--gray-400);">{{ $log->user?->getRoleNames()->first() }}</span>
          </td>
          <td style="font-size:12px;max-width:200px;">
            @if($log->new_values)
              @foreach(array_keys($log->new_values) as $field)
                <span style="display:inline-block;background:var(--brand-50);color:var(--brand-700);border-radius:4px;padding:2px 6px;font-size:11px;font-weight:600;margin:2px 2px 2px 0;">{{ $field }}</span>
              @endforeach
            @else
              <span style="color:var(--gray-400);">—</span>
            @endif
          </td>
          <td style="font-size:12px;color:var(--danger-600);">
            @if($log->old_values)
              @foreach($log->old_values as $field => $val)
                <div style="margin-bottom:2px;"><span style="color:var(--gray-400);font-size:11px;">{{ $field }}:</span> <strong>{{ $val }}</strong></div>
              @endforeach
            @else —
            @endif
          </td>
          <td style="font-size:12px;color:var(--success-600);">
            @if($log->new_values)
              @foreach($log->new_values as $field => $val)
                <div style="margin-bottom:2px;"><span style="color:var(--gray-400);font-size:11px;">{{ $field }}:</span> <strong>{{ $val }}</strong></div>
              @endforeach
            @else —
            @endif
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@else
<div class="card" style="margin-top:20px;">
  <div class="card-body" style="text-align:center;padding:32px;color:var(--gray-400);">
    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin-bottom:8px;opacity:0.4;"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
    <p style="margin:0;font-size:13px;">Belum ada riwayat perubahan aturan keuangan.</p>
  </div>
</div>
@endif

@endsection
