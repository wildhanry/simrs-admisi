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
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('medical_record_number', 50)->unique();
            $table->string('nik', 16)->unique()->nullable();
            $table->string('name', 150)->index();
            $table->date('birth_date')->index();
            $table->string('birth_place', 100)->nullable();
            $table->enum('gender', ['male', 'female'])->index();
            $table->text('address');
            $table->string('phone', 20)->nullable()->index();
            $table->enum('blood_type', ['A', 'B', 'AB', 'O', 'unknown'])->default('unknown');
            $table->enum('marital_status', ['single', 'married', 'divorced', 'widowed'])->nullable();
            $table->string('religion', 50)->nullable();
            $table->string('occupation', 100)->nullable();
            $table->string('emergency_contact_name', 150)->nullable();
            $table->string('emergency_contact_phone', 20)->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for search and filtering
            $table->index(['name', 'birth_date']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
