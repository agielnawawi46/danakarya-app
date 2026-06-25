# Dana Karya — Task List

## Phase 1: Dependencies
- `[x]` Install spatie/laravel-permission
- `[x]` Install maatwebsite/excel
- `[x]` Install intervention/image

## Phase 2: Database Migrations
- `[x]` Modify users migration (add org fields + salary)
- `[x]` Create organizations migration
- `[x]` Create deposits migration
- `[x]` Create loans migration
- `[x]` Create loan_schedules migration
- `[x]` Create payroll_imports migration
- `[x]` Create accounts (COA) migration
- `[x]` Create journal_entries migration
- `[x]` Create journal_entry_lines migration
- `[x]` Create shu_distributions migration
- `[x]` Create shu_member_details migration
- `[x]` Create audit_logs migration
- `[x]` Run php artisan migrate:fresh (+ seed)

## Phase 3: Models & Scopes
- `[x]` OrganizationScope (Global Scope)
- `[x]` Organization model
- `[x]` User model (update)
- `[x]` Deposit model
- `[x]` Loan model
- `[x]` LoanSchedule model
- `[x]` Account model
- `[x]` JournalEntry + JournalEntryLine model
- `[x]` PayrollImport model
- `[x]` ShuDistribution + ShuMemberDetail model
- `[x]` AuditLog model

## Phase 4: Services
- `[x]` AccountingService (double-entry)
- `[x]` LoanService (credit scoring + schedule)
- `[x]` ShuService (annual calculation)
- `[x]` PayrollService (export/import Excel)
- `[x]` AuditService (logging)

## Phase 5: Spatie Permission Setup
- `[x]` Publish & configure spatie permission (teams: disabled)
- `[x]` Create roles & permissions seeder
- `[x]` Create Superadmin artisan command
- `[x]` Create COA seeder

## Phase 6: Middleware & Routes
- `[x]` SetTeamPermission middleware (passthrough — OrganizationScope handles isolation)
- `[x]` EnsureOrganizationConfigured middleware
- `[x]` Define all routes in web.php

## Phase 7: Controllers (22)
- `[x]` AuthController
- `[x]` Superadmin: Dashboard, TenantController
- `[x]` Admin: Dashboard, Organization, FinancialRule, Member, Payroll
- `[x]` Pengurus: Dashboard, Deposit, Loan, Accounting, Report
- `[x]` Pengawas: Dashboard, AuditFinance, AuditTrail
- `[x]` Member: Dashboard, Deposit, Loan, Shu

## Phase 8: Frontend (CSS + Layout)
- `[x]` app.css (design system + extended utilities)
- `[x]` app.js (Alpine.js interactions, loan calculator)
- `[x]` layouts/app.blade.php (role-aware sidebar, flash messages)
- `[x]` layouts/auth.blade.php (branded split layout)
- `[x]` Sidebar components (5 roles)

## Phase 9: Blade Views (40+ files)
- `[x]` Auth: login, register
- `[x]` Superadmin: dashboard, tenants/index, tenants/show
- `[x]` Admin: dashboard, organization/index, organization/setup, rules/index, members/index, members/create, members/edit, payroll/index
- `[x]` Pengurus: dashboard, deposits/index, deposits/create, deposits/withdrawals, loans/index, loans/show, accounting/index, accounting/coa, accounting/journals, accounting/create-journal, reports/index, reports/kas, reports/neraca, reports/laba-rugi, reports/simpanan, reports/shu
- `[x]` Pengawas: dashboard, audit-finance/index, audit-finance/ledger, audit-finance/neraca, audit-finance/laba-rugi, audit-trail/index
- `[x]` Member: dashboard, deposits/index, deposits/withdraw, loans/index, loans/apply, loans/card, shu/index

## Phase 10: Verification
- `[x]` migrate:fresh --seed → Success (all 5 roles seeded)
- `[/]` npm build (in progress)
- `[ ]` php artisan serve → test login flow
- `[ ]` Verify all 5 role dashboards render correctly
