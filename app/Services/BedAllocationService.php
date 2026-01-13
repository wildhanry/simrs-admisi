<?php

namespace App\Services;

use App\Models\Bed;
use Illuminate\Support\Facades\DB;
use Exception;

class BedAllocationService
{
    /**
     * Allocate a bed with pessimistic locking to prevent double booking
     *
     * @param int $bedId
     * @return Bed
     * @throws Exception
     */
    public function allocateBed(int $bedId): Bed
    {
        return DB::transaction(function () use ($bedId) {
            // Lock the bed row for update (pessimistic locking)
            $bed = Bed::where('id', $bedId)
                ->lockForUpdate()
                ->first();

            if (!$bed) {
                throw new Exception('Bed not found.');
            }

            // Validate bed is still available
            if ($bed->status !== 'available') {
                throw new Exception("Bed {$bed->bed_number} is no longer available. Current status: {$bed->status}");
            }

            // Mark bed as occupied
            $bed->update([
                'status' => 'occupied',
                'occupied_at' => now(),
            ]);

            return $bed;
        });
    }

    /**
     * Release a bed and mark it as available
     *
     * @param int $bedId
     * @return Bed
     * @throws Exception
     */
    public function releaseBed(int $bedId): Bed
    {
        return DB::transaction(function () use ($bedId) {
            $bed = Bed::where('id', $bedId)
                ->lockForUpdate()
                ->first();

            if (!$bed) {
                throw new Exception('Bed not found.');
            }

            // Mark bed as available
            $bed->update([
                'status' => 'available',
                'occupied_at' => null,
            ]);

            return $bed;
        });
    }

    /**
     * Get available beds grouped by ward
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAvailableBeds()
    {
        return Bed::with('ward')
            ->where('status', 'available')
            ->orderBy('ward_id')
            ->orderBy('bed_number')
            ->get();
    }

    /**
     * Get available beds count by ward
     *
     * @return array
     */
    public function getAvailabilityByWard(): array
    {
        $wards = Bed::with('ward')
            ->select('ward_id', DB::raw('COUNT(*) as available_count'))
            ->where('status', 'available')
            ->groupBy('ward_id')
            ->get();

        return $wards->mapWithKeys(function ($item) {
            return [$item->ward_id => [
                'ward_name' => $item->ward->name,
                'ward_class' => $item->ward->class,
                'available_count' => $item->available_count,
            ]];
        })->toArray();
    }

    /**
     * Check if a bed is available (without locking)
     *
     * @param int $bedId
     * @return bool
     */
    public function isBedAvailable(int $bedId): bool
    {
        return Bed::where('id', $bedId)
            ->where('status', 'available')
            ->exists();
    }

    /**
     * Validate bed availability before allocation
     *
     * @param int $bedId
     * @throws Exception
     */
    public function validateBedAvailability(int $bedId): void
    {
        $bed = Bed::find($bedId);

        if (!$bed) {
            throw new Exception('Selected bed does not exist.');
        }

        if ($bed->status !== 'available') {
            throw new Exception("Bed {$bed->bed_number} in {$bed->ward->name} is currently {$bed->status}.");
        }
    }
}
