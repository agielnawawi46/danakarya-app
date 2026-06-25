<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('organization_id')->nullable()->after('id')->constrained('organizations')->nullOnDelete();
            $table->string('employee_id')->nullable()->after('organization_id');
            $table->string('department')->nullable()->after('employee_id');
            $table->text('salary')->nullable()->after('department'); // encrypted
            $table->string('phone')->nullable()->after('salary');
            $table->date('join_date')->nullable()->after('phone');
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active')->after('join_date');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['organization_id']);
            $table->dropColumn(['organization_id', 'employee_id', 'department', 'salary', 'phone', 'join_date', 'status']);
        });
    }
};
