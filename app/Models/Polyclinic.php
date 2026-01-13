<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Polyclinic extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
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
     * Get all registrations for this polyclinic
     */
    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }

    /**
     * Scope for active polyclinics only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get latest registration
     */
    public function latestRegistration()
    {
        return $this->hasOne(Registration::class)->latestOfMany();
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
