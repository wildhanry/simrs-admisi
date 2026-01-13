<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ward extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Ward class constants
     */
    const CLASS_VIP = 'VIP';
    const CLASS_I = 'I';
    const CLASS_II = 'II';
    const CLASS_III = 'III';

    protected $fillable = [
        'code',
        'name',
        'class',
        'location',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get all beds in this ward
     */
    public function beds()
    {
        return $this->hasMany(Bed::class);
    }

    /**
     * Get available beds in this ward
     */
    public function availableBeds()
    {
        return $this->beds()->where('status', 'available');
    }

    /**
     * Scope for active wards only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get total beds count
     */
    public function getTotalBedsAttribute(): int
    {
        return $this->beds()->count();
    }

    /**
     * Get available beds count
     */
    public function getAvailableBedsCountAttribute(): int
    {
        return $this->availableBeds()->count();
    }

    /**
     * Get occupied beds count
     */
    public function getOccupiedBedsCountAttribute(): int
    {
        return $this->beds()->where('status', 'occupied')->count();
    }

    /**
     * Get occupancy rate (percentage)
     */
    public function getOccupancyRateAttribute(): float
    {
        $total = $this->total_beds;
        if ($total === 0) return 0;
        
        return round(($this->occupied_beds_count / $total) * 100, 2);
    }

    /**
     * Scope for filtering by class
     */
    public function scopeClass($query, string $class)
    {
        return $query->where('class', $class);
    }

    /**
     * Get class label
     */
    public function getClassLabelAttribute(): string
    {
        return match($this->class) {
            self::CLASS_VIP => 'VIP',
            self::CLASS_I => 'Kelas I',
            self::CLASS_II => 'Kelas II',
            self::CLASS_III => 'Kelas III',
            default => 'Unknown',
        };
    }
}
