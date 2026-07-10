@extends('layouts.auth')

@section('title', 'Koperasi Dinonaktifkan')

@section('auth_content')
    <div class="text-center space-y-4">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-red-100 text-red-500 mb-4">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
        </div>
        
        <h1 class="text-2xl font-bold text-slate-800">Akses Dinonaktifkan</h1>
        
        <p class="text-slate-600">
            Mohon maaf, akun Koperasi <strong>{{ auth()->user()->organization?->name ?? 'Anda' }}</strong> saat ini sedang dinonaktifkan oleh sistem.
        </p>
        
        <p class="text-sm text-slate-500 mt-4">
            Silakan hubungi administrator layanan (Superadmin) untuk informasi lebih lanjut mengenai status koperasi Anda.
        </p>

        <div class="mt-8 pt-6 border-t border-slate-200">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-3 bg-slate-900 text-white rounded-xl hover:bg-slate-800 transition-colors focus:ring-4 focus:ring-slate-200 font-medium shadow-sm">
                    Keluar / Ganti Akun
                </button>
            </form>
        </div>
    </div>
@endsection
