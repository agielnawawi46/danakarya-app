@extends('layouts.auth')

@section('title', 'Login')

@section('auth_content')
    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 rounded-2xl px-6 py-4 text-emerald-700 text-sm font-bold shadow-sm mb-6">
        {{ session('success') }}
    </div>
    @endif

    @if($errors->any() || session('error'))
    <div class="bg-red-50 border border-red-200 rounded-2xl px-6 py-4 text-red-600 text-sm font-bold shadow-sm mb-6">
        @if(session('error'))
            <p>{{ session('error') }}</p>
        @endif
        @foreach($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
    @endif

    <div class="text-center lg:text-left">
        <h3 class="text-3xl font-black text-slate-900 tracking-tighter">Login.</h3>
        <p class="text-slate-500 text-[10px] font-black uppercase tracking-[0.2em] mt-2">Selamat datang kembali di sistem kami</p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <div class="space-y-2">
            <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Email Identifier</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                class="w-full bg-white border border-slate-200 rounded-2xl px-6 py-4 text-sm font-bold text-slate-700 focus:ring-4 focus:ring-blue-600/5 focus:border-blue-600 outline-none transition-all shadow-sm {{ $errors->has('email') ? 'border-red-400' : '' }}"
                placeholder="Masukkan email anda">
        </div>

        <div class="space-y-2">
            <div class="flex justify-between items-center px-1">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Password</label>
            </div>
            <input id="password" type="password" name="password" required
                class="w-full bg-white border border-slate-200 rounded-2xl px-6 py-4 text-sm font-bold text-slate-700 focus:ring-4 focus:ring-blue-600/5 focus:border-blue-600 outline-none transition-all shadow-sm {{ $errors->has('password') ? 'border-red-400' : '' }}"
                placeholder="••••••••">
        </div>

        <div class="flex items-center justify-between px-1">
            <label class="flex items-center cursor-pointer group">
                <input type="checkbox" name="remember" class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-0">
                <span class="ms-2 text-[10px] font-black text-slate-400 uppercase tracking-widest group-hover:text-slate-600 transition-colors">Ingat Saya</span>
            </label>
            <a href="#" class="text-[9px] font-black text-blue-600 uppercase tracking-widest hover:underline underline-offset-4">Bantuan Login?</a>
        </div>

        <button type="submit"
            class="w-full bg-slate-900 text-white py-5 rounded-2xl text-[10px] font-black uppercase tracking-[0.4em] hover:bg-blue-600 transition-all shadow-lg shadow-blue-900/10">
            Masuk Ke Sistem
        </button>

        <div class="pt-8 text-center">
            <p class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em]">
                Belum punya akun super admin? 
                <a href="{{ route('register') }}" class="text-slate-900 hover:text-blue-600 font-black transition-colors">Daftar Sekarang</a>
            </p>
        </div>
    </form>
@endsection
