<?php

namespace App\Services;

use App\Models\Registration;
use Illuminate\Support\Facades\DB;

class QueueService
{
    /**
     * Generate unique queue number for outpatient registration
     * Format: OP-YYYYMMDD-POLYCODE-XXX
     * Example: OP-20260112-UMUM-001
     *
     * @param string $polyclinicCode
     * @return string
     */
    public function generateQueueNumber(string $polyclinicCode): string
    {
        return DB::transaction(function () use ($polyclinicCode) {
            $today = now()->format('Ymd');
            $prefix = "OP-{$today}-{$polyclinicCode}";

            // Get the last queue number for today and this polyclinic with row lock
            $lastRegistration = Registration::where('type', 'outpatient')
                ->where('queue_number', 'like', "{$prefix}-%")
                ->whereDate('registration_date', now()->toDateString())
                ->lockForUpdate()
                ->orderBy('queue_number', 'desc')
                ->first();

            if ($lastRegistration) {
                // Extract the sequence number from the last queue number
                // Format: OP-20260112-UMUM-001
                $parts = explode('-', $lastRegistration->queue_number);
                $lastSequence = (int) end($parts);
                $newSequence = $lastSequence + 1;
            } else {
                // First registration for this polyclinic today
                $newSequence = 1;
            }

            // Pad sequence to 3 digits
            $sequence = str_pad($newSequence, 3, '0', STR_PAD_LEFT);

            return "{$prefix}-{$sequence}";
        });
    }

    /**
     * Validate queue number format
     *
     * @param string $queueNumber
     * @return bool
     */
    public function isValidFormat(string $queueNumber): bool
    {
        // Pattern: OP-YYYYMMDD-POLYCODE-XXX
        $pattern = '/^OP-\d{8}-[A-Z0-9]+-\d{3}$/';
        return preg_match($pattern, $queueNumber) === 1;
    }

    /**
     * Check if queue number already exists
     *
     * @param string $queueNumber
     * @return bool
     */
    public function exists(string $queueNumber): bool
    {
        return Registration::where('queue_number', $queueNumber)->exists();
    }

    /**
     * Get current queue position for a polyclinic
     *
     * @param string $polyclinicCode
     * @return int
     */
    public function getCurrentPosition(string $polyclinicCode): int
    {
        $today = now()->format('Ymd');
        $prefix = "OP-{$today}-{$polyclinicCode}";

        return Registration::where('type', 'outpatient')
            ->where('queue_number', 'like', "{$prefix}-%")
            ->whereDate('registration_date', now()->toDateString())
            ->count();
    }
}
