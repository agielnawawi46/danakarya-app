<?php

namespace App\Console\Commands;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class CreateSuperadmin extends Command
{
    protected $signature   = 'superadmin:create {name} {email} {password}';
    protected $description = 'Create a new Superadmin user (platform owner)';

    public function handle(): int
    {
        $name     = $this->argument('name');
        $email    = $this->argument('email');
        $password = $this->argument('password');

        if (User::where('email', $email)->exists()) {
            $this->error("User dengan email [{$email}] sudah ada.");
            return self::FAILURE;
        }

        $user = User::create([
            'name'     => $name,
            'email'    => $email,
            'password' => bcrypt($password),
            'status'   => 'active',
        ]);

        // Ensure superadmin role exists
        Role::firstOrCreate(['name' => 'superadmin', 'guard_name' => 'web']);

        setPermissionsTeamId(null);
        $user->assignRole('superadmin');

        $this->info("✅ Superadmin [{$name}] berhasil dibuat dengan email [{$email}].");
        return self::SUCCESS;
    }
}
