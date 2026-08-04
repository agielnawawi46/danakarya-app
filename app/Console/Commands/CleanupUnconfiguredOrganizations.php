<?php

namespace App\Console\Commands;

use App\Models\Organization;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
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
    public function handle(): void
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
        $failedCount  = 0;

        foreach ($organizations as $org) {
            $orgName = $org->name;
            $orgId   = $org->id;

            try {
                DB::transaction(function () use ($org, $orgName) {
                    // Step 1: Load all admin users of this unconfigured org
                    $users = $org->users()->get();

                    foreach ($users as $user) {
                        $this->info("   Deleting user: {$user->email} (ID: {$user->id})");

                        // Step 2: Remove all Spatie permission roles first.
                        // model_has_roles has FK to users.id — must clean this before deleting user.
                        $user->syncRoles([]);

                        // Step 3: Delete the user. Since journal_entries.created_by now has
                        // nullOnDelete(), any journals this user created will have created_by = NULL
                        // rather than causing a FK violation.
                        $user->delete();
                    }

                    // Step 4: Delete the organization.
                    // All other related tables (accounts, deposits, loans, journals, etc.)
                    // have cascadeOnDelete() set in migrations, so they are automatically removed.
                    $org->delete();
                });

                $deletedCount++;
                $this->info("✓ Deleted organization: {$orgName} (ID: {$orgId})");
                Log::info("Auto-cleanup: deleted unconfigured org [{$orgName}] ID={$orgId} and its users.");

            } catch (\Throwable $e) {
                $failedCount++;
                $this->error("✗ Failed to delete organization: {$orgName} (ID: {$orgId}) — {$e->getMessage()}");
                Log::error("Auto-cleanup FAILED for org [{$orgName}] ID={$orgId}: {$e->getMessage()}", [
                    'exception' => $e,
                ]);
            }
        }

        $this->info("─────────────────────────────────────────────");
        $this->info("Cleanup completed. Deleted: {$deletedCount} | Failed: {$failedCount}");

        if ($failedCount > 0) {
            $this->warn("Some organizations could not be deleted. Check logs for details.");
        }
    }
}
