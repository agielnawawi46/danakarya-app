<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shu_distributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('year');
            $table->decimal('total_profit', 15, 2)->default(0); // SHU Bersih = Total Pendapatan - Total Beban
            $table->decimal('total_dana_cadangan', 15, 2)->default(0);
            $table->decimal('total_anggota', 15, 2)->default(0);
            $table->decimal('total_pengurus', 15, 2)->default(0);
            $table->decimal('total_karyawan', 15, 2)->default(0);
            $table->decimal('total_pendidikan', 15, 2)->default(0);
            $table->decimal('total_jasa_modal', 15, 2)->default(0);   // breakdown from total_anggota
            $table->decimal('total_jasa_pinjaman', 15, 2)->default(0); // breakdown from total_anggota
            $table->enum('status', ['draft', 'approved', 'distributed'])->default('draft');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('distributed_at')->nullable();
            $table->timestamps();

            $table->unique(['organization_id', 'year']);
        });

        Schema::create('shu_member_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shu_distribution_id')->constrained()->cascadeOnDelete();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('total_simpanan', 15, 2)->default(0); // Basis Jasa Modal
            $table->decimal('total_bunga_paid', 15, 2)->default(0); // Basis Jasa Pinjaman
            $table->decimal('jasa_modal', 15, 2)->default(0);
            $table->decimal('jasa_pinjaman', 15, 2)->default(0);
            $table->decimal('total_shu', 15, 2)->default(0);
            $table->foreignId('deposit_id')->nullable()->constrained('deposits')->nullOnDelete(); // Reference ke deposit SHU
            $table->timestamp('deposited_at')->nullable();
            $table->timestamps();

            $table->index(['shu_distribution_id', 'user_id']);
            $table->index(['organization_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shu_member_details');
        Schema::dropIfExists('shu_distributions');
    }
};
