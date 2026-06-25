<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'Masuk') — Dana Karya</title>
  <meta name="description" content="Platform Manajemen Koperasi Karyawan Digital">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body style="background: var(--gray-900);">

<div class="auth-layout">
  <!-- Left branding panel -->
  <div class="auth-panel-left">
    <div style="position:relative; z-index:1;">
      <div style="display:flex; align-items:center; gap:12px; margin-bottom:48px;">
        <div style="width:48px;height:48px;background:linear-gradient(135deg,#6366f1,#8b5cf6);border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:22px;font-weight:900;color:white;">DK</div>
        <div>
          <div style="font-size:1.25rem;font-weight:800;color:white;">Dana Karya</div>
          <div style="font-size:12px;color:#94a3b8;">Koperasi Digital Platform</div>
        </div>
      </div>

      <h1 style="font-size:2.75rem;font-weight:900;color:white;line-height:1.1;letter-spacing:-.04em;margin-bottom:20px;">
        Kelola Koperasi<br>
        <span style="background:linear-gradient(135deg,#818cf8,#a78bfa);-webkit-background-clip:text;-webkit-text-fill-color:transparent;">Lebih Cerdas</span>
      </h1>

      <p style="font-size:1rem;color:#94a3b8;line-height:1.7;margin-bottom:48px;max-width:380px;">
        Platform multi-tenant untuk manajemen simpanan, pinjaman, payroll, akuntansi, dan distribusi SHU koperasi karyawan.
      </p>

      <!-- Feature list -->
      @foreach([
        ['icon'=>'💰','label'=>'Simpanan & Pinjaman Digital'],
        ['icon'=>'📊','label'=>'Akuntansi Double-Entry Otomatis'],
        ['icon'=>'🏢','label'=>'Multi-Tenant & Isolasi Data Mutlak'],
        ['icon'=>'📄','label'=>'Sinkronisasi Payroll & SHU'],
      ] as $feature)
      <div style="display:flex;align-items:center;gap:12px;margin-bottom:14px;">
        <div style="width:36px;height:36px;background:rgba(99,102,241,.2);border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:16px;">{{ $feature['icon'] }}</div>
        <span style="color:#e2e8f0;font-size:14px;font-weight:500;">{{ $feature['label'] }}</span>
      </div>
      @endforeach
    </div>
  </div>

  <!-- Right form panel -->
  <div class="auth-panel-right">
    <div class="auth-form-box">
      @yield('auth_content')
    </div>
  </div>
</div>

</body>
</html>
