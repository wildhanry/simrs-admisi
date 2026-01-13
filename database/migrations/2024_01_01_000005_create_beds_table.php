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
        Schema::create('beds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ward_id')->constrained()->cascadeOnDelete();
            $table->string('bed_number', 20);
            $table->enum('status', ['available', 'occupied', 'maintenance'])->default('available')->index();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Unique constraint for bed number within ward
            $table->unique(['ward_id', 'bed_number'], 'beds_ward_number_unique');
            
            // Composite indexes for finding available beds
            $table->index(['ward_id', 'status']);
            $table->index(['status', 'ward_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('beds');
    }
};
