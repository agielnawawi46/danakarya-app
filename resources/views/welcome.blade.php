<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DanaKarya — Koperasi Digital Masa Depan</title>
    @vite(['resources/css/welcome.css', 'resources/js/app.js'])
    <script defer src="https://unpkg.com/@alpinejs/collapse@3.x.x/dist/collapse.min.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-[#f8fafc] antialiased text-slate-800"
      x-data="{
          scrolled: false,
          mobileOpen: false,
          showScrollTop: false,
          counters: { anggota: 0, aset: 0, puas: 0, layanan: 0 },
          init() {
              window.addEventListener('scroll', () => {
                  this.scrolled = window.pageYOffset > 60;
                  this.showScrollTop = window.pageYOffset > 600;
              });
          }
      }"
      @scroll.window="scrolled = window.pageYOffset > 60; showScrollTop = window.pageYOffset > 600">

{{-- ================= FLOATING NAV ================= --}}
<header class="fixed top-0 left-0 right-0 z-[100] px-4 md:px-10 py-4 transition-all duration-500"
        :class="scrolled ? 'bg-white/80 backdrop-blur-xl border-b border-slate-100 shadow-sm' : ''">
    <nav class="max-w-7xl mx-auto flex items-center justify-between">
        <a href="#beranda" class="flex items-center gap-2.5 group">
            <img src="{{ asset('images/DanaKarya.png') }}" alt="DanaKarya" class="h-9 w-auto">
        </a>
        <div class="hidden md:flex items-center gap-8">
            <a href="#beranda" class="text-[10px] font-black uppercase tracking-[0.25em] text-slate-500 hover:text-slate-900 transition-colors">Beranda</a>
            <a href="#fitur" class="text-[10px] font-black uppercase tracking-[0.25em] text-slate-500 hover:text-slate-900 transition-colors">Fitur</a>
            <a href="#tentang" class="text-[10px] font-black uppercase tracking-[0.25em] text-slate-500 hover:text-slate-900 transition-colors">Tentang</a>
            <a href="#kontak" class="text-[10px] font-black uppercase tracking-[0.25em] text-slate-500 hover:text-slate-900 transition-colors">Kontak</a>
            @auth
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="px-6 py-3 border border-slate-200 text-slate-600 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-50 transition-all">
                        Keluar
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="px-6 py-3 bg-slate-900 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-blue-600 transition-all shadow-lg shadow-slate-200">
                    Masuk
                </a>
            @endauth
        </div>
        <button @click="mobileOpen = !mobileOpen" class="md:hidden p-2 text-slate-600">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
    </nav>
    {{-- Mobile Menu --}}
    <div x-show="mobileOpen" x-collapse class="md:hidden mt-4 bg-white rounded-2xl shadow-xl border border-slate-100 p-4 space-y-2">
        <a href="#beranda" @click="mobileOpen = false" class="block px-4 py-3 text-xs font-black uppercase tracking-widest text-slate-600 hover:bg-slate-50 rounded-xl">Beranda</a>
        <a href="#fitur" @click="mobileOpen = false" class="block px-4 py-3 text-xs font-black uppercase tracking-widest text-slate-600 hover:bg-slate-50 rounded-xl">Fitur</a>
        <a href="#tentang" @click="mobileOpen = false" class="block px-4 py-3 text-xs font-black uppercase tracking-widest text-slate-600 hover:bg-slate-50 rounded-xl">Tentang</a>
        <a href="#kontak" @click="mobileOpen = false" class="block px-4 py-3 text-xs font-black uppercase tracking-widest text-slate-600 hover:bg-slate-50 rounded-xl">Kontak</a>
        @auth
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full block px-4 py-3 text-xs font-black uppercase tracking-widest text-slate-600 border border-slate-200 rounded-xl text-center hover:bg-slate-50">Keluar</button>
            </form>
        @else
            <a href="{{ route('login') }}" class="block px-4 py-3 text-xs font-black uppercase tracking-widest text-white bg-slate-900 rounded-xl text-center">Masuk</a>
        @endauth
    </div>
