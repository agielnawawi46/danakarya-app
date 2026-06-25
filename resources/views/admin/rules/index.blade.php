@extends('layouts.app')
@section('title', 'Aturan Keuangan')
@section('page_title', 'Aturan Keuangan')

@section('content')
<div class="page-header">
  <div class="page-header-text">
    <h1 class="page-title">Aturan Keuangan Koperasi</h1>
    <p class="page-subtitle">Konfigurasi nominal simpanan, bunga pinjaman, dan alokasi SHU</p>
  </div>
</div>

<div class="grid grid-2" style="align-items:start;">
  {{-- Simpanan & Pinjaman --}}
  <div class="card">
    <div class="card-header"><h3>💰 Simpanan & Pinjaman</h3></div>
    <div class="card-body">
      <form method="POST" action="{{ route('admin.rules.update') }}">
        @csrf
        @error('shu_total')<div class="alert alert-danger">{{ $message }}</div>@enderror

        <div class="form-group">
          <label class="form-label">Simpanan Pokok (Rp) <span class="req">*</span></label>
          <input type="number" name="simpanan_pokok" class="form-control" value="{{ old('simpanan_pokok', $org->simpanan_pokok) }}" min="0" step="1000" required>
          <div class="form-hint">Dibayar sekali saat bergabung koperasi</div>
        </div>
        <div class="form-group">
          <label class="form-label">Simpanan Wajib / Bulan (Rp) <span class="req">*</span></label>
          <input type="number" name="simpanan_wajib" class="form-control" value="{{ old('simpanan_wajib', $org->simpanan_wajib) }}" min="0" step="1000" required>
        </div>
        <div class="form-group">
          <label class="form-label">Bunga Pinjaman (% / bulan) <span class="req">*</span></label>
          <input type="number" name="loan_interest_rate" class="form-control" value="{{ old('loan_interest_rate', $org->loan_interest_rate) }}" min="0" max="100" step="0.1" required>
        </div>
        <div class="form-group">
          <label class="form-label">Tenor Maksimum (bulan) <span class="req">*</span></label>
          <input type="number" name="loan_max_tenor" class="form-control" value="{{ old('loan_max_tenor', $org->loan_max_tenor) }}" min="1" max="360" required>
        </div>
        <div class="form-group">
          <label class="form-label">Plafon Maksimum (Rp) <span class="req">*</span></label>
          <input type="number" name="loan_max_plafon" class="form-control" value="{{ old('loan_max_plafon', $org->loan_max_plafon) }}" min="0" step="100000" required>
        </div>
        <div class="form-group">
          <label class="form-label">Metode Bunga <span class="req">*</span></label>
          <select name="loan_interest_method" class="form-control">
            <option value="flat" {{ $org->loan_interest_method === 'flat' ? 'selected' : '' }}>Flat</option>
            <option value="annuity" {{ $org->loan_interest_method === 'annuity' ? 'selected' : '' }}>Anuitas</option>
          </select>
        </div>

        <div class="divider"></div>
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

        <button type="submit" class="btn btn-primary btn-block">Simpan Aturan Keuangan</button>
      </form>
    </div>
  </div>

  {{-- Preview --}}
  <div class="card">
    <div class="card-header"><h3>📋 Ringkasan Aturan Saat Ini</h3></div>
    <div class="card-body">
      @foreach([
        ['label'=>'Simpanan Pokok','value'=>'Rp '.number_format($org->simpanan_pokok,0,',','.')],
        ['label'=>'Simpanan Wajib/Bulan','value'=>'Rp '.number_format($org->simpanan_wajib,0,',','.')],
        ['label'=>'Bunga Pinjaman','value'=>$org->loan_interest_rate.'% / bulan'],
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
          ['label'=>'Pendidikan','key'=>'shu_pendidikan_pct','color'=>'#8b5cf6'],
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
</div>
@endsection
