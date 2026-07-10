<?php

namespace App\Console\Commands;

use App\Models\Organization;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CleanupUnconfiguredOrganizations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:cleanup-unconfigured-organizations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up unconfigured organizations and their associated admin accounts older than 24 hours';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting cleanup of unconfigured organizations...');

        // Find organizations that are not configured and were created more than 24 hours ago
        $organizations = Organization::where('is_configured', false)
            ->where('created_at', '<', now()->subHours(24))
            ->get();

        if ($organizations->isEmpty()) {
            $this->info('No unconfigured organizations found to clean up.');
            return;
        }

        $deletedCount = 0;

        foreach ($organizations as $org) {
            $orgName = $org->name;
            $this->info("Processing deletion for organization: {$orgName} (ID: {$org->id})");

            // Delete associated users (Admins) manually since constrained()->nullOnDelete() prevents cascade
            $users = $org->users;
            foreach ($users as $user) {
                $this->info("   Deleting user: {$user->email}");
                $user->delete();
            }

            // Organization's other relations (deposits, loans, accounts, journals, etc.)
            // are set to cascadeOnDelete() in migrations, so deleting the org will clean them up.
            $org->delete();
            $deletedCount++;
            
            Log::info("Auto-cleanup deleted unconfigured organization: {$orgName} and its users.");
        }

        $this->info("Cleanup completed. Deleted {$deletedCount} organization(s).");
    }
}