</header>

<main class="space-y-0">

    {{-- ================= HERO ================= --}}
    <section id="beranda" class="relative min-h-screen flex items-center overflow-hidden bg-gradient-to-b from-white via-blue-50/20 to-slate-50/50">
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-40 -right-40 w-[600px] h-[600px] bg-blue-100/40 rounded-full blur-[80px] animate-pulse"></div>
            <div class="absolute -bottom-40 -left-40 w-[500px] h-[500px] bg-indigo-100/30 rounded-full blur-[80px]" style="animation: pulse 4s ease-in-out infinite"></div>
            <div class="absolute top-1/4 left-1/3 w-[400px] h-[400px] bg-cyan-100/20 rounded-full blur-[80px]" style="animation: pulse 6s ease-in-out infinite"></div>
        </div>

        <div class="max-w-7xl mx-auto px-6 md:px-12 w-full relative z-10 pt-32 pb-20">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                <div class="space-y-10">
                    <div class="space-y-6">
                        <h1 class="text-5xl md:text-7xl lg:text-8xl font-black tracking-tighter leading-[0.85] text-slate-900">
                            Solusi Finansial
                            <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600">Masa Depan.</span>
                        </h1>
                        <p class="text-lg md:text-xl text-slate-500 max-w-lg leading-relaxed font-medium">
                            Kelola simpanan, pantau aset, dan kembangkan ekonomi bersama <span class="text-slate-900 font-bold">DanaKarya</span> — transparan, aman, dan digital.
                        </p>
                    </div>
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                        <a href="{{ route('login') }}" class="group inline-flex items-center gap-3 px-8 py-5 bg-slate-900 text-white rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-blue-600 transition-all shadow-xl shadow-slate-200 active:scale-[0.97]">
                            Mulai Sekarang
                            <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                        </a>
                        <a href="#fitur" class="text-xs font-black uppercase tracking-widest text-slate-400 hover:text-slate-600 transition-colors flex items-center gap-2">
                            Lihat Fitur
                            <svg class="w-4 h-4 animate-bounce" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                        </a>
                    </div>
                </div>
                <div class="hidden lg:flex justify-center">
                    <div class="relative group">
                        <div class="w-96 h-96 bg-gradient-to-br from-slate-100 via-white to-slate-200 rounded-full rotate-3 shadow-2xl flex items-center justify-center overflow-hidden group-hover:rotate-0 transition-transform duration-700">
                            <img 
                                src="{{ asset('images/logo.jpg') }}" 
                                alt="DanaKarya Logo" 
                                class="w-full h-full object-cover"
                            >
                            <div class="absolute inset-0 bg-gradient-to-br from-blue-600/10 via-transparent to-indigo-600/10"></div>
                            <div class="absolute inset-0 bg-[radial-gradient(circle_at_50%_50%,_rgba(59,130,246,0.12),transparent_60%)]"></div>
                        </div>
                        <div class="absolute -bottom-6 -right-6 w-50 h-42 bg-white rounded-[2.5rem] shadow-xl border border-slate-100 p-6 flex flex-col justify-center group-hover:-translate-y-2 transition-transform duration-500">
                            <p class="text-[10px] font-black uppercase tracking-widest text-blue-600">Dana Karya</p>
                            <p class="text-3xl font-black text-slate-900">Koperasi</p>
                            <p class="text-[10px] font-bold text-slate-400 mt-1">Digital Masa Depan</p>
                        </div>
                        <div class="absolute top-4 left-4 w-16 h-16 bg-emerald-500 rounded-2xl flex items-center justify-center text-white shadow-xl shadow-emerald-200 group-hover:scale-110 transition-transform duration-500">
                            <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ================= STATS BAND (Animated Counter) ================= --}}
    <section x-data="{ visible: false, a: 0, b: 0, c: 0, d: 0 }"
             x-intersect.threshold.05="visible = true; if(visible) { let i = setInterval(() => { if(a<500) a+=25; if(b<24) b+=1; if(c<985) c+=49; if(d<247) d+=12; if(a>=500 && b>=24 && c>=985 && d>=247) clearInterval(i); }, 30) }"
             class="relative -mt-24 z-20 max-w-6xl mx-auto px-6">
        <div class="bg-white rounded-[3rem] shadow-xl border border-slate-100 grid grid-cols-2 md:grid-cols-4 divide-x divide-slate-100 overflow-hidden">
            <div class="p-8 text-center group hover:bg-slate-50 transition-colors">
                <p class="text-3xl md:text-4xl font-black text-slate-900" x-text="a + '+'" x-init="a = 0">0</p>
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mt-2">Anggota Aktif</p>
            </div>
            <div class="p-8 text-center group hover:bg-slate-50 transition-colors">
                <p class="text-3xl md:text-4xl font-black text-slate-900"><span x-text="b.toLocaleString()"></span>M+</p>
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mt-2">Total Aset Kelola</p>
            </div>
            <div class="p-8 text-center group hover:bg-slate-50 transition-colors">
                <p class="text-3xl md:text-4xl font-black text-slate-900"><span x-text="(c/10).toFixed(1)"></span>%</p>
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mt-2">Kepuasan Anggota</p>
            </div>
            <div class="p-8 text-center group hover:bg-slate-50 transition-colors">
                <p class="text-3xl md:text-4xl font-black text-slate-900" x-text="d + '/10'"></p>
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mt-2">Layanan Support</p>
            </div>
        </div>
    </section>

    {{-- ================= FITUR ================= --}}
    <section id="fitur" class="py-32 md:py-40 px-6">
        <div class="max-w-6xl mx-auto">
            <div class="text-center space-y-4 mb-20">
                <span class="text-[10px] font-black uppercase tracking-[0.35em] text-blue-600">Fitur Unggulan</span>
                <h2 class="text-4xl md:text-5xl font-black tracking-tighter text-slate-900">Lengkap untuk <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600">Koperasi Modern</span></h2>
                <p class="text-slate-500 text-lg max-w-xl mx-auto font-medium">Semua fitur dirancang untuk kemudahan, transparansi, dan pertumbuhan ekonomi anggota.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @php
                    $fiturs = [
                        ['img' => 'https://images.unsplash.com/photo-1563013544-824ae1b704d3?auto=format&fit=crop&q=80&w=400&h=250', 'icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z', 'title' => 'Simpanan Digital', 'desc' => 'Kelola simpanan pokok, wajib, dan sukarela secara real-time dengan riwayat transaksi lengkap.'],
                        ['img' => 'https://images.unsplash.com/photo-1554224155-6726b3ff858f?auto=format&fit=crop&q=80&w=400&h=250', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'title' => 'Pinjaman Fleksibel', 'desc' => 'Ajukan pinjaman dengan bunga kompetitif, tenor fleksibel, dan proses pencairan cepat tanpa ribet.'],
                        ['img' => 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?auto=format&fit=crop&q=80&w=400&h=250', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'title' => 'SHU Transparan', 'desc' => 'Pantau perhitungan Sisa Hasil Usaha secara detail dengan breakdown jasa modal dan jasa anggota.'],
                        ['img' => 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?auto=format&fit=crop&q=80&w=400&h=250', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', 'title' => 'Monitoring Aktif', 'desc' => 'Pantau kesehatan keuangan koperasi secara real-time dengan dashboard analitik yang intuitif.'],
                        ['img' => 'https://images.unsplash.com/photo-1555949963-aa79dcee981c?auto=format&fit=crop&q=80&w=400&h=250', 'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'title' => 'Keamanan Terjamin', 'desc' => 'Enkripsi data end-to-end dengan sistem keamanan berlapis untuk melindungi data dan transaksi Anda.'],
                        ['img' => 'https://images.unsplash.com/photo-1600880292203-757bb62b4baf?auto=format&fit=crop&q=80&w=400&h=250', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'title' => 'Manajemen Anggota', 'desc' => 'Kelola data anggota, riwayat transaksi, dan status keanggotaan dalam satu dasbor terpadu.'],
                    ];
                @endphp
                @foreach($fiturs as $fitur)
                <div class="group bg-white rounded-[2.5rem] border border-slate-100 shadow-sm hover:shadow-xl hover:border-blue-100 transition-all duration-500 overflow-hidden">
                    <div class="relative h-44 overflow-hidden">
                        <img src="{{ $fitur['img'] }}" alt="{{ $fitur['title'] }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" loading="lazy">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent"></div>
                        <div class="absolute top-4 left-4 p-2.5 bg-white/90 backdrop-blur-sm text-blue-600 rounded-xl shadow-lg group-hover:bg-blue-600 group-hover:text-white transition-all duration-500">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $fitur['icon'] }}"/></svg>
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-lg font-black text-slate-800 mb-2">{{ $fitur['title'] }}</h3>
                        <p class="text-sm text-slate-500 font-medium leading-relaxed">{{ $fitur['desc'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ================= TENTANG ================= --}}
    <section id="tentang" class="py-24 md:py-32 px-6 bg-white border-y border-slate-100">
        <div class="max-w-6xl mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-20 items-center">
                <div class="relative">
                    <div class="aspect-[4/5] rounded-[3rem] overflow-hidden border border-blue-100/50 shadow-inner relative">
                        <img src="https://images.unsplash.com/photo-1600880292203-757bb62b4baf?auto=format&fit=crop&q=80&w=600&h=750" alt="Tim DanaKarya" class="w-full h-full object-cover" loading="lazy">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/10 to-transparent"></div>
                    </div>
                </div>
                <div class="space-y-10">
                    <div class="space-y-5">
                        <span class="text-[10px] font-black uppercase tracking-[0.35em] text-blue-600">Tentang Kami</span>
                        <h2 class="text-4xl md:text-5xl font-black tracking-tighter text-slate-900 leading-[1.05]">
                            Membangun Ekonomi <br>
                            <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600">Tanpa Batas.</span>
                        </h2>
                        <p class="text-slate-500 text-lg leading-relaxed max-w-md">Platform koperasi digital terintegrasi yang fokus pada kesejahteraan anggota melalui transparansi data 100%.</p>
                    </div>
                    <div class="grid grid-cols-1 gap-3">
                        @foreach([
                            ['icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'label' => 'Transparansi Log Transaksi'],
                            ['icon' => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6', 'label' => 'Suku Bunga Kompetitif'],
                            ['icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'label' => 'Keanggotaan Inklusif'],
                        ] as $item)
                        <div class="flex items-center gap-4 p-5 bg-slate-50 rounded-2xl border border-slate-100 group hover:border-blue-200 hover:bg-white transition-all cursor-default">
                            <div class="p-2.5 bg-white text-blue-600 rounded-xl shadow-sm group-hover:bg-blue-600 group-hover:text-white group-hover:shadow-lg group-hover:shadow-blue-200 transition-all">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="{{ $item['icon'] }}"/></svg>
                            </div>
                            <span class="text-xs font-black text-slate-700 uppercase tracking-widest">{{ $item['label'] }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ================= CTA BAND ================= --}}
    <section class="py-20 px-6 bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900">
        <div class="max-w-4xl mx-auto text-center space-y-8">
            <h2 class="text-3xl md:text-5xl font-black tracking-tighter text-white">Siap Bergabung?</h2>
            <p class="text-slate-400 text-lg max-w-xl mx-auto font-medium">Ribuan anggota telah merasakan manfaat transparansi dan kemudahan layanan koperasi digital.</p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('login') }}" class="px-10 py-5 bg-blue-600 text-white rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-blue-500 transition-all shadow-xl shadow-blue-900/30 active:scale-[0.97]">
                    Mulai Sekarang
                </a>
                <a href="#kontak" class="px-10 py-5 bg-white/5 border border-white/20 text-white rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-white/10 transition-all">
                    Hubungi Kami
                </a>
            </div>
        </div>
    </section>

    {{-- ================= KONTAK ================= --}}
    <section id="kontak" class="relative overflow-hidden bg-slate-900">
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute top-20 right-20 w-96 h-96 bg-blue-500/5 rounded-full blur-[100px]"></div>
            <div class="absolute bottom-20 left-20 w-80 h-80 bg-indigo-500/5 rounded-full blur-[100px]"></div>
        </div>

        <div class="max-w-6xl mx-auto px-6 py-28 md:py-36 relative z-10">
            <div class="text-center space-y-5 mb-20">
                <h2 class="text-4xl md:text-6xl font-black tracking-tighter text-white">
                    Pilih Cara Anda <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-indigo-400">Berinteraksi.</span>
                </h2>
                <p class="text-slate-400 text-lg max-w-lg mx-auto font-medium">Klik ikon di bawah untuk langsung terhubung dengan tim representatif kami secara instan.</p>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 max-w-4xl mx-auto">
                <a href="https://wa.me/6281211112222" target="_blank" class="group p-6 md:p-8 bg-white/5 border border-white/10 rounded-[2rem] hover:bg-emerald-500/10 hover:border-emerald-400/40 transition-all duration-500 text-center">
                    <div class="flex flex-col items-center gap-3">
                        <div class="p-3.5 bg-white/10 rounded-xl text-white group-hover:scale-110 group-hover:-rotate-6 transition-all duration-500">
                            <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L0 24l6.335-1.662c1.72.937 3.659 1.432 5.628 1.433h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        </div>
                        <span class="text-[9px] font-black text-white/70 uppercase tracking-widest group-hover:text-white transition-colors">WhatsApp</span>
                    </div>
                </a>
                <a href="https://t.me/yourusername" target="_blank" class="group p-6 md:p-8 bg-white/5 border border-white/10 rounded-[2rem] hover:bg-blue-500/10 hover:border-blue-400/40 transition-all duration-500 text-center">
                    <div class="flex flex-col items-center gap-3">
                        <div class="p-3.5 bg-white/10 rounded-xl text-white group-hover:scale-110 group-hover:rotate-6 transition-all duration-500">
                            <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24"><path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.562 8.161c-.18.717-3.903 16.324-4.103 17.152-.2.828-.485 1.034-.781 1.058-.646.054-1.137-.432-1.762-.842-.979-.641-1.532-1.04-2.482-1.665-1.099-.723-.387-1.119.24-1.77.164-.17 3.013-2.763 3.068-2.997.007-.029.014-.138-.051-.195-.065-.057-.16-.038-.229-.022-.098.022-1.66 1.056-4.685 3.102-.444.305-.845.454-1.205.446-.395-.01-1.156-.224-1.722-.408-.694-.225-1.246-.345-1.198-.729.025-.2.301-.404.827-.613 3.235-1.411 5.392-2.343 6.474-2.798 3.085-1.294 3.725-1.519 4.142-1.527.092-.001.297.022.43.13.111.091.141.213.155.297.014.084.032.274.017.432z"/></svg>
                        </div>
                        <span class="text-[9px] font-black text-white/70 uppercase tracking-widest group-hover:text-white transition-colors">Telegram</span>
                    </div>
                </a>
                <a href="https://m.me/yourpage" target="_blank" class="group p-6 md:p-8 bg-white/5 border border-white/10 rounded-[2rem] hover:bg-indigo-500/10 hover:border-indigo-400/40 transition-all duration-500 text-center">
                    <div class="flex flex-col items-center gap-3">
                        <div class="p-3.5 bg-white/10 rounded-xl text-white group-hover:scale-110 group-hover:-rotate-6 transition-all duration-500">
                            <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24"><path d="M12 0C5.126 0 0 4.708 0 11.23c0 3.394 1.365 6.413 3.593 8.443.187.17.293.407.288.656l-.014 2.193c-.006.915.962 1.503 1.716 1.01l2.454-1.603a.76.76 0 01.52-.102c1.104.305 2.276.47 3.443.47 6.874 0 12-4.708 12-11.23C24 4.708 18.874 0 12 0z"/></svg>
                        </div>
                        <span class="text-[9px] font-black text-white/70 uppercase tracking-widest group-hover:text-white transition-colors">Messenger</span>
                    </div>
                </a>
                <a href="https://instagram.com/direct/t/yourusername" target="_blank" class="group p-6 md:p-8 bg-white/5 border border-white/10 rounded-[2rem] hover:bg-pink-500/10 hover:border-pink-400/40 transition-all duration-500 text-center">
                    <div class="flex flex-col items-center gap-3">
                        <div class="p-3.5 bg-white/10 rounded-xl text-white group-hover:scale-110 group-hover:rotate-6 transition-all duration-500">
                            <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073z"/></svg>
                        </div>
                        <span class="text-[9px] font-black text-white/70 uppercase tracking-widest group-hover:text-white transition-colors">Instagram</span>
                    </div>
                </a>
                <a href="https://line.me/ti/p/~yourid" target="_blank" class="group p-6 md:p-8 bg-white/5 border border-white/10 rounded-[2rem] hover:bg-lime-500/10 hover:border-lime-400/40 transition-all duration-500 text-center">
                    <div class="flex flex-col items-center gap-3">
                        <div class="p-3.5 bg-white/10 rounded-xl text-white group-hover:scale-110 group-hover:-rotate-6 transition-all duration-500">
                            <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24"><path d="M24 10.304c0-5.269-5.383-9.556-12-9.556s-12 4.287-12 9.556c0 4.723 4.266 8.672 10.021 9.428l-.711 2.347c-.082.271.19.508.443.342l5.161-3.39c4.276-.641 7.086-4.041 7.086-7.181z"/></svg>
                        </div>
                        <span class="text-[9px] font-black text-white/70 uppercase tracking-widest group-hover:text-white transition-colors">Line</span>
                    </div>
                </a>
                <a href="mailto:support@koperasi.id" target="_blank" class="group p-6 md:p-8 bg-white/5 border border-white/10 rounded-[2rem] hover:bg-sky-500/10 hover:border-sky-400/40 transition-all duration-500 text-center">
                    <div class="flex flex-col items-center gap-3">
                        <div class="p-3.5 bg-white/10 rounded-xl text-white group-hover:scale-110 group-hover:rotate-6 transition-all duration-500">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <span class="text-[9px] font-black text-white/70 uppercase tracking-widest group-hover:text-white transition-colors">Email</span>
                    </div>
                </a>
            </div>
        </div>
    </section>

    {{-- ================= FOOTER ================= --}}
    <footer class="bg-slate-950 border-t border-slate-800 py-12 px-6">
        <div class="max-w-6xl mx-auto text-center">
            <p class="text-[10px] font-black text-slate-600 uppercase tracking-[0.35em]">&copy; {{ date('Y') }} DanaKarya Digital Ecosystem. All rights reserved.</p>
        </div>
    </footer>
</main>

{{-- Scroll to Top --}}
<button x-show="showScrollTop"
        @click="window.scrollTo({ top: 0, behavior: 'smooth' })"
        x-transition
        class="fixed bottom-8 right-8 z-50 w-12 h-12 bg-blue-600 text-white rounded-2xl shadow-xl shadow-blue-200 hover:bg-blue-700 transition-all active:scale-90 flex items-center justify-center">
    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
</button>

<style>
    [x-cloak] { display: none !important; }
    ::selection { background-color: #3b82f6; color: white; }
    @keyframes fade-in { from { opacity: 0; transform: translateY(-16px); } to { opacity: 1; transform: translateY(0); } }
    .animate-fade-in { animation: fade-in 0.6s ease-out forwards; }
</style>

</body>
</html>
