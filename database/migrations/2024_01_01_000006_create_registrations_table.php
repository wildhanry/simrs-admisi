<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('registrations', function (Blueprint $table) {
            $table->id();
            $table->string('registration_number', 50)->unique();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->comment('User who registered');
            
            // Type: outpatient or inpatient
            $table->enum('type', ['outpatient', 'inpatient'])->index();
            
            // For outpatient
            $table->foreignId('polyclinic_id')->nullable()->constrained()->nullOnDelete();
            
            // For inpatient
            $table->foreignId('bed_id')->nullable()->constrained()->nullOnDelete();
            $table->dateTime('admission_date')->nullable()->index();
            $table->dateTime('discharge_date')->nullable()->index();
            
            $table->date('registration_date')->index();
            $table->time('registration_time');
            $table->enum('payment_method', ['BPJS', 'cash', 'insurance'])->default('cash')->index();
            $table->text('complaint')->nullable();
            $table->text('diagnosis')->nullable();
            $table->enum('status', ['waiting', 'in_progress', 'completed', 'cancelled'])->default('waiting')->index();
            $table->text('notes')->nullable();
            
            $table->timestamps();
            $table->softDeletes();

            // Composite indexes for common queries
            $table->index(['registration_date', 'type'], 'registrations_date_type_idx');
            $table->index(['patient_id', 'registration_date']);
            $table->index(['doctor_id', 'registration_date']);
            $table->index(['status', 'type']);
            $table->index(['type', 'status', 'registration_date']);
            
            // Index for finding active inpatient registrations
            $table->index(['bed_id', 'status', 'discharge_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registrations');
    }
};
