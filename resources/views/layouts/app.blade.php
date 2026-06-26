<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Dashboard') — {{ config('app.name') }}</title>
  <meta name="description" content="@yield('meta_description', 'Platform Manajemen Koperasi Karyawan Digital')">

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

  @vite(['resources/css/app.css', 'resources/js/app.js'])

  <!-- Alpine.js -->
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body>
<div class="app-wrapper">

  <!-- ═══ SIDEBAR ═══════════════════════════════════════════════════════ -->
  <aside class="sidebar">
    <div class="sidebar-brand">
      <div class="sidebar-brand-icon" style="background:transparent; box-shadow:none;">
        <img src="{{ asset('images/logo.jpg') }}" alt="Logo" style="width:100%; height:100%; object-fit:contain; border-radius:20px;">
      </div>
      <div class="sidebar-brand-text">
        <span class="sidebar-brand-name">Dana Karya</span>
        <span class="sidebar-brand-sub">
          @auth
            {{ auth()->user()->organization?->name ?? 'Platform' }}
          @endauth
        </span>
      </div>
    </div>

    <nav class="sidebar-nav">
      @auth
        @if(auth()->user()->isSuperadmin())
          @include('components.sidebar-superadmin')
        @elseif(auth()->user()->isAdmin())
          @include('components.sidebar-admin')
        @elseif(auth()->user()->isPengurus())
          @include('components.sidebar-pengurus')
        @elseif(auth()->user()->isPengawas())
          @include('components.sidebar-pengawas')
        @elseif(auth()->user()->isAnggota())
          @include('components.sidebar-member')
        @endif
      @endauth
    </nav>

    <div class="sidebar-footer">
      @auth
      <div class="sidebar-user">
        <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</div>
        <div class="user-info">
          <div class="user-name">{{ auth()->user()->name }}</div>
          <div class="user-role">{{ auth()->user()->getRoleLabel() }}</div>
        </div>
      </div>
      <form method="POST" action="{{ route('logout') }}" class="mt-2">
        @csrf
        <button type="submit" class="btn btn-secondary btn-sm btn-block" style="margin-top:8px;">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
          Keluar
        </button>
      </form>
      @endauth
    </div>
  </aside>

  <!-- ═══ MAIN CONTENT ═══════════════════════════════════════════════════ -->
  <div class="main-content">

    <!-- Topbar -->
    <header class="topbar">
      <button id="sidebar-toggle" class="btn btn-secondary btn-sm" style="display:none;">☰</button>
      <h1 class="topbar-title">@yield('page_title', 'Dashboard')</h1>
      <div class="flex items-center gap-3">
        @auth
        <span class="topbar-badge">
          {{ auth()->user()->getRoleLabel() }}
        </span>
        @endauth
      </div>
    </header>

    <!-- Page Content -->
    <main class="page-content">
      <!-- Flash Messages -->
      @if(session('success'))
        <div class="alert alert-success auto-dismiss">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
          {{ session('success') }}
          <button class="alert-dismiss" type="button">✕</button>
        </div>
      @endif
      @if(session('error') || $errors->any())
        <div class="alert alert-danger">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
          <div>
            {{ session('error') }}
            @if($errors->any())
              <ul style="margin:4px 0 0; padding-left:16px;">
                @foreach($errors->all() as $err) <li>{{ $err }}</li> @endforeach
              </ul>
            @endif
          </div>
        </div>
      @endif
      @if(session('warning'))
        <div class="alert alert-warning auto-dismiss">
          ⚠️ {{ session('warning') }}
        </div>
      @endif
      @if(session('info'))
        <div class="alert alert-info auto-dismiss">
          ℹ️ {{ session('info') }}
        </div>
      @endif

      {{-- Mandatory Setup Alert for Admin --}}
      @auth
        @if(auth()->user()->isAdmin() && auth()->user()->organization && !auth()->user()->organization->isConfigured())
          <div class="alert alert-warning" style="border-left-color:#f59e0b; background:linear-gradient(to right, #fffbeb, #fef3c7);">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
            <div style="flex:1;">
              <strong>Koperasi belum dikonfigurasi!</strong>
              Lengkapi profil dan aturan keuangan koperasi Anda untuk mengaktifkan semua fitur.
            </div>
            <a href="{{ route('admin.organization.setup') }}" class="btn btn-warning btn-sm">Setup Sekarang →</a>
          </div>
        @endif
      @endauth

      @yield('content')
    </main>

  </div><!-- /main-content -->
</div><!-- /app-wrapper -->

@stack('scripts')
</body>
</html>
