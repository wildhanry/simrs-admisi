<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Doctor extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sip_number',
        'name',
        'specialization',
        'polyclinic_id',
        'phone',
        'email',
        'availability',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get all registrations for this doctor
     */
    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }

    /**
     * Get the polyclinic that owns the doctor
     */
    public function polyclinic()
    {
        return $this->belongsTo(Polyclinic::class);
    }

    /**
     * Scope for active doctors only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for searching doctors
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('sip_number', 'like', "%{$search}%")
                ->orWhere('specialization', 'like', "%{$search}%");
        });
    }

    /**
     * Scope for filtering by specialization
     */
    public function scopeSpecialization($query, string $specialization)
    {
        return $query->where('specialization', $specialization);
    }

    /**
     * Get today's registrations count
     */
    public function getTodayRegistrationsCountAttribute(): int
    {
        return $this->registrations()
            ->whereDate('registration_date', now()->toDateString())
            ->count();
    }

    /**
     * Get active registrations count
     */
    public function getActiveRegistrationsCountAttribute(): int
    {
        return $this->registrations()
            ->whereIn('status', [Registration::STATUS_WAITING, Registration::STATUS_IN_PROGRESS])
            ->count();
    }
}
