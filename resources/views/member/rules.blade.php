@extends('layouts.app')
@section('title', 'Informasi Aturan Keuangan')
@section('page_title', 'Informasi Keuangan Koperasi')

@section('content')
<div class="page-header">
  <div class="page-header-text">
    <h1 class="page-title">Aturan & Kebijakan Keuangan</h1>
    <p class="page-subtitle">Informasi transparan mengenai aturan simpanan, pinjaman, dan pembagian hasil di {{ $org->name }}</p>
  </div>
</div>

<div class="grid grid-2" style="margin-bottom:24px;">
  {{-- Simpanan --}}
  <div class="card">
    <div class="card-header">
      <div style="display: flex; align-items: center; gap: 12px;">
        <div class="stat-card-icon indigo" style="width: 36px; height: 36px; border-radius: 10px;">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle></svg>
        </div>
        <h3 style="margin: 0;">Aturan Simpanan</h3>
      </div>
    </div>
    <div class="card-body">
      <div style="display:grid;gap:12px;">
        <div style="padding:16px;background:var(--gray-50);border-radius:12px;border:1px solid var(--gray-100);">
          <div style="font-weight:700;color:var(--gray-800);margin-bottom:4px;">Simpanan Pokok</div>
          <div class="money" style="font-size:1.4rem;color:var(--brand-600);font-weight:900;">Rp {{ number_format($org->simpanan_pokok, 0, ',', '.') }}</div>
          <p style="font-size:13px;color:var(--gray-500);margin-top:6px;line-height:1.4;">Wajib dibayarkan sekali saat bergabung menjadi anggota koperasi.</p>
        </div>
        
        <div style="padding:16px;background:var(--gray-50);border-radius:12px;border:1px solid var(--gray-100);">
          <div style="font-weight:700;color:var(--gray-800);margin-bottom:4px;">Simpanan Wajib (Bulanan)</div>
          <div class="money" style="font-size:1.4rem;color:var(--brand-600);font-weight:900;">Rp {{ number_format($org->simpanan_wajib, 0, ',', '.') }}</div>
          <p style="font-size:13px;color:var(--gray-500);margin-top:6px;line-height:1.4;">Tabungan wajib yang harus disetorkan atau dipotong dari gaji setiap bulan.</p>
        </div>

        <div style="padding:16px;background:var(--gray-50);border-radius:12px;border:1px solid var(--gray-100);">
          <div style="font-weight:700;color:var(--gray-800);margin-bottom:4px;">Simpanan Sukarela</div>
          <div class="money" style="font-size:1.2rem;color:var(--brand-600);font-weight:900;">Bebas</div>
          <p style="font-size:13px;color:var(--gray-500);margin-top:6px;line-height:1.4;">Tabungan tambahan yang dapat disetor mandiri dengan nominal bebas dan ditarik kapan saja.</p>
        </div>
      </div>
    </div>
  </div>

  {{-- Pinjaman --}}
  <div class="card">
    <div class="card-header">
      <div style="display: flex; align-items: center; gap: 12px;">
        <div class="stat-card-icon blue" style="width: 36px; height: 36px; border-radius: 10px;">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"></rect><line x1="2" y1="10" x2="22" y2="10"></line></svg>
        </div>
        <h3 style="margin: 0;">Aturan Pinjaman (Kredit)</h3>
      </div>
    </div>
    <div class="card-body">
      <div style="display:grid;gap:12px;">
        <div style="padding:16px;background:var(--gray-50);border-radius:12px;border:1px solid var(--gray-100);">
          <div style="font-weight:700;color:var(--gray-800);margin-bottom:4px;">Plafon Maksimal Pinjaman</div>
          <div style="font-size:1.4rem;color:var(--brand-600);font-weight:900;">Rp {{ number_format($org->loan_max_plafon, 0, ',', '.') }}</div>
          <p style="font-size:13px;color:var(--gray-500);margin-top:6px;line-height:1.4;">Batas maksimal nominal pinjaman yang bisa diajukan oleh satu anggota.</p>
        </div>

        <div style="padding:16px;background:var(--gray-50);border-radius:12px;border:1px solid var(--gray-100);display:grid;grid-template-columns:1fr 1fr;gap:16px;">
          <div>
            <div style="font-weight:700;color:var(--gray-800);margin-bottom:4px;">Bunga Pinjaman ({{ ucfirst($org->loan_interest_method) }})</div>
            <div style="font-size:1.4rem;color:var(--brand-600);font-weight:900;">{{ $org->loan_interest_rate }}% <span style="font-size:12px;font-weight:normal;color:var(--gray-500);">/bln</span></div>
          </div>
          <div>
            <div style="font-weight:700;color:var(--gray-800);margin-bottom:4px;">Tenor Maksimal</div>
            <div style="font-size:1.4rem;color:var(--brand-600);font-weight:900;">{{ $org->loan_max_tenor }} <span style="font-size:12px;font-weight:normal;color:var(--gray-500);">bulan</span></div>
          </div>
          <div style="grid-column: span 2;">
            <p style="font-size:13px;color:var(--gray-500);margin-top:6px;line-height:1.4;">
              Metode perhitungan bunga koperasi ini menggunakan sistem <strong>{{ ucfirst($org->loan_interest_method) }}</strong>.
              Anggota dapat mengangsur hingga maksimal {{ $org->loan_max_tenor }} bulan sesuai kesepakatan.
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="card" style="margin-bottom: 24px;">
  <div class="card-header">
    <div style="display: flex; align-items: center; gap: 12px;">
      <div class="stat-card-icon green" style="width: 36px; height: 36px; border-radius: 10px;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
      </div>
      <h3 style="margin: 0;">Pembagian SHU (Sisa Hasil Usaha)</h3>
    </div>
  </div>
  <div class="card-body">
    <p style="margin-top:0;margin-bottom:20px;color:var(--gray-600);">Keuntungan operasional koperasi selama 1 tahun buku akan dibagikan kembali dalam bentuk SHU dengan persentase berikut:</p>
    
    <div style="display:flex;height:32px;border-radius:16px;overflow:hidden;margin-bottom:16px;">
      @if($org->shu_anggota_pct > 0)
      <div style="width:{{ $org->shu_anggota_pct }}%;background:#10b981;display:flex;align-items:center;justify-content:center;color:white;font-size:11px;font-weight:bold;overflow:hidden;white-space:nowrap;padding:0 4px;" title="Anggota ({{ $org->shu_anggota_pct }}%)">Anggota {{ $org->shu_anggota_pct }}%</div>
      @endif
      @if($org->shu_pengurus_pct > 0)
      <div style="width:{{ $org->shu_pengurus_pct }}%;background:#f59e0b;display:flex;align-items:center;justify-content:center;color:white;font-size:11px;font-weight:bold;overflow:hidden;white-space:nowrap;padding:0 4px;" title="Pengurus ({{ $org->shu_pengurus_pct }}%)">Pengurus {{ $org->shu_pengurus_pct }}%</div>
      @endif
      @if($org->shu_karyawan_pct > 0)
      <div style="width:{{ $org->shu_karyawan_pct }}%;background:#3b82f6;display:flex;align-items:center;justify-content:center;color:white;font-size:11px;font-weight:bold;overflow:hidden;white-space:nowrap;padding:0 4px;" title="Karyawan ({{ $org->shu_karyawan_pct }}%)">Karyawan {{ $org->shu_karyawan_pct }}%</div>
      @endif
      @if($org->shu_pendidikan_pct > 0)
      <div style="width:{{ $org->shu_pendidikan_pct }}%;background:#8b5cf6;display:flex;align-items:center;justify-content:center;color:white;font-size:11px;font-weight:bold;overflow:hidden;white-space:nowrap;padding:0 4px;" title="Pendidikan ({{ $org->shu_pendidikan_pct }}%)">Pend. {{ $org->shu_pendidikan_pct }}%</div>
      @endif
      @if($org->shu_dana_cadangan_pct > 0)
      <div style="width:{{ $org->shu_dana_cadangan_pct }}%;background:#6366f1;display:flex;align-items:center;justify-content:center;color:white;font-size:11px;font-weight:bold;overflow:hidden;white-space:nowrap;padding:0 4px;" title="Cadangan ({{ $org->shu_dana_cadangan_pct }}%)">Cadangan {{ $org->shu_dana_cadangan_pct }}%</div>
      @endif
    </div>

    <div class="grid grid-3" style="gap:16px;">
      <div style="padding:16px;background:var(--gray-50);border-radius:12px;border:1px solid var(--gray-200);">
        <div style="font-weight:800;color:var(--gray-700);margin-bottom:8px;font-size:16px;">Alokasi Anggota</div>
        <p style="font-size:13px;color:var(--gray-600);margin:0;line-height:1.4;">Dibagikan proporsional berdasarkan total simpanan dan partisipasi transaksi anggota selama tahun buku tersebut.</p>
      </div>
      <div style="padding:16px;background:var(--gray-50);border-radius:12px;border:1px solid var(--gray-200);">
        <div style="font-weight:800;color:var(--gray-700);margin-bottom:8px;font-size:16px;">Alokasi Internal</div>
        <p style="font-size:13px;color:var(--gray-600);margin:0;line-height:1.4;">Meliputi bagian pengurus dan karyawan sebagai insentif operasional pengelolaan koperasi.</p>
      </div>
      <div style="padding:16px;background:var(--gray-50);border-radius:12px;border:1px solid var(--gray-200);">
        <div style="font-weight:800;color:var(--gray-700);margin-bottom:8px;font-size:16px;">Cadangan & Pendidikan</div>
        <p style="font-size:13px;color:var(--gray-600);margin:0;line-height:1.4;">Disimpan kembali ke kas untuk memperkuat kapasitas modal, serta dana sosial/pendidikan untuk anggota.</p>
      </div>
    </div>
  </div>
</div>
@endsection
