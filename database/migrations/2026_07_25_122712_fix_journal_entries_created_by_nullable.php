<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Fix: journal_entries.created_by FK must be nullable + nullOnDelete()
     * so that when an Admin user is deleted (e.g., auto-cleanup after 24h),
     * the FK constraint does not block the deletion.
     */
    public function up(): void
    {
        Schema::table('journal_entries', function (Blueprint $table) {
            // Drop the old non-nullable FK constraint
            $table->dropForeign(['created_by']);

            // Make the column nullable so it can be set to NULL when user is deleted
            $table->unsignedBigInteger('created_by')->nullable()->change();

            // Re-add the FK with nullOnDelete so deleting a user sets created_by to NULL
            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('journal_entries', function (Blueprint $table) {
            // Revert: drop nullable FK
            $table->dropForeign(['created_by']);

            // Re-create as non-nullable (note: only safe if no NULL values exist)
            $table->unsignedBigInteger('created_by')->nullable(false)->change();

            $table->foreign('created_by')
                ->references('id')
                ->on('users');
        });
    }
};
