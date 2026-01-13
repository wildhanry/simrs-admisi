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
        Schema::table('doctors', function (Blueprint $table) {
            $table->foreignId('polyclinic_id')->nullable()->after('specialization')->constrained()->onDelete('set null');
            $table->index(['polyclinic_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('doctors', function (Blueprint $table) {
            $table->dropForeign(['polyclinic_id']);
            $table->dropIndex(['polyclinic_id', 'is_active']);
            $table->dropColumn('polyclinic_id');
        });
    }
};
