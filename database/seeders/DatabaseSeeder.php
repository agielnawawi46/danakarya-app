<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\User;
use App\Services\AccountingService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. Create roles (without team) for superadmin ──────────────────
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create global roles (available in all teams)
        $roles = ['superadmin', 'admin', 'pengurus', 'pengawas', 'anggota'];
        foreach ($roles as $roleName) {
            // team_id = null → global role
            Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        }

        // ── 2. Create Superadmin ────────────────────────────────────────────
        $superadmin = User::firstOrCreate(
            ['email' => 'superadmin@danakarya.id'],
            [
                'name'     => 'Super Administrator',
                'password' => bcrypt('SuperAdmin@2024!'),
                'status'   => 'active',
            ]
        );

        $superadmin->assignRole('superadmin');

        $this->command->info('✅ Superadmin created: superadmin@danakarya.id / SuperAdmin@2024!');

        // ── 3. Create Demo Organization + Users ─────────────────────────────
        if (app()->environment('local')) {
            $this->call(DemoSeeder::class);
        }
    }
}
