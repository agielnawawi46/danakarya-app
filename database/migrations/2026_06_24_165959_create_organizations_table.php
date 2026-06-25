<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('Koperasi Baru (Belum Dikonfigurasi)');
            $table->string('legal_name')->nullable();
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('legal_number')->nullable();
            $table->string('logo')->nullable();
            $table->boolean('is_configured')->default(false);
            $table->boolean('is_active')->default(true);

            // Financial Rules
            $table->decimal('simpanan_pokok', 15, 2)->default(0);
            $table->decimal('simpanan_wajib', 15, 2)->default(0);
            $table->decimal('loan_interest_rate', 5, 2)->default(1.5); // % per month
            $table->integer('loan_max_tenor')->default(24); // months
            $table->decimal('loan_max_plafon', 15, 2)->default(50000000);
            $table->enum('loan_interest_method', ['flat', 'annuity'])->default('flat');

            // SHU Allocation Percentages
            $table->decimal('shu_dana_cadangan_pct', 5, 2)->default(40);
            $table->decimal('shu_anggota_pct', 5, 2)->default(40);
            $table->decimal('shu_pengurus_pct', 5, 2)->default(5);
            $table->decimal('shu_karyawan_pct', 5, 2)->default(5);
            $table->decimal('shu_pendidikan_pct', 5, 2)->default(10);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};
