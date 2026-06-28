<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Superadmin\DashboardController as SuperadminDashboard;
use App\Http\Controllers\Superadmin\TenantController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\OrganizationController;
use App\Http\Controllers\Admin\FinancialRuleController;
use App\Http\Controllers\Admin\MemberController;
use App\Http\Controllers\Admin\PayrollController;
use App\Http\Controllers\Pengurus\DashboardController as PengurusDashboard;
use App\Http\Controllers\Pengurus\DepositController as PengurusDeposit;
use App\Http\Controllers\Pengurus\LoanController as PengurusLoan;
use App\Http\Controllers\Pengurus\AccountingController;
use App\Http\Controllers\Pengurus\ReportController;
use App\Http\Controllers\Pengawas\DashboardController as PengawasDashboard;
use App\Http\Controllers\Pengawas\AuditFinanceController;
use App\Http\Controllers\Pengawas\AuditTrailController;
use App\Http\Controllers\Member\DashboardController as MemberDashboard;
use App\Http\Controllers\Member\DepositController as MemberDeposit;
use App\Http\Controllers\Member\LoanController as MemberLoan;
use App\Http\Controllers\Member\ShuController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

// ─── Public Auth Routes ──────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',    [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',   [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password');
});


// ─── SUPERADMIN Routes ───────────────────────────────────────────────────────
Route::prefix('superadmin')
    ->middleware(['auth', 'set.team', 'role:superadmin'])
    ->name('superadmin.')
    ->group(function () {
        Route::get('/dashboard', [SuperadminDashboard::class, 'index'])->name('dashboard');
        Route::resource('/tenants', TenantController::class);
        Route::post('/tenants/{tenant}/toggle-active', [TenantController::class, 'toggleActive'])->name('tenants.toggle-active');
    });

// ─── ADMIN Routes ────────────────────────────────────────────────────────────
Route::prefix('admin')
    ->middleware(['auth', 'set.team', 'role:admin', 'org.configured'])
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

        // Organization Profile
        Route::prefix('organization')->name('organization.')->group(function () {
            Route::get('/setup',   [OrganizationController::class, 'setup'])->name('setup')->withoutMiddleware('org.configured');
            Route::get('/',        [OrganizationController::class, 'index'])->name('index');
            Route::post('/',       [OrganizationController::class, 'update'])->name('update');
        });

        // Financial Rules
        Route::prefix('rules')->name('rules.')->group(function () {
            Route::get('/',  [FinancialRuleController::class, 'index'])->name('index');
            Route::post('/', [FinancialRuleController::class, 'update'])->name('update');
        });

        // Members
        Route::prefix('members')->name('members.')->group(function () {
            Route::get('/',               [MemberController::class, 'index'])->name('index');
            Route::get('/create',         [MemberController::class, 'create'])->name('create');
            Route::post('/',              [MemberController::class, 'store'])->name('store');
            Route::get('/{user}/edit',    [MemberController::class, 'edit'])->name('edit');
            Route::put('/{user}',         [MemberController::class, 'update'])->name('update');
            Route::delete('/{user}',      [MemberController::class, 'destroy'])->name('destroy');
            Route::get('/template',       [MemberController::class, 'downloadTemplate'])->name('template');
            Route::post('/import',        [MemberController::class, 'import'])->name('import');
        });

        // Payroll
        Route::prefix('payroll')->name('payroll.')->group(function () {
            Route::get('/',              [PayrollController::class, 'index'])->name('index');
            Route::get('/export',        [PayrollController::class, 'export'])->name('export');
            Route::post('/import',       [PayrollController::class, 'import'])->name('import');
        });
    });

// ─── PENGURUS Routes ─────────────────────────────────────────────────────────
Route::prefix('pengurus')
    ->middleware(['auth', 'set.team', 'role:pengurus'])
    ->name('pengurus.')
    ->group(function () {
        Route::get('/dashboard', [PengurusDashboard::class, 'index'])->name('dashboard');

        // Deposits (Loket)
        Route::prefix('deposits')->name('deposits.')->group(function () {
            Route::get('/',                      [PengurusDeposit::class, 'index'])->name('index');
            Route::get('/create',                [PengurusDeposit::class, 'create'])->name('create');
            Route::post('/',                     [PengurusDeposit::class, 'store'])->name('store');
            Route::post('/{deposit}/approve',    [PengurusDeposit::class, 'approve'])->name('approve');
            Route::post('/{deposit}/reject',     [PengurusDeposit::class, 'reject'])->name('reject');
            Route::get('/withdrawals',           [PengurusDeposit::class, 'withdrawals'])->name('withdrawals');
        });

        // Loans
        Route::prefix('loans')->name('loans.')->group(function () {
            Route::get('/',                    [PengurusLoan::class, 'index'])->name('index');
            Route::get('/{loan}',              [PengurusLoan::class, 'show'])->name('show');
            Route::post('/{loan}/approve',     [PengurusLoan::class, 'approve'])->name('approve');
            Route::post('/{loan}/reject',      [PengurusLoan::class, 'reject'])->name('reject');
            Route::post('/schedules/{schedule}/pay', [PengurusLoan::class, 'payInstallment'])->name('pay-installment');
        });

        // Accounting
        Route::prefix('accounting')->name('accounting.')->group(function () {
            Route::get('/',              [AccountingController::class, 'index'])->name('index');
            Route::get('/coa',           [AccountingController::class, 'coa'])->name('coa');
            Route::get('/journals',      [AccountingController::class, 'journals'])->name('journals');
            Route::get('/journals/create', [AccountingController::class, 'createJournal'])->name('journals.create');
            Route::post('/journals',     [AccountingController::class, 'storeJournal'])->name('journals.store');
        });

        // Reports
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/',          [ReportController::class, 'index'])->name('index');
            Route::get('/kas',       [ReportController::class, 'kas'])->name('kas');
            Route::get('/neraca',    [ReportController::class, 'neraca'])->name('neraca');
            Route::get('/laba-rugi', [ReportController::class, 'labaRugi'])->name('laba-rugi');
            Route::get('/simpanan',  [ReportController::class, 'simpanan'])->name('simpanan');
            Route::get('/shu',       [ReportController::class, 'shu'])->name('shu');
            Route::post('/shu/calculate', [ReportController::class, 'calculateShu'])->name('shu.calculate');
            Route::post('/shu/{distribution}/distribute', [ReportController::class, 'distributeShu'])->name('shu.distribute');
        });
    });

