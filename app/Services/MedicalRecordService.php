<?php

namespace App\Services;

use App\Models\Patient;
use Illuminate\Support\Facades\DB;

class MedicalRecordService
{
    /**
     * Generate unique medical record number
     * Format: RM-YYYYMMDD-XXXX
     * Example: RM-20260112-0001
     */
    public function generateMedicalRecordNumber(): string
    {
        return DB::transaction(function () {
            $date = now()->format('Ymd');
            $prefix = "RM-{$date}-";

            // Get the last medical record number for today
            $lastRecord = Patient::where('medical_record_number', 'like', "{$prefix}%")
                ->lockForUpdate()
                ->orderBy('medical_record_number', 'desc')
                ->first();

            if ($lastRecord) {
                // Extract the sequence number and increment
                $lastNumber = (int) substr($lastRecord->medical_record_number, -4);
                $newNumber = $lastNumber + 1;
            } else {
                // First record for today
                $newNumber = 1;
            }

            // Format with leading zeros
            return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
        });
    }

    /**
     * Validate medical record number format
     */
    public function isValidFormat(string $mrn): bool
    {
        return (bool) preg_match('/^RM-\d{8}-\d{4}$/', $mrn);
    }

    /**
     * Check if medical record number exists
     */
    public function exists(string $mrn): bool
    {
        return Patient::where('medical_record_number', $mrn)->exists();
    }
}
