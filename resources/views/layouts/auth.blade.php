<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Login') - DanaKarya</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="min-h-screen flex items-center justify-center bg-[#f4f7fa] p-6">
        
        <div class="flex flex-col lg:flex-row bg-white shadow-[0_40px_100px_-20px_rgba(0,0,0,0.1)] rounded-[3rem] overflow-hidden w-full max-w-5xl border border-slate-100">
            
            <div class="lg:w-1/2 bg-white flex flex-col items-center justify-center p-12 lg:p-20 relative">
                <div class="absolute inset-0 opacity-[0.03]" style="background-image: radial-gradient(#1e293b 1px, transparent 1px); background-size: 24px 24px;"></div>
                
                <div class="relative z-10 text-center space-y-8">
                    <div class="transition-all duration-700 hover:scale-105">
                    </div>
                    
                    <div class="transition-all duration-700 hover:scale-105">
                        <img src="{{ asset('images/dn.png') }}" alt="DanaKarya Logo" class="h-44 md:h-64 w-auto object-contain drop-shadow-[0_20px_50px_rgba(0,0,0,0.1)]">
                    </div>
                </div>
            </div>

            <div class="lg:w-1/2 bg-slate-50/50 p-10 lg:p-16 border-l border-slate-100 flex flex-col justify-center">
                <div class="max-w-sm mx-auto w-full space-y-10">
                    @yield('auth_content')
                </div>
            </div>

        </div>
    </div>
</body>
</html>