// ─── PENGAWAS Routes ─────────────────────────────────────────────────────────
Route::prefix('pengawas')
    ->middleware(['auth', 'set.team', 'role:pengawas'])
    ->name('pengawas.')
    ->group(function () {
        Route::get('/dashboard',          [PengawasDashboard::class, 'index'])->name('dashboard');
        Route::get('/audit-finance',      [AuditFinanceController::class, 'index'])->name('audit-finance.index');
        Route::get('/audit-finance/kas', [AuditFinanceController::class, 'kas'])->name('audit-finance.kas');
        Route::get('/audit-finance/kas/export', [AuditFinanceController::class, 'exportKas'])->name('audit-finance.kas.export');
        Route::get('/audit-finance/simpanan', [AuditFinanceController::class, 'simpanan'])->name('audit-finance.simpanan');
        Route::get('/audit-finance/simpanan/export', [AuditFinanceController::class, 'exportSimpanan'])->name('audit-finance.simpanan.export');
        Route::get('/audit-finance/shu', [AuditFinanceController::class, 'shu'])->name('audit-finance.shu');
        Route::get('/audit-finance/shu/export', [AuditFinanceController::class, 'exportShu'])->name('audit-finance.shu.export');
        Route::get('/audit-finance/ledger', [AuditFinanceController::class, 'ledger'])->name('audit-finance.ledger');
        Route::get('/audit-finance/ledger/export', [AuditFinanceController::class, 'exportLedger'])->name('audit-finance.ledger.export');
        Route::get('/audit-finance/neraca', [AuditFinanceController::class, 'neraca'])->name('audit-finance.neraca');
        Route::get('/audit-finance/neraca/export', [AuditFinanceController::class, 'exportNeraca'])->name('audit-finance.neraca.export');
        Route::get('/audit-finance/laba-rugi', [AuditFinanceController::class, 'labaRugi'])->name('audit-finance.laba-rugi');
        Route::get('/audit-finance/laba-rugi/export', [AuditFinanceController::class, 'exportLabaRugi'])->name('audit-finance.laba-rugi.export');
        Route::get('/audit-trail',        [AuditTrailController::class, 'index'])->name('audit-trail.index');
        Route::get('/audit-trail/export', [AuditTrailController::class, 'export'])->name('audit-trail.export');
    });

// ─── MEMBER (ANGGOTA) Routes ─────────────────────────────────────────────────
Route::prefix('member')
    ->middleware(['auth', 'set.team', 'role:anggota'])
    ->name('member.')
    ->group(function () {
        Route::get('/dashboard', [MemberDashboard::class, 'index'])->name('dashboard');

        // Deposits (Simpananku)
        Route::prefix('deposits')->name('deposits.')->group(function () {
            Route::get('/',           [MemberDeposit::class, 'index'])->name('index');
            Route::get('/withdraw',   [MemberDeposit::class, 'showWithdraw'])->name('withdraw');
            Route::post('/withdraw',  [MemberDeposit::class, 'storeWithdraw'])->name('withdraw.store');
        });

        // Loans (Pinjaman)
        Route::prefix('loans')->name('loans.')->group(function () {
            Route::get('/',             [MemberLoan::class, 'index'])->name('index');
            Route::get('/apply',        [MemberLoan::class, 'apply'])->name('apply');
            Route::post('/',            [MemberLoan::class, 'store'])->name('store');
            Route::get('/calculate',    [MemberLoan::class, 'calculate'])->name('calculate');
            Route::get('/{loan}/card',  [MemberLoan::class, 'card'])->name('card'); // Kartu Piutang
        });

        // SHU
        Route::get('/shu', [ShuController::class, 'index'])->name('shu.index');
    });
