<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bed extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Bed status constants
     */
    const STATUS_AVAILABLE = 'available';
    const STATUS_OCCUPIED = 'occupied';
    const STATUS_MAINTENANCE = 'maintenance';

    protected $fillable = [
        'ward_id',
        'bed_number',
        'status',
        'notes',
    ];

    /**
     * Get the ward this bed belongs to
     */
    public function ward()
    {
        return $this->belongsTo(Ward::class);
    }

    /**
     * Get all registrations using this bed
     */
    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }

    /**
     * Get active registration for this bed
     */
    public function activeRegistration()
    {
        return $this->hasOne(Registration::class)
            ->where('type', 'inpatient')
            ->whereIn('status', ['waiting', 'in_progress'])
            ->whereNull('discharge_date')
            ->latest();
    }

    /**
     * Scope for available beds only
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    /**
     * Check if bed is available
     */
    public function isAvailable(): bool
    {
        return $this->status === self::STATUS_AVAILABLE;
    }

    /**
     * Check if bed is occupied
     */
    public function isOccupied(): bool
    {
        return $this->status === self::STATUS_OCCUPIED;
    }

    /**
     * Check if bed is under maintenance
     */
    public function isMaintenance(): bool
    {
        return $this->status === self::STATUS_MAINTENANCE;
    }

    /**
     * Mark bed as occupied
     */
    public function markAsOccupied(): void
    {
        $this->update(['status' => self::STATUS_OCCUPIED]);
    }

    /**
     * Mark bed as available
     */
    public function markAsAvailable(): void
    {
        $this->update(['status' => self::STATUS_AVAILABLE]);
    }

    /**
     * Mark bed as maintenance
     */
    public function markAsMaintenance(): void
    {
        $this->update(['status' => self::STATUS_MAINTENANCE]);
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            self::STATUS_AVAILABLE => 'Tersedia',
            self::STATUS_OCCUPIED => 'Terisi',
            self::STATUS_MAINTENANCE => 'Perawatan',
            default => 'Unknown',
        };
    }

    /**
     * Get full bed identifier (Ward - Bed Number)
     */
    public function getFullIdentifierAttribute(): string
    {
        return $this->ward->name . ' - ' . $this->bed_number;
    }

    /**
     * Scope for beds in maintenance
     */
    public function scopeMaintenance($query)
    {
        return $query->where('status', self::STATUS_MAINTENANCE);
    }

    /**
     * Scope for occupied beds
     */
    public function scopeOccupied($query)
    {
        return $query->where('status', self::STATUS_OCCUPIED);
    }
}
