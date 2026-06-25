<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\User;
use App\Services\AccountingService;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuditService $auditService,
        private readonly AccountingService $accountingService,
    ) {}

    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'Email atau password salah.'])->withInput();
        }

        $request->session()->regenerate();

        $user = Auth::user();

        // Set Spatie team context
        if ($user->organization_id) {
            setPermissionsTeamId($user->organization_id);
        } else {
            setPermissionsTeamId(null);
        }

        $this->auditService->log('login', "Login dari IP {$request->ip()}");

        return redirect()->intended($user->getDashboardRoute());
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        DB::transaction(function () use ($validated) {
            // Create organization placeholder
            $org = Organization::create([
                'name'         => 'Koperasi Baru (Belum Dikonfigurasi)',
                'is_configured'=> false,
                'is_active'    => true,
            ]);

            // Create Admin user
            $user = User::create([
                'name'            => $validated['name'],
                'email'           => $validated['email'],
                'password'        => bcrypt($validated['password']),
                'organization_id' => $org->id,
                'status'          => 'active',
            ]);

            // Set team & assign admin role
            setPermissionsTeamId($org->id);
            $user->assignRole('admin');

            // Seed default Chart of Accounts for new org
            $this->accountingService->seedDefaultCoa($org->id);

            Auth::login($user);
        });

        return redirect()->route('admin.dashboard')
            ->with('info', 'Akun berhasil dibuat! Lengkapi profil koperasi Anda untuk mengaktifkan semua fitur.');
    }

    public function logout(Request $request): RedirectResponse
    {
        $this->auditService->log('logout', 'Logout');
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
